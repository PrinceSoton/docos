@extends('layouts.mentor')
@section('titre', 'Gestion des Tâches')
@section('breadcrumb', 'Mentor > Tâches')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnMini = 'inline-flex items-center justify-center gap-1 rounded-md px-2 py-1 text-xs font-medium transition-colors ' . $focus;
        $btnMiniSecondaire = $btnMini . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300';
        $btnMiniDanger = $btnMini . ' border border-red-200 bg-white text-red-700 hover:bg-red-50';

        $colonnes = [
            ['a_faire', 'À faire', 'bg-slate-400'],
            ['en_cours', 'En cours', 'bg-amber-500'],
            ['termine', 'Terminé', 'bg-emerald-500'],
        ];
        $priorites = [
            'faible' => ['Faible', 'bg-slate-400'],
            'normale' => ['Normale', 'bg-sky-500'],
            'haute' => ['Haute', 'bg-amber-500'],
            'urgente' => ['Urgente', 'bg-red-500'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Résumé, recherche et création -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <p class="text-sm text-slate-600">Vue Kanban de toutes les tâches de vos projets et tâches indépendantes</p>
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="search" id="search" placeholder="Rechercher une tâche, stagiaire, projet..."
                    aria-label="Rechercher une tâche, un stagiaire ou un projet" class="sm:w-72 {{ $champ }}">
                <a href="{{ route('mentor.tasks.create') }}" class="{{ $btnPrimaire }} whitespace-nowrap">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>Nouvelle tâche
                </a>
            </div>
        </div>

        <p id="aucunResultat" class="hidden text-sm text-slate-600 text-center py-2">Aucun résultat pour cette recherche.</p>

        {{-- SECTION 1 : TÂCHES INDÉPENDANTES --}}
        @if ($tachesIndependantes->count() > 0)
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
                <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-3">
                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-slate-900">Tâches indépendantes</h2>
                        <p class="text-xs text-slate-600">Tâches non rattachées à un projet, assignées à vos stagiaires</p>
                    </div>
                    <span class="text-sm font-medium text-slate-600 tabular-nums">{{ $tachesIndependantes->count() }} tâche(s)</span>
                </div>

                <div class="p-4 sm:p-5 grid grid-cols-1 xl:grid-cols-3 gap-4">
                    @foreach ($colonnes as [$statut, $label, $dot])
                        @php $taches = $tachesIndependantes->where('statut', $statut); @endphp
                        <div class="min-w-0 rounded-lg bg-brand-50 border border-brand-100 p-3">
                            <div class="flex items-center justify-between gap-2 px-1 pb-2 border-b border-brand-100">
                                <h3 class="flex items-center gap-2 text-sm font-medium text-slate-900">
                                    <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                                    {{ $label }}
                                </h3>
                                <span class="text-xs font-medium text-slate-600 tabular-nums">{{ $taches->count() }}</span>
                            </div>
                            <div class="mt-2 space-y-2">
                                @forelse($taches as $tache)
                                    @php
                                        [$libP, $dotP] = $priorites[$tache->priorite] ?? [ucfirst((string) $tache->priorite), 'bg-slate-300'];
                                        $enRetard = $tache->date_echeance && $tache->date_echeance->isPast() && $tache->statut !== 'termine';
                                    @endphp
                                    <article class="tache-card rounded-lg border border-brand-100 bg-white p-3">
                                        <div class="flex items-start justify-between gap-2">
                                            <h4 class="min-w-0 text-sm font-medium text-slate-900 leading-snug break-words">{{ $tache->titre }}</h4>
                                            <span class="flex items-center gap-1.5 flex-shrink-0 text-xs text-slate-600">
                                                <span class="h-2 w-2 rounded-full {{ $dotP }}" aria-hidden="true"></span>
                                                <span class="sr-only">Priorité :</span>{{ $libP }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500 truncate">{{ $tache->stagiaire->user->nom_complet ?? '—' }}</p>
                                        @if ($tache->date_echeance)
                                            <p class="mt-0.5 text-xs tabular-nums {{ $enRetard ? 'text-red-700 font-medium' : 'text-slate-500' }}">
                                                Échéance {{ $tache->date_echeance->format('d/m/Y') }}{{ $enRetard ? ' · En retard' : '' }}
                                            </p>
                                        @endif
                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                            <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">{{ ucfirst($tache->difficulte) }}</span>
                                            <div class="ml-auto flex flex-wrap gap-1.5">
                                                <a href="{{ route('mentor.tasks.edit', $tache) }}" class="{{ $btnMiniSecondaire }}"
                                                    aria-label="Modifier la tâche {{ $tache->titre }}">
                                                    <i class="fas fa-pen text-xs" aria-hidden="true"></i>Modifier
                                                </a>
                                                <form action="{{ route('mentor.tasks.destroy', $tache) }}" method="POST" id="del-t-ind-{{ $tache->id }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn-delete {{ $btnMiniDanger }}"
                                                        data-form="del-t-ind-{{ $tache->id }}"
                                                        aria-label="Supprimer la tâche {{ $tache->titre }}">
                                                        <i class="fas fa-trash text-xs" aria-hidden="true"></i>Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </article>
                                @empty
                                    <p class="px-1 py-3 text-sm text-slate-600">Aucune tâche</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- SECTION 2 : TÂCHES PAR PROJET --}}
        @forelse($projets as $projet)
            @php $pct = $projet->progressionPourcent(); @endphp
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
                <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <h2 class="min-w-0 text-base font-semibold text-slate-900 break-words">{{ $projet->titre }}</h2>
                    <div class="flex items-center gap-4 flex-shrink-0">
                        <div class="w-36">
                            <p class="flex justify-between gap-2 text-xs text-slate-600">
                                <span>Progression</span>
                                <span class="font-semibold text-slate-900 tabular-nums">{{ $pct }} %</span>
                            </p>
                            <div class="mt-1 h-2 rounded-full bg-brand-100 overflow-hidden" role="progressbar"
                                aria-label="Progression du projet {{ $projet->titre }}" aria-valuemin="0"
                                aria-valuemax="100" aria-valuenow="{{ $pct }}">
                                <div class="h-2 rounded-full bg-accent-500" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                        <a href="{{ route('mentor.projects.show', $projet) }}" class="{{ $lien }} whitespace-nowrap">
                            Voir le projet<span class="sr-only"> {{ $projet->titre }}</span>
                        </a>
                    </div>
                </div>

                <div class="p-4 sm:p-5 grid grid-cols-1 xl:grid-cols-3 gap-4">
                    @foreach ($colonnes as [$statut, $label, $dot])
                        @php $taches = $projet->tasks->where('statut', $statut); @endphp
                        <div class="min-w-0 rounded-lg bg-brand-50 border border-brand-100 p-3">
                            <div class="flex items-center justify-between gap-2 px-1 pb-2 border-b border-brand-100">
                                <h3 class="flex items-center gap-2 text-sm font-medium text-slate-900">
                                    <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                                    {{ $label }}
                                </h3>
                                <span class="text-xs font-medium text-slate-600 tabular-nums">{{ $taches->count() }}</span>
                            </div>
                            <div class="mt-2 space-y-2 tache-col" data-statut="{{ $statut }}">
                                @forelse($taches as $tache)
                                    @php
                                        [$libP, $dotP] = $priorites[$tache->priorite] ?? [ucfirst((string) $tache->priorite), 'bg-slate-300'];
                                        $enRetard = $tache->date_echeance && $tache->date_echeance->isPast() && $tache->statut !== 'termine';
                                    @endphp
                                    <article class="tache-card rounded-lg border border-brand-100 bg-white p-3">
                                        <div class="flex items-start justify-between gap-2">
                                            <h4 class="min-w-0 text-sm font-medium text-slate-900 leading-snug break-words">{{ $tache->titre }}</h4>
                                            <span class="flex items-center gap-1.5 flex-shrink-0 text-xs text-slate-600">
                                                <span class="h-2 w-2 rounded-full {{ $dotP }}" aria-hidden="true"></span>
                                                <span class="sr-only">Priorité :</span>{{ $libP }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500 truncate">{{ $tache->stagiaire->user->nom_complet ?? '—' }}</p>
                                        @if ($tache->date_echeance)
                                            <p class="mt-0.5 text-xs tabular-nums {{ $enRetard ? 'text-red-700 font-medium' : 'text-slate-500' }}">
                                                Échéance {{ $tache->date_echeance->format('d/m/Y') }}{{ $enRetard ? ' · En retard' : '' }}
                                            </p>
                                        @endif
                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                            <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">{{ ucfirst($tache->difficulte) }}</span>
                                            <div class="ml-auto flex flex-wrap gap-1.5">
                                                <a href="{{ route('mentor.tasks.edit', $tache) }}" class="{{ $btnMiniSecondaire }}"
                                                    aria-label="Modifier la tâche {{ $tache->titre }}">
                                                    <i class="fas fa-pen text-xs" aria-hidden="true"></i>Modifier
                                                </a>
                                                <form action="{{ route('mentor.tasks.destroy', $tache) }}" method="POST" id="del-t-{{ $tache->id }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn-delete {{ $btnMiniDanger }}"
                                                        data-form="del-t-{{ $tache->id }}"
                                                        aria-label="Supprimer la tâche {{ $tache->titre }}">
                                                        <i class="fas fa-trash text-xs" aria-hidden="true"></i>Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </article>
                                @empty
                                    <p class="px-1 py-3 text-sm text-slate-600">Aucune tâche</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            @if ($tachesIndependantes->count() === 0)
                <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                    <p class="text-sm font-medium text-slate-900">Aucune tâche pour le moment</p>
                    <p class="mt-1 text-sm text-slate-600">Créez une tâche liée à un projet ou une tâche indépendante pour l'un de vos stagiaires.</p>
                    <a href="{{ route('mentor.tasks.create') }}" class="mt-3 inline-block {{ $lien }}">Créer une tâche</a>
                </div>
            @endif
        @endforelse
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const champ = document.getElementById('search');
            if (!champ) return;
            const vide = document.getElementById('aucunResultat');
            champ.addEventListener('input', function() {
                const val = this.value.toLowerCase();
                const cartes = document.querySelectorAll('.tache-card');
                let visibles = 0;
                cartes.forEach(card => {
                    const ok = card.textContent.toLowerCase().includes(val);
                    card.style.display = ok ? '' : 'none';
                    if (ok) visibles++;
                });
                if (vide) vide.classList.toggle('hidden', cartes.length === 0 || visibles > 0 || val === '');
            });
        })();
    </script>
@endpush
