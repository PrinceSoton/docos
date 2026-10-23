@extends('layouts.mentor')
@section('titre', 'Modifier tâche')
@section('breadcrumb', 'Tâches > Modifier')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <p class="text-sm text-slate-500 mb-5 truncate">Tâche : <span class="font-medium text-slate-900">{{ $task->titre }}</span></p>

            <form action="{{ route('mentor.tasks.update', $task) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')

                <!-- Contexte actuel -->
                <dl class="rounded-lg bg-brand-50 border border-brand-100 p-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div class="min-w-0">
                        <dt class="text-xs text-slate-600">Rattachement</dt>
                        <dd class="mt-0.5 font-medium text-slate-900 break-words">
                            @if ($task->project)
                                Projet : {{ $task->project->titre }}
                            @else
                                Tâche indépendante <span class="font-normal text-slate-600">(non rattachée à un projet)</span>
                            @endif
                        </dd>
                    </div>
                    <div class="min-w-0">
                        <dt class="text-xs text-slate-600">Stagiaire</dt>
                        <dd class="mt-0.5 font-medium text-slate-900 break-words">{{ $task->stagiaire->user->nom_complet ?? '—' }}</dd>
                    </div>
                </dl>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Projet <span class="text-xs font-normal text-slate-500">(optionnel)</span>
                        </label>
                        <select id="project_id" name="project_id" required class="{{ $champ }}">
                            <option value="">-- Aucun projet (tâche indépendante) --</option>
                            @foreach ($projets as $p)
                                <option value="{{ $p->id }}"
                                    {{ old('project_id', $task->project_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->titre }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="stagiaire_id" class="block text-sm font-medium text-slate-700 mb-1">Stagiaire *</label>
                        <select id="stagiaire_id" name="stagiaire_id" required class="{{ $champ }}">
                            @foreach ($stagiaires as $stag)
                                <option value="{{ $stag->id }}"
                                    {{ old('stagiaire_id', $task->stagiaire_id) == $stag->id ? 'selected' : '' }}>
                                    {{ $stag->user->nom_complet }} — {{ $stag->matricule }}
                                </option>
                            @endforeach
                        </select>
                        @error('stagiaire_id')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="titre" class="block text-sm font-medium text-slate-700 mb-1">Titre *</label>
                    <input type="text" id="titre" name="titre" value="{{ old('titre', $task->titre) }}" required
                        class="{{ $champ }}">
                    @error('titre')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3" class="{{ $champ }} resize-y">{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="statut" class="block text-sm font-medium text-slate-700 mb-1">Statut *</label>
                        <select id="statut" name="statut" required class="{{ $champ }}">
                            @foreach (['a_faire' => 'À faire', 'en_cours' => 'En cours', 'termine' => 'Terminé'] as $v => $l)
                                <option value="{{ $v }}" {{ old('statut', $task->statut) === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('statut')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="priorite" class="block text-sm font-medium text-slate-700 mb-1">Priorité *</label>
                        <select id="priorite" name="priorite" required class="{{ $champ }}">
                            @foreach (['faible' => 'Faible', 'normale' => 'Normale', 'haute' => 'Haute', 'urgente' => 'Urgente'] as $v => $l)
                                <option value="{{ $v }}" {{ old('priorite', $task->priorite) === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('priorite')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="difficulte" class="block text-sm font-medium text-slate-700 mb-1">Difficulté *</label>
                        <select id="difficulte" name="difficulte" required class="{{ $champ }}">
                            @foreach (['facile' => 'Facile', 'moyen' => 'Moyen', 'difficile' => 'Difficile'] as $v => $l)
                                <option value="{{ $v }}" {{ old('difficulte', $task->difficulte) === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('difficulte')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_echeance" class="block text-sm font-medium text-slate-700 mb-1">Échéance</label>
                        <input type="date" id="date_echeance" name="date_echeance"
                            value="{{ old('date_echeance', $task->date_echeance?->format('Y-m-d')) }}"
                            class="{{ $champ }} tabular-nums">
                        @error('date_echeance')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
                    <a href="{{ route('mentor.tasks.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
