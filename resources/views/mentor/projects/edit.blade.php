@extends('layouts.mentor')
@section('titre', 'Modifier projet')
@section('breadcrumb', 'Projets > Modifier')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Modifier le projet</h2>
                    <p class="text-slate-400 text-sm truncate">{{ $project->titre }}</p>
                </div>
            </div>
            <form action="{{ route('mentor.projects.update', $project) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Titre *</label>
                    <input type="text" name="titre" value="{{ old('titre', $project->titre) }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm resize-none">{{ old('description', $project->description) }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Statut *</label>
                        <select name="statut" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                            @foreach (['en_attente' => 'En attente', 'en_cours' => 'En cours', 'termine' => 'Terminé', 'suspendu' => 'Suspendu'] as $val => $label)
                                <option value="{{ $val }}" {{ $project->statut === $val ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Priorité *</label>
                        <select name="priorite" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                            @foreach (['faible' => 'Faible', 'normale' => 'Normale', 'haute' => 'Haute', 'urgente' => 'Urgente'] as $val => $label)
                                <option value="{{ $val }}" {{ $project->priorite === $val ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date de début *</label>
                        <input type="date" name="date_debut"
                            value="{{ old('date_debut', $project->date_debut->format('Y-m-d')) }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date de fin</label>
                        <input type="date" name="date_fin"
                            value="{{ old('date_fin', $project->date_fin?->format('Y-m-d')) }}"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                    </div>
                </div>
                <!-- Stagiaires -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-user-graduate mr-1 text-emerald-500"></i>Stagiaires assignés *
                    </label>
                    <div class="space-y-2 max-h-48 overflow-y-auto p-3 bg-slate-50 rounded-xl border border-slate-200">
                        @foreach ($stagiaires as $stag)
                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition cursor-pointer">
                                <input type="checkbox" name="stagiaires[]" value="{{ $stag->id }}"
                                    {{ $project->stagiaires->contains($stag->id) ? 'checked' : '' }}
                                    class="w-4 h-4 text-emerald-600 rounded">
                                <span class="text-slate-700 text-sm font-medium">{{ $stag->user->nom_complet }}</span>
                                <span class="text-slate-400 text-xs">{{ $stag->matricule }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Mettre à jour
                    </button>
                    <a href="{{ route('mentor.projects.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
