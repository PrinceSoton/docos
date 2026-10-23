@extends('layouts.mentor')
@section('titre', $project->titre)
@section('breadcrumb', 'Projets > Détail')
@section('content')
    @php
        $statutsProjet = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'suspendu' => 'Suspendu',
        ];
        $priorites = [
            'faible' => ['Faible', 'bg-slate-400'],
            'normale' => ['Normale', 'bg-sky-500'],
            'haute' => ['Haute', 'bg-amber-500'],
            'urgente' => ['Urgente', 'bg-red-500'],
        ];
        $statutsRapport = [
            'soumis' => ['Soumis', 'bg-amber-50 text-amber-800'],
            'valide' => ['Validé', 'bg-emerald-50 text-emerald-800'],
            'rejete' => ['Rejeté', 'bg-red-50 text-red-800'],
            'en_revision' => ['En révision', 'bg-sky-50 text-sky-800'],
        ];
        $libStatut = $statutsProjet[$project->statut] ?? ucfirst(str_replace('_', ' ', $project->statut));
        [$libPriorite, $dotPriorite] = $priorites[$project->priorite] ?? [ucfirst((string) $project->priorite), 'bg-slate-300'];
        $pct = $project->progressionPourcent();

        $colonnes = [
            ['a_faire', 'À faire', 'bg-slate-400'],
            ['en_cours', 'En cours', 'bg-amber-500'],
            ['termine', 'Terminé', 'bg-emerald-500'],
        ];

        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $focusMarine = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-800';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnMarinePrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-brand-800 hover:bg-brand-50 transition-colors ' . $focusMarine;
        $btnMarineSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-white/30 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 transition-colors ' . $focusMarine;
        $btnMini = 'inline-flex items-center justify-center gap-1 rounded-md px-2 py-1 text-xs font-medium transition-colors ' . $focus;
        $btnMiniSecondaire = $btnMini . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300';
        $btnMiniDanger = $btnMini . ' border border-red-200 bg-white text-red-700 hover:bg-red-50';
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- En-tête du projet (bandeau marine) -->
        <section class="rounded-xl bg-brand-800 text-white p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-semibold text-white leading-snug break-words">{{ $project->titre }}</h2>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-white/15 text-white">{{ $libStatut }}</span>
                        @if (!$project->isOwner(Auth::user()))
                            <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-white/15 text-white">Collaborateur</span>
                        @endif
                    </div>
                    @if ($project->description)
                        <p class="mt-1.5 text-sm text-brand-100 max-w-3xl break-words">{{ $project->description }}</p>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2 flex-shrink-0">
                    <a href="{{ route('mentor.projects.edit', $project) }}" class="{{ $btnMarinePrimaire }}">
                        <i class="fas fa-pen text-xs" aria-hidden="true"></i>Modifier
                    </a>
                    <a href="{{ route('mentor.tasks.create') }}" class="{{ $btnMarineSecondaire }}">
                        <i class="fas fa-plus text-xs" aria-hidden="true"></i>Tâche
                    </a>
                </div>
            </div>

            <dl class="mt-5 pt-5 border-t border-white/15 grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs text-brand-100">Priorité</dt>
                    <dd class="mt-0.5 flex items-center gap-2 font-medium text-white">
                        <span class="h-2 w-2 rounded-full {{ $dotPriorite }}" aria-hidden="true"></span>
                        {{ $libPriorite }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-brand-100">Tâches</dt>
                    <dd class="mt-0.5 font-medium text-white tabular-nums">
                        {{ $project->tasks->where('statut', 'termine')->count() }}/{{ $project->tasks->count() }} terminée(s)
                    </dd>
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <dt class="text-xs text-brand-100">Période</dt>
                    <dd class="mt-0.5 font-medium text-white tabular-nums">
                        {{ $project->date_debut->format('d/m/Y') }}
                        @if ($project->date_fin)
                            → {{ $project->date_fin->format('d/m/Y') }}
                        @endif
                    </dd>
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <dt class="flex justify-between gap-3 text-xs text-brand-100">
                        <span>Progression globale</span>
                        <span class="font-semibold text-white tabular-nums">{{ $pct }} %</span>
                    </dt>
                    <dd class="mt-2">
                        <div class="h-2 rounded-full bg-white/15 overflow-hidden" role="progressbar"
                            aria-label="Progression du projet {{ $project->titre }}" aria-valuemin="0"
                            aria-valuemax="100" aria-valuenow="{{ $pct }}">
                            <div class="h-2 rounded-full bg-accent-500" style="width:{{ $pct }}%"></div>
                        </div>
                    </dd>
                </div>
            </dl>
        </section>

        <!-- Équipe : mentors et stagiaires -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
                <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900">Mentors du projet</h3>
                    <span class="text-sm font-medium text-slate-600 tabular-nums">{{ $project->mentors->count() }}</span>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @foreach ($project->mentors as $mentor)
                        <li class="flex items-center gap-3 py-3">
                            <p class="flex-1 min-w-0 text-sm font-medium text-slate-900 truncate">{{ $mentor->nom_complet }}</p>
                            @if ($mentor->id === $project->mentor_id)
                                <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 bg-brand-100 text-brand-800">Créateur</span>
                            @endif
                            @if ($mentor->id !== $project->mentor_id)
                                <form action="{{ route('mentor.projects.removeCollaborator', [$project, $mentor]) }}"
                                    method="POST" class="no-loader flex-shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="{{ $btnMiniDanger }}"
                                        aria-label="Retirer {{ $mentor->nom_complet }} du projet">
                                        <i class="fas fa-times text-xs" aria-hidden="true"></i>Retirer
                                    </button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <!-- Formulaire d'invitation rapide -->
                @if ($autresMentors->count() > 0)
                    <div class="px-5 sm:px-6 py-4 border-t border-brand-100">
                        <form action="{{ route('mentor.projects.invite', $project) }}" method="POST"
                            class="flex flex-col sm:flex-row gap-2">
                            @csrf
                            <label for="invite_mentor_id" class="sr-only">Inviter un autre mentor</label>
                            <select id="invite_mentor_id" name="mentor_id" required class="flex-1 {{ $champ }}">
                                <option value="">Inviter un autre mentor...</option>
                                @foreach ($autresMentors as $m)
                                    <option value="{{ $m->id }}">{{ $m->nom_complet }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="{{ $btnPrimaire }}">
                                <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>Inviter
                            </button>
                        </form>
                    </div>
                @endif
            </section>

            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
                <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900">Stagiaires</h3>
                    <span class="text-sm font-medium text-slate-600 tabular-nums">{{ $project->stagiaires->count() }}</span>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @forelse ($project->stagiaires as $stag)
                        <li class="py-3">
                            <a href="{{ route('mentor.stagiaires.show', $stag) }}" class="{{ $lien }}">{{ $stag->user->nom_complet }}</a>
                        </li>
                    @empty
                        <li class="py-4 text-sm text-slate-500">Aucun stagiaire sur ce projet.</li>
                    @endforelse
                </ul>
            </section>
        </div>

        <!-- Kanban tâches -->
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <h3 class="text-base font-semibold text-slate-900">Tâches du projet</h3>
            <div class="mt-4 grid grid-cols-1 xl:grid-cols-3 gap-4">
                @foreach ($colonnes as [$statut, $label, $dot])
                    @php $tachesFiltrees = $project->tasks->where('statut', $statut); @endphp
                    <div class="min-w-0 rounded-lg bg-brand-50 border border-brand-100 p-3">
                        <div class="flex items-center justify-between gap-2 px-1 pb-2 border-b border-brand-100">
                            <h4 class="flex items-center gap-2 text-sm font-medium text-slate-900">
                                <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                                {{ $label }}
                            </h4>
                            <span class="text-xs font-medium text-slate-600 tabular-nums">{{ $tachesFiltrees->count() }}</span>
                        </div>
                        <ul class="mt-2 space-y-2">
                            @forelse($tachesFiltrees as $tache)
                                @php
                                    [$libP, $dotP] = $priorites[$tache->priorite] ?? [ucfirst((string) $tache->priorite), 'bg-slate-300'];
                                    $enRetard = $tache->date_echeance && $tache->date_echeance->isPast() && $tache->statut !== 'termine';
                                @endphp
                                <li class="rounded-lg border border-brand-100 bg-white p-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="min-w-0 text-sm font-medium text-slate-900 break-words">{{ $tache->titre }}</p>
                                        <span class="flex items-center gap-1.5 flex-shrink-0 text-xs text-slate-600">
                                            <span class="h-2 w-2 rounded-full {{ $dotP }}" aria-hidden="true"></span>
                                            <span class="sr-only">Priorité :</span>{{ $libP }}
                                        </span>
                                    </div>
                                    @if ($tache->description)
                                        <p class="mt-1 text-xs text-slate-500 line-clamp-2 break-words">{{ $tache->description }}</p>
                                    @endif
                                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                                        <span class="text-slate-500">{{ $tache->stagiaire->user->prenom ?? '—' }}</span>
                                        @if ($tache->date_echeance)
                                            <span class="tabular-nums {{ $enRetard ? 'text-red-700 font-medium' : 'text-slate-500' }}">
                                                Échéance {{ $tache->date_echeance->format('d/m') }}{{ $enRetard ? ' · En retard' : '' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">{{ ucfirst($tache->difficulte) }}</span>
                                        <div class="ml-auto flex flex-wrap gap-1.5">
                                            <a href="{{ route('mentor.tasks.edit', $tache) }}" class="{{ $btnMiniSecondaire }}"
                                                aria-label="Modifier la tâche {{ $tache->titre }}">
                                                <i class="fas fa-pen text-xs" aria-hidden="true"></i>Modifier
                                            </a>
                                            <form action="{{ route('mentor.tasks.destroy', $tache) }}" method="POST"
                                                id="del-t-{{ $tache->id }}">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-delete {{ $btnMiniDanger }}"
                                                    data-form="del-t-{{ $tache->id }}"
                                                    aria-label="Supprimer la tâche {{ $tache->titre }}">
                                                    <i class="fas fa-trash text-xs" aria-hidden="true"></i>Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="px-1 py-3 text-sm text-slate-600">Aucune tâche</li>
                            @endforelse
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Rapports liés -->
        @if ($project->reports->count() > 0)
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
                <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900">Rapports liés</h3>
                    <span class="text-sm font-medium text-slate-600 tabular-nums">{{ $project->reports->count() }}</span>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @foreach ($project->reports as $rapport)
                        @php [$libR, $clsR] = $statutsRapport[$rapport->statut] ?? [ucfirst($rapport->statut), 'bg-slate-100 text-slate-700']; @endphp
                        <li class="flex flex-wrap sm:flex-nowrap items-center gap-3 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $rapport->titre }}</p>
                                <p class="text-xs text-slate-500 tabular-nums">{{ $rapport->stagiaire->user->nom_complet ?? '' }} ·
                                    {{ $rapport->created_at->format('d/m/Y') }}</p>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsR }}">{{ $libR }}</span>
                            <a href="{{ route('mentor.reports.show', $rapport) }}" class="{{ $btnMiniSecondaire }} flex-shrink-0"
                                aria-label="Voir le rapport {{ $rapport->titre }}">Voir</a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <a href="{{ route('mentor.projects.index') }}" class="inline-flex items-center gap-2 {{ $lien }}">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour aux projets
        </a>
    </div>
@endsection
