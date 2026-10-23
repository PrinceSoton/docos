@extends('layouts.admin')
@section('titre', 'Stagiaires')
@section('breadcrumb', 'Gestion > Stagiaires')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnPetitSecondaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors whitespace-nowrap ' . $focus;
        $btnPetitDanger = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50 transition-colors whitespace-nowrap ' . $focus;
        // Boutons icône carrés (actions secondaires du tableau)
        $btnIcone = 'inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-800 hover:border-brand-200 transition-colors ' . $focus;
        $btnIconeDanger = 'inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-red-200 bg-white text-red-700 hover:bg-red-50 transition-colors ' . $focus;

        $statutsStagiaire = [
            'en_cours' => ['En cours', 'bg-sky-50 text-sky-800'],
            'termine' => ['Terminé', 'bg-emerald-50 text-emerald-800'],
            'suspendu' => ['Suspendu', 'bg-red-50 text-red-800'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- En-tête de liste -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <p class="text-sm text-slate-600 tabular-nums">
                <span class="font-semibold text-slate-900">{{ $stagiaires->total() }}</span> stagiaire(s) enregistré(s)
            </p>
            <a href="{{ route('admin.stagiaires.create') }}" class="w-full sm:w-auto whitespace-nowrap {{ $btnPrimaire }}">
                <i class="fas fa-plus text-xs" aria-hidden="true"></i>Nouveau stagiaire
            </a>
        </div>

        <!-- Filtres -->
        <div class="bg-white border border-brand-100 rounded-xl p-4 flex flex-col sm:flex-row sm:flex-wrap gap-3">
            <div class="flex-1 min-w-0 sm:min-w-[16rem]">
                <label for="search" class="sr-only">Rechercher un stagiaire</label>
                <input type="search" id="search" placeholder="Nom, matricule, école, spécialité..." class="{{ $champ }}">
            </div>
            <div class="sm:w-56">
                <label for="filterStatut" class="sr-only">Filtrer par statut</label>
                <select id="filterStatut" class="{{ $champ }}">
                    <option value="">Tous les statuts</option>
                    <option value="en_cours">En cours</option>
                    <option value="termine">Terminé</option>
                    <option value="suspendu">Suspendu</option>
                </select>
            </div>
        </div>

        <section class="bg-white border border-brand-100 rounded-xl overflow-hidden" aria-label="Liste des stagiaires">

            <!-- Tableau (écrans larges) -->
            <div class="hidden xl:block">
                <table class="w-full table-fixed text-sm">
                    <colgroup>
                        <col>
                        <col class="w-[7.5rem]">
                        <col class="w-[14%]">
                        <col class="w-[11%]">
                        <col class="w-[7.5rem]">
                        <col class="w-24">
                        <col class="w-[13rem]">
                    </colgroup>
                    <thead class="bg-brand-50 border-b border-brand-100">
                        <tr class="text-left text-xs font-medium text-slate-600">
                            <th scope="col" class="px-5 py-2.5">Stagiaire</th>
                            <th scope="col" class="px-3 py-2.5">Matricule</th>
                            <th scope="col" class="px-3 py-2.5">École / Spécialité</th>
                            <th scope="col" class="px-3 py-2.5">Mentor</th>
                            <th scope="col" class="px-3 py-2.5">Période</th>
                            <th scope="col" class="px-3 py-2.5">Statut</th>
                            <th scope="col" class="px-4 py-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="stagTable">
                        @forelse($stagiaires as $stag)
                            @php [$libelle, $cls] = $statutsStagiaire[$stag->statut] ?? [ucfirst($stag->statut), 'bg-slate-100 text-slate-700']; @endphp
                            <tr class="hover:bg-brand-50/60 stag-row" data-statut="{{ $stag->statut }}">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="hidden 2xl:flex h-9 w-9 rounded-full bg-brand-100 items-center justify-center flex-shrink-0 overflow-hidden">
                                            @if ($stag->user->photo)
                                                <img src="{{ asset('storage/' . $stag->user->photo) }}" alt=""
                                                    class="h-full w-full object-cover" loading="lazy">
                                            @else
                                                <span class="text-sm font-semibold text-brand-800"
                                                    aria-hidden="true">{{ strtoupper(substr($stag->user->prenom, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-slate-900 truncate" title="{{ $stag->user->nom_complet }}">
                                                {{ $stag->user->nom_complet }}</p>
                                            <p class="text-xs text-slate-500 truncate" title="{{ $stag->user->email }}">
                                                {{ $stag->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="block truncate font-mono text-xs text-slate-700" title="{{ $stag->matricule }}">{{ $stag->matricule }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    <p class="text-slate-900 truncate" title="{{ $stag->ecole }}">{{ $stag->ecole ?: '—' }}</p>
                                    <p class="text-xs text-slate-500 truncate" title="{{ $stag->specialite }}">
                                        {{ $stag->specialite ?: '' }}</p>
                                </td>
                                <td class="px-3 py-3">
                                    <p class="text-slate-700 truncate" title="{{ $stag->mentor?->nom_complet }}">
                                        {{ $stag->mentor?->nom_complet ?: '—' }}</p>
                                </td>
                                <td class="px-3 py-3 tabular-nums whitespace-nowrap">
                                    <span class="block text-slate-700">{{ $stag->date_debut->format('d/m/Y') }}</span>
                                    <span class="block text-xs text-slate-500">→ {{ $stag->date_fin->format('d/m/Y') }}</span>
                                    <span class="block text-xs font-medium text-brand-700">{{ $stag->jours_restants }} j restants</span>
                                </td>
                                <td class="px-3 py-3">
                                    <span
                                        class="text-xs font-medium px-2 py-0.5 rounded-md whitespace-nowrap {{ $cls }}">{{ $libelle }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.stagiaires.show', $stag) }}" class="{{ $btnPetitSecondaire }}"
                                            aria-label="Voir la fiche de {{ $stag->user->nom_complet }}">Voir</a>
                                        <a href="{{ route('admin.stagiaires.edit', $stag) }}" class="{{ $btnIcone }}"
                                            aria-label="Modifier {{ $stag->user->nom_complet }}" title="Modifier">
                                            <i class="fas fa-pen text-xs" aria-hidden="true"></i>
                                        </a>
                                        @if ($stag->cv)
                                            <a href="{{ asset('storage/' . $stag->cv) }}" download class="{{ $btnIcone }}"
                                                aria-label="Télécharger le CV de {{ $stag->user->nom_complet }}"
                                                title="Télécharger CV">
                                                <i class="fas fa-download text-xs" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.stagiaires.destroy', $stag) }}" method="POST"
                                            id="del-stag-{{ $stag->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                                data-form="del-stag-{{ $stag->id }}"
                                                aria-label="Supprimer {{ $stag->user->nom_complet }}" title="Supprimer">
                                                <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-5">
                                    <div
                                        class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                                        <p class="text-sm font-medium text-slate-900">Aucun stagiaire enregistré</p>
                                        <p class="mt-1 text-sm text-slate-600">Ajoutez un premier stagiaire pour commencer le
                                            suivi.</p>
                                        <a href="{{ route('admin.stagiaires.create') }}"
                                            class="mt-2 inline-block {{ $lien }}">Nouveau stagiaire</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Liste en cartes (téléphone et tablette) -->
            <ul class="xl:hidden divide-y divide-slate-100" id="stagCartes">
                @forelse($stagiaires as $stag)
                    @php [$libelle, $cls] = $statutsStagiaire[$stag->statut] ?? [ucfirst($stag->statut), 'bg-slate-100 text-slate-700']; @endphp
                    <li class="px-4 sm:px-5 py-4 stag-row" data-statut="{{ $stag->statut }}">
                        <div class="flex items-start gap-3">
                            <div
                                class="h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                @if ($stag->user->photo)
                                    <img src="{{ asset('storage/' . $stag->user->photo) }}" alt=""
                                        class="h-full w-full object-cover" loading="lazy">
                                @else
                                    <span class="text-sm font-semibold text-brand-800"
                                        aria-hidden="true">{{ strtoupper(substr($stag->user->prenom, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-900 truncate">{{ $stag->user->nom_complet }}</p>
                                        <p class="text-xs text-slate-500 truncate">{{ $stag->user->email }}</p>
                                    </div>
                                    <span
                                        class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 whitespace-nowrap {{ $cls }}">{{ $libelle }}</span>
                                </div>

                                <dl class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                                    <div class="min-w-0">
                                        <dt class="text-xs text-slate-500">Matricule</dt>
                                        <dd class="font-mono text-xs text-slate-900">{{ $stag->matricule }}</dd>
                                    </div>
                                    <div class="min-w-0">
                                        <dt class="text-xs text-slate-500">Mentor</dt>
                                        <dd class="text-slate-900 break-words">{{ $stag->mentor?->nom_complet ?: '—' }}</dd>
                                    </div>
                                    <div class="min-w-0">
                                        <dt class="text-xs text-slate-500">École / Spécialité</dt>
                                        <dd class="text-slate-900 break-words">{{ $stag->ecole ?: '—' }}@if ($stag->specialite)
                                                <span class="text-slate-500">· {{ $stag->specialite }}</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="min-w-0">
                                        <dt class="text-xs text-slate-500">Période</dt>
                                        <dd class="text-slate-900 tabular-nums">{{ $stag->date_debut->format('d/m/Y') }} →
                                            {{ $stag->date_fin->format('d/m/Y') }}
                                            <span class="block text-xs font-medium text-brand-700">{{ $stag->jours_restants }} j
                                                restants</span>
                                        </dd>
                                    </div>
                                </dl>

                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.stagiaires.show', $stag) }}" class="{{ $btnPetitSecondaire }}"
                                        aria-label="Voir la fiche de {{ $stag->user->nom_complet }}">
                                        <i class="fas fa-eye" aria-hidden="true"></i>Voir
                                    </a>
                                    <a href="{{ route('admin.stagiaires.edit', $stag) }}" class="{{ $btnPetitSecondaire }}"
                                        aria-label="Modifier {{ $stag->user->nom_complet }}">
                                        <i class="fas fa-pen" aria-hidden="true"></i>Modifier
                                    </a>
                                    @if ($stag->cv)
                                        <a href="{{ asset('storage/' . $stag->cv) }}" download class="{{ $btnPetitSecondaire }}"
                                            aria-label="Télécharger le CV de {{ $stag->user->nom_complet }}">
                                            <i class="fas fa-download" aria-hidden="true"></i>CV
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.stagiaires.destroy', $stag) }}" method="POST"
                                        id="del-stag-{{ $stag->id }}-carte">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-delete {{ $btnPetitDanger }}"
                                            data-form="del-stag-{{ $stag->id }}-carte"
                                            aria-label="Supprimer {{ $stag->user->nom_complet }}">
                                            <i class="fas fa-trash" aria-hidden="true"></i>Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="p-4 sm:p-5">
                        <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                            <p class="text-sm font-medium text-slate-900">Aucun stagiaire enregistré</p>
                            <p class="mt-1 text-sm text-slate-600">Ajoutez un premier stagiaire pour commencer le suivi.</p>
                            <a href="{{ route('admin.stagiaires.create') }}" class="mt-2 inline-block {{ $lien }}">Nouveau
                                stagiaire</a>
                        </div>
                    </li>
                @endforelse
            </ul>

            <p id="stagNoResult" class="hidden px-5 sm:px-6 py-6 text-center text-sm text-slate-500" aria-live="polite">
                Aucun résultat pour cette recherche.</p>

            <div class="px-4 sm:px-5 py-4 border-t border-slate-100">{{ $stagiaires->links() }}</div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        function filterTable() {
            const search = document.getElementById('search').value.toLowerCase();
            const statut = document.getElementById('filterStatut').value;
            document.querySelectorAll('.stag-row').forEach(row => {
                const matchSearch = row.textContent.toLowerCase().includes(search);
                const matchStatut = !statut || row.dataset.statut === statut;
                row.style.display = matchSearch && matchStatut ? '' : 'none';
            });
            // Message « aucun résultat » (le tableau sert de référence ; tableau et cartes portent les mêmes données)
            const lignes = document.querySelectorAll('#stagTable .stag-row');
            const visibles = Array.from(lignes).filter(r => r.style.display !== 'none').length;
            document.getElementById('stagNoResult')?.classList.toggle('hidden', lignes.length === 0 || visibles > 0);
        }
        document.getElementById('search').addEventListener('input', filterTable);
        document.getElementById('filterStatut').addEventListener('change', filterTable);
    </script>
@endpush
