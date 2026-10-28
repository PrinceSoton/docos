@extends('layouts.mentor')
@section('titre', 'Attestations & Conventions')
@section('breadcrumb', 'Mentor > Attestations')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPetit = 'inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium transition-colors ' . $focus;
        $btnPetitPrimaire = $btnPetit . ' bg-brand-800 text-white hover:bg-brand-700';
        $btnPetitSecondaire = $btnPetit . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300';

        $statuts = [
            'en_attente' => ['En attente', 'bg-amber-50 text-amber-800'],
            'valide_mentor' => ['Validé', 'bg-sky-50 text-sky-800'],
            'approuve_admin' => ['Approuvé', 'bg-sky-50 text-sky-800'],
            'envoye' => ['Envoyé', 'bg-emerald-50 text-emerald-800'],
            'refuse' => ['Refusé', 'bg-red-50 text-red-800'],
        ];
        $types = [
            'attestation' => 'Attestation',
            'convention' => 'Convention',
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Résumé et filtres (sur le fond de page) -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-slate-900">Attestations et conventions</h2>
                <p class="text-sm text-slate-600 tabular-nums">
                    <span class="font-semibold text-slate-900">{{ $attestations->total() }}</span> demande(s) de vos stagiaires
                </p>
            </div>
            @if ($attestations->count() > 0)
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="search" id="search" placeholder="Rechercher stagiaire, type..."
                        aria-label="Rechercher une demande" class="w-full sm:w-64 {{ $champ }}">
                    <select id="filterStatut" aria-label="Filtrer par statut" class="w-full sm:w-44 {{ $champ }}">
                        <option value="">Tous statuts</option>
                        <option value="en_attente">En attente</option>
                        <option value="valide_mentor">Validé</option>
                        <option value="approuve_admin">Approuvé</option>
                        <option value="refuse">Refusé</option>
                        <option value="envoye">Envoyé</option>
                    </select>
                </div>
            @endif
        </div>

        @if ($attestations->count() > 0)
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
                    <h3 class="text-base font-semibold text-slate-900">Demandes reçues</h3>
                    <span class="text-xs text-slate-600 tabular-nums">Page {{ $attestations->currentPage() }} / {{ $attestations->lastPage() }}</span>
                </div>

                <!-- Tableau (grands écrans) -->
                <div class="hidden xl:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr class="text-left text-xs font-medium text-slate-600">
                                <th scope="col" class="px-5 py-3">Stagiaire</th>
                                <th scope="col" class="px-5 py-3">Type</th>
                                <th scope="col" class="px-5 py-3">Statut</th>
                                <th scope="col" class="px-5 py-3">Demandé le</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="attTable">
                            @foreach ($attestations as $att)
                                @php
                                    [$libStatut, $clsStatut] = $statuts[$att->statut] ?? [$att->statut, 'bg-slate-100 text-slate-700'];
                                    $libType = $types[$att->type] ?? ucfirst((string) $att->type);
                                @endphp
                                <tr class="hover:bg-slate-50 att-row" data-statut="{{ $att->statut }}">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="h-8 w-8 rounded-full bg-brand-100 text-brand-800 text-xs font-semibold flex items-center justify-center flex-shrink-0"
                                                aria-hidden="true">{{ strtoupper(substr($att->stagiaire->user->prenom ?? '', 0, 1)) }}</span>
                                            <div class="min-w-0">
                                                <p class="font-medium text-slate-900 truncate">{{ $att->stagiaire->user->nom_complet ?? '—' }}</p>
                                                <p class="text-xs text-slate-500 tabular-nums">{{ $att->stagiaire->matricule ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">{{ $libType }}</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md whitespace-nowrap {{ $clsStatut }}">{{ $libStatut }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-slate-500 text-xs tabular-nums">{{ $att->created_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($att->statut === 'en_attente')
                                                <a href="{{ route('mentor.attestations.validate', $att) }}" class="{{ $btnPetitPrimaire }}">
                                                    <i class="fas fa-check text-xs" aria-hidden="true"></i>Valider / Refuser
                                                </a>
                                            @endif
                                            <a href="{{ route('mentor.attestations.show', $att) }}" class="{{ $btnPetitSecondaire }}">
                                                <i class="fas fa-eye text-xs" aria-hidden="true"></i>Voir
                                            </a>
                                            @if ($att->fichier)
                                                <a href="{{ route('mentor.attestations.telecharger', $att) }}" class="{{ $btnPetitSecondaire }}">
                                                    <i class="fas fa-download text-xs" aria-hidden="true"></i>Document
                                                </a>
                                            @endif
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
                            $libType = $types[$att->type] ?? ucfirst((string) $att->type);
                        @endphp
                        <li class="px-5 sm:px-6 py-4 att-row" data-statut="{{ $att->statut }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-900 break-words">{{ $att->stagiaire->user->nom_complet ?? '—' }}</p>
                                    <p class="text-xs text-slate-500 tabular-nums">{{ $att->stagiaire->matricule ?? '' }}</p>
                                </div>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsStatut }}">{{ $libStatut }}</span>
                            </div>
                            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                <div>
                                    <dt class="text-xs text-slate-500">Type</dt>
                                    <dd class="mt-0.5 font-medium text-slate-900">{{ $libType }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Demandé le</dt>
                                    <dd class="mt-0.5 font-medium text-slate-900 tabular-nums">{{ $att->created_at->format('d/m/Y') }}</dd>
                                </div>
                            </dl>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @if ($att->statut === 'en_attente')
                                    <a href="{{ route('mentor.attestations.validate', $att) }}" class="{{ $btnPetitPrimaire }}">
                                        <i class="fas fa-check text-xs" aria-hidden="true"></i>Valider / Refuser
                                    </a>
                                @endif
                                <a href="{{ route('mentor.attestations.show', $att) }}" class="{{ $btnPetitSecondaire }}">
                                    <i class="fas fa-eye text-xs" aria-hidden="true"></i>Voir
                                </a>
                                @if ($att->fichier)
                                    <a href="{{ route('mentor.attestations.telecharger', $att) }}" class="{{ $btnPetitSecondaire }}">
                                        <i class="fas fa-download text-xs" aria-hidden="true"></i>Document
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

                <p id="aucunResultat" class="hidden px-5 sm:px-6 py-8 text-center text-sm text-slate-600">
                    Aucun résultat pour cette recherche.
                </p>

                @if ($attestations->hasPages())
                    <div class="px-5 sm:px-6 py-4 border-t border-brand-100">{{ $attestations->links() }}</div>
                @endif
            </section>
        @else
            <!-- État vide -->
            <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                <p class="text-sm font-medium text-slate-900">Aucune demande</p>
                <p class="mt-1 text-sm text-slate-600 max-w-md mx-auto">
                    Les demandes d'attestation ou de convention de vos stagiaires apparaîtront ici pour validation.
                </p>
            </div>
            <div>{{ $attestations->links() }}</div>
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

            function filterAtt() {
                const s = search.value.toLowerCase();
                const st = statut.value;
                let visibles = 0;
                document.querySelectorAll('.att-row').forEach(row => {
                    const ok = row.textContent.toLowerCase().includes(s) && (!st || row.dataset.statut === st);
                    row.style.display = ok ? '' : 'none';
                    if (ok) visibles++;
                });
                if (vide) vide.classList.toggle('hidden', visibles > 0);
            }
            search.addEventListener('input', filterAtt);
            statut.addEventListener('change', filterAtt);
        })();
    </script>
@endpush
