@extends('layouts.mentor')
@section('titre', 'Mes Stagiaires')
@section('breadcrumb', 'Mentor > Mes stagiaires')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPetitSecondaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $statutsStagiaire = [
            'en_cours' => ['En cours', 'bg-sky-50 text-sky-800'],
            'termine' => ['Terminé', 'bg-emerald-50 text-emerald-800'],
            'suspendu' => ['Suspendu', 'bg-slate-100 text-slate-700'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Barre d'outils -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <p class="text-sm text-slate-600 tabular-nums">
                <span class="font-semibold text-slate-900">{{ $stagiaires->total() }}</span> stagiaire(s) sous votre
                encadrement
            </p>
            <input type="search" id="search" placeholder="Rechercher par nom, matricule, école..."
                aria-label="Rechercher un stagiaire" class="w-full sm:w-72 {{ $champ }}">
        </div>

        @if ($stagiaires->count() > 0)
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">

                <!-- Tableau (écrans larges) -->
                <div class="hidden xl:block overflow-x-auto">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col>
                            <col class="w-[16%]">
                            <col class="w-[7.5rem]">
                            <col class="w-24">
                            <col class="w-28">
                            <col class="w-24">
                            <col class="w-40">
                        </colgroup>
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr class="text-left text-xs font-medium text-slate-600">
                                <th scope="col" class="px-5 py-2.5">Stagiaire</th>
                                <th scope="col" class="px-3 py-2.5">Formation</th>
                                <th scope="col" class="px-3 py-2.5">Période</th>
                                <th scope="col" class="px-3 py-2.5">Progression</th>
                                <th scope="col" class="px-3 py-2.5">Suivi</th>
                                <th scope="col" class="px-3 py-2.5">Statut</th>
                                <th scope="col" class="px-4 py-2.5 text-right"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="stagGrid">
                            @foreach ($stagiaires as $stag)
                                @php
                                    $totalT = $stag->tasks->count();
                                    $doneT = $stag->tasks->where('statut', 'termine')->count();
                                    $pct = $totalT > 0 ? round(($doneT / $totalT) * 100) : 0;
                                    [$libelle, $cls] = $statutsStagiaire[$stag->statut] ?? [ucfirst($stag->statut), 'bg-slate-100 text-slate-700'];
                                @endphp
                                <tr class="hover:bg-slate-50 stag-card">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="h-9 w-9 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                @if ($stag->user->photo)
                                                    <img src="{{ asset('storage/' . $stag->user->photo) }}" alt=""
                                                        class="h-full w-full object-cover" loading="lazy">
                                                @else
                                                    <span class="text-sm font-semibold text-brand-800"
                                                        aria-hidden="true">{{ strtoupper(substr($stag->user->prenom, 0, 1)) }}</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-medium text-slate-900 truncate" title="{{ $stag->user->nom_complet }}">{{ $stag->user->nom_complet }}</p>
                                                <p class="text-xs text-slate-500 font-mono truncate">{{ $stag->matricule }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <p class="text-slate-900 truncate" title="{{ $stag->ecole }}">{{ $stag->ecole ?: '—' }}</p>
                                        <p class="text-xs text-slate-500 truncate" title="{{ $stag->specialite }}">
                                            {{ $stag->specialite ?: '—' }}</p>
                                    </td>
                                    <td class="px-3 py-3 text-slate-700 tabular-nums whitespace-nowrap">
                                        <span class="block">{{ $stag->date_debut->format('d/m/Y') }}</span>
                                        <span class="block text-xs text-slate-500">→ {{ $stag->date_fin->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <p class="text-xs font-medium text-slate-900 tabular-nums">{{ $pct }} %</p>
                                        <div class="mt-1 h-1.5 rounded-full bg-slate-100 overflow-hidden" role="progressbar"
                                            aria-label="Progression des tâches de {{ $stag->user->nom_complet }}"
                                            aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $pct }}">
                                            <div class="h-1.5 rounded-full bg-accent-500" style="width:{{ $pct }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-slate-600 tabular-nums whitespace-nowrap leading-5">
                                        <span class="block"><span class="font-semibold text-slate-900">{{ $stag->presences->where('statut', 'present')->count() }}</span> présent</span>
                                        <span class="block"><span class="font-semibold text-slate-900">{{ $stag->reports->count() }}</span> rapports</span>
                                        <span class="block"><span class="font-semibold text-slate-900">{{ $totalT }}</span> tâches</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md whitespace-nowrap {{ $cls }}">{{ $libelle }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('mentor.stagiaires.show', $stag) }}" class="{{ $btnPetitSecondaire }} whitespace-nowrap"
                                            aria-label="Voir le profil complet de {{ $stag->user->nom_complet }}">Voir le profil</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Liste (téléphone et tablette) -->
                <ul class="xl:hidden divide-y divide-slate-100">
                    @foreach ($stagiaires as $stag)
                        @php
                            $totalT = $stag->tasks->count();
                            $doneT = $stag->tasks->where('statut', 'termine')->count();
                            $pct = $totalT > 0 ? round(($doneT / $totalT) * 100) : 0;
                            [$libelle, $cls] = $statutsStagiaire[$stag->statut] ?? [ucfirst($stag->statut), 'bg-slate-100 text-slate-700'];
                        @endphp
                        <li class="px-5 py-4 stag-card">
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
                                            <p class="text-xs text-slate-500 font-mono">{{ $stag->matricule }}</p>
                                        </div>
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $cls }}">{{ $libelle }}</span>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-600 break-words">
                                        {{ $stag->ecole ?: '—' }} · {{ $stag->specialite ?: '—' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500 tabular-nums">
                                        {{ $stag->date_debut->format('d/m/Y') }} → {{ $stag->date_fin->format('d/m/Y') }}</p>

                                    <div class="mt-3 flex items-baseline justify-between gap-3 text-xs">
                                        <span class="text-slate-500">Progression</span>
                                        <span class="font-medium text-slate-900 tabular-nums">{{ $pct }} %</span>
                                    </div>
                                    <div class="mt-1 h-2 rounded-full bg-slate-100 overflow-hidden" role="progressbar"
                                        aria-label="Progression des tâches de {{ $stag->user->nom_complet }}" aria-valuemin="0"
                                        aria-valuemax="100" aria-valuenow="{{ $pct }}">
                                        <div class="h-2 rounded-full bg-accent-500" style="width:{{ $pct }}%"></div>
                                    </div>

                                    <dl class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-xs">
                                        @foreach ([['Présent', $stag->presences->where('statut', 'present')->count()], ['Rapports', $stag->reports->count()], ['Tâches', $totalT]] as [$label, $val])
                                            <div class="flex items-center gap-1.5">
                                                <dt class="text-slate-500">{{ $label }}</dt>
                                                <dd class="font-semibold text-slate-900 tabular-nums">{{ $val }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>

                                    <a href="{{ route('mentor.stagiaires.show', $stag) }}"
                                        class="mt-3 w-full sm:w-auto {{ $btnPetitSecondaire }}">Voir le profil complet</a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <p id="stagNoResult" class="hidden px-5 sm:px-6 py-6 text-center text-sm text-slate-500">Aucun résultat pour
                    cette recherche.</p>
            </section>
        @else
            <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                <p class="text-sm font-medium text-slate-900">Aucun stagiaire assigné</p>
                <p class="mt-1 text-sm text-slate-600">Les stagiaires que l'administration vous affecte apparaîtront ici.</p>
            </div>
        @endif

        <div>{{ $stagiaires->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.stag-card').forEach(card => {
                card.style.display = card.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
            // Le tableau sert de référence pour compter les résultats visibles
            const lignes = document.querySelectorAll('#stagGrid .stag-card');
            const visibles = Array.from(lignes).filter(r => r.style.display !== 'none').length;
            document.getElementById('stagNoResult')?.classList.toggle('hidden', lignes.length === 0 || visibles > 0);
        });
    </script>
@endpush
