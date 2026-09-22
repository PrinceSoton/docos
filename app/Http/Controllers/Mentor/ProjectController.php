<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projets = Project::with('stagiaires.user', 'mentors', 'tasks')
            ->where(function ($q) {
                $q->where('mentor_id', Auth::id())
                  ->orWhereHas('mentors', fn($q2) => $q2->where('user_id', Auth::id()));
            })
            ->paginate(15);

        return view('mentor.projects.index', compact('projets'));
    }

    public function create()
    {
        $stagiaires = Stagiaire::with('user')
            ->where('mentor_id', Auth::id())
            ->where('statut', 'en_cours')
            ->get();

        // Autres mentors disponibles pour invitation
        $autresMentors = User::where('role', 'mentor')
            ->where('id', '!=', Auth::id())
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        return view('mentor.projects.create', compact('stagiaires', 'autresMentors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'              => 'required|string|max:200',
            'description'        => 'nullable|string',
            'date_debut'         => 'required|date',
            'date_fin'           => 'nullable|date|after_or_equal:date_debut',
            'statut'             => 'required|in:en_attente,en_cours,termine,suspendu',
            'priorite'           => 'required|in:faible,normale,haute,urgente',
            'stagiaires'         => 'required|array|min:1',
            'stagiaires.*'       => 'integer',
            'collaborateurs'     => 'nullable|array',
            'collaborateurs.*'   => 'integer',
        ]);

        $stagiaireIds = Stagiaire::where('mentor_id', Auth::id())
            ->where('statut', 'en_cours')
            ->whereIn('id', $request->stagiaires)
            ->pluck('id');
        abort_unless($stagiaireIds->count() === count(array_unique($request->stagiaires)), 403, 'Stagiaire non affecté à ce mentor.');

        $collaborateurIds = User::where('role', 'mentor')
            ->where('actif', true)
            ->whereIn('id', $request->input('collaborateurs', []))
            ->pluck('id')->all();
        abort_unless(count($collaborateurIds) === count(array_unique($request->input('collaborateurs', []))), 403, 'Collaborateur invalide.');

        $projet = Project::create([
            'titre'       => $request->titre,
            'description' => $request->description,
            'date_debut'  => $request->date_debut,
            'date_fin'    => $request->date_fin,
            'statut'      => $request->statut,
            'priorite'    => $request->priorite,
            'mentor_id'   => Auth::id(),
        ]);

        // Enregistrer les mentors (créateur + collaborateurs) dans le pivot
        $mentorIds = array_unique(array_merge(
            [Auth::id()],
            (array) $request->input('collaborateurs', [])
        ));
        $projet->mentors()->sync($mentorIds);

        $projet->stagiaires()->sync($stagiaireIds);

        return redirect()->route('mentor.projects.index')->with('succes', 'Projet créé avec succès.');
    }

    public function show(Project $project)
    {
        abort_unless($project->hasAccess(Auth::user()), 403);
        $project->load('stagiaires.user', 'tasks.stagiaire.user', 'reports.stagiaire.user', 'mentors');

        // Mentors disponibles pour invitation (excluant ceux déjà présents)
        $existingMentorIds = $project->mentors->pluck('id')->toArray();
        $autresMentors = User::where('role', 'mentor')
            ->whereNotIn('id', $existingMentorIds)
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        return view('mentor.projects.show', compact('project', 'autresMentors'));
    }

    public function edit(Project $project)
    {
        abort_unless($project->hasAccess(Auth::user()), 403);

        $mentorIds = $project->mentors()->pluck('users.id')->toArray();

        // Stagiaires disponibles : ceux des mentors du projet + ceux déjà rattachés
        $stagiaires = Stagiaire::with('user')
            ->where(function ($q) use ($mentorIds, $project) {
                $q->whereIn('mentor_id', $mentorIds)
                  ->orWhereIn('id', $project->stagiaires()->pluck('stagiaires.id'));
            })
            ->get();

        // Mentors disponibles pour invitation
        $autresMentors = User::where('role', 'mentor')
            ->whereNotIn('id', $mentorIds)
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        return view('mentor.projects.edit', compact('project', 'stagiaires', 'autresMentors'));
    }

    public function update(Request $request, Project $project)
    {
        abort_unless($project->isOwner(Auth::user()), 403, 'Seul le créateur peut modifier ce projet.');

        $request->validate([
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string',
            'date_debut'   => 'required|date',
            'date_fin'     => 'nullable|date|after_or_equal:date_debut',
            'statut'       => 'required|in:en_attente,en_cours,termine,suspendu',
            'priorite'     => 'required|in:faible,normale,haute,urgente',
            'stagiaires'   => 'required|array|min:1',
            'stagiaires.*' => 'integer',
        ]);

        $stagiaireIds = Stagiaire::whereIn('mentor_id', $project->mentors()->pluck('users.id'))
            ->where('statut', 'en_cours')
            ->whereIn('id', $request->stagiaires)
            ->pluck('id');
        abort_unless($stagiaireIds->count() === count(array_unique($request->stagiaires)), 403, 'Stagiaire non affecté au projet.');

        $project->update($request->only('titre', 'description', 'date_debut', 'date_fin', 'statut', 'priorite'));
        $project->stagiaires()->sync($stagiaireIds);

        return redirect()->route('mentor.projects.index')->with('succes', 'Projet mis à jour.');
    }

    public function destroy(Project $project)
    {
        abort_unless($project->isOwner(Auth::user()), 403, 'Seul le créateur peut supprimer ce projet.');
        $project->delete();
        return redirect()->route('mentor.projects.index')->with('succes', 'Projet supprimé.');
    }

    /** Inviter un mentor à collaborer */
    public function invite(Request $request, Project $project)
    {
        abort_unless($project->isOwner(Auth::user()), 403, 'Seul le créateur peut inviter un mentor.');

        $request->validate([
            'mentor_id' => 'required|exists:users,id',
        ]);

        $mentor = User::where('id', $request->mentor_id)
            ->where('role', 'mentor')
            ->where('actif', true)
            ->firstOrFail();

        if (!$project->mentors()->where('user_id', $mentor->id)->exists()) {
            $project->mentors()->attach($mentor->id);
        }

        return back()->with('succes', 'Mentor invité avec succès.');
    }

    /** Retirer un collaborateur (le créateur ne peut pas être retiré) */
    public function removeCollaborator(Project $project, User $user)
    {
        abort_unless($project->isOwner(Auth::user()) || $user->is(Auth::user()), 403);

        // Empêcher le retrait du créateur
        abort_if($project->mentor_id === $user->id, 403, 'Le créateur du projet ne peut pas être retiré.');

        // Empêcher le retrait de soi-même par un collaborateur ?
        // On autorise : un collaborateur peut se retirer lui-même
        $project->mentors()->detach($user->id);

        return back()->with('succes', 'Collaborateur retiré du projet.');
    }
}