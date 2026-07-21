@extends('layouts.mentor')
@section('titre', 'Nouveau projet')
@section('breadcrumb', 'Projets > Créer')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-project-diagram text-emerald-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Nouveau projet</h2>
                    <p class="text-slate-400 text-sm">Créez et assignez un projet à vos stagiaires</p>
                </div>
            </div>
            <form action="{{ route('mentor.projects.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Titre du projet *</label>
                    <input type="text" name="titre" value="{{ old('titre') }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 transition text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Statut *</label>
                        <select name="statut" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm">
                            <option value="en_attente" {{ old('statut') === 'en_attente' ? 'selected' : '' }}>En attente
                            </option>
                            <option value="en_cours" {{ old('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="termine" {{ old('statut') === 'termine' ? 'selected' : '' }}>Terminé</option>
                            <option value="suspendu" {{ old('statut') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Priorité *</label>
                        <select name="priorite" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm">
                            <option value="faible" {{ old('priorite') === 'faible' ? 'selected' : '' }}>Faible</option>
                            <option value="normale" {{ old('priorite', 'normale') === 'normale' ? 'selected' : '' }}>Normale
                            </option>
                            <option value="haute" {{ old('priorite') === 'haute' ? 'selected' : '' }}>Haute</option>
                            <option value="urgente" {{ old('priorite') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date de début *</label>
                        <input type="date" name="date_debut" value="{{ old('date_debut', date('Y-m-d')) }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date de fin</label>
                        <input type="date" name="date_fin" value="{{ old('date_fin') }}"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm">
                    </div>
                </div>

                <!-- Stagiaires -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-user-graduate mr-1 text-emerald-500"></i>Assigner des stagiaires * (un ou
                        plusieurs)
                    </label>
                    <div class="space-y-2 max-h-48 overflow-y-auto p-3 bg-slate-50 rounded-xl border border-slate-200">
                        @foreach ($stagiaires as $stag)
                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition cursor-pointer">
                                <input type="checkbox" name="stagiaires[]" value="{{ $stag->id }}"
                                    {{ in_array($stag->id, (array) old('stagiaires', [])) ? 'checked' : '' }}
                                    class="w-4 h-4 text-emerald-600 rounded">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-emerald-200 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                        {{ strtoupper(substr($stag->user->prenom, 0, 1)) }}
                                    </div>
                                    <span class="text-slate-700 text-sm font-medium">{{ $stag->user->nom_complet }}</span>
                                    <span class="text-slate-400 text-xs">{{ $stag->matricule }}</span>
                                </div>
                            </label>
                        @endforeach
                        @if ($stagiaires->isEmpty())
                            <p class="text-slate-400 text-sm text-center py-3">Aucun stagiaire assigné</p>
                        @endif
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Créer le projet
                    </button>
                    <a href="{{ route('mentor.projects.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
