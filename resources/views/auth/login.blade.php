@extends('layouts.app')
@section('titre', 'Connexion')
@section('panneau_surtitre', 'Gestion numérique des documents des stagiaires')
@section('panneau_titre', 'Gestion Professionnelle des Stagiaires')
@section('panneau_texte', 'Administrateurs, mentors et stagiaires se connectent ici avec le même formulaire : chacun retrouve ensuite son propre espace.')
@section('content')
    <div class="rounded-2xl border border-brand-100 bg-white p-5 sm:p-8 shadow-[0_24px_48px_-28px_rgba(11,37,64,.4)]">
        <h1 class="text-2xl font-bold tracking-tight text-brand-900">Connexion</h1>
        <p class="mt-1 text-sm text-slate-600">Bienvenue ! Veuillez vous identifier.</p>

        {{-- Confirmation transmise après une réinitialisation du mot de passe --}}
        @if (session('status'))
            <div class="auth-info mt-5 flex items-start gap-2" role="status">
                <i class="fas fa-check-circle mt-0.5" aria-hidden="true"></i>
                <p>{{ session('status') }}</p>
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

        <form action="{{ route('login') }}" method="POST" id="loginForm" class="no-loader mt-6 space-y-5">
            @csrf
            <div>
                <label for="email" class="auth-label">Adresse email</label>
                <div class="relative">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                        @error('email') aria-invalid="true" @enderror
                        class="auth-input {{ $errors->has('email') ? 'auth-input-invalide' : '' }}" placeholder="votre@email.com">
                    <i class="fas fa-envelope auth-icone" aria-hidden="true"></i>
                </div>
            </div>

            <div>
                <div class="flex items-baseline justify-between gap-3 mb-1.5">
                    <label for="passwordField" class="auth-label !mb-0">Mot de passe</label>
                    <a href="{{ route('password.request') }}" class="auth-lien">Mot de passe oublié ?</a>
                </div>
                <div class="relative">
                    <input type="password" name="password" id="passwordField" required autocomplete="current-password"
                        class="auth-input pr-12" placeholder="••••••••">
                    <i class="fas fa-lock auth-icone" aria-hidden="true"></i>
                    <button type="button" id="togglePwd" class="auth-oeil" aria-label="Afficher le mot de passe"
                        aria-controls="passwordField">
                        <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <label class="flex w-fit items-center gap-2.5 cursor-pointer">
                <input type="checkbox" name="remember"
                    class="h-4 w-4 rounded border-slate-300 text-brand-800 focus:ring-brand-600">
                <span class="text-sm text-slate-700">Se souvenir de moi</span>
            </label>

            <button type="submit" id="loginBtn" class="auth-btn">
                <i class="fas fa-sign-in-alt text-xs" aria-hidden="true"></i>
                <span id="loginBtnText">Se connecter</span>
            </button>
        </form>
    </div>

    <p class="mt-5 text-center text-sm text-slate-600">
        Pas encore de compte ? Les accès sont créés par l'administration de votre structure.
    </p>
@endsection
@push('scripts')
    <script>
        // Icone de password
        document.getElementById('togglePwd').addEventListener('click', function() {
            const input = document.getElementById('passwordField');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash text-sm';
                this.setAttribute('aria-label', 'Masquer le mot de passe');
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye text-sm';
                this.setAttribute('aria-label', 'Afficher le mot de passe');
            }
        });

        // Le loader pour la connexion
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Connexion en cours...';
            btn.classList.add('opacity-80');
        });
    </script>
@endpush
