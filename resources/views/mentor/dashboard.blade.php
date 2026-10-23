@extends('layouts.mentor')
@section('titre', 'Tableau de bord Mentor')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $btnPetitPrimaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg bg-brand-800 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnPetitDanger = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50 transition-colors ' . $focus;
        $btnPetitSecondaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        // Variantes sur le bandeau marine
        $focusMarine = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-800';
        $panneau = 'bg-white border border-brand-100 rounded-xl overflow-hidden';
        $enTete = 'px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex items-center justify-between gap-3';

        $chiffres = [
            ['Stagiaires', $stats['stagiaires']],
            ['Projets', $stats['projets']],
            ['Rapports à évaluer', $stats['rapports_soumis']],
            ['Permissions en attente', $stats['permissions_en_attente']],
            ['Attestations à valider', $stats['attestations_a_valider']],
        ];
    @endphp

    <div class="space-y-5 max-w-6xl mx-auto">

        <!-- Accueil et chiffres clés (bandeau marine) -->
        <section class="rounded-xl bg-brand-800 text-white p-5 sm:p-6">
            <h2 class="text-2xl font-semibold text-white tracking-tight">Bonjour, {{ Auth::user()->prenom }}</h2>
            <p class="mt-1 text-sm text-brand-100">
                Vous encadrez <span class="font-medium text-white tabular-nums">{{ $stats['stagiaires'] }}</span>
                stagiaire(s) actuellement.
            </p>

            <dl class="mt-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-x-6 gap-y-4">
                @foreach ($chiffres as [$label, $val])
                    <div class="min-w-0">
                        <dd class="text-2xl font-semibold text-white tabular-nums">{{ $val }}</dd>
                        <dt class="mt-0.5 text-xs text-brand-100">{{ $label }}</dt>
                    </div>
                @endforeach
            </dl>

            <!-- Raccourcis -->
            <div class="mt-6 pt-5 border-t border-white/15 grid grid-cols-1 sm:flex sm:flex-wrap gap-2">
                <a href="{{ route('mentor.projects.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-brand-800 hover:bg-brand-50 transition-colors {{ $focusMarine }}">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>Créer un projet
                </a>
                @foreach ([[route('mentor.tasks.create'), 'tasks', 'Créer une tâche'], [route('mentor.reports.index'), 'file-alt', 'Voir les rapports'], [route('mentor.stagiaires.index'), 'user-graduate', 'Mes stagiaires']] as [$route, $icon, $label])
                    <a href="{{ $route }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/30 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/10 transition-colors {{ $focusMarine }}">
                        <i class="fas fa-{{ $icon }} text-xs text-brand-100" aria-hidden="true"></i>{{ $label }}
                    </a>
                @endforeach
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

            <!-- Avancement des stagiaires -->
            <section class="{{ $panneau }} lg:col-span-2">
                <div class="{{ $enTete }}">
                    <h3 class="text-base font-semibold text-slate-900">Avancement de mes stagiaires</h3>
                    <a href="{{ route('mentor.stagiaires.index') }}" class="flex-shrink-0 {{ $lien }}">Voir tout →</a>
                </div>
                <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                    @forelse($avancementStagiaires as $stag)
                        <li class="py-4">
                            <div class="flex items-center gap-3">
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
                                    <div class="flex items-baseline justify-between gap-3">
                                        <p class="text-sm font-medium text-slate-900 truncate">{{ $stag->user->nom_complet }}</p>
                                        <p class="text-sm font-medium text-slate-900 tabular-nums flex-shrink-0">
                                            {{ $stag->progression }} %</p>
                                    </div>
                                    <p class="text-xs text-slate-500 font-mono">{{ $stag->matricule }}</p>
                                </div>
                            </div>
                            <div class="mt-2 ml-12 h-2 rounded-full bg-slate-100 overflow-hidden" role="progressbar"
                                aria-label="Progression des tâches de {{ $stag->user->nom_complet }}" aria-valuemin="0"
                                aria-valuemax="100" aria-valuenow="{{ $stag->progression }}">
                                <div class="h-2 rounded-full bg-accent-500 transition-[width] duration-700 ease-out motion-reduce:transition-none"
                                    style="width:{{ $stag->progression }}%"></div>
                            </div>
                        </li>
                    @empty
                        <li class="py-8 text-center">
                            <p class="text-sm font-medium text-slate-700">Aucun stagiaire assigné</p>
                            <p class="mt-1 text-sm text-slate-500">Les stagiaires que l'administration vous affecte
                                apparaîtront ici.</p>
                        </li>
                    @endforelse
                </ul>
            </section>

            <!-- Colonne de droite : rapports et permissions -->
            <div class="space-y-5">

                <!-- Rapports à évaluer -->
                <section class="{{ $panneau }}">
                    <div class="{{ $enTete }}">
                        <h3 class="text-base font-semibold text-slate-900">Rapports à évaluer</h3>
                        <a href="{{ route('mentor.reports.index') }}" class="flex-shrink-0 {{ $lien }}">Voir tout →</a>
                    </div>
                    <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                        @forelse($rapportsRecents as $rapport)
                            <li class="flex items-center gap-3 py-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 truncate">{{ $rapport->titre }}</p>
                                    <p class="text-xs text-slate-500 truncate">
                                        {{ $rapport->stagiaire->user->nom_complet ?? '—' }}</p>
                                </div>
                                <a href="{{ route('mentor.reports.show', $rapport) }}"
                                    class="flex-shrink-0 {{ $btnPetitSecondaire }}">Évaluer</a>
                            </li>
                        @empty
                            <li class="py-6 text-center">
                                <p class="text-sm font-medium text-slate-700">Aucun rapport en attente</p>
                                <p class="mt-1 text-sm text-slate-500">Les rapports soumis par vos stagiaires
                                    apparaîtront ici.</p>
                            </li>
                        @endforelse
                    </ul>
                </section>

                <!-- Permissions à valider -->
                <section class="{{ $panneau }}">
                    <div class="{{ $enTete }}">
                        <h3 class="text-base font-semibold text-slate-900">Permissions à valider</h3>
                        <a href="{{ route('mentor.presences.index') }}" class="flex-shrink-0 {{ $lien }}">Voir →</a>
                    </div>
                    <ul class="px-5 sm:px-6 py-1 divide-y divide-slate-100">
                        @forelse($permissionsEnAttente as $perm)
                            <li class="py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-sm font-medium text-slate-900 min-w-0 truncate">
                                        {{ $perm->stagiaire->user->nom_complet ?? '—' }}</p>
                                    <span
                                        class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 bg-amber-50 text-amber-800">En
                                        attente</span>
                                </div>
                                <p class="mt-0.5 text-xs text-slate-500 tabular-nums">
                                    {{ $perm->date_debut->format('d/m/Y') }} → {{ $perm->date_fin->format('d/m/Y') }}</p>
                                <p class="mt-1 text-sm text-slate-600 truncate" title="{{ $perm->motif }}">{{ $perm->motif }}
                                </p>
                                <div class="mt-3 flex gap-2">
                                    <form action="{{ route('mentor.presences.validerPermission', $perm) }}" method="POST"
                                        class="no-loader">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="valide">
                                        <button type="submit" class="{{ $btnPetitPrimaire }}"
                                            aria-label="Valider la permission de {{ $perm->stagiaire->user->nom_complet ?? 'ce stagiaire' }}">
                                            <i class="fas fa-check" aria-hidden="true"></i>Valider
                                        </button>
                                    </form>
                                    <form action="{{ route('mentor.presences.validerPermission', $perm) }}" method="POST"
                                        class="no-loader">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="refuse">
                                        <button type="submit" class="{{ $btnPetitDanger }}"
                                            aria-label="Refuser la permission de {{ $perm->stagiaire->user->nom_complet ?? 'ce stagiaire' }}">
                                            <i class="fas fa-times" aria-hidden="true"></i>Refuser
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="py-6 text-center">
                                <p class="text-sm font-medium text-slate-700">Aucune permission en attente</p>
                                <p class="mt-1 text-sm text-slate-500">Les demandes d'absence de vos stagiaires
                                    apparaîtront ici.</p>
                            </li>
                        @endforelse
                    </ul>
                </section>
            </div>
        </div>
    </div>
@endsection
