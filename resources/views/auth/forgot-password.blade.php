@extends('layouts.app')
@section('titre', 'Mot de passe oublié')
@php
    // Fil d'étapes : une fois le lien envoyé, l'étape suivante se passe dans la boîte de réception
    $lienEnvoye = (bool) session('status');
    $etapes = [
        ['Demander le lien', $lienEnvoye ? 'fait' : 'actif'],
        ["Ouvrir l'e-mail reçu", $lienEnvoye ? 'actif' : 'a_venir'],
        ['Choisir un nouveau mot de passe', 'a_venir'],
    ];
@endphp
@section('panneau_surtitre', 'Réinitialisation du mot de passe')
@section('panneau_titre', 'Retrouvez l\'accès à votre espace')
@section('panneau_texte', 'Indiquez l\'adresse email de votre compte : vous recevrez un lien pour choisir un nouveau mot de passe.')
@section('content')
    <div class="rounded-2xl border border-brand-100 bg-white p-5 sm:p-8 shadow-[0_24px_48px_-28px_rgba(11,37,64,.4)]">
        <h1 class="text-2xl font-bold tracking-tight text-brand-900">Mot de passe oublié</h1>
        <p class="mt-1 text-sm text-slate-600">
            Saisissez votre adresse email. Un lien de réinitialisation vous sera envoyé.
        </p>

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

        @if (session('status'))
            <div class="auth-info mt-5" role="status">
                <p class="flex items-start gap-2">
                    <i class="fas fa-envelope-open-text mt-0.5" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </p>
                <p class="mt-1.5 pl-6 text-accent-700">Ouvrez l'e-mail reçu et suivez le lien pour continuer.</p>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="no-loader mt-6 space-y-5">
            @csrf

            <div>
                <label for="email" class="auth-label">Adresse email</label>
                <div class="relative">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email" @error('email') aria-invalid="true" @enderror
                        class="auth-input {{ $errors->has('email') ? 'auth-input-invalide' : '' }}" placeholder="votre@email.com">
                    <i class="fas fa-envelope auth-icone" aria-hidden="true"></i>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="auth-btn">
                <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>
                <span>Envoyer le lien</span>
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
        // Loader lors de la soumission
        document.querySelector('main form')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi en cours...';
                btn.classList.add('opacity-80');
            }
        });
    </script>
@endpush
