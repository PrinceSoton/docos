@extends('layouts.mentor')
@section('titre', 'Rapports')
@section('breadcrumb', 'Mentor > Rapports')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPetit = 'inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium transition-colors ' . $focus;
        $btnPetitPrimaire = $btnPetit . ' bg-brand-800 text-white hover:bg-brand-700';
        $btnPetitSecondaire = $btnPetit . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300';

        $statuts = [
            'soumis' => ['À évaluer', 'bg-amber-50 text-amber-800'],
            'valide' => ['Validé', 'bg-emerald-50 text-emerald-800'],
            'rejete' => ['Rejeté', 'bg-red-50 text-red-800'],
            'en_revision' => ['En révision', 'bg-sky-50 text-sky-800'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Résumé et filtres (sur le fond de page) -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-slate-900">Rapports des stagiaires</h2>
                <p class="text-sm text-slate-600 tabular-nums">
                    <span class="font-semibold text-slate-900">{{ $rapports->total() }}</span> rapport(s) reçu(s)
                </p>
            </div>
            @if ($rapports->count() > 0)
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="search" id="search" placeholder="Titre, stagiaire, type..."
                        aria-label="Rechercher un rapport" class="w-full sm:w-64 {{ $champ }}">
                    <select id="filterStatut" aria-label="Filtrer par statut" class="w-full sm:w-44 {{ $champ }}">
                        <option value="">Tous statuts</option>
                        <option value="soumis">À évaluer</option>
                        <option value="valide">Validé</option>
                        <option value="rejete">Rejeté</option>
                        <option value="en_revision">En révision</option>
                    </select>
                </div>
            @endif
        </div>

        @if ($rapports->count() > 0)
            <!-- Liste des rapports -->
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
                    <h3 class="text-base font-semibold text-slate-900">Rapports reçus</h3>
                    <span class="text-xs text-slate-600 tabular-nums">Page {{ $rapports->currentPage() }} / {{ $rapports->lastPage() }}</span>
                </div>

                <!-- Tableau (grands écrans) -->
                <div class="hidden xl:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr class="text-left text-xs font-medium text-slate-600">
                                <th scope="col" class="px-5 py-3">Rapport</th>
                                <th scope="col" class="px-5 py-3">Stagiaire</th>
                                <th scope="col" class="px-5 py-3 text-center">Note</th>
                                <th scope="col" class="px-5 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3">Date</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="rapportTable">
                            @foreach ($rapports as $rapport)
                                @php
                                    [$libStatut, $clsStatut] = $statuts[$rapport->statut] ?? [ucfirst(str_replace('_', ' ', $rapport->statut)), 'bg-slate-100 text-slate-700'];
                                    $clsNote = $rapport->note !== null
                                        ? ($rapport->note >= 14 ? 'text-emerald-700' : ($rapport->note >= 10 ? 'text-amber-800' : 'text-red-700'))
                                        : '';
                                @endphp
                                <tr class="hover:bg-slate-50 rapport-row" data-statut="{{ $rapport->statut }}">
                                    <td class="px-5 py-4 align-top">
                                        <p class="font-medium text-slate-900 break-words">{{ $rapport->titre }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500 break-words">
                                            <span class="capitalize">{{ $rapport->type_affiche }}</span>
                                            · Projet : {{ $rapport->project?->titre ?: '—' }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-4 align-top">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="h-8 w-8 rounded-full bg-brand-100 text-brand-800 text-xs font-semibold flex items-center justify-center flex-shrink-0"
                                                aria-hidden="true">{{ strtoupper(substr($rapport->stagiaire->user->prenom ?? '', 0, 1)) }}</span>
                                            <span class="text-slate-700 font-medium truncate">{{ $rapport->stagiaire->user->nom_complet ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 align-top text-center tabular-nums">
                                        @if ($rapport->note !== null)
                                            <span class="font-semibold {{ $clsNote }}">{{ $rapport->note }}/20</span>
                                        @else
                                            <span class="text-slate-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 align-top">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md whitespace-nowrap {{ $clsStatut }}">{{ $libStatut }}</span>
                                    </td>
                                    <td class="px-5 py-4 align-top text-slate-500 text-xs tabular-nums whitespace-nowrap">
                                        {{ $rapport->created_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-4 align-top">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($rapport->statut === 'soumis')
                                                <a href="{{ route('mentor.reports.evaluate', $rapport) }}" class="{{ $btnPetitPrimaire }}">
                                                    <i class="fas fa-star text-xs" aria-hidden="true"></i>Évaluer
                                                </a>
                                            @endif
                                            <a href="{{ route('mentor.reports.show', $rapport) }}" class="{{ $btnPetitSecondaire }}">
                                                <i class="fas fa-eye text-xs" aria-hidden="true"></i>Voir
                                            </a>
                                            <a href="{{ route('mentor.reports.telecharger', $rapport) }}" class="{{ $btnPetitSecondaire }}"
                                                aria-label="Télécharger le rapport {{ $rapport->titre }}">
                                                <i class="fas fa-download text-xs" aria-hidden="true"></i>Fichier
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Liste empilée (téléphone, tablette) -->
                <ul class="xl:hidden divide-y divide-slate-100">
                    @foreach ($rapports as $rapport)
                        @php
                            [$libStatut, $clsStatut] = $statuts[$rapport->statut] ?? [ucfirst(str_replace('_', ' ', $rapport->statut)), 'bg-slate-100 text-slate-700'];
                            $clsNote = $rapport->note !== null
                                ? ($rapport->note >= 14 ? 'text-emerald-700' : ($rapport->note >= 10 ? 'text-amber-800' : 'text-red-700'))
                                : '';
                        @endphp
                        <li class="px-5 sm:px-6 py-4 rapport-row" data-statut="{{ $rapport->statut }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-medium text-sm text-slate-900 break-words">{{ $rapport->titre }}</p>
                                    <p class="mt-0.5 text-sm text-slate-700">{{ $rapport->stagiaire->user->nom_complet ?? '—' }}</p>
                                </div>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsStatut }}">{{ $libStatut }}</span>
                            </div>
                            <dl class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-2 text-sm">
                                <div class="min-w-0">
                                    <dt class="text-xs text-slate-500">Type</dt>
                                    <dd class="mt-0.5 font-medium text-slate-900 capitalize break-words">{{ $rapport->type_affiche }}</dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="text-xs text-slate-500">Projet</dt>
                                    <dd class="mt-0.5 font-medium text-slate-900 break-words">{{ $rapport->project?->titre ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Note</dt>
                                    <dd class="mt-0.5 font-medium tabular-nums {{ $rapport->note !== null ? $clsNote : 'text-slate-500' }}">
                                        {{ $rapport->note !== null ? $rapport->note . '/20' : '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Date</dt>
                                    <dd class="mt-0.5 font-medium text-slate-900 tabular-nums">{{ $rapport->created_at->format('d/m/Y') }}</dd>
                                </div>
                            </dl>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @if ($rapport->statut === 'soumis')
                                    <a href="{{ route('mentor.reports.evaluate', $rapport) }}" class="{{ $btnPetitPrimaire }}">
                                        <i class="fas fa-star text-xs" aria-hidden="true"></i>Évaluer
                                    </a>
                                @endif
                                <a href="{{ route('mentor.reports.show', $rapport) }}" class="{{ $btnPetitSecondaire }}">
                                    <i class="fas fa-eye text-xs" aria-hidden="true"></i>Voir
                                </a>
                                <a href="{{ route('mentor.reports.telecharger', $rapport) }}" class="{{ $btnPetitSecondaire }}"
                                    aria-label="Télécharger le rapport {{ $rapport->titre }}">
                                    <i class="fas fa-download text-xs" aria-hidden="true"></i>Fichier
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <p id="aucunResultat" class="hidden px-5 sm:px-6 py-8 text-center text-sm text-slate-600">
                    Aucun résultat pour cette recherche.
                </p>

                @if ($rapports->hasPages())
                    <div class="px-5 sm:px-6 py-4 border-t border-brand-100">{{ $rapports->links() }}</div>
                @endif
            </section>
        @else
            <!-- État vide -->
            <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                <img src="{{ asset('images/illustrations/documents-empty.svg') }}" alt="" class="mx-auto w-32 h-auto mb-4">
                <p class="text-sm font-medium text-slate-900">Aucun rapport reçu</p>
                <p class="mt-1 text-sm text-slate-600 max-w-md mx-auto">
                    Les rapports déposés par vos stagiaires apparaîtront ici pour que vous puissiez les consulter et les évaluer.
                </p>
            </div>
            <div>{{ $rapports->links() }}</div>
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        (function() {
            const search = document.getElementById('search');
            const statut = document.getElementById('filterStatut');
            if (!search || !statut) return;
            const vide = document.getElementById('aucunResultat');

            function filterRap() {
                const s = search.value.toLowerCase();
                const st = statut.value;
                let visibles = 0;
                document.querySelectorAll('.rapport-row').forEach(row => {
                    const ok = row.textContent.toLowerCase().includes(s) && (!st || row.dataset.statut === st);
                    row.style.display = ok ? '' : 'none';
                    if (ok) visibles++;
                });
                if (vide) vide.classList.toggle('hidden', visibles > 0);
            }
            search.addEventListener('input', filterRap);
            statut.addEventListener('change', filterRap);
        })();
    </script>
@endpush
