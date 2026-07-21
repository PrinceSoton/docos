@extends('layouts.app')
@section('titre', 'Bienvenue')
@section('content')
    <div class="min-h-screen relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-slate-900"></div>
        <div class="absolute top-20 left-10 w-72 h-72 bg-indigo-500 rounded-full opacity-10 blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-purple-500 rounded-full opacity-10 blur-3xl animate-pulse"
            style="animation-delay:1s"></div>

        <!-- Nav -->
        <nav class="relative z-10 flex items-center justify-between px-8 py-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo.png') }}" alt="DOCOS" class="w-10 h-10 rounded-xl object-contain">
                <span class="text-white font-bold text-2xl">DOCOS</span>
            </div>
            <a href="{{ route('login') }}"
                class="bg-white/10 hover:bg-white/20 border border-white/20 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-300 hover:shadow-lg backdrop-blur-sm">
                <i class="fas fa-sign-in-alt mr-2"></i>Connexion
            </a>
        </nav>

        <!-- Hero -->
        <div class="relative z-10 flex flex-col items-center justify-center min-h-[85vh] text-center px-4"
            data-aos="fade-up">
            <div
                class="inline-flex items-center gap-2 bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 px-4 py-2 rounded-full text-sm font-medium mb-8 backdrop-blur-sm">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                Système de Gestion Professionnelle
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-white mb-6 leading-tight">
                Gérez vos<br>
                <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Stagiaires</span><br>
                avec excellence
            </h1>
            <p class="text-indigo-200 text-xl max-w-2xl mb-10 leading-relaxed">
                DOCOS centralise le suivi des stagiaires, la gestion des présences, des projets et des documents dans une
                interface moderne et intuitive.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('login') }}"
                    class="bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white px-8 py-4 rounded-2xl font-semibold text-lg transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-500/30 hover:-translate-y-1 flex items-center gap-3">
                    <i class="fas fa-rocket"></i>Commencer maintenant
                </a>
            </div>

            <!-- Features grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 max-w-4xl w-full">
                @foreach ([['fas fa-user-graduate', 'Stagiaires', 'Gestion complète'], ['fas fa-tasks', 'Projets & Tâches', 'Suivi en temps réel'], ['fas fa-clipboard-check', 'Présences', 'Marquage automatique'], ['fas fa-certificate', 'Attestations', 'Gestion simplifiée']] as [$icon, $titre, $desc])
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300 hover:-translate-y-1"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="w-10 h-10 bg-indigo-500/20 rounded-xl flex items-center justify-center mb-3 mx-auto">
                            <i class="{{ $icon }} text-indigo-400"></i>
                        </div>
                        <p class="text-white font-semibold text-sm">{{ $titre }}</p>
                        <p class="text-indigo-300 text-xs mt-1">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
