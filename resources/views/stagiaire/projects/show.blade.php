@extends('layouts.stagiaire')
@section('titre', $project->titre)
@section('breadcrumb', 'Projet > Détail')
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
        $statutsRapport = [
            'soumis' => ['Soumis', 'bg-amber-50 text-amber-800'],
            'valide' => ['Validé', 'bg-emerald-50 text-emerald-800'],
            'rejete' => ['Rejeté', 'bg-red-50 text-red-800'],
            'en_revision' => ['En révision', 'bg-sky-50 text-sky-800'],
        ];
        [$libStatut, $clsStatut] = $statutsProjet[$project->statut] ?? [ucfirst(str_replace('_', ' ', $project->statut)), 'bg-slate-100 text-slate-700'];
        [$libPriorite, $dotPriorite] = $priorites[$project->priorite] ?? [ucfirst((string) $project->priorite), 'bg-slate-300'];
        $pct = $project->progressionPourcent();

        // Colonnes de tâches : statut, liste, libellé, pastille
        $colonnes = [
            ['a_faire', $taches['a_faire'], 'À faire', 'bg-slate-400'],
            ['en_cours', $taches['en_cours'], 'En cours', 'bg-amber-500'],
            ['termine', $taches['termine'], 'Terminé', 'bg-emerald-500'],
        ];

        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $focusCarte = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 focus-visible:ring-offset-brand-50';
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- En-tête du projet (bandeau marine) -->
        <section class="rounded-xl bg-brand-800 text-white p-5 sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <h2 class="text-lg font-semibold text-white leading-snug min-w-0 break-words">{{ $project->titre }}</h2>
                <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 bg-white/15 text-white">{{ $libStatut }}</span>
            </div>
            @if ($project->description)
                <p class="mt-1.5 text-sm text-brand-100 max-w-3xl break-words">{{ $project->description }}</p>
            @endif

            <dl class="mt-5 pt-5 border-t border-white/15 grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs text-brand-100">Mentor</dt>
                    <dd class="mt-0.5 font-medium text-white">{{ $project->mentor->nom_complet ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-brand-100">Priorité</dt>
                    <dd class="mt-0.5 flex items-center gap-2 font-medium text-white">
                        <span class="h-2 w-2 rounded-full {{ $dotPriorite }}" aria-hidden="true"></span>
                        {{ $libPriorite }}
                    </dd>
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <dt class="text-xs text-brand-100">Période</dt>
                    <dd class="mt-0.5 font-medium text-white tabular-nums">
                        {{ $project->date_debut->format('d/m/Y') }} →
                        {{ $project->date_fin?->format('d/m/Y') ?? 'Indéfini' }}
                    </dd>
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <dt class="flex justify-between text-xs text-brand-100">
                        <span>Progression</span>
                        <span class="font-semibold text-white tabular-nums">{{ $pct }} % terminé</span>
                    </dt>
                    <dd class="mt-2">
                        <div class="h-2 rounded-full bg-white/15 overflow-hidden" role="progressbar"
                            aria-label="Progression du projet {{ $project->titre }}" aria-valuemin="0"
                            aria-valuemax="100" aria-valuenow="{{ $pct }}">
                            <div class="h-2 rounded-full bg-accent-500" style="width:{{ $pct }}%"></div>
                        </div>
                    </dd>
                </div>
            </dl>
        </section>

        <!-- Mes tâches dans ce projet -->
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <h3 class="text-base font-semibold text-slate-900">Mes tâches dans ce projet</h3>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($colonnes as [$statut, $liste, $label, $dot])
                    <div class="min-w-0 rounded-lg bg-brand-50 border border-brand-100 p-3">
                        <div class="flex items-center justify-between gap-2 px-1 pb-2 border-b border-brand-100">
                            <h4 class="flex items-center gap-2 text-sm font-medium text-slate-900">
                                <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                                {{ $label }}
                            </h4>
                            <span class="text-xs font-medium text-slate-600 tabular-nums">{{ $liste->count() }}</span>
                        </div>
                        <ul class="mt-2 space-y-2">
                            @forelse($liste as $tache)
                                <li>
                                    <a href="{{ route('stagiaire.tasks.show', $tache) }}"
                                        class="block rounded-lg border border-brand-100 bg-white p-3 hover:border-brand-200 transition-colors {{ $focusCarte }}">
                                        <p class="text-sm font-medium text-slate-900 break-words">{{ $tache->titre }}</p>
                                        @if ($tache->date_echeance)
                                            @php $enRetard = $tache->date_echeance->isPast() && $statut !== 'termine'; @endphp
                                            <p class="mt-1 text-xs tabular-nums {{ $enRetard ? 'text-red-700 font-medium' : 'text-slate-500' }}">
                                                Échéance : {{ $tache->date_echeance->format('d/m/Y') }}{{ $enRetard ? ' (en retard)' : '' }}
                                            </p>
                                        @endif
                                    </a>
                                </li>
                            @empty
                                <li class="px-1 py-3 text-sm text-slate-600">Aucune tâche</li>
                            @endforelse
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Rapports liés -->
        @php $rapportsLies = $project->reports->where('stagiaire_id', Auth::user()->stagiaire->id ?? 0); @endphp
        @if ($rapportsLies->count() > 0)
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
                <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex items-center justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900">Mes rapports liés</h3>
                    <a href="{{ route('stagiaire.reports.create') }}" class="{{ $lien }}">+ Rapport</a>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @foreach ($rapportsLies as $rapport)
                        @php [$libelle, $cls] = $statutsRapport[$rapport->statut] ?? [ucfirst($rapport->statut), 'bg-slate-100 text-slate-700']; @endphp
                        <li class="flex items-center gap-3 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $rapport->titre }}</p>
                                <p class="text-xs text-slate-500 tabular-nums">{{ $rapport->created_at->format('d/m/Y') }}</p>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $cls }}">{{ $libelle }}</span>
                            <a href="{{ route('stagiaire.reports.telecharger', $rapport) }}" download
                                aria-label="Télécharger le rapport {{ $rapport->titre }}"
                                class="{{ $btnSecondaire }} flex-shrink-0">
                                <i class="fas fa-download text-xs" aria-hidden="true"></i><span class="hidden sm:inline">Télécharger</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <a href="{{ route('stagiaire.projects.index') }}" class="inline-flex items-center gap-2 {{ $lien }}">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour
        </a>
    </div>
@endsection
