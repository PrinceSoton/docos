@extends('layouts.admin')
@section('titre', 'Statistiques')
@section('breadcrumb', 'Système > Statistiques')
@section('content')
    @php
        $panneau = 'bg-white border border-brand-100 rounded-xl overflow-hidden';
        $enTete = 'px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex flex-wrap items-center justify-between gap-x-3 gap-y-1';
        $pct = fn($part, $tout) => $tout > 0 ? (int) round(($part / $tout) * 100) : 0;

        // Indicateurs clés : [libellé, valeur, icône, pastille, précision, part (ou null), libellé de la part]
        $kpis = [
            ['Utilisateurs totaux', $stats['users'], 'users', 'bg-brand-50 text-brand-700', 'Comptes de tous les rôles', null, null],
            ['Stagiaires actifs', $stats['stagiaires_actifs'], 'user-graduate', 'bg-accent-50 text-accent-700', 'sur ' . $stats['stagiaires'] . ' stagiaire(s)', $pct($stats['stagiaires_actifs'], $stats['stagiaires']), 'Part des stagiaires actifs'],
            ['Projets en cours', $stats['projets_en_cours'], 'project-diagram', 'bg-brand-50 text-brand-700', 'sur ' . $stats['projets'] . ' projet(s)', $pct($stats['projets_en_cours'], $stats['projets']), 'Part des projets en cours'],
            ['Rapports validés', $stats['rapports_valides'], 'file-alt', 'bg-accent-50 text-accent-700', 'sur ' . $stats['rapports'] . ' rapport(s)', $pct($stats['rapports_valides'], $stats['rapports']), 'Part des rapports validés'],
        ];

        // Volumes : [valeur, libellé]
        $volumes = [
            [$stats['stagiaires'], 'Total stagiaires'],
            [$stats['stagiaires_fin'], 'Stages terminés'],
            [$stats['mentors'], 'Mentors'],
            [$stats['projets'], 'Total projets'],
            [$stats['taches'], 'Total tâches'],
            [$stats['taches_terminees'], 'Tâches terminées'],
            [$stats['absences'], 'Total absences'],
            [$stats['attestations_envoyees'], 'Attestations envoyées'],
        ];
        $pctTaches = $pct($stats['taches_terminees'], $stats['taches']);
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Introduction -->
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Statistiques globales</h2>
            <p class="text-sm text-slate-600">Vue d'ensemble de l'activité de l'entreprise</p>
        </div>

        <!-- Indicateurs clés -->
        <section aria-labelledby="kpis-titre">
            <h3 id="kpis-titre" class="sr-only">Indicateurs clés</h3>
            <dl class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach ($kpis as [$label, $val, $icon, $pastille, $precision, $part, $libellePart])
                    <div class="bg-white border border-brand-100 rounded-xl p-4 sm:p-5">
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-sm font-medium text-slate-700">{{ $label }}</dt>
                            <span class="h-9 w-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ $pastille }}"
                                aria-hidden="true"><i class="fas fa-{{ $icon }} text-sm"></i></span>
                        </div>
                        <dd class="mt-1">
                            <span class="block text-2xl font-semibold text-slate-900 tabular-nums">{{ $val }}</span>
                            <span class="mt-0.5 flex items-baseline justify-between gap-2 text-xs text-slate-500 tabular-nums">
                                <span>{{ $precision }}</span>
                                @if (!is_null($part))
                                    <span class="font-medium text-slate-700">{{ $part }} %</span>
                                @endif
                            </span>
                            @if (!is_null($part))
                                <span class="mt-2 block h-1.5 rounded-full bg-brand-100 overflow-hidden" role="progressbar"
                                    aria-label="{{ $libellePart }}" aria-valuemin="0" aria-valuemax="100"
                                    aria-valuenow="{{ $part }}">
                                    <span class="block h-1.5 rounded-full bg-accent-500" style="width:{{ $part }}%"></span>
                                </span>
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <!-- Volumes -->
        <section class="{{ $panneau }}" aria-labelledby="volumes-titre">
            <div class="{{ $enTete }}">
                <h3 id="volumes-titre" class="text-base font-semibold text-slate-900">Volumes</h3>
                <p class="text-xs text-slate-600 tabular-nums">Tâches terminées : <span
                        class="font-medium text-slate-900">{{ $pctTaches }} %</span></p>
            </div>
            <dl class="grid grid-cols-2 sm:grid-cols-4 gap-px bg-slate-100">
                @foreach ($volumes as [$val, $label])
                    <div class="bg-white px-5 sm:px-6 py-4 min-w-0 flex flex-col-reverse">
                        <dt class="mt-0.5 text-xs text-slate-500">{{ $label }}</dt>
                        <dd class="text-xl font-semibold text-slate-900 tabular-nums">{{ $val }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">

            <!-- Présences par mois -->
            <section class="{{ $panneau }}" aria-labelledby="presences-mois-titre">
                <div class="{{ $enTete }}">
                    <h3 id="presences-mois-titre" class="text-base font-semibold text-slate-900">Présences sur 6 mois</h3>
                    <ul class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-600" aria-label="Légende">
                        <li class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"
                                aria-hidden="true"></span>Présent</li>
                        <li class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-amber-500"
                                aria-hidden="true"></span>Retard</li>
                        <li class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-red-500"
                                aria-hidden="true"></span>Absent</li>
                    </ul>
                </div>
                <ul class="px-5 sm:px-6 py-4 space-y-4">
                    @foreach ($presencesParMois as $mois)
                        @php $total = $mois['present'] + $mois['retard'] + $mois['absent']; @endphp
                        <li>
                            <div class="flex items-baseline justify-between gap-3 text-xs">
                                <span class="font-medium text-slate-900 capitalize">{{ $mois['mois'] }}</span>
                                <span class="flex flex-wrap justify-end gap-x-3 text-slate-600 tabular-nums">
                                    <span><span class="font-semibold text-slate-900">{{ $mois['present'] }}</span> présent</span>
                                    <span><span class="font-semibold text-slate-900">{{ $mois['retard'] }}</span> retard</span>
                                    <span><span class="font-semibold text-slate-900">{{ $mois['absent'] }}</span> absent</span>
                                </span>
                            </div>
                            <div class="mt-1.5 flex h-2.5 rounded-full overflow-hidden bg-brand-100" aria-hidden="true">
                                @if ($total > 0)
                                    <div class="bg-emerald-500" style="width:{{ round(($mois['present'] / $total) * 100) }}%"></div>
                                    <div class="bg-amber-500" style="width:{{ round(($mois['retard'] / $total) * 100) }}%"></div>
                                    <div class="bg-red-500" style="width:{{ round(($mois['absent'] / $total) * 100) }}%"></div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            <!-- Stagiaires par mentor -->
            <section class="{{ $panneau }}" aria-labelledby="par-mentor-titre">
                <div class="{{ $enTete }}">
                    <h3 id="par-mentor-titre" class="text-base font-semibold text-slate-900">Stagiaires par mentor</h3>
                </div>
                @php $maxStag = $stagiaresParMentor->max('stagiaires_count') ?: 1; @endphp
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @foreach ($stagiaresParMentor as $mentor)
                        <li class="flex items-center gap-3 py-3">
                            <div
                                class="h-8 w-8 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                <span class="text-xs font-semibold text-brand-800"
                                    aria-hidden="true">{{ strtoupper(substr($mentor->user->prenom, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline justify-between gap-3 text-sm">
                                    <span class="font-medium text-slate-900 truncate"
                                        title="{{ $mentor->user->nom_complet }}">{{ $mentor->user->nom_complet }}</span>
                                    <span class="font-semibold text-slate-900 tabular-nums flex-shrink-0">{{ $mentor->stagiaires_count }}
                                        <span class="font-normal text-xs text-slate-500">stagiaire(s)</span></span>
                                </div>
                                <div class="mt-1.5 h-1.5 rounded-full bg-brand-100 overflow-hidden" aria-hidden="true">
                                    <div class="h-1.5 rounded-full bg-accent-500"
                                        style="width:{{ round(($mentor->stagiaires_count / $maxStag) * 100) }}%"></div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                    @if ($stagiaresParMentor->isEmpty())
                        <li class="py-8 text-center">
                            <p class="text-sm font-medium text-slate-700">Aucun mentor</p>
                            <p class="mt-1 text-sm text-slate-500">La répartition apparaîtra dès qu'un mentor sera enregistré.</p>
                        </li>
                    @endif
                </ul>
            </section>
        </div>

        <!-- Résumé général des présences -->
        <section class="{{ $panneau }}" aria-labelledby="resume-presences-titre">
            @php
                $totalP = $stats['presences_total'] ?: 1;
                $effectives = $stats['presences_total'] - $stats['absences'] - $stats['retards'];
                $resume = [
                    [$effectives, 'Présences effectives', round(($effectives / $totalP) * 100), 'bg-emerald-500'],
                    [$stats['retards'], 'Retards enregistrés', round(($stats['retards'] / $totalP) * 100), 'bg-amber-500'],
                    [$stats['absences'], 'Absences enregistrées', round(($stats['absences'] / $totalP) * 100), 'bg-red-500'],
                ];
            @endphp
            <div class="{{ $enTete }}">
                <h3 id="resume-presences-titre" class="text-base font-semibold text-slate-900">Résumé général des présences</h3>
                <p class="text-xs text-slate-600 tabular-nums"><span
                        class="font-medium text-slate-900">{{ $stats['presences_total'] }}</span> pointage(s) au total</p>
            </div>
            <div class="p-5 sm:p-6">
                <div class="flex h-3 rounded-full overflow-hidden bg-brand-100" aria-hidden="true">
                    @if ($stats['presences_total'] > 0)
                        @foreach ($resume as [$val, $label, $part, $couleur])
                            <div class="{{ $couleur }}" style="width:{{ max(0, $part) }}%"></div>
                        @endforeach
                    @endif
                </div>
                <dl class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:divide-x divide-brand-100">
                    @foreach ($resume as [$val, $label, $part, $couleur])
                        <div class="sm:px-5 sm:first:pl-0 sm:last:pr-0 flex flex-col-reverse">
                            <dt class="mt-1 flex items-center gap-1.5 text-sm text-slate-600">
                                <span class="h-2 w-2 rounded-full {{ $couleur }}" aria-hidden="true"></span>{{ $label }}
                            </dt>
                            <dd class="flex items-baseline gap-2">
                                <span class="text-2xl font-semibold text-slate-900 tabular-nums">{{ $val }}</span>
                                <span class="text-sm font-medium text-slate-600 tabular-nums">{{ $part }} %</span>
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>
    </div>
@endsection
