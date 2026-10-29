<?php

namespace Tests\Feature;

use App\Models\Attestation;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Report;
use App\Models\Stagiaire;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisplayStateConsistencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        User::query()->update(['password_changed_at' => now()]);
    }

    public function test_admin_pending_count_includes_both_states_ready_for_delivery(): void
    {
        foreach (['en_attente', 'valide_mentor', 'approuve_admin', 'envoye', 'refuse'] as $status) {
            Attestation::create(['stagiaire_id' => Stagiaire::firstOrFail()->id, 'type' => 'attestation', 'statut' => $status]);
        }
        $this->actingAs(User::where('role', 'admin')->firstOrFail())->get(route('admin.dashboard'))
            ->assertOk()->assertViewHas('stats', fn ($stats) => $stats['attestations_en_attente'] === 2);
    }

    public function test_approved_attestations_are_available_in_both_status_filters(): void
    {
        Attestation::create(['stagiaire_id' => Stagiaire::firstOrFail()->id, 'type' => 'attestation', 'statut' => 'approuve_admin']);
        foreach (['admin', 'mentor'] as $role) {
            $html = $this->actingAs(User::where('role', $role)->firstOrFail())->get(route($role.'.attestations.index'))->assertOk()->getContent();
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $this->assertCount(1, (new \DOMXPath($dom))->query('//select[@id="filterStatut"]/option[@value="approuve_admin"]'));
        }
    }

    public function test_refused_attestation_is_not_described_as_approved_by_the_mentor(): void
    {
        $attestation = Attestation::create([
            'stagiaire_id' => Stagiaire::firstOrFail()->id, 'type' => 'attestation', 'statut' => 'refuse',
            'valide_par_mentor' => User::where('role', 'mentor')->firstOrFail()->id, 'valide_le_mentor' => now(),
        ]);
        $this->actingAs(User::where('role', 'admin')->firstOrFail())->get(route('admin.attestations.show', $attestation))
            ->assertOk()->assertSee('Refusé par le mentor')->assertDontSee('Validé par le mentor');
    }

    public function test_mentor_project_count_matches_the_accessible_project_list(): void
    {
        $mentor = User::where('role', 'mentor')->firstOrFail();
        $other = User::factory()->create(['role' => 'mentor']);
        $own = Project::create(['titre' => 'Créé', 'mentor_id' => $mentor->id, 'date_debut' => today(), 'statut' => 'en_cours', 'priorite' => 'normale']);
        $own->mentors()->attach($mentor);
        $invited = Project::create(['titre' => 'Invité', 'mentor_id' => $other->id, 'date_debut' => today(), 'statut' => 'en_cours', 'priorite' => 'normale']);
        $invited->mentors()->attach([$other->id, $mentor->id]);
        Project::create(['titre' => 'Privé', 'mentor_id' => $other->id, 'date_debut' => today(), 'statut' => 'en_cours', 'priorite' => 'normale']);
        $this->actingAs($mentor)->get(route('mentor.dashboard'))->assertOk()->assertViewHas('stats', fn ($stats) => $stats['projets'] === 2);
        $this->get(route('mentor.projects.index'))->assertOk()->assertViewHas('projets', fn ($projets) => $projets->total() === 2);
    }

    public function test_pending_work_is_not_hidden_when_the_stage_has_ended(): void
    {
        $stagiaire = Stagiaire::firstOrFail();
        $stagiaire->update(['statut' => 'termine']);
        Report::create(['stagiaire_id' => $stagiaire->id, 'titre' => 'Rapport restant', 'type' => 'final', 'fichier' => 'audit.pdf', 'statut' => 'soumis']);
        Attestation::create(['stagiaire_id' => $stagiaire->id, 'type' => 'attestation', 'statut' => 'en_attente']);
        Permission::create(['stagiaire_id' => $stagiaire->id, 'date_debut' => today(), 'date_fin' => today(), 'motif' => 'À traiter', 'statut' => 'en_attente']);
        $this->actingAs(User::where('role', 'mentor')->firstOrFail())->get(route('mentor.dashboard'))->assertOk()
            ->assertViewHas('stats', fn ($stats) => $stats['stagiaires'] === 0 && $stats['rapports_soumis'] === 1 && $stats['attestations_a_valider'] === 1 && $stats['permissions_en_attente'] === 1)
            ->assertSee('Rapport restant')->assertSee('À traiter');
    }

    public function test_report_status_in_admin_stagiaire_profile_does_not_link_to_attendance(): void
    {
        $stagiaire = Stagiaire::firstOrFail();
        Report::create(['stagiaire_id' => $stagiaire->id, 'titre' => 'Rapport', 'type' => 'journalier', 'fichier' => 'audit.pdf', 'statut' => 'soumis']);
        $html = $this->actingAs(User::where('role', 'admin')->firstOrFail())->get(route('admin.stagiaires.show', $stagiaire))->assertOk()->getContent();
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $links = (new \DOMXPath($dom))->query('//h3[@id="rapports-titre"]/ancestor::section[1]//a');
        $this->assertCount(0, $links);
        $this->assertStringContainsString('Soumis', $html);
    }

    public function test_project_progress_matches_task_completion_in_both_spaces(): void
    {
        $mentor = User::where('role', 'mentor')->firstOrFail();
        $stagiaire = Stagiaire::firstOrFail();
        $project = Project::create(['titre' => 'Progression', 'mentor_id' => $mentor->id, 'date_debut' => today(), 'statut' => 'en_cours', 'priorite' => 'normale']);
        $project->mentors()->attach($mentor);
        $project->stagiaires()->attach($stagiaire);
        $tasks = collect();
        foreach (['a_faire', 'en_cours'] as $status) {
            $tasks->push(Task::create(['project_id' => $project->id, 'stagiaire_id' => $stagiaire->id, 'titre' => 'Tâche', 'statut' => $status, 'priorite' => 'normale', 'difficulte' => 'moyen']));
        }
        foreach ([0, 50, 100] as $index => $expectedProgress) {
            if ($index > 0) {
                $this->actingAs($stagiaire->user)->patch(route('stagiaire.tasks.updateStatut', $tasks[$index - 1]), ['statut' => 'termine'])->assertSessionHasNoErrors();
            }
            foreach ([$mentor, $stagiaire->user] as $user) {
                $html = $this->actingAs($user)->get(route($user->role.'.projects.show', $project))->assertOk()->getContent();
                $dom = new \DOMDocument();
                @$dom->loadHTML($html);
                $bars = (new \DOMXPath($dom))->query('//*[@role="progressbar" and starts-with(@aria-label, "Progression du projet ")]');
                $this->assertCount(1, $bars);
                $this->assertSame((string) $expectedProgress, $bars->item(0)->getAttribute('aria-valuenow'));
            }
        }
    }
}
