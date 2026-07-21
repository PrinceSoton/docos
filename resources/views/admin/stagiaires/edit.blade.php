@extends('layouts.admin')
@section('titre', 'Modifier stagiaire')
@section('breadcrumb', 'Stagiaires > Modifier')
@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-edit text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Modifier : {{ $stagiaire->user->nom_complet }}</h2>
                    <p class="text-slate-400 text-sm">Matricule : <span
                            class="font-mono font-bold text-indigo-600">{{ $stagiaire->matricule }}</span></p>
                </div>
            </div>

            <form action="{{ route('admin.stagiaires.update', $stagiaire) }}" method="POST" enctype="multipart/form-data"
                class="space-y-5">
                @csrf @method('PUT')

                <div class="p-5 bg-slate-50 rounded-2xl">
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide mb-4"><i
                            class="fas fa-user mr-1"></i>Compte utilisateur</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Prénom *</label>
                            <input type="text" name="prenom" value="{{ old('prenom', $stagiaire->user->prenom) }}"
                                required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nom *</label>
                            <input type="text" name="nom" value="{{ old('nom', $stagiaire->user->nom) }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
                            <input type="email" name="email" value="{{ old('email', $stagiaire->user->email) }}"
                                required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Téléphone</label>
                            <input type="text" name="telephone"
                                value="{{ old('telephone', $stagiaire->user->telephone) }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nouveau mot de passe</label>
                            <input type="password" name="password"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm"
                                placeholder="Laisser vide pour ne pas changer">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Confirmation</label>
                            <input type="password" name="password_confirmation"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Photo</label>
                        @if ($stagiaire->user->photo)
                            <img src="{{ asset('storage/' . $stagiaire->user->photo) }}"
                                class="w-12 h-12 rounded-xl object-cover mb-2 border-2 border-white shadow">
                        @endif
                        <input type="file" name="photo" accept="image/*"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-amber-50 file:text-amber-700 file:font-medium hover:file:bg-amber-100 transition">
                    </div>
                </div>

                <div class="p-5 bg-slate-50 rounded-2xl">
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide mb-4"><i
                            class="fas fa-graduation-cap mr-1"></i>Informations de stage</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">École</label>
                            <input type="text" name="ecole" value="{{ old('ecole', $stagiaire->ecole) }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Spécialité</label>
                            <input type="text" name="specialite" value="{{ old('specialite', $stagiaire->specialite) }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Niveau d'étude</label>
                            <input type="text" name="niveau_etude"
                                value="{{ old('niveau_etude', $stagiaire->niveau_etude) }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Statut</label>
                            <select name="statut" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                                <option value="en_cours" {{ $stagiaire->statut === 'en_cours' ? 'selected' : '' }}>En cours
                                </option>
                                <option value="termine" {{ $stagiaire->statut === 'termine' ? 'selected' : '' }}>Terminé
                                </option>
                                <option value="suspendu" {{ $stagiaire->statut === 'suspendu' ? 'selected' : '' }}>Suspendu
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Mentor</label>
                            <select name="mentor_id"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                                <option value="">-- Aucun --</option>
                                @foreach ($mentors as $mentor)
                                    <option value="{{ $mentor->user_id }}"
                                        {{ $stagiaire->mentor_id == $mentor->user_id ? 'selected' : '' }}>
                                        {{ $mentor->user->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Date de début *</label>
                            <input type="date" name="date_debut"
                                value="{{ old('date_debut', $stagiaire->date_debut->format('Y-m-d')) }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Date de fin *</label>
                            <input type="date" name="date_fin"
                                value="{{ old('date_fin', $stagiaire->date_fin->format('Y-m-d')) }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm resize-none">{{ old('description', $stagiaire->description) }}</textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">CV</label>
                        @if ($stagiaire->cv)
                            <div class="flex items-center gap-3 mb-2">
                                <a href="{{ asset('storage/' . $stagiaire->cv) }}" download
                                    class="flex items-center gap-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <i class="fas fa-file-pdf"></i>CV actuel — Télécharger
                                </a>
                            </div>
                        @endif
                        <input type="file" name="cv" accept=".pdf,.doc,.docx"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100 transition">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Mettre à jour
                    </button>
                    <a href="{{ route('admin.stagiaires.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
