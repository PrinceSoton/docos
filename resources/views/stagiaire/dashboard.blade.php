@extends('layouts.stagiaire')
@section('titre', 'Mon Tableau de bord')
@section('content')
    @php
        // Avancement du stage, calculé en jours calendaires (0 % avant le début, 100 % au dernier jour et après)
        $jourDebut = $stagiaire->date_debut->copy()->startOfDay();
        $jourFin = $stagiaire->date_fin->copy()->startOfDay();
        $aujourdhui = now()->startOfDay();
        $total = (int) $jourDebut->diffInDays($jourFin, false);
        $joursAvantDebut = (int) $aujourdhui->diffInDays($jourDebut, false); // > 0 : le stage n'a pas commencé
        $joursAvantFin = (int) $aujourdhui->diffInDays($jourFin, false); // 0 : dernier jour, < 0 : stage terminé

        if ($joursAvantDebut > 0) {
            $pct = 0;
        } elseif ($joursAvantFin <= 0) {
            $pct = 100;
        } else {
            $pct = (int) round(((int) $jourDebut->diffInDays($aujourdhui, false) / $total) * 100);
        }

        // Tâches
        $totalT = $stats['a_faire'] + $stats['en_cours'] + $stats['terminees'];

        // Libellés et couleurs de statut
        $statutsTache = [
            'a_faire' => ['À faire', 'bg-slate-400'],
            'en_cours' => ['En cours', 'bg-amber-500'],
            'termine' => ['Terminée', 'bg-emerald-500'],
        ];
        $statutsRapport = [
            'soumis' => ['Soumis', 'bg-amber-50 text-amber-800'],
            'valide' => ['Validé', 'bg-emerald-50 text-emerald-800'],
            'rejete' => ['Rejeté', 'bg-red-50 text-red-800'],
            'en_revision' => ['En révision', 'bg-sky-50 text-sky-800'],
        ];

        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        // Variantes sur le bandeau marine
        $focusMarine = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-800';
        $panneau = 'bg-white border border-brand-100 rounded-xl overflow-hidden';
        $enTete = 'px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex items-center justify-between gap-3';
    @endphp

    <div class="space-y-5 max-w-6xl mx-auto">

        <!-- Identité, mentor et avancement du stage (bandeau marine) -->
        <section class="rounded-xl bg-brand-800 text-white p-5 sm:p-6">
            <div class="grid gap-6 lg:grid-cols-2 lg:gap-12">
                <div>
                    <h2 class="text-2xl font-semibold text-white tracking-tight">
                        Bonjour, {{ Auth::user()->prenom }}
                    </h2>
                    <dl class="mt-4 grid grid-cols-[auto_1fr] gap-x-6 gap-y-1.5 text-sm">
                        <dt class="text-brand-100">Matricule</dt>
                        <dd class="font-mono font-medium text-white">{{ $stagiaire->matricule }}</dd>
                        <dt class="text-brand-100">Mentor</dt>
                        @if ($stagiaire->mentor)
                            <dd class="font-medium text-white">{{ $stagiaire->mentor->nom_complet }}</dd>
                        @else
                            <dd class="text-brand-100">Pas encore assigné</dd>
                        @endif
                    </dl>
                </div>

                <div>
                    <div class="flex items-baseline justify-between gap-3">
                        <p class="text-sm text-brand-100">Avancement du stage</p>
                        <p class="text-sm font-medium text-white tabular-nums">{{ $pct }} % effectué</p>
                    </div>
                    <p class="mt-1 text-lg font-semibold text-white tabular-nums">
                        @if ($joursAvantDebut > 0)
                            Débute dans {{ $joursAvantDebut }} {{ $joursAvantDebut > 1 ? 'jours' : 'jour' }}
                        @elseif ($joursAvantFin > 0)
                            {{ $joursAvantFin }} {{ $joursAvantFin > 1 ? 'jours restants' : 'jour restant' }}
                        @elseif ($joursAvantFin === 0)
                            Dernier jour
                        @else
                            Stage terminé
                        @endif
                    </p>
                    <div class="mt-3 h-2 rounded-full bg-white/15 overflow-hidden" role="progressbar"
                        aria-label="Avancement du stage" aria-valuemin="0" aria-valuemax="100"
                        aria-valuenow="{{ $pct }}">
                        <div class="h-2 rounded-full bg-accent-500 transition-[width] duration-700 ease-out motion-reduce:transition-none"
                            style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="mt-2 flex justify-between text-xs text-brand-100 tabular-nums">
                        <span>Début : {{ $stagiaire->date_debut->format('d/m/Y') }}</span>
                        <span>Fin : {{ $stagiaire->date_fin->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Raccourcis -->
            <div class="mt-6 pt-5 border-t border-white/15 grid grid-cols-1 sm:flex sm:flex-wrap gap-2">
                <a href="{{ route('stagiaire.presence.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-brand-800 hover:bg-brand-50 transition-colors {{ $focusMarine }}">
                    <i class="fas fa-user-check text-xs" aria-hidden="true"></i>Marquer ma présence
                </a>
                @foreach ([[route('stagiaire.reports.create'), 'file-upload', 'Déposer un rapport'], [route('stagiaire.attestations.request'), 'certificate', 'Demander une attestation']] as [$route, $icon, $label])
                    <a href="{{ $route }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/30 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/10 transition-colors {{ $focusMarine }}">
                        <i class="fas fa-{{ $icon }} text-xs text-brand-100" aria-hidden="true"></i>{{ $label }}
                    </a>
                @endforeach
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

            <!-- Tâches : synthèse puis dernières tâches -->
            <section class="{{ $panneau }} lg:col-span-2">
                <div class="{{ $enTete }}">
                    <h3 class="text-base font-semibold text-slate-900">Mes tâches</h3>
                    <a href="{{ route('stagiaire.tasks.index') }}" class="{{ $lien }}">Voir tout →</a>
                </div>

                <div class="px-5 sm:px-6 py-4">
                @if ($totalT > 0)
                    <div class="mt-1">
                        <div class="flex items-baseline justify-between gap-3 text-sm">
                            <p class="text-slate-600 tabular-nums">
                                <span class="font-semibold text-slate-900">{{ $stats['terminees'] }}</span>
                                sur {{ $totalT }} {{ $totalT > 1 ? 'tâches terminées' : 'tâche terminée' }}
                            </p>
                            <p class="font-medium text-slate-900 tabular-nums">{{ $progression }} %</p>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-100 overflow-hidden" role="progressbar"
                            aria-label="Progression des tâches" aria-valuemin="0" aria-valuemax="100"
                            aria-valuenow="{{ $progression }}">
                            <div class="h-2 rounded-full bg-accent-500 transition-[width] duration-700 ease-out motion-reduce:transition-none"
                                style="width:{{ $progression }}%"></div>
                        </div>
                        <dl class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-sm">
                            @foreach ([['À faire', $stats['a_faire'], 'bg-slate-400'], ['En cours', $stats['en_cours'], 'bg-amber-500'], ['Terminées', $stats['terminees'], 'bg-emerald-500']] as [$label, $val, $dot])
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                                    <dt class="text-slate-500">{{ $label }}</dt>
                                    <dd class="font-semibold text-slate-900 tabular-nums">{{ $val }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif

                @if (count($tachesRecentes) > 0)
                    <h4 class="mt-6 mb-1 text-sm font-medium text-slate-500">Dernières tâches assignées</h4>
                @endif
                <ul class="divide-y divide-slate-100">
                    @forelse($tachesRecentes as $tache)
                        @php [$libelle, $dot] = $statutsTache[$tache->statut] ?? [$tache->statut, 'bg-slate-300']; @endphp
                        <li class="flex items-center gap-4 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $tache->titre }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $tache->project->titre ?? '—' }}</p>
                            </div>
                            <span class="flex items-center gap-2 text-xs text-slate-600 flex-shrink-0">
                                <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                                {{ $libelle }}
                            </span>
                        </li>
                    @empty
                        <li class="py-8 text-center">
                            <p class="text-sm font-medium text-slate-700">Aucune tâche pour le moment</p>
                            <p class="mt-1 text-sm text-slate-500">Les tâches que votre mentor vous assigne apparaîtront
                                ici.</p>
                        </li>
                    @endforelse
                </ul>
                </div>
            </section>

            <!-- Colonne de droite : présence et rapports -->
            <div class="space-y-5">

            <!-- Présence : synthèse dans un bloc teinté -->
            <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
                <h3 class="text-base font-semibold text-slate-900">Ma présence</h3>
                <dl class="mt-4 grid grid-cols-3 divide-x divide-brand-100 rounded-lg bg-brand-50 border border-brand-100 p-4">
                    @foreach ([['Présences', $stats['present'], 'bg-emerald-500'], ['Retards', $stats['retard'], 'bg-amber-500'], ['Absences', $stats['absent'], 'bg-red-500']] as [$label, $val, $dot])
                        <div class="px-3 first:pl-0 last:pr-0">
                            <dd class="text-2xl font-semibold text-slate-900 tabular-nums">{{ $val }}</dd>
                            <dt class="mt-1 flex items-center gap-1.5 text-xs text-slate-600">
                                <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                                {{ $label }}
                            </dt>
                        </div>
                    @endforeach
                </dl>
            </section>

            <!-- Rapports -->
            <section class="{{ $panneau }}">
                <div class="{{ $enTete }}">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-slate-900">Mes rapports</h3>
                        <p class="text-xs text-slate-600 tabular-nums">
                            {{ $stats['rapports'] }} {{ $stats['rapports'] > 1 ? 'rapports déposés' : 'rapport déposé' }}
                        </p>
                    </div>
                    <a href="{{ route('stagiaire.reports.index') }}" class="flex-shrink-0 {{ $lien }}">Voir tout →</a>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @forelse($rapportsRecents as $rapport)
                        @php [$libelle, $cls] = $statutsRapport[$rapport->statut] ?? [ucfirst($rapport->statut), 'bg-slate-100 text-slate-700']; @endphp
                        <li class="flex items-center gap-3 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $rapport->titre }}</p>
                                <p class="text-xs text-slate-500 tabular-nums">{{ $rapport->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $cls }}">
                                {{ $libelle }}
                            </span>
                        </li>
                    @empty
                        <li class="py-6 text-center">
                            <p class="text-sm font-medium text-slate-700">Aucun rapport déposé</p>
                            <a href="{{ route('stagiaire.reports.create') }}" class="mt-1 inline-block {{ $lien }}">Déposer
                                mon premier rapport</a>
                        </li>
                    @endforelse
                </ul>
            </section>
            </div>
        </div>
    </div>
@endsection
