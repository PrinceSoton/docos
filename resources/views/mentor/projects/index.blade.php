@extends('layouts.mentor')
@section('titre', 'Mes Projets')
@section('breadcrumb', 'Mentor > Projets')
@section('content')
    @php
        $statutsProjet = [
            'en_attente' => ['En attente', 'bg-slate-100 text-slate-700'],
            'en_cours' => ['En cours', 'bg-sky-50 text-sky-800'],
            'termine' => ['Terminé', 'bg-emerald-50 text-emerald-800'],
            'suspendu' => ['Suspendu', 'bg-red-50 text-red-800'],
        ];
        $priorites = [
            'faible' => ['Faible', 'bg-slate-400'],
            'normale' => ['Normale', 'bg-sky-500'],
            'haute' => ['Haute', 'bg-amber-500'],
            'urgente' => ['Urgente', 'bg-red-500'],
        ];

        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnCarte = 'inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium transition-colors ' . $focus;
        $btnCarteSecondaire = $btnCarte . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300';
        $btnCarteDanger = $btnCarte . ' border border-red-200 bg-white text-red-700 hover:bg-red-50';
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Résumé, recherche, filtre et création -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <p class="text-sm text-slate-600 tabular-nums">
                <span class="font-semibold text-slate-900">{{ $projets->total() }}</span> projet(s)
            </p>
            <div class="flex flex-col sm:flex-row sm:flex-wrap lg:flex-nowrap gap-3">
                <input type="search" id="search" placeholder="Rechercher un projet..." aria-label="Rechercher un projet"
                    class="sm:w-64 {{ $champ }}">
                <select id="filterStatut" aria-label="Filtrer par statut" class="sm:w-44 {{ $champ }}">
                    <option value="">Tous statuts</option>
                    <option value="en_attente">En attente</option>
                    <option value="en_cours">En cours</option>
                    <option value="termine">Terminé</option>
                    <option value="suspendu">Suspendu</option>
                </select>
                <a href="{{ route('mentor.projects.create') }}" class="{{ $btnPrimaire }} whitespace-nowrap">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>Nouveau projet
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start" id="projetGrid">
            @forelse($projets as $projet)
                @php
                    [$libStatut, $clsStatut] = $statutsProjet[$projet->statut] ?? [ucfirst(str_replace('_', ' ', $projet->statut)), 'bg-slate-100 text-slate-700'];
                    [$libPriorite, $dotPriorite] = $priorites[$projet->priorite] ?? [ucfirst((string) $projet->priorite), 'bg-slate-300'];
                    $pct = $projet->progressionPourcent();
                @endphp
                <article class="projet-card min-w-0 bg-white border border-brand-100 rounded-xl p-5 sm:p-6"
                    data-statut="{{ $projet->statut }}">
                    <!-- Titre, statut -->
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="text-base font-semibold text-slate-900 leading-snug min-w-0 break-words">{{ $projet->titre }}</h2>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsStatut }}">{{ $libStatut }}</span>
                    </div>

                    @if (!$projet->isOwner(Auth::user()))
                        <span class="mt-2 inline-block text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">Collaborateur</span>
                    @endif

                    @if ($projet->description)
                        <p class="mt-1.5 text-sm text-slate-500 line-clamp-2 break-words">{{ $projet->description }}</p>
                    @endif

                    <!-- Synthèse -->
                    <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-3 text-sm rounded-lg bg-brand-50 border border-brand-100 p-4">
                        <div>
                            <dt class="text-xs text-slate-600">Priorité</dt>
                            <dd class="mt-0.5 flex items-center gap-2 font-medium text-slate-900">
                                <span class="h-2 w-2 rounded-full {{ $dotPriorite }}" aria-hidden="true"></span>
                                {{ $libPriorite }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-600">Période</dt>
                            <dd class="mt-0.5 font-medium text-slate-900 tabular-nums">
                                {{ $projet->date_debut->format('d/m/Y') }}
                                @if ($projet->date_fin)
                                    → {{ $projet->date_fin->format('d/m/Y') }}
                                @endif
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-xs text-slate-600">Mentors</dt>
                            <dd class="mt-0.5 font-medium text-slate-900 break-words">
                                {{ $projet->mentors->take(3)->pluck('prenom')->implode(', ') ?: '—' }}
                            </dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-xs text-slate-600">Stagiaires</dt>
                            <dd class="mt-0.5 font-medium text-slate-900 break-words">
                                @forelse ($projet->stagiaires->take(3) as $stag)
                                    {{ $stag->user->prenom ?? '' }}{{ !$loop->last ? ',' : '' }}
                                @empty
                                    —
                                @endforelse
                                @if ($projet->stagiaires->count() > 3)
                                    <span class="text-slate-600 font-normal tabular-nums">+{{ $projet->stagiaires->count() - 3 }}</span>
                                @endif
                            </dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="flex justify-between gap-3 text-xs text-slate-600">
                                <span class="tabular-nums">Progression
                                    ({{ $projet->tasks->where('statut', 'termine')->count() }}/{{ $projet->tasks->count() }}
                                    tâches)</span>
                                <span class="font-semibold text-slate-900 tabular-nums">{{ $pct }} %</span>
                            </dt>
                            <dd class="mt-2">
                                <div class="h-2 rounded-full bg-brand-100 overflow-hidden" role="progressbar"
                                    aria-label="Progression du projet {{ $projet->titre }}" aria-valuemin="0"
                                    aria-valuemax="100" aria-valuenow="{{ $pct }}">
                                    <div class="h-2 rounded-full bg-accent-500" style="width:{{ $pct }}%"></div>
                                </div>
                            </dd>
                        </div>
                    </dl>

                    <!-- Actions -->
                    <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                        <a href="{{ route('mentor.projects.show', $projet) }}" class="{{ $btnCarteSecondaire }}">
                            Voir<span class="sr-only"> le projet {{ $projet->titre }}</span>
                        </a>
                        <a href="{{ route('mentor.projects.edit', $projet) }}" class="{{ $btnCarteSecondaire }}">
                            <i class="fas fa-pen text-xs" aria-hidden="true"></i>Modifier<span class="sr-only"> le projet {{ $projet->titre }}</span>
                        </a>
                        <form action="{{ route('mentor.projects.destroy', $projet) }}" method="POST"
                            id="del-proj-{{ $projet->id }}">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-delete {{ $btnCarteDanger }}"
                                data-form="del-proj-{{ $projet->id }}"
                                aria-label="Supprimer le projet {{ $projet->titre }}">
                                <i class="fas fa-trash text-xs" aria-hidden="true"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="lg:col-span-2 rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                    <p class="text-sm font-medium text-slate-900">Aucun projet</p>
                    <p class="mt-1 text-sm text-slate-600">Créez un projet pour y assigner vos stagiaires et organiser leurs tâches.</p>
                    <a href="{{ route('mentor.projects.create') }}" class="mt-3 inline-block {{ $lien }}">Créer un projet</a>
                </div>
            @endforelse
        </div>

        <p id="projetNoResult" class="hidden rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center text-sm text-slate-600">
            Aucun résultat pour cette recherche.</p>

        <div>{{ $projets->links() }}</div>
    </div>
@endsection

@push('scripts')
    <script>
        function filterProjets() {
            const s = document.getElementById('search').value.toLowerCase();
            const st = document.getElementById('filterStatut').value;
            const cartes = document.querySelectorAll('.projet-card');
            let visibles = 0;
            cartes.forEach(card => {
                const ok = card.textContent.toLowerCase().includes(s) && (!st || card.dataset.statut === st);
                card.style.display = ok ? '' : 'none';
                if (ok) visibles++;
            });
            document.getElementById('projetNoResult')?.classList.toggle('hidden', cartes.length === 0 || visibles > 0);
        }
        document.getElementById('search').addEventListener('input', filterProjets);
        document.getElementById('filterStatut').addEventListener('change', filterProjets);
    </script>
@endpush
