@extends('layouts.mentor')
@section('titre', 'Modifier projet')
@section('breadcrumb', 'Projets > Modifier')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $case = 'h-4 w-4 flex-shrink-0 rounded border-slate-300 accent-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <p class="text-sm text-slate-500 mb-5 truncate">Projet : <span class="font-medium text-slate-900">{{ $project->titre }}</span></p>

            {{-- FORMULAIRE PRINCIPAL (projet + stagiaires + collaborateurs) --}}
            <form action="{{ route('mentor.projects.update', $project) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label for="titre" class="block text-sm font-medium text-slate-700 mb-1">Titre *</label>
                    <input type="text" id="titre" name="titre" value="{{ old('titre', $project->titre) }}" required
                        class="{{ $champ }}">
                    @error('titre')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" class="{{ $champ }} resize-y">{{ old('description', $project->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="statut" class="block text-sm font-medium text-slate-700 mb-1">Statut *</label>
                        <select id="statut" name="statut" required class="{{ $champ }}">
                            @foreach (['en_attente' => 'En attente', 'en_cours' => 'En cours', 'termine' => 'Terminé', 'suspendu' => 'Suspendu'] as $val => $label)
                                <option value="{{ $val }}" {{ $project->statut === $val ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                        @error('statut')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="priorite" class="block text-sm font-medium text-slate-700 mb-1">Priorité *</label>
                        <select id="priorite" name="priorite" required class="{{ $champ }}">
                            @foreach (['faible' => 'Faible', 'normale' => 'Normale', 'haute' => 'Haute', 'urgente' => 'Urgente'] as $val => $label)
                                <option value="{{ $val }}" {{ $project->priorite === $val ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priorite')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-slate-700 mb-1">Date de début *</label>
                        <input type="date" id="date_debut" name="date_debut"
                            value="{{ old('date_debut', $project->date_debut->format('Y-m-d')) }}" required
                            class="{{ $champ }} tabular-nums">
                        @error('date_debut')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-slate-700 mb-1">Date de fin</label>
                        <input type="date" id="date_fin" name="date_fin"
                            value="{{ old('date_fin', $project->date_fin?->format('Y-m-d')) }}"
                            class="{{ $champ }} tabular-nums">
                        @error('date_fin')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Stagiaires (tous ceux accessibles : créateur + collaborateurs) --}}
                <fieldset>
                    <legend class="block text-sm font-medium text-slate-700">Stagiaires du projet *</legend>
                    <p class="mt-1 mb-2 text-xs text-slate-500">
                        Vous pouvez ajouter des stagiaires appartenant à n'importe quel mentor du projet.
                    </p>
                    <div class="max-h-64 overflow-y-auto rounded-lg border border-slate-300 bg-white divide-y divide-slate-100">
                        @forelse ($stagiaires as $stag)
                            <label class="flex items-center gap-3 px-3 py-2.5 hover:bg-brand-50 transition-colors cursor-pointer">
                                <input type="checkbox" name="stagiaires[]" value="{{ $stag->id }}"
                                    {{ in_array($stag->id, old('stagiaires', $project->stagiaires->pluck('id')->toArray())) ? 'checked' : '' }}
                                    class="{{ $case }}">
                                <span class="min-w-0 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                                    <span class="text-sm font-medium text-slate-900">{{ $stag->user->nom_complet }}</span>
                                    <span class="text-xs text-slate-500 tabular-nums">{{ $stag->matricule }}</span>
                                    @if ($stag->mentor_id && $stag->mentor_id !== Auth::id())
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                            Mentor : {{ $stag->mentor?->nom_complet }}
                                        </span>
                                    @endif
                                </span>
                            </label>
                        @empty
                            <p class="px-3 py-4 text-sm text-slate-500 text-center">Aucun stagiaire disponible</p>
                        @endforelse
                    </div>
                    @error('stagiaires')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </fieldset>

                {{-- Collaborateurs (créateur + autres mentors) --}}
                <fieldset class="rounded-lg bg-brand-50 border border-brand-100 p-4">
                    <legend class="sr-only">Mentors du projet</legend>
                    <p class="text-sm font-medium text-slate-900">Mentors du projet (créateur + collaborateurs)</p>
                    <p class="mt-1 mb-3 text-xs text-slate-600">
                        Les mentors cochés ont les <strong class="font-semibold text-slate-900">mêmes droits</strong> :
                        ajouter/modifier/supprimer des tâches, inviter d'autres mentors, gérer les stagiaires.
                    </p>

                    <div class="max-h-64 overflow-y-auto rounded-lg border border-brand-100 bg-white divide-y divide-slate-100">
                        {{-- Créateur : coché et désactivé --}}
                        @php
                            $createur = \App\Models\User::find($project->mentor_id);
                        @endphp
                        @if ($createur)
                            <label class="flex items-center gap-3 px-3 py-2.5 cursor-not-allowed">
                                <input type="checkbox" checked disabled class="{{ $case }}">
                                <span class="min-w-0 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                                    <span class="text-sm font-medium text-slate-900">{{ $createur->nom_complet }}</span>
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-brand-100 text-brand-800">Créateur</span>
                                </span>
                            </label>
                        @endif

                        {{-- Collaborateurs actuels + disponibles --}}
                        @php
                            $currentCollaborators = $project->mentors()
                                ->where('users.id', '!=', $project->mentor_id)
                                ->orderBy('users.nom')
                                ->get();
                            $allAvailable = $currentCollaborators->concat($autresMentors);
                        @endphp

                        @forelse ($allAvailable as $m)
                            @php
                                $isCurrent = $currentCollaborators->contains('id', $m->id);
                            @endphp
                            <label class="flex items-center gap-3 px-3 py-2.5 hover:bg-brand-50 transition-colors cursor-pointer">
                                <input type="checkbox" name="collaborateurs[]" value="{{ $m->id }}"
                                    {{ $isCurrent ? 'checked' : '' }}
                                    class="{{ $case }}">
                                <span class="min-w-0 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                                    <span class="text-sm font-medium text-slate-900">{{ $m->nom_complet }}</span>
                                    @if (!$isCurrent)
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">Disponible</span>
                                    @endif
                                </span>
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
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>

        {{-- SECTION INVITATION RAPIDE (hors formulaire principal) --}}
        @if ($autresMentors->count() > 0)
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
                <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
                    <h2 class="text-base font-semibold text-slate-900">Invitation rapide</h2>
                </div>
                <div class="p-5 sm:p-6">
                    <p class="text-sm text-slate-500 mb-3">
                        Alternative : invitez directement un mentor sans passer par le formulaire de modification.
                    </p>
                    <form action="{{ route('mentor.projects.invite', $project) }}" method="POST"
                        class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <label for="invite_mentor_id" class="sr-only">Mentor à inviter</label>
                        <select id="invite_mentor_id" name="mentor_id" required class="flex-1 {{ $champ }}">
                            <option value="">Sélectionner un mentor...</option>
                            @foreach ($autresMentors as $m)
                                <option value="{{ $m->id }}">{{ $m->nom_complet }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="{{ $btnPrimaire }}">
                            <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>Inviter
                        </button>
                    </form>
                </div>
            </section>
        @endif
    </div>
@endsection
