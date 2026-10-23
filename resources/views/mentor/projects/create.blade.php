@extends('layouts.mentor')
@section('titre', 'Nouveau projet')
@section('breadcrumb', 'Projets > Créer')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $case = 'h-4 w-4 flex-shrink-0 rounded border-slate-300 accent-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <p class="text-sm text-slate-500 mb-5">Créez et assignez un projet à vos stagiaires.</p>

            <form action="{{ route('mentor.projects.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="titre" class="block text-sm font-medium text-slate-700 mb-1">Titre du projet *</label>
                    <input type="text" id="titre" name="titre" value="{{ old('titre') }}" required class="{{ $champ }}">
                    @error('titre')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" class="{{ $champ }} resize-y">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="statut" class="block text-sm font-medium text-slate-700 mb-1">Statut *</label>
                        <select id="statut" name="statut" required class="{{ $champ }}">
                            @foreach (['en_attente' => 'En attente', 'en_cours' => 'En cours', 'termine' => 'Terminé', 'suspendu' => 'Suspendu'] as $v => $l)
                                <option value="{{ $v }}" {{ old('statut') === $v ? 'selected' : '' }}>
                                    {{ $l }}</option>
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
                                <option value="{{ $v }}"
                                    {{ old('priorite', 'normale') === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('priorite')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-slate-700 mb-1">Date de début *</label>
                        <input type="date" id="date_debut" name="date_debut" value="{{ old('date_debut', date('Y-m-d')) }}" required
                            class="{{ $champ }} tabular-nums">
                        @error('date_debut')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-slate-700 mb-1">Date de fin</label>
                        <input type="date" id="date_fin" name="date_fin" value="{{ old('date_fin') }}"
                            class="{{ $champ }} tabular-nums">
                        @error('date_fin')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Stagiaires -->
                <fieldset>
                    <legend class="block text-sm font-medium text-slate-700 mb-1">Assigner des stagiaires * (vos stagiaires)</legend>
                    <div class="max-h-56 overflow-y-auto rounded-lg border border-slate-300 bg-white divide-y divide-slate-100">
                        @forelse ($stagiaires as $stag)
                            <label class="flex items-center gap-3 px-3 py-2.5 hover:bg-brand-50 transition-colors cursor-pointer">
                                <input type="checkbox" name="stagiaires[]" value="{{ $stag->id }}"
                                    {{ in_array($stag->id, (array) old('stagiaires', [])) ? 'checked' : '' }}
                                    class="{{ $case }}">
                                <span class="min-w-0 flex flex-wrap items-baseline gap-x-2">
                                    <span class="text-sm font-medium text-slate-900">{{ $stag->user->nom_complet }}</span>
                                    <span class="text-xs text-slate-500 tabular-nums">{{ $stag->matricule }}</span>
                                </span>
                            </label>
                        @empty
                            <p class="px-3 py-4 text-sm text-slate-500 text-center">Aucun stagiaire assigné</p>
                        @endforelse
                    </div>
                    @error('stagiaires')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Collaborateurs (invitations) -->
                <fieldset class="rounded-lg bg-brand-50 border border-brand-100 p-4">
                    <legend class="sr-only">Inviter des mentors à collaborer</legend>
                    <p class="text-sm font-medium text-slate-900">Inviter des mentors à collaborer (optionnel)</p>
                    <p class="mt-1 mb-3 text-xs text-slate-600">
                        Les mentors invités auront les mêmes droits sur ce projet : ajouter leurs stagiaires, créer,
                        modifier et supprimer des tâches.
                    </p>

                    <div class="max-h-56 overflow-y-auto rounded-lg border border-brand-100 bg-white divide-y divide-slate-100">
                        @forelse ($autresMentors as $m)
                            <label class="flex items-center gap-3 px-3 py-2.5 hover:bg-brand-50 transition-colors cursor-pointer">
                                <input type="checkbox" name="collaborateurs[]" value="{{ $m->id }}"
                                    {{ in_array($m->id, (array) old('collaborateurs', [])) ? 'checked' : '' }}
                                    class="{{ $case }}">
                                <span class="text-sm font-medium text-slate-900">{{ $m->nom_complet }}</span>
                            </label>
                        @empty
                            <p class="px-3 py-4 text-sm text-slate-500 text-center">Aucun autre mentor disponible</p>
                        @endforelse
                    </div>
                    @error('collaborateurs')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
                    <a href="{{ route('mentor.projects.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Créer le projet
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
