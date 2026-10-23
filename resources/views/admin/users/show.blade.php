@extends('layouts.admin')
@section('titre', 'Profil utilisateur')
@section('breadcrumb', 'Utilisateurs > Détail')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $focusMarine = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-800';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $panneau = 'bg-white border border-brand-100 rounded-xl overflow-hidden';
        $enTete = 'px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex items-center justify-between gap-3';
        $statutsStagiaire = [
            'en_cours' => ['En cours', 'bg-sky-50 text-sky-800'],
            'termine' => ['Terminé', 'bg-emerald-50 text-emerald-800'],
            'suspendu' => ['Suspendu', 'bg-red-50 text-red-800'],
        ];
    @endphp

    <div class="max-w-4xl mx-auto space-y-5">

        <!-- Identité (bandeau marine) -->
        <section class="rounded-xl bg-brand-800 text-white p-5 sm:p-6" aria-labelledby="utilisateur-nom">
            <div class="flex flex-col sm:flex-row sm:items-start gap-5">
                <div class="h-20 w-20 rounded-full bg-white/15 flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if ($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="" class="h-full w-full object-cover">
                    @else
                        <span class="text-2xl font-semibold text-white"
                            aria-hidden="true">{{ strtoupper(substr($user->prenom, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 id="utilisateur-nom" class="text-2xl font-semibold text-white tracking-tight break-words">
                            {{ $user->nom_complet }}</h2>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-white/15 text-white">
                            {{ ucfirst($user->role) }}</span>
                    </div>
                    <p class="mt-1 text-sm text-brand-100 break-words">{{ $user->email }}</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto flex-shrink-0">
                    <a href="{{ route('admin.users.edit', $user) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-brand-800 hover:bg-brand-50 transition-colors whitespace-nowrap {{ $focusMarine }}">
                        <i class="fas fa-pen text-xs" aria-hidden="true"></i>Modifier
                    </a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" id="del-user">
                        @csrf @method('DELETE')
                        <button type="button"
                            class="btn-delete w-full inline-flex items-center justify-center gap-2 rounded-lg border border-white/30 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/10 transition-colors whitespace-nowrap {{ $focusMarine }}"
                            data-form="del-user">
                            <i class="fas fa-trash text-xs" aria-hidden="true"></i>Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <dl class="mt-6 pt-5 border-t border-white/15 grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4 text-sm">
                <div class="min-w-0">
                    <dt class="text-xs text-brand-100">Rôle</dt>
                    <dd class="mt-0.5 font-medium text-white">{{ ucfirst($user->role) }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-xs text-brand-100">Téléphone</dt>
                    <dd class="mt-0.5 font-medium text-white tabular-nums break-words">{{ $user->telephone ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-brand-100">Statut</dt>
                    <dd class="mt-0.5 flex items-center gap-1.5 font-medium text-white">
                        <span class="h-2 w-2 rounded-full {{ $user->actif ? 'bg-accent-500' : 'bg-red-400' }}"
                            aria-hidden="true"></span>
                        {{ $user->actif ? 'Actif' : 'Inactif' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-brand-100">Inscrit le</dt>
                    <dd class="mt-0.5 font-medium text-white tabular-nums">{{ $user->created_at->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </section>

        @if ($user->stagiaire)
            @php
                [$libStatut, $clsStatut] = $statutsStagiaire[$user->stagiaire->statut] ?? [ucfirst($user->stagiaire->statut), 'bg-slate-100 text-slate-700'];
            @endphp
            <section class="{{ $panneau }}" aria-labelledby="titre-stage">
                <div class="{{ $enTete }}">
                    <h3 id="titre-stage" class="text-base font-semibold text-slate-900">Stage</h3>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-md {{ $clsStatut }}">{{ $libStatut }}</span>
                </div>
                <dl class="p-5 sm:p-6 grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4">
                    <div class="min-w-0">
                        <dt class="text-xs text-slate-500">Matricule</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900 font-mono break-words">
                            {{ $user->stagiaire->matricule }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-xs text-slate-500">École</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">{{ $user->stagiaire->ecole ?: '—' }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-xs text-slate-500">Mentor</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">
                            {{ $user->stagiaire->mentor?->nom_complet ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Début</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">
                            {{ $user->stagiaire->date_debut->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Fin</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">
                            {{ $user->stagiaire->date_fin->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Statut</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900">{{ $libStatut }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if ($user->mentor)
            <section class="{{ $panneau }}" aria-labelledby="titre-mentor">
                <div class="{{ $enTete }}">
                    <h3 id="titre-mentor" class="text-base font-semibold text-slate-900">Mentor</h3>
                </div>
                <dl class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div class="min-w-0">
                        <dt class="text-xs text-slate-500">Département</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">{{ $user->mentor->departement ?: '—' }}</dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-xs text-slate-500">Poste</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">{{ $user->mentor->poste ?: '—' }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        <div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 {{ $lien }}">
                <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour à la liste
            </a>
        </div>
    </div>
@endsection
