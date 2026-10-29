@extends('layouts.app')
@section('titre', 'Modifier le mot de passe')
@php
    // Premier accès : le compte a été créé par l'administration avec un mot de passe provisoire
    $etapes = [
        ['Connexion', 'fait'],
        ['Nouveau mot de passe', 'actif'],
        ['Accès à votre espace', 'a_venir'],
    ];
@endphp
@section('panneau_surtitre', 'Premier accès')
@section('panneau_titre', 'Choisissez votre mot de passe personnel')
@section('panneau_texte', 'Remplacez le mot de passe reçu à la création de votre compte. Dès qu\'il est enregistré, vous accédez directement à votre espace.')
@section('content')
    <div class="rounded-2xl border border-brand-100 bg-white p-5 sm:p-8 shadow-[0_24px_48px_-28px_rgba(11,37,64,.4)]">
        <h1 class="text-2xl font-bold tracking-tight text-brand-900">Changer le mot de passe</h1>
        <p class="mt-1 text-sm text-slate-600">Veuillez renseigner votre mot de passe actuel et le nouveau.</p>

        {{-- Message transmis lors de la redirection obligatoire vers cette page --}}
        @if (session('warning'))
            <div class="auth-alerte mt-5 flex items-start gap-2" role="status">
                <i class="fas fa-shield-alt mt-0.5 text-amber-700" aria-hidden="true"></i>
                <p>{{ session('warning') }}</p>
            </div>
        @endif

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

        <form method="POST" action="{{ route('password.change.update') }}" id="passwordForm" class="no-loader mt-6 space-y-5">
            @csrf

            <!-- Mot de passe actuel -->
            <div>
                <label for="currentPassword" class="auth-label">Mot de passe actuel</label>
                <div class="relative">
                    <input type="password" name="current_password" id="currentPassword" required autocomplete="current-password"
                        @error('current_password') aria-invalid="true" @enderror
                        class="auth-input pr-12 {{ $errors->has('current_password') ? 'auth-input-invalide' : '' }}" placeholder="••••••••">
                    <i class="fas fa-key auth-icone" aria-hidden="true"></i>
                    <button type="button" class="toggle-pwd auth-oeil" data-target="currentPassword"
                        aria-label="Afficher le mot de passe actuel" aria-controls="currentPassword">
                        <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="border-t border-brand-100"></div>

            <!-- Nouveau mot de passe -->
            <div>
                <label for="newPassword" class="auth-label">Nouveau mot de passe</label>
                <div class="relative">
                    <input type="password" name="password" id="newPassword" required autocomplete="new-password"
                        @error('password') aria-invalid="true" @enderror
                        class="auth-input pr-12 {{ $errors->has('password') ? 'auth-input-invalide' : '' }}" placeholder="••••••••">
                    <i class="fas fa-lock auth-icone" aria-hidden="true"></i>
                    <button type="button" class="toggle-pwd auth-oeil" data-target="newPassword"
                        aria-label="Afficher le nouveau mot de passe" aria-controls="newPassword">
                        <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <!-- Confirmation -->
            <div>
                <label for="confirmPassword" class="auth-label">Confirmer le nouveau mot de passe</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="confirmPassword" required autocomplete="new-password"
                        class="auth-input pr-12" placeholder="••••••••">
                    <i class="fas fa-lock auth-icone" aria-hidden="true"></i>
                    <button type="button" class="toggle-pwd auth-oeil" data-target="confirmPassword"
                        aria-label="Afficher la confirmation du mot de passe" aria-controls="confirmPassword">
                        <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="auth-btn">
                <i class="fas fa-save text-xs" aria-hidden="true"></i>
                <span id="submitBtnText">Enregistrer le nouveau mot de passe</span>
            </button>
        </form>
        <form action="{{ route('logout') }}" method="POST" class="mt-5 text-center">
            @csrf
            <button type="submit" class="auth-lien">Se déconnecter</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle password visibility
        document.querySelectorAll('.toggle-pwd').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                const libelle = this.getAttribute('aria-label') || '';
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash text-sm';
                    this.setAttribute('aria-label', libelle.replace('Afficher', 'Masquer'));
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye text-sm';
                    this.setAttribute('aria-label', libelle.replace('Masquer', 'Afficher'));
                }
            });
        });

        // Button loading state
        document.getElementById('passwordForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';
            btn.classList.add('opacity-80');
        });
    </script>
@endpush
