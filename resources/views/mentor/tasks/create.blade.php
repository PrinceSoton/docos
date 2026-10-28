@extends('layouts.mentor')
@section('titre', 'Nouvelle tâche')
@section('breadcrumb', 'Tâches > Créer')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <p class="text-sm text-slate-500 mb-5">Créez une tâche liée à un projet ou indépendante.</p>

            <form action="{{ route('mentor.tasks.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Affectation -->
                <div class="rounded-lg bg-brand-50 border border-brand-100 p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="projetSelect" class="block text-sm font-medium text-slate-700 mb-1">
                            Projet <span class="text-xs font-normal text-slate-600">(optionnel)</span>
                        </label>
                        <select name="project_id" id="projetSelect" class="{{ $champ }}">
                            <option value="">-- Aucun projet (tâche indépendante) --</option>
                            @foreach ($projets as $p)
                                <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->titre }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-600">
                            Laissez vide pour créer une tâche indépendante adressée à vos stagiaires.
                        </p>
                        @error('project_id')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="stagiaireSelect" class="block text-sm font-medium text-slate-700 mb-1">Stagiaire *</label>
                        <select name="stagiaire_id" id="stagiaireSelect" required class="{{ $champ }}">
                            <option value="">-- Sélectionner un stagiaire --</option>
                            @foreach ($stagiaires as $stag)
                                <option value="{{ $stag->id }}"
                                    {{ old('stagiaire_id') == $stag->id ? 'selected' : '' }}>
                                    {{ $stag->user->nom_complet }} — {{ $stag->matricule }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-600">La liste s'adapte au projet choisi.</p>
                        @error('stagiaire_id')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="titre" class="block text-sm font-medium text-slate-700 mb-1">Titre de la tâche *</label>
                    <input type="text" id="titre" name="titre" value="{{ old('titre') }}" required class="{{ $champ }}">
                    @error('titre')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3" class="{{ $champ }} resize-y">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label for="statut" class="block text-sm font-medium text-slate-700 mb-1">Statut *</label>
                        <select id="statut" name="statut" required class="{{ $champ }}">
                            <option value="a_faire" {{ old('statut', 'a_faire') === 'a_faire' ? 'selected' : '' }}>À faire</option>
                            <option value="en_cours" {{ old('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="termine" {{ old('statut') === 'termine' ? 'selected' : '' }}>Terminé</option>
                        </select>
                        @error('statut')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="priorite" class="block text-sm font-medium text-slate-700 mb-1">Priorité *</label>
                        <select id="priorite" name="priorite" required class="{{ $champ }}">
                            @foreach (['faible' => 'Faible', 'normale' => 'Normale', 'haute' => 'Haute', 'urgente' => 'Urgente'] as $v => $l)
                                <option value="{{ $v }}" {{ old('priorite', 'normale') === $v ? 'selected' : '' }}>{{ $l }}</option>
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
                                <option value="{{ $v }}" {{ old('difficulte', 'moyen') === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('difficulte')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_echeance" class="block text-sm font-medium text-slate-700 mb-1">Échéance</label>
                        <input type="date" id="date_echeance" name="date_echeance" value="{{ old('date_echeance') }}"
                            class="{{ $champ }} tabular-nums">
                        @error('date_echeance')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
                    <a href="{{ route('mentor.tasks.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Créer la tâche
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('mentor.tasks.stagiaire-select-script', ['projectSelectId' => 'projetSelect', 'stagiaireSelectId' => 'stagiaireSelect'])
