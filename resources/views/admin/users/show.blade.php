@extends('layouts.admin')
@section('titre', 'Profil utilisateur')
@section('breadcrumb', 'Utilisateurs > Détail')
@section('content')
    <div class="max-w-3xl mx-auto space-y-5">
        <div class="card p-0 overflow-hidden" data-aos="fade-up">
            <div class="h-28 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
            <div class="px-8 pb-6">
                <div class="flex items-end gap-5 -mt-10 flex-wrap">
                    <div
                        class="w-20 h-20 rounded-2xl border-4 border-white bg-gradient-to-br from-indigo-400 to-purple-400 flex items-center justify-center shadow-xl overflow-hidden flex-shrink-0">
                        @if ($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-white font-black text-2xl">{{ strtoupper(substr($user->prenom, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 mt-10">
                        <h2 class="text-slate-800 font-bold text-2xl">{{ $user->nom_complet }}</h2>
                        <p class="text-slate-500">{{ $user->email }}</p>
                    </div>
                    <div class="flex gap-2 mt-4">
                        <a href="{{ route('admin.users.edit', $user) }}"
                            class="flex items-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-amber-600 transition">
                            <i class="fas fa-edit"></i>Modifier
                        </a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" id="del-user">
                            @csrf @method('DELETE')
                            <button type="button"
                                class="btn-delete flex items-center gap-2 bg-red-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-red-600 transition"
                                data-form="del-user">
                                <i class="fas fa-trash"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">Rôle</p>
                        <span
                            class="mt-1 inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : ($user->role === 'mentor' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">Téléphone</p>
                        <p class="text-slate-700 font-semibold mt-1 text-sm">{{ $user->telephone ?: '—' }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">Statut</p>
                        <p class="font-semibold mt-1 text-sm {{ $user->actif ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->actif ? 'Actif' : 'Inactif' }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">Inscrit le</p>
                        <p class="text-slate-700 font-semibold mt-1 text-sm">{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                @if ($user->stagiaire)
                    <div class="mt-5 p-5 bg-indigo-50 rounded-2xl">
                        <h3 class="text-indigo-700 font-bold mb-3"><i class="fas fa-id-card mr-2"></i>Stage</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <div>
                                <p class="text-xs text-slate-500">Matricule</p>
                                <p class="font-bold text-slate-800">{{ $user->stagiaire->matricule }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">École</p>
                                <p class="font-semibold text-slate-700">{{ $user->stagiaire->ecole ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Mentor</p>
                                <p class="font-semibold text-slate-700">{{ $user->stagiaire->mentor?->nom_complet ?: '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Début</p>
                                <p class="font-semibold text-slate-700">{{ $user->stagiaire->date_debut->format('d/m/Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Fin</p>
                                <p class="font-semibold text-slate-700">{{ $user->stagiaire->date_fin->format('d/m/Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Statut</p>
                                <p class="font-semibold text-slate-700">{{ ucfirst($user->stagiaire->statut) }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($user->mentor)
                    <div class="mt-5 p-5 bg-emerald-50 rounded-2xl">
                        <h3 class="text-emerald-700 font-bold mb-3"><i class="fas fa-chalkboard-teacher mr-2"></i>Mentor
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs text-slate-500">Département</p>
                                <p class="font-semibold text-slate-700">{{ $user->mentor->departement ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Poste</p>
                                <p class="font-semibold text-slate-700">{{ $user->mentor->poste ?: '—' }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <a href="{{ route('admin.users.index') }}"
            class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour à la liste
        </a>
    </div>
@endsection
