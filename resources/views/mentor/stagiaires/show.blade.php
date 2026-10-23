@extends('layouts.mentor')
@section('titre', 'Profil Stagiaire')
@section('breadcrumb', 'Stagiaires > Profil')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $focusMarine = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-800';
        $panneau = 'bg-white border border-brand-100 rounded-xl overflow-hidden';
        $enTete = 'px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex items-center justify-between gap-3';

        $statutsStagiaire = ['en_cours' => 'En cours', 'termine' => 'Terminé', 'suspendu' => 'Suspendu'];
        $statutsRapport = [
            'soumis' => ['Soumis', 'bg-amber-50 text-amber-800'],
            'valide' => ['Validé', 'bg-emerald-50 text-emerald-800'],
            'rejete' => ['Rejeté', 'bg-red-50 text-red-800'],
            'en_revision' => ['En révision', 'bg-sky-50 text-sky-800'],
        ];
    @endphp

    <div class="max-w-5xl mx-auto space-y-5">

        <!-- Identité et stage (bandeau marine) -->
        <section class="rounded-xl bg-brand-800 text-white p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start gap-5">
                <div
                    class="h-20 w-20 rounded-full bg-white/15 flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if ($stagiaire->user->photo)
                        <img src="{{ asset('storage/' . $stagiaire->user->photo) }}" alt=""
                            class="h-full w-full object-cover" loading="lazy">
                    @else
                        <span class="text-2xl font-semibold text-white"
                            aria-hidden="true">{{ strtoupper(substr($stagiaire->user->prenom, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-2xl font-semibold text-white tracking-tight break-words">
                            {{ $stagiaire->user->nom_complet }}</h2>
                        <span class="text-xs font-medium font-mono px-2 py-0.5 rounded-md bg-white/15 text-white">
                            {{ $stagiaire->matricule }}</span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-white/15 text-white">
                            {{ $statutsStagiaire[$stagiaire->statut] ?? ucfirst($stagiaire->statut) }}</span>
                    </div>
                    <p class="mt-1 text-sm text-brand-100 break-words">{{ $stagiaire->user->email }} ·
                        {{ $stagiaire->user->telephone ?: '—' }}</p>
                </div>
                @if ($stagiaire->cv)
                    <a href="{{ asset('storage/' . $stagiaire->cv) }}" download
                        class="w-full sm:w-auto flex-shrink-0 inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-brand-800 hover:bg-brand-50 transition-colors {{ $focusMarine }}">
                        <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger le CV
                    </a>
                @endif
            </div>

            <dl class="mt-6 pt-5 border-t border-white/15 grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4 text-sm">
                <div class="min-w-0">
                    <dt class="text-xs text-brand-100">École</dt>
                    <dd class="mt-0.5 font-medium text-white break-words">{{ $stagiaire->ecole ?: '—' }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-brand-100">Spécialité</dt>
                    <dd class="mt-0.5 font-medium text-white break-words">{{ $stagiaire->specialite ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-brand-100">Début</dt>
                    <dd class="mt-0.5 font-medium text-white tabular-nums">{{ $stagiaire->date_debut->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-brand-100">Fin ({{ $stagiaire->dureeStageDays() }} j)</dt>
                    <dd class="mt-0.5 font-medium text-white tabular-nums">{{ $stagiaire->date_fin->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">

            <!-- Suivi : présence et rapports -->
            <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
                <h3 class="text-base font-semibold text-slate-900">Suivi</h3>
                <dl
                    class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-y-4 sm:divide-x divide-brand-100 rounded-lg bg-brand-50 border border-brand-100 p-4">
                    @foreach ([['Présences', $stats['present'], 'bg-emerald-500'], ['Retards', $stats['retard'], 'bg-amber-500'], ['Absences', $stats['absent'], 'bg-red-500'], ['Rapports', $stats['rapports'], 'bg-slate-400']] as [$label, $val, $dot])
                        <div class="px-3 sm:first:pl-0 sm:last:pr-0">
                            <dd class="text-2xl font-semibold text-slate-900 tabular-nums">{{ $val }}</dd>
                            <dt class="mt-1 flex items-center gap-1.5 text-xs text-slate-600">
                                <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                                {{ $label }}
                            </dt>
                        </div>
                    @endforeach
                </dl>
            </section>

            <!-- Progression des tâches -->
            <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
                <div class="flex items-baseline justify-between gap-3">
                    <h3 class="text-base font-semibold text-slate-900">Progression des tâches</h3>
                    <p class="text-lg font-semibold text-slate-900 tabular-nums">{{ $progression }} %</p>
                </div>
                <p class="mt-1 text-sm text-slate-500 tabular-nums">
                    {{ $stats['taches_terminees'] }} sur {{ $stats['taches_total'] }} tâche(s) terminée(s)</p>
                <div class="mt-3 h-2 rounded-full bg-slate-100 overflow-hidden" role="progressbar"
                    aria-label="Progression des tâches" aria-valuemin="0" aria-valuemax="100"
                    aria-valuenow="{{ $progression }}">
                    <div class="h-2 rounded-full bg-accent-500 transition-[width] duration-700 ease-out motion-reduce:transition-none"
                        style="width:{{ $progression }}%"></div>
                </div>
                <dl class="mt-4 flex flex-wrap gap-x-6 gap-y-1 text-sm">
                    @foreach ([['a_faire', 'À faire', 'bg-slate-400'], ['en_cours', 'En cours', 'bg-amber-500'], ['termine', 'Terminé', 'bg-emerald-500']] as [$s, $l, $dot])
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                            <dt class="text-slate-500">{{ $l }}</dt>
                            <dd class="font-semibold text-slate-900 tabular-nums">
                                {{ $stagiaire->tasks->where('statut', $s)->count() }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <!-- Projets -->
            <section class="{{ $panneau }}">
                <div class="{{ $enTete }}">
                    <h3 class="text-base font-semibold text-slate-900">Projets
                        <span class="font-normal text-slate-600 tabular-nums">({{ $stagiaire->projects->count() }})</span>
                    </h3>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @forelse($stagiaire->projects as $p)
                        <li class="py-3">
                            <div class="flex items-baseline justify-between gap-3">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $p->titre }}</p>
                                <p class="text-xs font-medium text-slate-900 tabular-nums flex-shrink-0">
                                    {{ $p->progressionPourcent() }} %</p>
                            </div>
                            <div class="mt-1.5 h-1.5 rounded-full bg-slate-100 overflow-hidden" role="progressbar"
                                aria-label="Progression du projet {{ $p->titre }}" aria-valuemin="0" aria-valuemax="100"
                                aria-valuenow="{{ $p->progressionPourcent() }}">
                                <div class="h-1.5 rounded-full bg-accent-500" style="width:{{ $p->progressionPourcent() }}%">
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center">
                            <p class="text-sm font-medium text-slate-700">Aucun projet</p>
                            <p class="mt-1 text-sm text-slate-500">Ce stagiaire n'est rattaché à aucun projet pour le moment.</p>
                        </li>
                    @endforelse
                </ul>
            </section>

            <!-- Rapports -->
            <section class="{{ $panneau }}">
                <div class="{{ $enTete }}">
                    <h3 class="text-base font-semibold text-slate-900">Rapports
                        <span class="font-normal text-slate-600 tabular-nums">({{ $stats['rapports'] }})</span>
                    </h3>
                    <a href="{{ route('mentor.reports.index') }}" class="flex-shrink-0 {{ $lien }}">Voir tout →</a>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @forelse($stagiaire->reports->take(5) as $rapport)
                        @php [$libelle, $cls] = $statutsRapport[$rapport->statut] ?? [ucfirst($rapport->statut), 'bg-slate-100 text-slate-700']; @endphp
                        <li class="flex items-center gap-3 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ $rapport->titre }}</p>
                                <p class="text-xs text-slate-500 tabular-nums">{{ $rapport->created_at->format('d/m/Y') }}</p>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $cls }}">{{ $libelle }}</span>
                        </li>
                    @empty
                        <li class="py-6 text-center">
                            <p class="text-sm font-medium text-slate-700">Aucun rapport</p>
                            <p class="mt-1 text-sm text-slate-500">Les rapports déposés par ce stagiaire apparaîtront ici.</p>
                        </li>
                    @endforelse
                </ul>
            </section>
        </div>

        <div>
            <a href="{{ route('mentor.stagiaires.index') }}" class="inline-flex items-center gap-2 {{ $lien }}">
                <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour à mes stagiaires
            </a>
        </div>
    </div>
@endsection
