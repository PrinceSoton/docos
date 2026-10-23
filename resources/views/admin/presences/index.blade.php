@extends('layouts.admin')
@section('titre', 'Présences')
@section('breadcrumb', 'Suivi > Présences')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors whitespace-nowrap ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors whitespace-nowrap ' . $focus;
        $lienFichier = 'inline-flex items-center gap-1.5 text-xs font-medium text-brand-700 hover:underline underline-offset-4 rounded whitespace-nowrap ' . $focus;
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
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-slate-900">Suivi des présences</h2>
                <p class="text-sm text-slate-600">Historique complet de présence de chaque stagiaire</p>
            </div>
            <a href="{{ route('admin.presences.formMarquage') }}" class="w-full sm:w-auto {{ $btnPrimaire }}">
                <i class="fas fa-user-check text-xs" aria-hidden="true"></i>Marquer les présences
            </a>
        </div>

        <!-- Filtres -->
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <h3 class="text-base font-semibold text-slate-900">Filtrer les présences</h3>
            <form action="{{ route('admin.presences.index') }}" method="GET"
                class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))] gap-4 items-end no-loader">
                <div class="sm:col-span-2 lg:col-span-1">
                    <label for="filtre-stagiaire" class="block text-sm font-medium text-slate-700 mb-1">Stagiaire</label>
                    <select name="stagiaire_id" id="filtre-stagiaire" class="{{ $champ }}">
                        <option value="">Tous les stagiaires</option>
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
                    <a href="{{ route('admin.presences.index') }}" class="{{ $btnSecondaire }}">
                        <i class="fas fa-undo text-xs text-slate-400" aria-hidden="true"></i>Réinitialiser
                    </a>
                    @if (request('stagiaire_id'))
                        <a href="{{ route('admin.presences.show', request('stagiaire_id')) }}"
                            class="sm:ml-auto {{ $btnSecondaire }}">
                            <i class="fas fa-eye text-xs text-slate-400" aria-hidden="true"></i>Fiche complète
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- Synthèse du stagiaire sélectionné -->
        @if ($stagiaire)
            <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
                <h3 class="text-base font-semibold text-slate-900 break-words">Synthèse — {{ $stagiaire->user->nom_complet }}</h3>
                <dl class="mt-4 grid grid-cols-3 divide-x divide-brand-100 rounded-lg bg-brand-50 border border-brand-100 p-4">
                    @foreach ([['present', 'Présent', 'bg-emerald-500'], ['retard', 'Retard', 'bg-amber-500'], ['absent', 'Absent', 'bg-red-500']] as [$statut, $label, $dot])
                        @php $count = \App\Models\Presence::where('stagiaire_id', $stagiaire->id)->where('statut', $statut)->count(); @endphp
                        <div class="px-3 first:pl-0 last:pr-0">
                            <dd class="text-2xl font-semibold text-slate-900 tabular-nums">{{ $count }}</dd>
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
                <div class="{{ $enTete }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900 min-w-0">
                        @if ($stagiaire)
                            Présences de {{ $stagiaire->user->nom_complet }}
                        @else
                            Toutes les présences
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

                <!-- Tableau (grands écrans) -->
                <div class="hidden xl:block">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col class="w-32">
                            @if (!$stagiaire)
                                <col>
                            @endif
                            <col class="w-24">
                            <col class="w-20">
                            <col class="w-20">
                            <col>
                            <col class="w-36">
                            <col class="w-28">
                        </colgroup>
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
                        <tbody class="divide-y divide-slate-100" id="presenceTable">
                            @foreach ($presences as $presence)
                                @php [$libelle, $cls] = $statutsPresence[$presence->statut] ?? [ucfirst($presence->statut), 'bg-slate-100 text-slate-700']; @endphp
                                <tr class="hover:bg-brand-50/60 pres-row">
                                    <td class="px-5 py-3 whitespace-nowrap tabular-nums">
                                        <span class="block font-medium text-slate-900">{{ $presence->date->translatedFormat('d/m/Y') }}</span>
                                        <span class="block text-xs text-slate-500">{{ $presence->date->translatedFormat('l') }}</span></td>
                                    @if (!$stagiaire)
                                        <td class="px-3 py-3 text-slate-700 truncate"
                                            title="{{ $presence->stagiaire->user->nom_complet ?? '' }}">
                                            {{ $presence->stagiaire->user->nom_complet ?? '—' }}</td>
                                    @endif
                                    <td class="px-3 py-3">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md whitespace-nowrap {{ $cls }}">{{ $libelle }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-slate-700 tabular-nums">
                                        {{ $presence->heure_arrivee ? substr($presence->heure_arrivee, 0, 5) : '—' }}</td>
                                    <td class="px-3 py-3 text-slate-700 tabular-nums">
                                        {{ $presence->heure_depart ? substr($presence->heure_depart, 0, 5) : '—' }}</td>
                                    <td class="px-3 py-3 text-slate-500 truncate" title="{{ $presence->motif }}">
                                        {{ $presence->motif ?: '—' }}</td>
                                    <td class="px-3 py-3 text-slate-700 truncate"
                                        title="{{ $presence->marquePar ? $presence->marquePar->nom_complet : '' }}">
                                        {{ $presence->marquePar ? $presence->marquePar->nom_complet : '—' }}</td>
                                    <td class="px-5 py-3 text-right">
                                        @if ($presence->justificatif)
                                            <a href="{{ asset('storage/' . $presence->justificatif) }}" download
                                                class="{{ $lienFichier }}"
                                                aria-label="Télécharger le justificatif du {{ $presence->date->format('d/m/Y') }}">
                                                <i class="fas fa-download" aria-hidden="true"></i>Fichier
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
                    @foreach ($presences as $presence)
                        @php [$libelle, $cls] = $statutsPresence[$presence->statut] ?? [ucfirst($presence->statut), 'bg-slate-100 text-slate-700']; @endphp
                        <li class="px-5 sm:px-6 py-3 pres-row">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-medium text-slate-900 tabular-nums">
                                    {{ $presence->date->translatedFormat('l d/m/Y') }}</p>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $cls }}">{{ $libelle }}</span>
                            </div>
                            @if (!$stagiaire)
                                <p class="mt-0.5 text-sm text-slate-700 break-words">{{ $presence->stagiaire->user->nom_complet ?? '—' }}</p>
                            @endif
                            <p class="mt-1 text-sm text-slate-600 tabular-nums">
                                Arrivée {{ $presence->heure_arrivee ? substr($presence->heure_arrivee, 0, 5) : '—' }}
                                · Départ {{ $presence->heure_depart ? substr($presence->heure_depart, 0, 5) : '—' }}
                            </p>
                            @if ($presence->motif)
                                <p class="mt-1 text-sm text-slate-500 break-words">{{ $presence->motif }}</p>
                            @endif
                            @if ($presence->marquePar)
                                <p class="mt-1 text-xs text-slate-500">Marqué par {{ $presence->marquePar->nom_complet }}</p>
                            @endif
                            @if ($presence->justificatif)
                                <a href="{{ asset('storage/' . $presence->justificatif) }}" download class="mt-2 {{ $lienFichier }}">
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
                <p class="text-sm font-medium text-slate-900">Aucune présence trouvée pour les critères sélectionnés</p>
                <p class="mt-1 text-sm text-slate-600">Modifiez les filtres ou
                    <a href="{{ route('admin.presences.index') }}" class="{{ $lien }}">réinitialisez-les</a>.
                </p>
            </div>
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        /* Recherche instantanée dans le tableau et la liste */
        document.getElementById('search')?.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.pres-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
            // Le tableau sert de référence pour compter les résultats visibles
            const lignes = document.querySelectorAll('#presenceTable .pres-row');
            const visibles = Array.from(lignes).filter(r => r.style.display !== 'none').length;
            document.getElementById('presNoResult')?.classList.toggle('hidden', lignes.length === 0 || visibles > 0);
        });
    </script>
@endpush
