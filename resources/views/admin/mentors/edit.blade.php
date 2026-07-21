@extends('layouts.admin')
@section('titre', 'Modifier mentor')
@section('breadcrumb', 'Mentors > Modifier')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-edit text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Modifier : {{ $mentor->user->nom_complet }}</h2>
                    <p class="text-slate-400 text-sm">Mettre à jour les informations du mentor</p>
                </div>
            </div>

            <form action="{{ route('admin.mentors.update', $mentor) }}" method="POST" enctype="multipart/form-data"
                class="space-y-5">
                @csrf @method('PUT')

                <div class="p-4 bg-slate-50 rounded-2xl">
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide mb-3">Informations personnelles
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Prénom *</label>
                            <input type="text" name="prenom" value="{{ old('prenom', $mentor->user->prenom) }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nom *</label>
                            <input type="text" name="nom" value="{{ old('nom', $mentor->user->nom) }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
                            <input type="email" name="email" value="{{ old('email', $mentor->user->email) }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Téléphone</label>
                            <input type="text" name="telephone" value="{{ old('telephone', $mentor->user->telephone) }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nouveau mot de passe</label>
                            <input type="password" name="password"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm"
                                placeholder="Laisser vide pour conserver">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Confirmation</label>
                            <input type="password" name="password_confirmation"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Photo</label>
                        @if ($mentor->user->photo)
                            <img src="{{ asset('storage/' . $mentor->user->photo) }}"
                                class="w-12 h-12 rounded-xl object-cover mb-2 border-2 border-white shadow">
                        @endif
                        <input type="file" name="photo" accept="image/*"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-amber-50 file:text-amber-700 file:font-medium hover:file:bg-amber-100 transition">
                    </div>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl">
                    <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide mb-3">Informations
                        professionnelles</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Département</label>
                            <input type="text" name="departement" value="{{ old('departement', $mentor->departement) }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Poste</label>
                            <input type="text" name="poste" value="{{ old('poste', $mentor->poste) }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Biographie</label>
                        <textarea name="bio" rows="3"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm resize-none">{{ old('bio', $mentor->bio) }}</textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Mettre à jour
                    </button>
                    <a href="{{ route('admin.mentors.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
