@extends('layouts.admin')
@section('titre', 'Nouveau stagiaire')
@section('breadcrumb', 'Stagiaires > Créer')
@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-plus text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Nouveau stagiaire</h2>
                    <p class="text-slate-400 text-sm">Enregistrez un nouveau stagiaire dans le système</p>
                </div>
            </div>

            <form action="{{ route('admin.stagiaires.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-5">
                @csrf
                <!-- Infos compte -->
                <div class="p-5 bg-slate-50 rounded-2xl">
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide mb-4">
                        <i class="fas fa-user mr-1"></i>Compte utilisateur
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Prénom *</label>
                            <input type="text" name="prenom" value="{{ old('prenom') }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nom *</label>
                            <input type="text" name="nom" value="{{ old('nom') }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Téléphone</label>
                            <input type="text" name="telephone" value="{{ old('telephone') }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Mot de passe *</label>
                            <input type="password" name="password" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Confirmation *</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Photo de profil</label>
                        <input type="file" name="photo" accept="image/*"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100 transition">
                    </div>
                </div>

                <!-- Infos stage -->
                <div class="p-5 bg-slate-50 rounded-2xl">
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide mb-4">
                        <i class="fas fa-graduation-cap mr-1"></i>Informations de stage
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">École / Université</label>
                            <input type="text" name="ecole" value="{{ old('ecole') }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Spécialité</label>
                            <input type="text" name="specialite" value="{{ old('specialite') }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Niveau d'étude</label>
                            <input type="text" name="niveau_etude" value="{{ old('niveau_etude') }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm"
                                placeholder="Licence, Master, BTS...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Mentor / Tuteur</label>
                            <select name="mentor_id"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                                <option value="">-- Assigner plus tard --</option>
                                @foreach ($mentors as $mentor)
                                    <option value="{{ $mentor->user_id }}"
                                        {{ old('mentor_id') == $mentor->user_id ? 'selected' : '' }}>
                                        {{ $mentor->user->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Date de début *</label>
                            <input type="date" name="date_debut" value="{{ old('date_debut') }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Date de fin *</label>
                            <input type="date" name="date_fin" value="{{ old('date_fin') }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description / Notes</label>
                        <textarea name="description" rows="3"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm resize-none">{{ old('description') }}</textarea>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">CV (PDF, DOC)</label>
                        <input type="file" name="cv" accept=".pdf,.doc,.docx"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100 transition">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Enregistrer le stagiaire
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
