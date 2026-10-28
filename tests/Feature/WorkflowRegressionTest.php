<?php

namespace Tests\Feature;

use App\Models\Attestation;
use App\Models\Document;
use App\Models\Evenement;
use App\Models\Project;
use App\Models\Stagiaire;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkflowRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Storage::fake('public');
        $this->seed(UserSeeder::class);
        User::query()->update(['password_changed_at' => now()]);
    }

    private function admin(): User
    {
        return User::where('role', 'admin')->firstOrFail();
    }

    private function mentor(): User
    {
        return User::where('role', 'mentor')->firstOrFail();
    }

    private function account(string $role, string $email): array
    {
        return [
            'nom' => 'Audit', 'prenom' => 'Compte', 'email' => $email,
            'role' => $role, 'password' => 'Audit-password-2026',
            'password_confirmation' => 'Audit-password-2026',
            'date_debut' => today()->toDateString(),
            'date_fin' => today()->addMonths(2)->toDateString(),
            'mentor_id' => $this->mentor()->id,
        ];
    }

    public function test_users_form_and_creation_preserve_each_role_profile(): void
    {
        $this->actingAs($this->admin())->get(route('admin.users.create'))->assertOk()->assertViewHas('mentors');
        foreach (['admin', 'mentor', 'stagiaire'] as $role) {
            $data = $this->account($role, "$role@example.test");
            $this->post(route('admin.users.store'), $data)->assertSessionHasNoErrors()->assertRedirect(route('admin.users.index'));
            $user = User::where('email', $data['email'])->firstOrFail();
            $this->assertSame($role, $user->role);
            if ($role === 'mentor') $this->assertNotNull($user->mentor);
            if ($role === 'stagiaire') {
                $this->assertNotNull($user->stagiaire);
                $this->assertSame($data['date_fin'], $user->stagiaire->date_fin->format('Y-m-d'));
            }
        }
        Mail::assertSentCount(2);
    }

    public function test_role_changes_require_stage_dates_and_create_the_related_profile(): void
    {
        $this->actingAs($this->admin());
        $data = $this->account('admin', 'switch@example.test');
        $this->post(route('admin.users.store'), $data)->assertSessionHasNoErrors();
        $user = User::where('email', $data['email'])->firstOrFail();
        $data['role'] = 'stagiaire';
        $invalid = $data;
        unset($invalid['date_debut'], $invalid['date_fin']);
        $this->put(route('admin.users.update', $user), $invalid)->assertSessionHasErrors(['date_debut', 'date_fin']);
        $this->assertSame('admin', $user->fresh()->role);
        $this->put(route('admin.users.update', $user), $data)->assertSessionHasNoErrors();
        $this->assertNotNull($user->fresh()->stagiaire);
        $data['role'] = 'mentor';
        $this->put(route('admin.users.update', $user), $data)->assertSessionHasNoErrors();
        $this->assertNotNull($user->fresh()->mentor);
    }

    public function test_revoking_public_document_sharing_does_not_keep_obsolete_recipients(): void
    {
        $admin = $this->admin();
        $stagiaire = User::where('role', 'stagiaire')->firstOrFail();
        $document = Document::create(['user_id' => $admin->id, 'titre' => 'Document', 'fichier' => 'audit.pdf', 'partage_tous' => false]);
        $document->partagesAvec()->attach($stagiaire);
        $this->actingAs($admin)->put(route('documents.update', $document), ['titre' => 'Document', 'partage_tous' => 1])->assertSessionHasNoErrors();
        $this->put(route('documents.update', $document), ['titre' => 'Document'])->assertSessionHasNoErrors();
        $this->assertFalse((bool) $document->fresh()->partage_tous);
        $this->assertSame(0, $document->partagesAvec()->count());
        $this->actingAs($stagiaire)->get(route('documents.show', $document))->assertForbidden();
    }

    public function test_event_sharing_can_be_unchecked(): void
    {
        $event = Evenement::create(['cree_par' => $this->admin()->id, 'titre' => 'Événement', 'type' => 'information', 'partage_tous' => true]);
        $this->actingAs($this->admin())->put(route('admin.evenements.update', $event), ['titre' => 'Événement', 'type' => 'information'])->assertSessionHasNoErrors();
        $this->assertFalse($event->fresh()->partage_tous);
    }

    public function test_mentor_assignment_and_project_participants_are_checked(): void
    {
        $stagiaire = Stagiaire::firstOrFail();
        $this->actingAs($this->admin())->post(route('admin.mentors.doAssign'), ['mentor_id' => $this->admin()->id, 'stagiaire_id' => $stagiaire->id])->assertSessionHasErrors('mentor_id');
        $project = ['titre' => 'Projet', 'date_debut' => today()->toDateString(), 'statut' => 'en_cours', 'priorite' => 'normale', 'stagiaires' => [$stagiaire->id], 'collaborateurs' => [$this->admin()->id]];
        $this->actingAs($this->mentor())->post(route('mentor.projects.store'), $project)->assertSessionHasErrors('collaborateurs.0');
        $stagiaire->update(['mentor_id' => null]);
        unset($project['collaborateurs']);
        $this->post(route('mentor.projects.store'), $project)->assertSessionHasErrors('stagiaires.0');
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_reports_cannot_be_attached_to_an_unassigned_project(): void
    {
        $project = Project::create(['titre' => 'Privé', 'mentor_id' => $this->mentor()->id, 'date_debut' => today(), 'statut' => 'en_cours', 'priorite' => 'normale']);
        $this->actingAs(User::where('role', 'stagiaire')->firstOrFail())->post(route('stagiaire.reports.store'), [
            'titre' => 'Rapport', 'type' => 'journalier', 'project_id' => $project->id,
            'fichier' => UploadedFile::fake()->create('audit.pdf', 1, 'application/pdf'),
        ])->assertSessionHasErrors('project_id');
        $this->assertDatabaseCount('reports', 0);
    }

    public function test_attestation_upload_and_validation_obey_workflow_states(): void
    {
        $attestation = Attestation::create(['stagiaire_id' => Stagiaire::firstOrFail()->id, 'type' => 'attestation', 'statut' => 'en_attente']);
        $this->actingAs($this->admin())->put(route('admin.attestations.upload', $attestation), ['fichier' => UploadedFile::fake()->create('audit.pdf', 1, 'application/pdf')])->assertForbidden();
        $attestation->update(['statut' => 'envoye']);
        $this->actingAs($this->mentor())->put(route('mentor.attestations.doValidate', $attestation), ['statut' => 'refuse'])->assertForbidden();
        $this->assertSame('envoye', $attestation->fresh()->statut);
    }

    public function test_attestation_tracking_advances_at_each_completed_action(): void
    {
        $stagiaire = User::where('role', 'stagiaire')->firstOrFail();
        $this->actingAs($stagiaire)->post(route('stagiaire.attestations.store'), ['type' => 'attestation'])
            ->assertSessionHasNoErrors();
        $attestation = Attestation::firstOrFail();
        $this->assertAttestationSteps($stagiaire, ['bg-emerald-500', 'bg-slate-300', 'bg-slate-300']);

        $this->actingAs($this->mentor())->put(route('mentor.attestations.doValidate', $attestation), ['statut' => 'valide_mentor'])
            ->assertSessionHasNoErrors();
        $this->assertAttestationSteps($stagiaire, ['bg-emerald-500', 'bg-emerald-500', 'bg-slate-300']);

        $this->actingAs($this->admin())->put(route('admin.attestations.upload', $attestation), [
            'fichier' => UploadedFile::fake()->create('attestation.pdf', 1, 'application/pdf'),
        ])->assertSessionHasNoErrors();
        $this->assertAttestationSteps($stagiaire, ['bg-emerald-500', 'bg-emerald-500', 'bg-emerald-500']);
    }

    public function test_attestation_tracking_keeps_submission_done_when_refused_or_awaiting_delivery(): void
    {
        $stagiaire = User::where('role', 'stagiaire')->firstOrFail();
        $attestation = Attestation::create(['stagiaire_id' => $stagiaire->stagiaire->id, 'type' => 'attestation', 'statut' => 'approuve_admin']);
        $this->assertAttestationSteps($stagiaire, ['bg-emerald-500', 'bg-emerald-500', 'bg-slate-300']);
        $attestation->update(['statut' => 'refuse']);
        $this->assertAttestationSteps($stagiaire, ['bg-emerald-500', 'bg-red-500', 'bg-slate-300']);
    }

    private function assertAttestationSteps(User $stagiaire, array $expectedColors): void
    {
        $html = $this->actingAs($stagiaire)->get(route('stagiaire.attestations.index'))->assertOk()->getContent();
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        $bars = (new \DOMXPath($dom))->query('//ol[@aria-label="Étapes de la demande"]/li/div');
        $this->assertCount(3, $bars);
        foreach ($expectedColors as $index => $color) {
            $this->assertContains($color, explode(' ', $bars->item($index)->getAttribute('class')));
        }
    }

    public function test_password_reset_counts_as_the_first_password_change(): void
    {
        $user = User::where('role', 'stagiaire')->firstOrFail();
        $user->update(['password_changed_at' => null]);
        $token = Password::createToken($user);
        $this->post(route('password.update'), ['email' => $user->email, 'token' => $token, 'password' => 'Nouveau-password-2026', 'password_confirmation' => 'Nouveau-password-2026'])->assertRedirect(route('login'));
        $this->assertNotNull($user->fresh()->password_changed_at);
        $this->assertTrue(Hash::check('Nouveau-password-2026', $user->fresh()->password));
    }

    public function test_invalid_presence_filters_and_overlong_motifs_do_not_raise_server_errors(): void
    {
        foreach ([$this->admin(), $this->mentor()] as $user) {
            $this->actingAs($user)->getJson(route($user->role.'.presences.index', ['date' => 'incorrect']))->assertUnprocessable()->assertJsonValidationErrors('date');
            $this->getJson(route($user->role.'.presences.formMarquage', ['date' => 'incorrect']))->assertUnprocessable()->assertJsonValidationErrors('date');
            $this->postJson(route($user->role.'.presences.marquerLot'), ['date' => today()->toDateString(), 'presences' => [['stagiaire_id' => Stagiaire::firstOrFail()->id, 'statut' => 'retard', 'motif' => str_repeat('A', 400)]]])->assertUnprocessable()->assertJsonValidationErrors('presences.0.motif');
        }
    }
}
