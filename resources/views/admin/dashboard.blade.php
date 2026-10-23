@extends('layouts.admin')
@section('titre', 'Tableau de bord')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        // Variantes sur le bandeau marine
        $focusMarine = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-800';
        $panneau = 'bg-white border border-brand-100 rounded-xl overflow-hidden';
        $enTete = 'px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex items-center justify-between gap-3';

        // Chiffres clés du bandeau
        $chiffres = [
            ['Utilisateurs', $stats['total_users']],
            ['Stagiaires actifs', $stats['stagiaires_actifs']],
            ['Mentors', $stats['total_mentors']],
            ['Projets', $stats['total_projets']],
        ];

        // Indicateurs de suivi : [libellé, valeur, icône, pastille, précision]
        $indicateurs = [
            ['Rapports à valider', $stats['rapports_en_attente'], 'file-alt', 'bg-brand-50 text-brand-700', 'Rapports au statut « soumis »'],
            ['Attestations à traiter', $stats['attestations_en_attente'], 'certificate', 'bg-brand-50 text-brand-700', 'Validées par le mentor'],
            ["Présences aujourd'hui", $stats['presences_aujourd_hui'], 'user-check', 'bg-accent-50 text-accent-700', 'Pointages du jour'],
            ['Total stagiaires', $stats['total_stagiaires'], 'graduation-cap', 'bg-accent-50 text-accent-700', 'Tous statuts confondus'],
        ];

        $statutsStagiaire = [
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
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Accueil et chiffres clés (bandeau marine) -->
        <section class="rounded-xl bg-brand-800 text-white p-5 sm:p-6" aria-labelledby="accueil-titre">
            <h2 id="accueil-titre" class="text-2xl font-semibold text-white tracking-tight">Bonjour, {{ Auth::user()->prenom }}</h2>
            <p class="mt-1 text-sm text-brand-100">Vue d'ensemble de la plateforme DOCOS.</p>

            <dl class="mt-5 grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4">
                @foreach ($chiffres as [$label, $val])
                    <div class="min-w-0 flex flex-col-reverse">
                        <dt class="mt-0.5 text-xs text-brand-100">{{ $label }}</dt>
                        <dd class="text-2xl font-semibold text-white tabular-nums">{{ $val }}</dd>
                    </div>
                @endforeach
            </dl>

            <!-- Raccourci pour la création des modules -->
            <div class="mt-6 pt-5 border-t border-white/15">
                <p class="text-xs font-medium text-brand-100">Créer rapidement</p>
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap gap-2">
                    <a href="{{ route('admin.stagiaires.create') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-brand-800 hover:bg-brand-50 transition-colors whitespace-nowrap {{ $focusMarine }}">
                        <i class="fas fa-user-graduate text-xs" aria-hidden="true"></i>Ajouter stagiaire
                    </a>
                    @foreach ([[route('admin.users.create'), 'user-plus', 'Ajouter utilisateur'], [route('admin.evenements.create'), 'bullhorn', 'Créer événement'], [route('admin.archives.create'), 'archive', 'Créer archive']] as [$route, $icon, $label])
                        <a href="{{ $route }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/30 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/10 transition-colors whitespace-nowrap {{ $focusMarine }}">
                            <i class="fas fa-{{ $icon }} text-xs text-brand-100" aria-hidden="true"></i>{{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Indicateurs de suivi -->
        <section aria-labelledby="suivi-titre">
            <h2 id="suivi-titre" class="sr-only">Suivi de l'activité</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach ($indicateurs as [$label, $val, $icon, $pastille, $precision])
                    <div class="bg-white border border-brand-100 rounded-xl p-4 sm:p-5 flex items-start gap-3">
                        <span class="h-9 w-9 rounded-lg flex items-center justify-center flex-shrink-0 {{ $pastille }}"
                            aria-hidden="true"><i class="fas fa-{{ $icon }} text-sm"></i></span>
                        <div class="min-w-0 flex flex-col-reverse">
                            <dt class="mt-0.5">
                                <span class="block text-sm font-medium text-slate-700">{{ $label }}</span>
                                <span class="block text-xs text-slate-500">{{ $precision }}</span>
                            </dt>
                            <dd class="text-2xl font-semibold text-slate-900 tabular-nums">{{ $val }}</dd>
                        </div>
                    </div>
                @endforeach
            </dl>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">

            <!-- Stagiaires récents -->
            <section class="{{ $panneau }}" aria-labelledby="stagiaires-recents-titre">
                <div class="{{ $enTete }}">
                    <h3 id="stagiaires-recents-titre" class="text-base font-semibold text-slate-900">Stagiaires récents</h3>
                    <a href="{{ route('admin.stagiaires.index') }}" class="flex-shrink-0 whitespace-nowrap {{ $lien }}">Voir
                        tous →</a>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @forelse($stagiairesRecents as $stag)
                        @php [$libelle, $cls] = $statutsStagiaire[$stag->statut] ?? [ucfirst($stag->statut), 'bg-slate-100 text-slate-700']; @endphp
                        <li class="flex items-center gap-3 py-3">
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
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate" title="{{ $stag->user->nom_complet }}">
                                    {{ $stag->user->nom_complet }}</p>
                                <p class="text-xs text-slate-500 truncate" title="{{ $stag->ecole }}">
                                    <span class="font-mono">{{ $stag->matricule }}</span> · {{ $stag->ecole ?: '—' }}</p>
                            </div>
                            <span
                                class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 whitespace-nowrap {{ $cls }}">{{ $libelle }}</span>
                        </li>
                    @empty
                        <li class="py-8 text-center">
                            <p class="text-sm font-medium text-slate-700">Aucun stagiaire</p>
                            <p class="mt-1 text-sm text-slate-500">Les derniers stagiaires enregistrés apparaîtront ici.</p>
                            <a href="{{ route('admin.stagiaires.create') }}" class="mt-2 inline-block {{ $lien }}">Ajouter un
                                stagiaire</a>
                        </li>
                    @endforelse
                </ul>
            </section>

            <!-- Rapports soumis -->
            <section class="{{ $panneau }}" aria-labelledby="rapports-recents-titre">
                <div class="{{ $enTete }}">
                    <h3 id="rapports-recents-titre" class="text-base font-semibold text-slate-900">Rapports soumis</h3>
                    <a href="{{ route('admin.statistics.index') }}" class="flex-shrink-0 whitespace-nowrap {{ $lien }}">Autres
                        →</a>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @forelse($rapportsRecents as $rapport)
                        @php [$libelle, $cls] = $statutsRapport[$rapport->statut] ?? [ucfirst((string) $rapport->statut), 'bg-slate-100 text-slate-700']; @endphp
                        <li class="flex items-center gap-3 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate" title="{{ $rapport->titre }}">
                                    {{ $rapport->titre }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $rapport->stagiaire->user->nom_complet ?? '—' }}</p>
                            </div>
                            <span
                                class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 whitespace-nowrap {{ $cls }}">{{ $libelle }}</span>
                        </li>
                    @empty
                        <li class="py-8 text-center">
                            <p class="text-sm font-medium text-slate-700">Aucun rapport</p>
                            <p class="mt-1 text-sm text-slate-500">Les rapports déposés par les stagiaires apparaîtront ici.</p>
                        </li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
@endsection
