@extends('layouts.stagiaire')
@section('titre', 'Mes Tâches')
@section('breadcrumb', 'Mon espace > Tâches')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        // Champ de recherche posé sur le bandeau marine : fond blanc, focus blanc visible sur le marine
        $champBandeau = 'w-full rounded-lg border border-white/30 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-800';
        $btnCarte = 'inline-flex w-full items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium transition-colors ' . $focus;
        $btnCartePrimaire = $btnCarte . ' bg-brand-800 text-white hover:bg-brand-700';
        $btnCarteSecondaire = $btnCarte . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300';

        $totalT = $aFaire->count() + $enCours->count() + $terminees->count();
        $pctT = $totalT > 0 ? round(($terminees->count() / $totalT) * 100) : 0;

        $colonnes = [
            ['a_faire', $aFaire, 'À faire', 'bg-slate-400'],
            ['en_cours', $enCours, 'En cours', 'bg-amber-500'],
            ['termine', $terminees, 'Terminé', 'bg-emerald-500'],
        ];
        $priorites = [
            'faible' => ['Faible', 'bg-slate-400'],
            'normale' => ['Normale', 'bg-sky-500'],
            'haute' => ['Haute', 'bg-amber-500'],
            'urgente' => ['Urgente', 'bg-red-500'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">
        <!-- Bandeau de synthèse : total, progression et recherche -->
        <div class="rounded-xl bg-brand-800 text-white p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <p class="text-sm text-brand-100">Tâches assignées</p>
                    <p class="mt-1 text-sm text-brand-100 tabular-nums">
                        <span class="text-2xl font-semibold text-white">{{ $totalT }}</span> tâche(s) au total
                    </p>
                </div>
                <div class="w-full sm:w-72">
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-brand-100">Tâches terminées</span>
                        <span class="font-medium text-white tabular-nums">{{ $pctT }}%</span>
                    </div>
                    <div class="mt-2 h-2 rounded-full bg-white/15 overflow-hidden" role="progressbar"
                        aria-label="Tâches terminées" aria-valuemin="0" aria-valuemax="100"
                        aria-valuenow="{{ $pctT }}">
                        <div class="h-2 rounded-full bg-accent-500" style="width:{{ $pctT }}%"></div>
                    </div>
                </div>
            </div>
            @if ($totalT > 0)
                <div class="mt-5">
                    <input type="search" id="search" placeholder="Rechercher une tâche, projet..."
                        aria-label="Rechercher une tâche ou un projet" class="sm:w-72 {{ $champBandeau }}">
                </div>
            @endif
        </div>

        @if ($totalT === 0)
            <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                <p class="text-sm font-medium text-slate-900">Aucune tâche pour le moment</p>
                <p class="mt-1 text-sm text-slate-600">Les tâches qui vous sont assignées apparaîtront ici, classées par statut.</p>
            </div>
        @else
            <p id="aucunResultat" class="hidden text-sm text-slate-600 text-center py-2">Aucun résultat pour cette recherche.</p>

            <!-- Colonnes par statut (empilées sur téléphone) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
                @foreach ($colonnes as [$statut, $liste, $label, $pastille])
                    <section aria-labelledby="col-{{ $statut }}"
                        class="min-w-0 rounded-xl bg-brand-50 border border-brand-100 overflow-hidden">
                        <div class="flex items-center gap-2 px-4 py-3 border-b border-brand-100">
                            <span class="h-2 w-2 rounded-full {{ $pastille }}" aria-hidden="true"></span>
                            <h2 id="col-{{ $statut }}" class="text-base font-semibold text-slate-900">{{ $label }}</h2>
                            <span class="ml-auto text-sm font-medium text-slate-600 tabular-nums">{{ $liste->count() }}</span>
                        </div>
                        <div class="p-3 space-y-3">
                            @forelse($liste as $tache)
                                @php
                                    [$libPriorite, $dotPriorite] = $priorites[$tache->priorite] ?? [ucfirst((string) $tache->priorite), 'bg-slate-300'];
                                    $enRetard = $tache->date_echeance && $tache->date_echeance->isPast() && $tache->statut !== 'termine';
                                @endphp
                                <article class="bg-white border border-brand-100 rounded-xl p-4 tache-card">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="min-w-0 text-sm font-semibold text-slate-900 leading-snug break-words">{{ $tache->titre }}</h3>
                                        <span class="flex items-center gap-1.5 flex-shrink-0 text-xs text-slate-600">
                                            <span class="h-2 w-2 rounded-full {{ $dotPriorite }}" aria-hidden="true"></span>
                                            <span class="sr-only">Priorité :</span>{{ $libPriorite }}
                                        </span>
                                    </div>
                                    @if ($tache->description)
                                        <p class="mt-1.5 text-sm text-slate-500 line-clamp-2 break-words">{{ $tache->description }}</p>
                                    @endif
                                    <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-2">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                            {{ ucfirst($tache->difficulte) }}
                                        </span>
                                        @if ($tache->date_echeance)
                                            <span class="text-xs tabular-nums {{ $enRetard ? 'text-red-700 font-medium' : 'text-slate-500' }}">
                                                Échéance {{ $tache->date_echeance->format('d/m') }}@if ($enRetard) · En retard @endif
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Contexte : projet OU indépendante --}}
                                    <div class="mt-3">
                                        @if ($tache->project)
                                            <p class="text-xs text-slate-600 truncate" title="{{ $tache->project->titre }}">
                                                <span class="text-slate-500">Projet :</span>
                                                <span class="font-medium">{{ $tache->project->titre }}</span>
                                            </p>
                                        @else
                                            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">Tâche indépendante</span>
                                        @endif
                                    </div>

                                    {{-- Actions selon statut --}}
                                    <div class="mt-4 space-y-2">
                                        @if ($tache->statut === 'a_faire')
                                            <form action="{{ route('stagiaire.tasks.updateStatut', $tache) }}" method="POST"
                                                class="no-loader">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="statut" value="en_cours">
                                                <button type="submit" class="{{ $btnCartePrimaire }}">
                                                    <i class="fas fa-play text-xs" aria-hidden="true"></i>Commencer
                                                </button>
                                            </form>
                                        @elseif ($tache->statut === 'en_cours')
                                            <div class="grid grid-cols-2 gap-2">
                                                <form action="{{ route('stagiaire.tasks.updateStatut', $tache) }}" method="POST"
                                                    class="no-loader">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="statut" value="a_faire">
                                                    <button type="submit" class="{{ $btnCarteSecondaire }}">
                                                        <i class="fas fa-undo text-xs" aria-hidden="true"></i>Retour
                                                    </button>
                                                </form>
                                                <form action="{{ route('stagiaire.tasks.updateStatut', $tache) }}" method="POST"
                                                    class="no-loader">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="statut" value="termine">
                                                    <button type="submit" class="{{ $btnCartePrimaire }}">
                                                        <i class="fas fa-check text-xs" aria-hidden="true"></i>Terminer
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <form action="{{ route('stagiaire.tasks.updateStatut', $tache) }}" method="POST"
                                                class="no-loader">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="statut" value="en_cours">
                                                <button type="submit" class="{{ $btnCarteSecondaire }}">
                                                    <i class="fas fa-undo text-xs" aria-hidden="true"></i>Rouvrir
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Voir le détail --}}
                                        <a href="{{ route('stagiaire.tasks.show', $tache) }}"
                                            class="block text-center py-1 {{ $lien }}">Voir le détail</a>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-lg border border-dashed border-brand-200 px-4 py-6 text-center">
                                    <p class="text-sm text-slate-600">Aucune tâche</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        @endif
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
                let visibles = 0;
                document.querySelectorAll('.tache-card').forEach(card => {
                    const ok = card.textContent.toLowerCase().includes(val);
                    card.style.display = ok ? '' : 'none';
                    if (ok) visibles++;
                });
                if (vide) vide.classList.toggle('hidden', visibles > 0);
            });
        })();
    </script>
@endpush
