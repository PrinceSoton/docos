<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /* ------------------------------------------------------------------ */
    /*  Liste                                                             */
    /* ------------------------------------------------------------------ */
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

    /* ------------------------------------------------------------------ */
    /*  Création                                                          */
    /* ------------------------------------------------------------------ */
    public function create()
    {
        $stagiaires = Stagiaire::with('user')
            ->where('mentor_id', Auth::id())
            ->where('statut', 'en_cours')
            ->get();

        // Autres mentors disponibles pour invitation (excluant soi-même)
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
            'stagiaires.*'       => ['integer', Rule::exists('stagiaires', 'id')->where('mentor_id', Auth::id())->where('statut', 'en_cours')],
            'collaborateurs'     => 'nullable|array',
            'collaborateurs.*'   => [Rule::exists('users', 'id')->where('role', 'mentor')],
        ]);

        DB::transaction(function () use ($request) {
            $projet = Project::create([
                'titre'       => $request->titre,
                'description' => $request->description,
                'date_debut'  => $request->date_debut,
                'date_fin'    => $request->date_fin,
                'statut'      => $request->statut,
                'priorite'    => $request->priorite,
                'mentor_id'   => Auth::id(),
            ]);

            // Pivot mentors : créateur + collaborateurs
            $mentorIds = array_unique(array_merge(
                [Auth::id()],
                (array) $request->input('collaborateurs', [])
            ));
            $projet->mentors()->sync($mentorIds);

            // Pivot stagiaires
            $projet->stagiaires()->sync($request->stagiaires);
        });

        return redirect()->route('mentor.projects.index')
            ->with('succes', 'Projet créé avec succès.');
    }

    /* ------------------------------------------------------------------ */
    /*  Affichage                                                         */
    /* ------------------------------------------------------------------ */
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

    /* ------------------------------------------------------------------ */
    /*  Édition                                                           */
    /* ------------------------------------------------------------------ */
    public function edit(Project $project)
    {
        abort_unless($project->hasAccess(Auth::user()), 403);

        // IDs de tous les mentors actuellement rattachés (créateur inclus)
        $currentMentorIds = $project->mentors()->pluck('users.id')->toArray();

        // Stagiaires accessibles : ceux des mentors du projet
        //                     + ceux déjà rattachés à ce projet
        $stagiaires = Stagiaire::with('user')
            ->where(function ($q) use ($currentMentorIds, $project) {
                $q->whereIn('mentor_id', $currentMentorIds)
                  ->orWhereIn('id', $project->stagiaires()->pluck('stagiaires.id'));
            })
            ->orderBy('matricule')
            ->get();

        // Mentors disponibles pour invitation (excluant ceux déjà rattachés)
        $autresMentors = User::where('role', 'mentor')
            ->whereNotIn('id', $currentMentorIds)
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        return view('mentor.projects.edit', compact(
            'project',
            'stagiaires',
            'autresMentors',
            'currentMentorIds'
        ));
    }

    public function update(Request $request, Project $project)
    {
        abort_unless($project->hasAccess(Auth::user()), 403);

        // Même périmètre que les stagiaires proposés dans le formulaire de modification.
        $mentorIds = $project->mentors()->pluck('users.id');
        $stagiairesAccessibles = Stagiaire::where(function ($query) use ($mentorIds, $project) {
            $query->whereIn('mentor_id', $mentorIds)
                ->orWhereIn('id', $project->stagiaires()->pluck('stagiaires.id'));
        })->pluck('id')->all();

        $request->validate([
            'titre'              => 'required|string|max:200',
            'description'        => 'nullable|string',
            'date_debut'         => 'required|date',
            'date_fin'           => 'nullable|date|after_or_equal:date_debut',
            'statut'             => 'required|in:en_attente,en_cours,termine,suspendu',
            'priorite'           => 'required|in:faible,normale,haute,urgente',
            'stagiaires'         => 'required|array|min:1',
            'stagiaires.*'       => [Rule::exists('stagiaires', 'id')->whereIn('id', $stagiairesAccessibles)],
            'collaborateurs'     => 'nullable|array',
            'collaborateurs.*'   => [Rule::exists('users', 'id')->where('role', 'mentor')],
        ]);

        DB::transaction(function () use ($request, $project) {
            $project->update($request->only(
                'titre', 'description', 'date_debut', 'date_fin', 'statut', 'priorite'
            ));

            // Synchronisation des stagiaires
            $project->stagiaires()->sync($request->stagiaires);

            // Synchronisation des mentors : le créateur est TOUJOURS conservé
            $mentorIds = array_unique(array_merge(
                [$project->mentor_id],
                (array) $request->input('collaborateurs', [])
            ));
            $project->mentors()->sync($mentorIds);
        });

        return redirect()->route('mentor.projects.index')
            ->with('succes', 'Projet mis à jour.');
    }

    /* ------------------------------------------------------------------ */
    /*  Suppression                                                       */
    /* ------------------------------------------------------------------ */
    public function destroy(Project $project)
    {
        abort_unless($project->hasAccess(Auth::user()), 403);
        $project->delete();

        return redirect()->route('mentor.projects.index')
            ->with('succes', 'Projet supprimé.');
    }

    /* ------------------------------------------------------------------ */
    /*  Invitation d'un ou plusieurs mentors                              */
    /*  Accepte : mentor_id (singulier) OU mentor_ids[] (array)            */
    /* ------------------------------------------------------------------ */
    public function invite(Request $request, Project $project)
    {
        abort_unless($project->hasAccess(Auth::user()), 403);

        $request->validate([
            'mentor_id'   => 'nullable|exists:users,id',
            'mentor_ids'  => 'nullable|array',
            'mentor_ids.*'=> 'exists:users,id',
        ]);

        // Construction de la liste finale d'IDs à inviter
        $ids = [];
        if ($request->filled('mentor_id')) {
            $ids[] = (int) $request->mentor_id;
        }
        if ($request->filled('mentor_ids') && is_array($request->mentor_ids)) {
            $ids = array_merge($ids, array_map('intval', $request->mentor_ids));
        }
        $ids = array_unique(array_filter($ids));

        if (empty($ids)) {
            return back()->with('erreur', 'Veuillez sélectionner au moins un mentor.');
        }

        // Filtrer uniquement les utilisateurs avec le rôle mentor et actifs
        $mentors = User::whereIn('id', $ids)
            ->where('role', 'mentor')
            ->where('actif', true)
            ->get();

        $added = 0;
        foreach ($mentors as $mentor) {
            if (!$project->mentors()->where('user_id', $mentor->id)->exists()) {
                $project->mentors()->attach($mentor->id);
                $added++;
            }
        }

        $message = $added > 1
            ? "{$added} mentors invités avec succès."
            : ($added === 1 ? 'Mentor invité avec succès.' : 'Aucun nouveau mentor ajouté.');

        return back()->with('succes', $message);
    }

    /* ------------------------------------------------------------------ */
    /*  Retrait d'un collaborateur                                        */
    /* ------------------------------------------------------------------ */
    public function removeCollaborator(Project $project, User $user)
    {
        abort_unless($project->hasAccess(Auth::user()), 403);

        // Le créateur ne peut jamais être retiré
        abort_if(
            $project->mentor_id === $user->id,
            403,
            'Le créateur du projet ne peut pas être retiré.'
        );

        $project->mentors()->detach($user->id);

        return back()->with('succes', 'Collaborateur retiré du projet.');
    }
}
