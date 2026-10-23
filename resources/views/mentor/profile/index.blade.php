{{-- Présentation mentor de la vue partagée profile/index (incluse uniquement pour le rôle mentor) --}}
@php
    $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
    $focusMarine = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-800';
    $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
    $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
    $btnPetit = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    $btnMarine = 'inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-brand-800 hover:bg-brand-50 transition-colors ' . $focusMarine;
    $enTete = 'px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100';
@endphp

<div class="max-w-4xl mx-auto space-y-5">

    <!-- Identité (bandeau marine) -->
    <section class="rounded-xl bg-brand-800 text-white p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div
                class="w-16 h-16 rounded-full bg-brand-700 ring-2 ring-white/30 flex items-center justify-center flex-shrink-0 overflow-hidden">
                @if ($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo de profil"
                        class="w-full h-full object-cover">
                @else
                    <span class="text-white font-semibold text-xl">{{ strtoupper(substr($user->prenom, 0, 1)) }}</span>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-lg font-semibold text-white break-words">{{ $user->nom_complet }}</h2>
                <p class="text-sm text-brand-100 break-all">{{ $user->email }}</p>
                <span class="mt-2 inline-block text-xs font-medium px-2 py-0.5 rounded-md bg-white/15 text-white">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
            <a href="{{ route('profile.edit') }}" class="sm:self-start {{ $btnMarine }}">
                <i class="fas fa-edit text-xs" aria-hidden="true"></i>Modifier le profil
            </a>
        </div>

        <dl class="mt-5 pt-5 border-t border-white/15 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <dt class="text-xs text-brand-100">Téléphone</dt>
                <dd class="mt-0.5 text-sm font-medium text-white tabular-nums">{{ $user->telephone ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-brand-100">Statut</dt>
                <dd class="mt-0.5 flex items-center gap-2 text-sm font-medium text-white">
                    <span class="h-2 w-2 rounded-full {{ $user->actif ? 'bg-emerald-500' : 'bg-red-500' }}"
                        aria-hidden="true"></span>
                    {{ $user->actif ? 'Actif' : 'Inactif' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs text-brand-100">Membre depuis</dt>
                <dd class="mt-0.5 text-sm font-medium text-white tabular-nums">{{ $user->created_at->format('d/m/Y') }}</dd>
            </div>
        </dl>
    </section>

    <!-- Informations de stage -->
    @if ($user->isStagiaire() && $user->stagiaire)
        @php $stag = $user->stagiaire; @endphp
        <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
            <div class="{{ $enTete }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-slate-900">Informations de stage</h3>
                @if ($stag->cv)
                    <a href="{{ asset('storage/' . $stag->cv) }}" download class="self-start sm:self-auto {{ $btnPetit }}">
                        <i class="fas fa-download" aria-hidden="true"></i>Télécharger mon CV
                    </a>
                @endif
            </div>
            <dl class="p-5 sm:p-6 grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <dt class="text-xs text-slate-500">Matricule</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 font-mono">{{ $stag->matricule }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500">École</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">{{ $stag->ecole ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500">Spécialité</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">{{ $stag->specialite ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500">Début</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">{{ $stag->date_debut->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500">Fin</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">{{ $stag->date_fin->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500">Durée</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">{{ $stag->dureeStageDays() }} jours</dd>
                </div>
            </dl>
        </section>
    @endif

    <!-- Changer le mot de passe -->
    <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
        <div class="{{ $enTete }}">
            <h3 class="text-base font-semibold text-slate-900">Changer le mot de passe</h3>
        </div>
        <form action="{{ route('profile.password') }}" method="POST" class="p-5 sm:p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="mdp-actuel" class="block text-sm font-medium text-slate-700 mb-1">Mot de passe actuel</label>
                    <input type="password" name="ancien_mot_de_passe" id="mdp-actuel" autocomplete="current-password"
                        class="{{ $champ }}" required>
                    @error('ancien_mot_de_passe')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="mdp-nouveau" class="block text-sm font-medium text-slate-700 mb-1">Nouveau mot de
                        passe</label>
                    <input type="password" name="password" id="mdp-nouveau" autocomplete="new-password"
                        class="{{ $champ }}" required>
                    @error('password')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="mdp-confirmation"
                        class="block text-sm font-medium text-slate-700 mb-1">Confirmation</label>
                    <input type="password" name="password_confirmation" id="mdp-confirmation"
                        autocomplete="new-password" class="{{ $champ }}" required>
                </div>
            </div>
            <button type="submit" class="{{ $btnPrimaire }}">
                <i class="fas fa-save text-xs" aria-hidden="true"></i>Mettre à jour
            </button>
        </form>
    </section>
</div>
