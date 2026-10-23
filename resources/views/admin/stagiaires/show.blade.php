@extends('layouts.admin')
@section('titre', 'Fiche stagiaire')
@section('breadcrumb', 'Stagiaires > Détail')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $btnDanger = 'inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-50 transition-colors ' . $focus;
        $focusMarine = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-800';
        $panneau = 'bg-white border border-brand-100 rounded-xl overflow-hidden';
        $enTete = 'px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex items-center justify-between gap-3';
        $badge = 'text-xs font-medium px-2 py-0.5 rounded-md whitespace-nowrap flex-shrink-0';
        $neutre = 'bg-slate-100 text-slate-700';

        $statutsStagiaire = ['en_cours' => 'En cours', 'termine' => 'Terminé', 'suspendu' => 'Suspendu'];
        $statutsProjet = [
            'en_attente' => ['En attente', 'bg-amber-50 text-amber-800'],
            'en_cours' => ['En cours', 'bg-sky-50 text-sky-800'],
            'termine' => ['Terminé', 'bg-emerald-50 text-emerald-800'],
            'suspendu' => ['Suspendu', 'bg-red-50 text-red-800'],
        ];
        $statutsRapport = [
            'soumis' => ['Soumis', 'bg-amber-50 text-amber-800'],
            'valide' => ['Validé', 'bg-emerald-50 text-emerald-800'],
            'rejete' => ['Rejeté', 'bg-red-50 text-red-800'],
            'en_revision' => ['En révision', 'bg-sky-50 text-sky-800'],
        ];
        $statutsAttestation = [
            'en_attente' => ['En attente', 'bg-amber-50 text-amber-800'],
            'valide_mentor' => ['Validée par le mentor', 'bg-sky-50 text-sky-800'],
            'approuve_admin' => ['Approuvée', 'bg-sky-50 text-sky-800'],
            'envoye' => ['Envoyée', 'bg-emerald-50 text-emerald-800'],
            'refuse' => ['Refusée', 'bg-red-50 text-red-800'],
        ];

        $nbPresent = $stagiaire->presences->where('statut', 'present')->count();
        $nbAbsent = $stagiaire->presences->where('statut', 'absent')->count();
    @endphp

    <div class="max-w-5xl mx-auto space-y-5">

        <!-- Identité et stage (bandeau marine) -->
        <section class="rounded-xl bg-brand-800 text-white p-5 sm:p-6" aria-labelledby="stagiaire-nom">
            <div class="flex flex-col sm:flex-row sm:items-start gap-5">
                <div class="h-20 w-20 rounded-full bg-white/15 flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if ($stagiaire->user->photo)
                        <img src="{{ asset('storage/' . $stagiaire->user->photo) }}" alt=""
                            class="h-full w-full object-cover">
                    @else
                        <span class="text-2xl font-semibold text-white"
                            aria-hidden="true">{{ strtoupper(substr($stagiaire->user->prenom, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 id="stagiaire-nom" class="text-2xl font-semibold text-white tracking-tight break-words">
                            {{ $stagiaire->user->nom_complet }}</h2>
                        <span class="text-xs font-medium font-mono px-2 py-0.5 rounded-md bg-white/15 text-white">
                            {{ $stagiaire->matricule }}</span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-white/15 text-white">
                            {{ $statutsStagiaire[$stagiaire->statut] ?? ucfirst($stagiaire->statut) }}</span>
                    </div>
                    <p class="mt-1 text-sm text-brand-100 break-words">{{ $stagiaire->user->email }} ·
                        {{ $stagiaire->user->telephone ?: '—' }}</p>
                </div>
                <div class="grid grid-cols-1 xs:grid-cols-2 sm:flex sm:flex-col lg:flex-row gap-2 flex-shrink-0">
                    <a href="{{ route('admin.stagiaires.edit', $stagiaire) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-brand-800 hover:bg-brand-50 transition-colors whitespace-nowrap {{ $focusMarine }}">
                        <i class="fas fa-pen text-xs" aria-hidden="true"></i>Modifier
                    </a>
                    @if ($stagiaire->cv)
                        <a href="{{ asset('storage/' . $stagiaire->cv) }}" download
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/30 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/10 transition-colors whitespace-nowrap {{ $focusMarine }}">
                            <i class="fas fa-download text-xs text-brand-100" aria-hidden="true"></i>CV
                        </a>
                    @endif
                </div>
            </div>

            <dl class="mt-6 pt-5 border-t border-white/15 grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                <div class="min-w-0">
                    <dt class="text-xs text-brand-100">École</dt>
                    <dd class="mt-0.5 font-medium text-white break-words">{{ $stagiaire->ecole ?: '—' }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-brand-100">Spécialité</dt>
                    <dd class="mt-0.5 font-medium text-white break-words">{{ $stagiaire->specialite ?: '—' }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-brand-100">Mentor</dt>
                    <dd class="mt-0.5 font-medium text-white break-words">{{ $stagiaire->mentor?->nom_complet ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-brand-100">Durée de stage</dt>
                    <dd class="mt-0.5 font-medium text-white tabular-nums">{{ $stagiaire->dureeStageDays() }} jours</dd>
                </div>
                <div>
                    <dt class="text-xs text-brand-100">Début</dt>
                    <dd class="mt-0.5 font-medium text-white tabular-nums">{{ $stagiaire->date_debut->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-brand-100">Fin</dt>
                    <dd class="mt-0.5 font-medium text-white tabular-nums">{{ $stagiaire->date_fin->format('d/m/Y') }}
                        <span class="block text-xs font-normal text-brand-100">{{ $stagiaire->jours_restants }} j
                            restants</span>
                    </dd>
                </div>
            </dl>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">

            <!-- Présences -->
            <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6" aria-labelledby="presences-titre">
                <div class="flex items-center justify-between gap-3">
                    <h3 id="presences-titre" class="text-base font-semibold text-slate-900">Présences</h3>
                </div>
                <dl class="mt-4 grid grid-cols-2 divide-x divide-brand-100 rounded-lg bg-brand-50 border border-brand-100 p-4">
                    @foreach ([['Présences', $nbPresent, 'bg-emerald-500'], ['Absences', $nbAbsent, 'bg-red-500']] as [$label, $val, $dot])
                        <div class="px-4 first:pl-0 last:pr-0 flex flex-col-reverse">
                            <dt class="mt-1 flex items-center gap-1.5 text-xs text-slate-600">
                                <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>{{ $label }}
                            </dt>
                            <dd class="text-2xl font-semibold text-slate-900 tabular-nums">{{ $val }}
                                <span class="text-sm font-normal text-slate-600">jours</span></dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <!-- Tâches -->
            @php
                $total = $stagiaire->tasks->count();
                $terminees = $stagiaire->tasks->where('statut', 'termine')->count();
                $pct = $total > 0 ? round(($terminees / $total) * 100) : 0;
            @endphp
            <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6" aria-labelledby="taches-titre">
                <div class="flex items-baseline justify-between gap-3">
                    <h3 id="taches-titre" class="text-base font-semibold text-slate-900">Tâches
                        <span class="font-normal text-slate-500 tabular-nums">({{ $stagiaire->tasks->count() }})</span>
                    </h3>
                    <p class="text-lg font-semibold text-slate-900 tabular-nums">{{ $pct }} %</p>
                </div>
                <p class="mt-1 text-sm text-slate-500">Progression globale</p>
                <div class="mt-3 h-2 rounded-full bg-brand-100 overflow-hidden" role="progressbar"
                    aria-label="Progression globale des tâches" aria-valuemin="0" aria-valuemax="100"
                    aria-valuenow="{{ $pct }}">
                    <div class="h-2 rounded-full bg-accent-500" style="width:{{ $pct }}%"></div>
                </div>
                <dl class="mt-4 flex flex-wrap gap-x-6 gap-y-1 text-sm">
                    @foreach ([['À faire', $stagiaire->tasks->where('statut', 'a_faire')->count(), 'bg-slate-400'], ['En cours', $stagiaire->tasks->where('statut', 'en_cours')->count(), 'bg-amber-500'], ['Terminées', $terminees, 'bg-emerald-500']] as [$l, $v, $dot])
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                            <dt class="text-slate-500">{{ $l }}</dt>
                            <dd class="font-semibold text-slate-900 tabular-nums">{{ $v }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <!-- Projets -->
            <section class="{{ $panneau }}" aria-labelledby="projets-titre">
                <div class="{{ $enTete }}">
                    <h3 id="projets-titre" class="text-base font-semibold text-slate-900">Projets
                        <span class="font-normal text-slate-600 tabular-nums">({{ $stagiaire->projects->count() }})</span>
                    </h3>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @forelse($stagiaire->projects as $projet)
                        @php
                            $pctProjet = $projet->progressionPourcent();
                            [$libelle, $cls] = $statutsProjet[$projet->statut] ?? [ucfirst($projet->statut), $neutre];
                        @endphp
                        <li class="py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-900 truncate" title="{{ $projet->titre }}">
                                        {{ $projet->titre }}</p>
                                    <p class="text-xs text-slate-500 tabular-nums">{{ $pctProjet }}% terminé</p>
                                </div>
                                <span class="{{ $badge }} {{ $cls }}">{{ $libelle }}</span>
                            </div>
                            <div class="mt-1.5 h-1.5 rounded-full bg-brand-100 overflow-hidden" role="progressbar"
                                aria-label="Progression du projet {{ $projet->titre }}" aria-valuemin="0"
                                aria-valuemax="100" aria-valuenow="{{ $pctProjet }}">
                                <div class="h-1.5 rounded-full bg-accent-500" style="width:{{ $pctProjet }}%"></div>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center">
                            <p class="text-sm font-medium text-slate-700">Aucun projet assigné</p>
                            <p class="mt-1 text-sm text-slate-500">Les projets confiés par le mentor apparaîtront ici.</p>
                        </li>
                    @endforelse
                </ul>
            </section>

            <!-- Rapports -->
            <section class="{{ $panneau }}" aria-labelledby="rapports-titre">
                <div class="{{ $enTete }}">
                    <h3 id="rapports-titre" class="text-base font-semibold text-slate-900">Rapports
                        <span class="font-normal text-slate-600 tabular-nums">({{ $stagiaire->reports->count() }})</span>
                    </h3>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @forelse($stagiaire->reports->take(5) as $rapport)
                        @php [$libelle, $cls] = $statutsRapport[$rapport->statut] ?? [ucfirst($rapport->statut), $neutre]; @endphp
                        <li class="flex items-center gap-3 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate" title="{{ $rapport->titre }}">
                                    {{ $rapport->titre }}</p>
                                <p class="text-xs text-slate-500 tabular-nums">{{ $rapport->created_at->format('d/m/Y') }}</p>
                            </div>
                            <a href="{{ route('admin.presences.index', ['stagiaire_id' => $stagiaire->id]) }}"
                                class="flex-shrink-0 rounded-md hover:opacity-80 {{ $focus }}"
                                title="Statut : {{ $libelle }}">
                                <span class="{{ $badge }} {{ $cls }}">{{ $libelle }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="py-6 text-center">
                            <p class="text-sm font-medium text-slate-700">Aucun rapport</p>
                            <p class="mt-1 text-sm text-slate-500">Les rapports déposés par ce stagiaire apparaîtront ici.</p>
                        </li>
                    @endforelse
                </ul>
            </section>

            <!-- Attestations -->
            <section class="{{ $panneau }} lg:col-span-2" aria-labelledby="attestations-titre">
                <div class="{{ $enTete }}">
                    <h3 id="attestations-titre" class="text-base font-semibold text-slate-900">Attestations
                        <span class="font-normal text-slate-600 tabular-nums">({{ $stagiaire->attestations->count() }})</span>
                    </h3>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @forelse($stagiaire->attestations as $att)
                        @php [$libelle, $cls] = $statutsAttestation[$att->statut] ?? [ucfirst(str_replace('_', ' ', $att->statut)), $neutre]; @endphp
                        <li class="flex flex-wrap sm:flex-nowrap items-center gap-3 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 capitalize truncate">{{ $att->type }}</p>
                                <p class="text-xs text-slate-500 tabular-nums">{{ $att->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="{{ $badge }} {{ $cls }}">{{ $libelle }}</span>
                                @if ($att->fichier)
                                    <a href="{{ route('admin.attestations.telecharger', $att) }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-800 hover:border-brand-200 transition-colors {{ $focus }}"
                                        aria-label="Télécharger l'attestation {{ $att->type }}" title="Télécharger">
                                        <i class="fas fa-download text-xs" aria-hidden="true"></i>
                                    </a>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center">
                            <p class="text-sm font-medium text-slate-700">Aucune demande</p>
                            <p class="mt-1 text-sm text-slate-500">Les demandes d'attestation de ce stagiaire apparaîtront ici.</p>
                        </li>
                    @endforelse
                </ul>
            </section>
        </div>

        <!-- Navigation et action sensible -->
        <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-3">
            <a href="{{ route('admin.stagiaires.index') }}" class="inline-flex items-center gap-2 {{ $lien }}">
                <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour à la liste
            </a>
            <form action="{{ route('admin.stagiaires.destroy', $stagiaire) }}" method="POST">
                @csrf @method('DELETE')
                <button type="button" class="btn-delete w-full sm:w-auto {{ $btnDanger }}">
                    <i class="fas fa-trash text-xs" aria-hidden="true"></i>Supprimer le stagiaire
                </button>
            </form>
        </div>
    </div>
@endsection
