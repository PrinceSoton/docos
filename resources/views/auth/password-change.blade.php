@extends('layouts.app')
@section('titre', 'Modifier le mot de passe')
@section('content')
    <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
        <!-- Background -->
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
                <p class="text-indigo-300 text-sm mt-1">Modification du mot de passe</p>
            </div>

            <!-- Card -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
                <h2 class="text-white font-bold text-xl mb-1 text-center">Changer le mot de passe</h2>
                <p class="text-indigo-300 text-sm text-center mb-6">Veuillez renseigner votre mot de passe actuel et le
                    nouveau.</p>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-500/20 border border-red-400/30 rounded-xl">
                        @foreach ($errors->all() as $e)
                            <p class="text-red-200 text-sm">{{ $e }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" id="passwordForm" class="no-loader">
                    @csrf

                    <!-- Mot de passe actuel -->
                    <div class="mb-5">
                        <label class="block text-indigo-200 text-sm font-medium mb-2">
                            <i class="fas fa-lock mr-2"></i>Mot de passe actuel
                        </label>
                        <div class="relative">
                            <input type="password" name="current_password" id="currentPassword" required
                                class="w-full bg-white/10 border border-white/20 text-white placeholder-indigo-300 rounded-xl px-4 py-3 pl-11 pr-11 focus:outline-none focus:border-indigo-400 focus:bg-white/15 transition-all duration-200"
                                placeholder="••••••••">
                            <i class="fas fa-key absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 text-sm"></i>
                            <button type="button"
                                class="toggle-pwd absolute right-4 top-1/2 -translate-y-1/2 text-indigo-300 hover:text-white transition"
                                data-target="currentPassword">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Nouveau mot de passe -->
                    <div class="mb-5">
                        <label class="block text-indigo-200 text-sm font-medium mb-2">
                            <i class="fas fa-lock mr-2"></i>Nouveau mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="newPassword" required
                                class="w-full bg-white/10 border border-white/20 text-white placeholder-indigo-300 rounded-xl px-4 py-3 pl-11 pr-11 focus:outline-none focus:border-indigo-400 focus:bg-white/15 transition-all duration-200"
                                placeholder="••••••••">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 text-sm"></i>
                            <button type="button"
                                class="toggle-pwd absolute right-4 top-1/2 -translate-y-1/2 text-indigo-300 hover:text-white transition"
                                data-target="newPassword">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirmation -->
                    <div class="mb-6">
                        <label class="block text-indigo-200 text-sm font-medium mb-2">
                            <i class="fas fa-check-circle mr-2"></i>Confirmer le nouveau mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="confirmPassword" required
                                class="w-full bg-white/10 border border-white/20 text-white placeholder-indigo-300 rounded-xl px-4 py-3 pl-11 pr-11 focus:outline-none focus:border-indigo-400 focus:bg-white/15 transition-all duration-200"
                                placeholder="••••••••">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 text-sm"></i>
                            <button type="button"
                                class="toggle-pwd absolute right-4 top-1/2 -translate-y-1/2 text-indigo-300 hover:text-white transition"
                                data-target="confirmPassword">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        <span id="submitBtnText">Enregistrer le nouveau mot de passe</span>
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
        // Toggle password visibility
        document.querySelectorAll('.toggle-pwd').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.dataset.target;
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash text-sm';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye text-sm';
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

        // GSAP entrance animation
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
