@extends('layouts.mentor')
@section('titre', 'Nouvelle tâche')
@section('breadcrumb', 'Tâches > Créer')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-plus-circle text-emerald-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Nouvelle tâche</h2>
                    <p class="text-slate-400 text-sm">Découpez votre projet en sous-tâches</p>
                </div>
            </div>
            <form action="{{ route('mentor.tasks.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Projet *</label>
                        <select name="project_id" required id="projetSelect"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm">
                            <option value="">Sélectionner un projet</option>
                            @foreach ($projets as $p)
                                <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->titre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Stagiaire *</label>
                        <select name="stagiaire_id" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm">
                            <option value="">Sélectionner un stagiaire</option>
                            @foreach ($stagiaires as $stag)
                                <option value="{{ $stag->id }}"
                                    {{ old('stagiaire_id') == $stag->id ? 'selected' : '' }}>
                                    {{ $stag->user->nom_complet }} — {{ $stag->matricule }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Titre de la tâche *</label>
                    <input type="text" name="titre" value="{{ old('titre') }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Statut *</label>
                        <select name="statut" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm">
                            <option value="a_faire" {{ old('statut', 'a_faire') === 'a_faire' ? 'selected' : '' }}>À faire
                            </option>
                            <option value="en_cours" {{ old('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="termine" {{ old('statut') === 'termine' ? 'selected' : '' }}>Terminé</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Priorité *</label>
                        <select name="priorite" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm">
                            @foreach (['faible' => 'Faible', 'normale' => 'Normale', 'haute' => 'Haute', 'urgente' => 'Urgente'] as $v => $l)
                                <option value="{{ $v }}"
                                    {{ old('priorite', 'normale') === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Difficulté *</label>
                        <select name="difficulte" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm">
                            @foreach (['facile' => 'Facile', 'moyen' => 'Moyen', 'difficile' => 'Difficile'] as $v => $l)
                                <option value="{{ $v }}"
                                    {{ old('difficulte', 'moyen') === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Échéance</label>
                        <input type="date" name="date_echeance" value="{{ old('date_echeance') }}"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Créer la tâche
                    </button>
                    <a href="{{ route('mentor.tasks.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
