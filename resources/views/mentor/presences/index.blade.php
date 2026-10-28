@extends('layouts.mentor')
@section('titre', 'Présences & Permissions')
@section('breadcrumb', 'Mentor > Présences')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $btnDanger = 'inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-50 transition-colors ' . $focus;
        $lienFichier = 'inline-flex items-center gap-1.5 text-xs font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $panneau = 'bg-white border border-brand-100 rounded-xl overflow-hidden';
        $enTete = 'px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100';

        $statutsPresence = [
            'present' => ['Présent', 'bg-emerald-50 text-emerald-800'],
            'retard' => ['Retard', 'bg-amber-50 text-amber-800'],
            'absent' => ['Absent', 'bg-red-50 text-red-800'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Barre d'outils -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <p class="text-sm text-slate-600">Suivi des présences et validation des permissions</p>
            <a href="{{ route('mentor.presences.formMarquage') }}" class="w-full sm:w-auto {{ $btnPrimaire }}">
                <i class="fas fa-user-check text-xs" aria-hidden="true"></i>Marquer les présences
            </a>
        </div>

        <!-- Permissions en attente -->
        @if ($permissionsEnAttente->count() > 0)
            <section class="{{ $panneau }}">
                <div class="{{ $enTete }} flex items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900">Permissions en attente de validation</h3>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 tabular-nums flex-shrink-0">
                        {{ $permissionsEnAttente->count() }} en attente</span>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach ($permissionsEnAttente as $perm)
                        @php $nbJours = $perm->date_debut->diffInDays($perm->date_fin) + 1; @endphp
                        <li class="px-5 sm:px-6 py-4">
                            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div
                                        class="h-9 w-9 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-semibold text-brand-800"
                                            aria-hidden="true">{{ strtoupper(substr($perm->stagiaire->user->prenom ?? '', 0, 1)) }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-900">
                                            {{ $perm->stagiaire->user->nom_complet ?? '—' }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500 tabular-nums">
                                            {{ $perm->date_debut->format('d/m/Y') }} → {{ $perm->date_fin->format('d/m/Y') }}
                                            ({{ $nbJours }} jour(s))
                                        </p>
                                        <p class="mt-1 text-sm text-slate-600 break-words">{{ $perm->motif }}</p>
                                        @if ($perm->justificatif)
                                            <a href="{{ asset('storage/' . $perm->justificatif) }}" download
                                                class="mt-2 {{ $lienFichier }}">
                                                <i class="fas fa-paperclip" aria-hidden="true"></i>Télécharger le justificatif
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2 w-full lg:w-auto flex-shrink-0">
                                    <form action="{{ route('mentor.presences.validerPermission', $perm) }}" method="POST"
                                        class="no-loader flex-1 lg:flex-none">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="valide">
                                        <button type="submit" class="w-full {{ $btnPrimaire }}">
                                            <i class="fas fa-check text-xs" aria-hidden="true"></i>Valider
                                        </button>
                                    </form>
                                    <button type="button" onclick="ouvrirRefus({{ $perm->id }})"
                                        class="flex-1 lg:flex-none {{ $btnDanger }}">
                                        <i class="fas fa-times text-xs" aria-hidden="true"></i>Refuser
                                    </button>
                                    <form action="{{ route('mentor.presences.validerPermission', $perm) }}" method="POST"
                                        id="refusForm-{{ $perm->id }}" class="hidden no-loader">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="refuse">
                                        <input type="hidden" name="commentaire_mentor"
                                            id="commentaireRefus-{{ $perm->id }}">
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <!-- Filtres -->
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <h3 class="text-base font-semibold text-slate-900">Filtrer les présences</h3>
            <form action="{{ route('mentor.presences.index') }}" method="GET"
                class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] gap-4 items-end no-loader">
                <div class="sm:col-span-2 lg:col-span-1">
                    <label for="filtre-stagiaire" class="block text-sm font-medium text-slate-700 mb-1">Stagiaire</label>
                    <select name="stagiaire_id" id="filtre-stagiaire" class="{{ $champ }}">
                        <option value="">Tous mes stagiaires</option>
                        @foreach ($stagiaires as $stag)
                            <option value="{{ $stag->id }}"
                                {{ request('stagiaire_id') == $stag->id ? 'selected' : '' }}>
                                {{ $stag->user->nom_complet }} — {{ $stag->matricule }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filtre-date" class="block text-sm font-medium text-slate-700 mb-1">Jour</label>
                    <input type="date" name="date" id="filtre-date" value="{{ request('date') }}"
                        class="{{ $champ }} tabular-nums">
                </div>
                <div>
                    <label for="filtre-mois" class="block text-sm font-medium text-slate-700 mb-1">Mois</label>
                    <input type="month" name="mois" id="filtre-mois" value="{{ request('mois', date('Y-m')) }}"
                        class="{{ $champ }} tabular-nums">
                </div>
                <div>
                    <label for="filtre-annee" class="block text-sm font-medium text-slate-700 mb-1">Année</label>
                    <input type="number" name="annee" id="filtre-annee" value="{{ request('annee') }}" min="2000"
                        max="{{ date('Y') }}" placeholder="Ex: 2025" class="{{ $champ }} tabular-nums">
                </div>

                <div class="sm:col-span-2 lg:col-span-4 flex flex-col sm:flex-row sm:flex-wrap gap-2">
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-search text-xs" aria-hidden="true"></i>Filtrer
                    </button>
                    <a href="{{ route('mentor.presences.index') }}" class="{{ $btnSecondaire }}">
                        <i class="fas fa-undo text-xs text-slate-400" aria-hidden="true"></i>Réinitialiser
                    </a>
                    @if (request('stagiaire_id'))
                        <a href="{{ route('mentor.presences.show', request('stagiaire_id')) }}"
                            class="sm:ml-auto {{ $btnSecondaire }}">
                            <i class="fas fa-eye text-xs text-slate-400" aria-hidden="true"></i>Fiche complète
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- Synthèse du stagiaire sélectionné -->
        @if ($stagiaire && $statsStagiaire)
            <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
                <h3 class="text-base font-semibold text-slate-900">Synthèse — {{ $stagiaire->user->nom_complet }}</h3>
                <dl class="mt-4 grid grid-cols-3 divide-x divide-brand-100 rounded-lg bg-brand-50 border border-brand-100 p-4">
                    @foreach ([['present', 'Présent', 'bg-emerald-500'], ['retard', 'Retard', 'bg-amber-500'], ['absent', 'Absent', 'bg-red-500']] as [$statut, $label, $dot])
                        <div class="px-3 first:pl-0 last:pr-0">
                            <dd class="text-2xl font-semibold text-slate-900 tabular-nums">{{ $statsStagiaire[$statut] }}</dd>
                            <dt class="mt-1 flex items-center gap-1.5 text-xs text-slate-600">
                                <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                                {{ $label }}
                            </dt>
                        </div>
                    @endforeach
                </dl>
            </section>
        @endif

        <!-- Tableau des présences -->
        @if ($presences->count() > 0)
            <section class="{{ $panneau }}">
                <div class="{{ $enTete }} flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900 min-w-0">
                        @if ($stagiaire)
                            Présences de {{ $stagiaire->user->nom_complet }}
                        @else
                            Toutes les présences de mes stagiaires
                        @endif
                        @if (request('date'))
                            — le {{ \Carbon\Carbon::parse(request('date'))->translatedFormat('d/m/Y') }}
                        @elseif(request('mois'))
                            — {{ \Carbon\Carbon::parse(request('mois'))->translatedFormat('F Y') }}
                        @elseif(request('annee'))
                            — Année {{ request('annee') }}
                        @endif
                    </h3>
                    <input type="search" id="search" placeholder="Rechercher..." aria-label="Rechercher dans les présences"
                        class="w-full sm:w-56 {{ $champ }}">
                </div>

                <!-- Tableau (écrans larges) -->
                <div class="hidden xl:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr class="text-left text-xs font-medium text-slate-600">
                                <th scope="col" class="px-5 py-2.5">Date</th>
                                @if (!$stagiaire)
                                    <th scope="col" class="px-3 py-2.5">Stagiaire</th>
                                @endif
                                <th scope="col" class="px-3 py-2.5">Statut</th>
                                <th scope="col" class="px-3 py-2.5">Arrivée</th>
                                <th scope="col" class="px-3 py-2.5">Départ</th>
                                <th scope="col" class="px-3 py-2.5">Motif</th>
                                <th scope="col" class="px-3 py-2.5">Marqué par</th>
                                <th scope="col" class="px-5 py-2.5 text-right">Justificatif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="presTable">
                            @foreach ($presences as $p)
                                @php [$libelle, $cls] = $statutsPresence[$p->statut] ?? [ucfirst($p->statut), 'bg-slate-100 text-slate-700']; @endphp
                                <tr class="hover:bg-slate-50 pres-row">
                                    <td class="px-5 py-3 font-medium text-slate-900 whitespace-nowrap tabular-nums">
                                        {{ $p->date->translatedFormat('D d/m/Y') }}</td>
                                    @if (!$stagiaire)
                                        <td class="px-3 py-3 text-slate-700">
                                            {{ $p->stagiaire->user->nom_complet ?? '—' }}
                                        </td>
                                    @endif
                                    <td class="px-3 py-3">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md {{ $cls }}">{{ $libelle }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-slate-700 tabular-nums">
                                        {{ $p->heure_arrivee ? substr($p->heure_arrivee, 0, 5) : '—' }}</td>
                                    <td class="px-3 py-3 text-slate-700 tabular-nums">
                                        {{ $p->heure_depart ? substr($p->heure_depart, 0, 5) : '—' }}</td>
                                    <td class="px-3 py-3 text-slate-500 max-w-[12rem] truncate" title="{{ $p->motif }}">
                                        {{ $p->motif ?: '—' }}</td>
                                    <td class="px-3 py-3 text-slate-700">
                                        {{ $p->marquePar ? $p->marquePar->nom_complet : '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        @if ($p->justificatif)
                                            <a href="{{ asset('storage/' . $p->justificatif) }}" download
                                                class="{{ $lienFichier }}"
                                                aria-label="Télécharger le justificatif du {{ $p->date->format('d/m/Y') }}">
                                                <i class="fas fa-download" aria-hidden="true"></i>Télécharger
                                            </a>
                                        @else
                                            <span class="text-slate-500">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Liste (téléphone et tablette) -->
                <ul class="xl:hidden divide-y divide-slate-100">
                    @foreach ($presences as $p)
                        @php [$libelle, $cls] = $statutsPresence[$p->statut] ?? [ucfirst($p->statut), 'bg-slate-100 text-slate-700']; @endphp
                        <li class="px-5 py-3 pres-row">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-medium text-slate-900 tabular-nums">
                                    {{ $p->date->translatedFormat('D d/m/Y') }}</p>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $cls }}">{{ $libelle }}</span>
                            </div>
                            @if (!$stagiaire)
                                <p class="mt-0.5 text-sm text-slate-700">{{ $p->stagiaire->user->nom_complet ?? '—' }}</p>
                            @endif
                            <p class="mt-1 text-sm text-slate-600 tabular-nums">
                                Arrivée {{ $p->heure_arrivee ? substr($p->heure_arrivee, 0, 5) : '—' }}
                                · Départ {{ $p->heure_depart ? substr($p->heure_depart, 0, 5) : '—' }}
                            </p>
                            @if ($p->motif)
                                <p class="mt-1 text-sm text-slate-500 break-words">{{ $p->motif }}</p>
                            @endif
                            @if ($p->marquePar)
                                <p class="mt-1 text-xs text-slate-500">Marqué par {{ $p->marquePar->nom_complet }}</p>
                            @endif
                            @if ($p->justificatif)
                                <a href="{{ asset('storage/' . $p->justificatif) }}" download class="mt-2 {{ $lienFichier }}">
                                    <i class="fas fa-download" aria-hidden="true"></i>Télécharger le justificatif
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <p id="presNoResult" class="hidden px-5 sm:px-6 py-6 text-center text-sm text-slate-500">Aucun résultat pour
                    cette recherche.</p>

                <div class="px-5 sm:px-6 py-4 border-t border-brand-100">{{ $presences->links() }}</div>
            </section>
        @else
            <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                @if (request('date') || request('mois') || request('annee') || request('stagiaire_id'))
                    <p class="text-sm font-medium text-slate-900">Aucune présence trouvée pour les critères sélectionnés</p>
                    <p class="mt-1 text-sm text-slate-600">Modifiez les filtres ou
                        <a href="{{ route('mentor.presences.index') }}" class="{{ $lien }}">réinitialisez-les</a>.
                    </p>
                @else
                    <p class="text-sm font-medium text-slate-900">Aucune présence enregistrée pour vos stagiaires</p>
                    <p class="mt-1 text-sm text-slate-600">Les présences apparaîtront ici dès qu'elles seront marquées.</p>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        /* Recherche instantanée dans le tableau et la liste */
        document.getElementById('search')?.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.pres-row').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
            // Le tableau sert de référence pour compter les résultats visibles
            const lignes = document.querySelectorAll('#presTable .pres-row');
            const visibles = Array.from(lignes).filter(r => r.style.display !== 'none').length;
            document.getElementById('presNoResult')?.classList.toggle('hidden', lignes.length === 0 || visibles > 0);
        });

        /* Refus de permission avec motif (SweetAlert) */
        function ouvrirRefus(id) {
            Swal.fire({
                title: 'Motif du refus',
                input: 'textarea',
                inputLabel: 'Ce commentaire sera transmis au stagiaire',
                inputPlaceholder: 'Expliquez le motif du refus (optionnel)...',
                showCancelButton: true,
                confirmButtonText: 'Confirmer le refus',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#b91c1c',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                inputAttributes: {
                    rows: 3,
                    'aria-label': 'Motif du refus'
                }
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('commentaireRefus-' + id).value = result.value || '';
                    document.getElementById('refusForm-' + id).submit();
                }
            });
        }
    </script>
@endpush
