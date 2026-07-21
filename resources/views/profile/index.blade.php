@extends('layouts.' . Auth::user()->role)
@section('titre', 'Mon Profil')
@section('breadcrumb', 'Profil utilisateur')
@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Card profil -->
        <div class="card p-0 overflow-hidden" data-aos="fade-up">
            <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600 relative">
                <div class="absolute bottom-0 left-8 translate-y-1/2">
                    <div
                        class="w-20 h-20 rounded-2xl border-4 border-white bg-gradient-to-br from-indigo-400 to-purple-400 flex items-center justify-center shadow-xl overflow-hidden">
                        @if ($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-white font-black text-2xl">{{ strtoupper(substr($user->prenom, 0, 1)) }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="pt-14 pb-6 px-8">
                <div class="flex items-start justify-between flex-wrap gap-4">
                    <div>
                        <h2 class="text-slate-800 font-bold text-2xl">{{ $user->nom_complet }}</h2>
                        <p class="text-slate-500">{{ $user->email }}</p>
                        <span
                            class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold
                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : ($user->role === 'mentor' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') }}">
                            <i
                                class="fas fa-{{ $user->role === 'admin' ? 'shield-alt' : ($user->role === 'mentor' ? 'chalkboard-teacher' : 'user-graduate') }} mr-1"></i>
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5">
                        <i class="fas fa-edit"></i>Modifier le profil
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-slate-400 text-xs font-medium uppercase tracking-wide">Téléphone</p>
                        <p class="text-slate-700 font-semibold mt-1">{{ $user->telephone ?: '—' }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-slate-400 text-xs font-medium uppercase tracking-wide">Statut</p>
                        <p class="font-semibold mt-1 {{ $user->actif ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->actif ? 'Actif' : 'Inactif' }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-slate-400 text-xs font-medium uppercase tracking-wide">Membre depuis</p>
                        <p class="text-slate-700 font-semibold mt-1">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                @if ($user->isStagiaire() && $user->stagiaire)
                    @php $stag = $user->stagiaire; @endphp
                    <div class="mt-6 p-5 bg-indigo-50 rounded-2xl border border-indigo-100">
                        <h3 class="text-indigo-700 font-bold mb-4"><i class="fas fa-id-card mr-2"></i>Informations de stage
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-slate-500">Matricule</p>
                                <p class="font-bold text-slate-800">{{ $stag->matricule }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">École</p>
                                <p class="font-semibold text-slate-700">{{ $stag->ecole ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Spécialité</p>
                                <p class="font-semibold text-slate-700">{{ $stag->specialite ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Début</p>
                                <p class="font-semibold text-slate-700">{{ $stag->date_debut->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Fin</p>
                                <p class="font-semibold text-slate-700">{{ $stag->date_fin->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Durée</p>
                                <p class="font-semibold text-slate-700">{{ $stag->dureeStageDays() }} jours</p>
                            </div>
                        </div>
                        @if ($stag->cv)
                            <a href="{{ asset('storage/' . $stag->cv) }}" download
                                class="inline-flex items-center gap-2 mt-4 bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-indigo-700 transition">
                                <i class="fas fa-download"></i>Télécharger mon CV
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Changer mot de passe -->
        <div class="card p-6"> {{-- data-aos="fade-up"data-aos-delay="100"> --}}
            <h3 class="text-slate-800 font-bold text-lg mb-4"><i class="fas fa-key mr-2 text-indigo-500"></i>Changer le mot
                de passe</h3>
            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mot de passe actuel</label>
                        <input type="password" name="ancien_mot_de_passe"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nouveau mot de passe</label>
                        <input type="password" name="password"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Confirmation</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm"
                            required>
                    </div>
                </div>
                <button type="submit"
                    class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-xl font-medium transition-all hover:-translate-y-0.5 hover:shadow-lg text-sm">
                    <i class="fas fa-save mr-2"></i>Mettre à jour
                </button>
            </form>
        </div>
    </div>
@endsection
