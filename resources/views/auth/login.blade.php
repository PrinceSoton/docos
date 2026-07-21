@extends('layouts.app')
@section('titre', 'Connexion')
@section('content')
    <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
        <!-- Pour l'arrière plan -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-blue-900"></div>
            <div
                class="absolute top-0 left-0 w-96 h-96 bg-indigo-500 rounded-full opacity-10 blur-3xl -translate-x-1/2 -translate-y-1/2">
            </div>
            <div
                class="absolute bottom-0 right-0 w-96 h-96 bg-purple-500 rounded-full opacity-10 blur-3xl translate-x-1/2 translate-y-1/2">
            </div>
            <div
                class="absolute top-1/2 left-1/2 w-64 h-64 bg-blue-400 rounded-full opacity-5 blur-3xl -translate-x-1/2 -translate-y-1/2">
            </div>
        </div>

        <div class="w-full max-w-md relative z-10" data-aos="fade-up">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 shadow-2xl mb-4 hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('logo.png') }}" alt="DOCOS" class="w-14 h-14 object-contain rounded-xl">
                </div>
                <h1 class="text-white font-bold text-3xl tracking-tight">DOCOS</h1>
                <p class="text-indigo-300 text-sm mt-1">Gestion Professionnelle des Stagiaires</p>
            </div>

            <!-- La Carte de connexion -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
                <h2 class="text-white font-bold text-xl mb-1 text-center">Connexion</h2>
                <p class="text-indigo-300 text-sm text-center mb-6">Bienvenue ! Veuillez vous identifier.</p>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-500/20 border border-red-400/30 rounded-xl">
                        @foreach ($errors->all() as $e)
                            <p class="text-red-200 text-sm">{{ $e }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" id="loginForm" class="no-loader">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-indigo-200 text-sm font-medium mb-2">
                            <i class="fas fa-envelope mr-2"></i>Adresse email
                        </label>
                        <div class="relative">
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full bg-white/10 border border-white/20 text-white placeholder-indigo-300 rounded-xl px-4 py-3 pl-11 focus:outline-none focus:border-indigo-400 focus:bg-white/15 transition-all duration-200"
                                placeholder="votre@email.com">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 text-sm"></i>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-indigo-200 text-sm font-medium mb-2">
                            <i class="fas fa-lock mr-2"></i>Mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="passwordField" required
                                class="w-full bg-white/10 border border-white/20 text-white placeholder-indigo-300 rounded-xl px-4 py-3 pl-11 pr-11 focus:outline-none focus:border-indigo-400 focus:bg-white/15 transition-all duration-200"
                                placeholder="••••••••">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 text-sm"></i>
                            <button type="button" id="togglePwd"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-indigo-300 hover:text-white transition">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-white/30 bg-white/10 text-indigo-500 focus:ring-indigo-400">
                            <span class="text-indigo-200 text-sm">Se souvenir de moi</span>
                        </label>
                    </div>

                    <button type="submit" id="loginBtn"
                        class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span id="loginBtnText">Se connecter</span>
                    </button>
                </form>
            </div>

            <p class="text-center text-indigo-300/60 text-xs mt-6">
                © {{ date('Y') }} DOCOS — Système de Gestion des Stagiaires
            </p>
        </div>
    </div>
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
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye text-sm';
            }
        });

        // Le loader pour la connexion
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Connexion en cours...';
            btn.classList.add('opacity-80');
        });

        // GSAP entrance
        gsap.from('.max-w-md > *', {
            y: 30,
            opacity: 0,
            duration: .8,
            stagger: .15,
            ease: 'power2.out',
            delay: .3
        });
    </script>
@endpush
