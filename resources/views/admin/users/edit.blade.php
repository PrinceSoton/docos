@extends('layouts.admin')
@section('titre', 'Modifier utilisateur')
@section('breadcrumb', 'Utilisateurs > Modifier')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-edit text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Modifier : {{ $user->nom_complet }}</h2>
                    <p class="text-slate-400 text-sm">Mettre à jour les informations</p>
                </div>
            </div>
            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data"
                class="space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Prénom *</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nom *</label>
                        <input type="text" name="nom" value="{{ old('nom', $user->nom) }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Rôle *</label>
                        <select name="role" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrateur</option>
                            <option value="mentor" {{ $user->role === 'mentor' ? 'selected' : '' }}>Mentor</option>
                            <option value="stagiaire" {{ $user->role === 'stagiaire' ? 'selected' : '' }}>Stagiaire</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3 mt-6">
                        <input type="checkbox" name="actif" id="actif" value="1"
                            {{ $user->actif ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
                        <label for="actif" class="text-sm font-medium text-slate-700">Compte actif</label>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nouveau mot de passe</label>
                        <input type="password" name="password"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm"
                            placeholder="Laisser vide pour ne pas changer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Confirmation</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Photo</label>
                    @if ($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" class="w-12 h-12 rounded-xl object-cover mb-2">
                    @endif
                    <input type="file" name="photo" accept="image/*"
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Mettre à jour
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
