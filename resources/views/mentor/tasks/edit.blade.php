@extends('layouts.mentor')
@section('titre', 'Modifier tâche')
@section('breadcrumb', 'Tâches > Modifier')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Modifier la tâche</h2>
                    <p class="text-slate-400 text-sm truncate">{{ $task->titre }}</p>
                </div>
            </div>
            <form action="{{ route('mentor.tasks.update', $task) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Titre *</label>
                    <input type="text" name="titre" value="{{ old('titre', $task->titre) }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm resize-none">{{ old('description', $task->description) }}</textarea>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Statut *</label>
                        <select name="statut" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                            @foreach (['a_faire' => 'À faire', 'en_cours' => 'En cours', 'termine' => 'Terminé'] as $v => $l)
                                <option value="{{ $v }}" {{ $task->statut === $v ? 'selected' : '' }}>
                                    {{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Priorité *</label>
                        <select name="priorite" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                            @foreach (['faible' => 'Faible', 'normale' => 'Normale', 'haute' => 'Haute', 'urgente' => 'Urgente'] as $v => $l)
                                <option value="{{ $v }}" {{ $task->priorite === $v ? 'selected' : '' }}>
                                    {{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Difficulté *</label>
                        <select name="difficulte" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                            @foreach (['facile' => 'Facile', 'moyen' => 'Moyen', 'difficile' => 'Difficile'] as $v => $l)
                                <option value="{{ $v }}" {{ $task->difficulte === $v ? 'selected' : '' }}>
                                    {{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Échéance</label>
                        <input type="date" name="date_echeance"
                            value="{{ old('date_echeance', $task->date_echeance?->format('Y-m-d')) }}"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                    </div>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl text-sm text-slate-500">
                    <p><i class="fas fa-project-diagram mr-2 text-emerald-500"></i><strong>Projet :</strong>
                        {{ $task->project->titre }}</p>
                    <p class="mt-1"><i class="fas fa-user-graduate mr-2 text-indigo-500"></i><strong>Stagiaire :</strong>
                        {{ $task->stagiaire->user->nom_complet ?? '—' }}</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Mettre à jour
                    </button>
                    <a href="{{ route('mentor.tasks.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
