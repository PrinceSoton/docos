@extends('layouts.admin')
@section('titre', 'Tableau de bord')
@section('content')
    <div class="space-y-6">

        <!-- Bienvenue -->
        {{-- <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-3xl p-8 text-white relative overflow-hidden"
            data-aos="fade-down">
            <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
                {{--   <p class="text-indigo-200 text-sm font-medium mb-1">{{ now()->translatedFormat('l d F Y') }}</p>
        <h2 class="text-white font-black text-3xl mb-2">Bienvenue, {{ Auth::user()->prenom }} </h2>
        {{-- <p class="text-indigo-200">Vue d'ensemble du système DOCOS</p>
    </div>
    </div> --}}

        <!-- Stats principales -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([['bg-gradient-to-br from-blue-500 to-indigo-600', 'fas fa-users', 'Utilisateurs', $stats['total_users'], 'route'], ['bg-gradient-to-br from-emerald-500 to-teal-600', 'fas fa-user-graduate', 'Stagiaires actifs', $stats['stagiaires_actifs'], 'route'], ['bg-gradient-to-br from-purple-500 to-pink-600', 'fas fa-chalkboard-teacher', 'Mentors', $stats['total_mentors'], 'route'], ['bg-gradient-to-br from-amber-500 to-orange-600', 'fas fa-project-diagram', 'Projets', $stats['total_projets'], 'route']] as $i => [$gradient, $icon, $label, $value])
                <div class="{{ $gradient }} text-white rounded-2xl p-6 relative overflow-hidden card" data-aos="fade-up"
                    data-aos-delay="{{ $i * 75 }}">
                    <div class="absolute right-3 top-3 opacity-20">
                        <i class="{{ $icon }} text-4xl"></i>
                    </div>
                    <p class="text-white/80 text-sm font-medium">{{ $label }}</p>
                    <p class="text-white font-black text-3xl mt-1">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <!-- Stats secondaires -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="card p-5 text-center" data-aos="fade-up">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-file-alt text-red-600 text-xl"></i>
                </div>
                <p class="text-2xl font-black text-slate-800">{{ $stats['rapports_en_attente'] }}</p>
                <p class="text-slate-400 text-xs mt-1">Rapports à valider</p>
            </div>
            <div class="card p-5 text-center" data-aos="fade-up" data-aos-delay="75">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-certificate text-amber-600 text-xl"></i>
                </div>
                <p class="text-2xl font-black text-slate-800">{{ $stats['attestations_en_attente'] }}</p>
                <p class="text-slate-400 text-xs mt-1">Attestations à traiter</p>
            </div>
            <div class="card p-5 text-center" data-aos="fade-up" data-aos-delay="150">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
                <p class="text-2xl font-black text-slate-800">{{ $stats['presences_aujourd_hui'] }}</p>
                <p class="text-slate-400 text-xs mt-1">Présences aujourd'hui</p>
            </div>
            <div class="card p-5 text-center" data-aos="fade-up" data-aos-delay="225">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-graduation-cap text-indigo-600 text-xl"></i>
                </div>
                <p class="text-2xl font-black text-slate-800">{{ $stats['total_stagiaires'] }}</p>
                <p class="text-slate-400 text-xs mt-1">Total stagiaires</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Stagiaires récents -->
            <div class="card p-6" data-aos="fade-right">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-slate-800 font-bold text-lg"><i
                            class="fas fa-user-graduate text-indigo-500 mr-2"></i>Stagiaires récents</h3>
                    <a href="{{ route('admin.stagiaires.index') }}" class="text-indigo-600 text-sm hover:underline">Voir
                        tous →</a>
                </div>
                @forelse($stagiairesRecents as $stag)
                    <div
                        class="flex items-center gap-4 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded-xl px-2 transition">
                        <div
                            class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-400 to-purple-400 flex items-center justify-center flex-shrink-0">
                            <span
                                class="text-white font-bold text-sm">{{ strtoupper(substr($stag->user->prenom, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-800 font-semibold text-sm truncate">{{ $stag->user->nom_complet }}</p>
                            <p class="text-slate-400 text-xs">{{ $stag->matricule }} • {{ $stag->ecole ?: '—' }}</p>
                        </div>
                        <span
                            class="text-xs px-2 py-1 rounded-full font-medium {{ $stag->statut === 'en_cours' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $stag->statut === 'en_cours' ? 'En cours' : ucfirst($stag->statut) }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-6">Aucun stagiaire</p>
                @endforelse
            </div>

            <!-- Rapports récents -->
            <div class="card p-6" data-aos="fade-left">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-slate-800 font-bold text-lg"><i
                            class="fas fa-file-alt text-purple-500 mr-2"></i>Rapports
                        soumis</h3>
                    <a href="{{ route('admin.statistics.index') }}" class="text-indigo-600 text-sm hover:underline">Autres
                        →</a>
                </div>
                @forelse($rapportsRecents as $rapport)
                    <div
                        class="flex items-center gap-4 py-3 border-b border-slate-50 last:border-0 hover:bg-slate-50 rounded-xl px-2 transition">
                        <div
                            class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-alt text-white text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-800 font-semibold text-sm truncate">{{ $rapport->titre }}</p>
                            <p class="text-slate-400 text-xs">{{ $rapport->stagiaire->user->nom_complet ?? '—' }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-medium bg-amber-100 text-amber-700">Soumis</span>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-6">Aucun rapport</p>
                @endforelse
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card p-6">
            <h3 class="text-slate-800 font-bold text-lg mb-4">
                {{-- <iclass="fasfa-bolttext-amber-500mr-2"></i> --}}
                Raccourci pour la création des modules
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach ([[route('admin.users.create'), 'fas fa-user-plus', 'bg-blue-50 text-blue-700 hover:bg-blue-100', 'Ajouter utilisateur'], [route('admin.stagiaires.create'), 'fas fa-user-graduate', 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100', 'Ajouter stagiaire'], [route('admin.evenements.create'), 'fas fa-bullhorn', 'bg-purple-50 text-purple-700 hover:bg-purple-100', 'Créer événement'], [route('admin.archives.create'), 'fas fa-archive', 'bg-amber-50 text-amber-700 hover:bg-amber-100', 'Créer archive']] as [$route, $icon, $cls, $label])
                    <a href="{{ $route }}"
                        class="{{ $cls }} flex flex-col items-center gap-2 py-5 rounded-2xl font-medium text-sm transition-all hover:-translate-y-1 hover:shadow-md">
                        <i class="{{ $icon }} text-2xl"></i>{{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
