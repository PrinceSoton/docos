@extends('layouts.app')
@section('titre', 'Réinitialiser le mot de passe')
@php
    $etapes = [
        ['Demander le lien', 'fait'],
        ["Ouvrir l'e-mail reçu", 'fait'],
        ['Choisir un nouveau mot de passe', 'actif'],
    ];
@endphp
@section('panneau_surtitre', 'Réinitialisation du mot de passe')
@section('panneau_titre', 'Dernière étape : votre nouveau mot de passe')
@section('panneau_texte', 'Une fois enregistré, vous serez redirigé vers la connexion pour vous identifier avec ce nouveau mot de passe.')
@section('content')
    <div class="rounded-2xl border border-brand-100 bg-white p-5 sm:p-8 shadow-[0_24px_48px_-28px_rgba(11,37,64,.4)]">
        <h1 class="text-2xl font-bold tracking-tight text-brand-900">Définir un nouveau mot de passe</h1>
        <p class="mt-1 text-sm text-slate-600">Choisissez un mot de passe sécurisé.</p>

        @if ($errors->any())
            <div class="auth-erreur mt-5" role="alert">
                @foreach ($errors->all() as $e)
                    <p class="flex items-start gap-2">
                        <i class="fas fa-exclamation-circle mt-0.5 text-red-700" aria-hidden="true"></i>
                        <span>{{ $e }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="no-loader mt-6 space-y-5">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email (lecture seule) -->
            <div>
                <label for="email" class="auth-label">Adresse email</label>
                <div class="relative">
                    <input type="email" name="email" id="email" value="{{ old('email', $request->email) }}" required
                        class="auth-input auth-input-lecture {{ $errors->has('email') ? 'auth-input-invalide' : '' }}"
                        placeholder="votre@email.com" readonly aria-describedby="email-aide">
                    <i class="fas fa-envelope auth-icone" aria-hidden="true"></i>
                </div>
                <p id="email-aide" class="mt-1.5 text-xs text-slate-600">Adresse associée au lien reçu par e-mail.</p>
            </div>

            <!-- Nouveau mot de passe -->
            <div>
                <label for="newPassword" class="auth-label">Nouveau mot de passe</label>
                <div class="relative">
                    <input type="password" name="password" id="newPassword" required autocomplete="new-password"
                        @error('password') aria-invalid="true" @enderror
                        class="auth-input pr-12 {{ $errors->has('password') ? 'auth-input-invalide' : '' }}" placeholder="••••••••">
                    <i class="fas fa-lock auth-icone" aria-hidden="true"></i>
                    <button type="button" onclick="togglePwd('newPassword', this)" class="auth-oeil"
                        aria-label="Afficher le nouveau mot de passe" aria-controls="newPassword">
                        <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <!-- Confirmation -->
            <div>
                <label for="confirmPassword" class="auth-label">Confirmer le mot de passe</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="confirmPassword" required autocomplete="new-password"
                        class="auth-input pr-12" placeholder="••••••••">
                    <i class="fas fa-lock auth-icone" aria-hidden="true"></i>
                    <button type="button" onclick="togglePwd('confirmPassword', this)" class="auth-oeil"
                        aria-label="Afficher la confirmation du mot de passe" aria-controls="confirmPassword">
                        <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="auth-btn">
                <i class="fas fa-save text-xs" aria-hidden="true"></i>
                <span>Réinitialiser le mot de passe</span>
            </button>
        </form>
    </div>

    <p class="mt-5 text-center">
        <a href="{{ route('login') }}" class="auth-lien inline-flex items-center gap-1.5">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour à la connexion
        </a>
    </p>
@endsection

@push('scripts')
    <script>
        // Toggle visibilité du mot de passe
        function togglePwd(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            const libelle = btn.getAttribute('aria-label') || '';
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash text-sm';
                btn.setAttribute('aria-label', libelle.replace('Afficher', 'Masquer'));
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye text-sm';
                btn.setAttribute('aria-label', libelle.replace('Masquer', 'Afficher'));
            }
        }

        // Loader lors de la soumission
        document.querySelector('main form')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Réinitialisation...';
                btn.classList.add('opacity-80');
            }
        });
    </script>
@endpush
