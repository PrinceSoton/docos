@extends('layouts.admin')
@section('titre', 'Attestations & Conventions')
@section('breadcrumb', 'Suivi > Attestations')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPetit = 'inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium transition-colors whitespace-nowrap ' . $focus;
        $btnPetitPrimaire = $btnPetit . ' bg-brand-800 text-white hover:bg-brand-700';
        $btnPetitSecondaire = $btnPetit . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300';
        $btnPetitDanger = $btnPetit . ' border border-red-200 bg-white text-red-700 hover:bg-red-50';
        $btnIcone = 'inline-flex h-9 w-9 items-center justify-center rounded-lg border transition-colors flex-shrink-0 ' . $focus;
        $btnIconeSecondaire = $btnIcone . ' border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900';
        $btnIconeAccent = $btnIcone . ' border-accent-100 bg-accent-50 text-accent-700 hover:bg-accent-100';
        $btnIconeDanger = $btnIcone . ' border-red-200 bg-white text-red-700 hover:bg-red-50';

        $statuts = [
            'en_attente' => ['En attente', 'bg-amber-50 text-amber-800'],
            'valide_mentor' => ['Validé mentor', 'bg-sky-50 text-sky-800'],
            'approuve_admin' => ['Approuvé', 'bg-sky-50 text-sky-800'],
            'envoye' => ['Envoyé', 'bg-emerald-50 text-emerald-800'],
            'refuse' => ['Refusé', 'bg-red-50 text-red-800'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Résumé -->
        <div class="min-w-0">
            <h2 class="text-base font-semibold text-slate-900">Attestations &amp; Conventions</h2>
            <p class="text-sm text-slate-600 tabular-nums">
                <span class="font-semibold text-slate-900">{{ $attestations->total() }}</span> demande(s) au total
            </p>
        </div>

        <!-- Filtres (côté client) -->
        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3">
            <div class="w-full sm:flex-1 sm:min-w-[16rem]">
                <label for="search" class="sr-only">Rechercher une demande</label>
                <input type="search" id="search" placeholder="Rechercher stagiaire, type, statut..."
                    class="{{ $champ }}">
            </div>
            <div class="w-full sm:w-44">
                <label for="filterType" class="sr-only">Filtrer par type</label>
                <select id="filterType" class="{{ $champ }}">
                    <option value="">Tous types</option>
                    <option value="attestation">Attestation</option>
                    <option value="convention">Convention</option>
                </select>
            </div>
            <div class="w-full sm:w-44">
                <label for="filterStatut" class="sr-only">Filtrer par statut</label>
                <select id="filterStatut" class="{{ $champ }}">
                    <option value="">Tous statuts</option>
                    <option value="en_attente">En attente</option>
                    <option value="valide_mentor">Validé mentor</option>
                    <option value="approuve_admin">Approuvé</option>
                    <option value="envoye">Envoyé</option>
                    <option value="refuse">Refusé</option>
                </select>
            </div>
        </div>

        <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
                <h3 class="text-base font-semibold text-slate-900">Demandes</h3>
                @if ($attestations->count() > 0)
                    <span class="text-xs text-slate-600 tabular-nums">Page {{ $attestations->currentPage() }} /
                        {{ $attestations->lastPage() }}</span>
                @endif
            </div>

            @if ($attestations->count() > 0)
                <!-- Tableau (grands écrans) -->
                <div class="hidden xl:block">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col>
                            <col class="w-32">
                            <col class="w-32">
                            <col class="w-36">
                            <col class="w-28">
                            <col class="w-56">
                        </colgroup>
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr class="text-left text-xs font-medium text-slate-600">
                                <th scope="col" class="px-5 py-2.5">Stagiaire</th>
                                <th scope="col" class="px-3 py-2.5">Type</th>
                                <th scope="col" class="px-3 py-2.5">Statut</th>
                                <th scope="col" class="px-3 py-2.5">Demandé le</th>
                                <th scope="col" class="px-3 py-2.5">Décision mentor</th>
                                <th scope="col" class="px-5 py-2.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="attTable">
                            @foreach ($attestations as $att)
                                @php
                                    [$libStatut, $clsStatut] = $statuts[$att->statut] ?? [$att->statut, 'bg-slate-100 text-slate-700'];
                                    $nomStagiaire = $att->stagiaire->user->nom_complet ?? '—';
                                @endphp
                                <tr class="hover:bg-brand-50/60 att-row" data-type="{{ $att->type }}"
                                    data-statut="{{ $att->statut }}">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span
                                                class="h-8 w-8 rounded-full bg-brand-100 text-brand-800 text-xs font-semibold flex items-center justify-center flex-shrink-0"
                                                aria-hidden="true">{{ strtoupper(substr($att->stagiaire->user->prenom ?? '', 0, 1)) }}</span>
                                            <div class="min-w-0">
                                                <p class="font-medium text-slate-900 truncate" title="{{ $nomStagiaire }}">
                                                    {{ $nomStagiaire }}</p>
                                                <p class="text-xs text-slate-500 tabular-nums truncate">
                                                    {{ $att->stagiaire->matricule ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span
                                            class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 capitalize whitespace-nowrap">{{ $att->type }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span
                                            class="text-xs font-medium px-2 py-0.5 rounded-md whitespace-nowrap {{ $clsStatut }}">{{ $libStatut }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-slate-600 tabular-nums whitespace-nowrap">
                                        {{ $att->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-3 py-3 text-xs text-slate-600 tabular-nums whitespace-nowrap">
                                        {{ $att->valide_le_mentor ? $att->valide_le_mentor->format('d/m/Y') : '—' }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.attestations.show', $att) }}"
                                                class="{{ $btnPetitSecondaire }}"
                                                aria-label="Voir la demande de {{ $nomStagiaire }}">
                                                <i class="fas fa-eye text-xs" aria-hidden="true"></i>Voir
                                            </a>
                                            @if (in_array($att->statut, ['valide_mentor', 'approuve_admin']))
                                                <a href="{{ route('admin.attestations.uploadForm', $att) }}"
                                                    class="{{ $btnIconeAccent }}" title="Envoyer document"
                                                    aria-label="Envoyer le document à {{ $nomStagiaire }}">
                                                    <i class="fas fa-upload text-xs" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                            @if ($att->fichier)
                                                <a href="{{ route('admin.attestations.telecharger', $att) }}"
                                                    class="{{ $btnIconeSecondaire }}" title="Télécharger"
                                                    aria-label="Télécharger le document de {{ $nomStagiaire }}">
                                                    <i class="fas fa-download text-xs" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                            <form action="{{ route('admin.attestations.destroy', $att) }}" method="POST"
                                                id="del-att-{{ $att->id }}">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                                    data-form="del-att-{{ $att->id }}" title="Supprimer"
                                                    aria-label="Supprimer la demande de {{ $nomStagiaire }}">
                                                    <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Liste empilée (téléphone, tablette) -->
                <ul class="xl:hidden divide-y divide-slate-100">
                    @foreach ($attestations as $att)
                        @php
                            [$libStatut, $clsStatut] = $statuts[$att->statut] ?? [$att->statut, 'bg-slate-100 text-slate-700'];
                            $nomStagiaire = $att->stagiaire->user->nom_complet ?? '—';
                        @endphp
                        <li class="px-5 sm:px-6 py-4 att-row" data-type="{{ $att->type }}"
                            data-statut="{{ $att->statut }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span
                                        class="h-8 w-8 rounded-full bg-brand-100 text-brand-800 text-xs font-semibold flex items-center justify-center flex-shrink-0"
                                        aria-hidden="true">{{ strtoupper(substr($att->stagiaire->user->prenom ?? '', 0, 1)) }}</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-900 break-words">{{ $nomStagiaire }}</p>
                                        <p class="text-xs text-slate-500 tabular-nums">{{ $att->stagiaire->matricule ?? '' }}</p>
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 whitespace-nowrap {{ $clsStatut }}">{{ $libStatut }}</span>
                            </div>
                            <dl class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-2 text-sm">
                                <div>
                                    <dt class="text-xs text-slate-500">Type</dt>
                                    <dd class="mt-0.5 font-medium text-slate-900 capitalize">{{ $att->type }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Demandé le</dt>
                                    <dd class="mt-0.5 font-medium text-slate-900 tabular-nums">
                                        {{ $att->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Décision mentor</dt>
                                    <dd class="mt-0.5 font-medium text-slate-900 tabular-nums">
                                        {{ $att->valide_le_mentor ? $att->valide_le_mentor->format('d/m/Y') : '—' }}</dd>
                                </div>
                            </dl>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('admin.attestations.show', $att) }}" class="{{ $btnPetitSecondaire }}">
                                    <i class="fas fa-eye text-xs" aria-hidden="true"></i>Voir
                                </a>
                                @if (in_array($att->statut, ['valide_mentor', 'approuve_admin']))
                                    <a href="{{ route('admin.attestations.uploadForm', $att) }}"
                                        class="{{ $btnPetitPrimaire }}">
                                        <i class="fas fa-upload text-xs" aria-hidden="true"></i>Envoyer document
                                    </a>
                                @endif
                                @if ($att->fichier)
                                    <a href="{{ route('admin.attestations.telecharger', $att) }}"
                                        class="{{ $btnPetitSecondaire }}">
                                        <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger
                                    </a>
                                @endif
                                <form action="{{ route('admin.attestations.destroy', $att) }}" method="POST"
                                    id="del-att-{{ $att->id }}-carte">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-delete {{ $btnPetitDanger }}"
                                        data-form="del-att-{{ $att->id }}-carte"
                                        aria-label="Supprimer la demande de {{ $nomStagiaire }}">
                                        <i class="fas fa-trash text-xs" aria-hidden="true"></i>Supprimer
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <p id="aucunResultat" class="hidden px-5 sm:px-6 py-8 text-center text-sm text-slate-600">
                    Aucun résultat pour cette recherche.
                </p>
            @else
                <!-- État vide -->
                <div class="p-5 sm:p-6">
                    <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                        <p class="text-sm font-medium text-slate-900">Aucune demande d'attestation</p>
                        <p class="mt-1 text-sm text-slate-600 max-w-md mx-auto">Les demandes d'attestation ou de convention
                            des stagiaires apparaîtront ici dès leur envoi.</p>
                    </div>
                </div>
            @endif

            <div class="px-5 sm:px-6 py-4 border-t border-brand-100">{{ $attestations->links() }}</div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        function filterAtt() {
            const s = document.getElementById('search').value.toLowerCase();
            const t = document.getElementById('filterType').value;
            const st = document.getElementById('filterStatut').value;
            document.querySelectorAll('.att-row').forEach(row => {
                const ms = row.textContent.toLowerCase().includes(s);
                const mt = !t || row.dataset.type === t;
                const mst = !st || row.dataset.statut === st;
                row.style.display = ms && mt && mst ? '' : 'none';
            });
            // Le tableau sert de référence pour compter les résultats visibles
            const lignes = document.querySelectorAll('#attTable .att-row');
            const visibles = Array.from(lignes).filter(r => r.style.display !== 'none').length;
            document.getElementById('aucunResultat')?.classList.toggle('hidden', lignes.length === 0 || visibles > 0);
        }
        ['search', 'filterType', 'filterStatut'].forEach(id => document.getElementById(id).addEventListener('input',
            filterAtt));
    </script>
@endpush
