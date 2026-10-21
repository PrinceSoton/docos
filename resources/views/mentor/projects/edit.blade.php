@extends('layouts.mentor')
@section('titre', 'Modifier projet')
@section('breadcrumb', 'Projets > Modifier')
@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-0 space-y-5">
        <div class="card p-5 sm:p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Modifier le projet</h2>
                    <p class="text-slate-400 text-sm truncate">{{ $project->titre }}</p>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- FORMULAIRE PRINCIPAL (projet + stagiaires + collaborateurs) --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
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

                {{-- Stagiaires (tous ceux accessibles : créateur + collaborateurs) --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-user-graduate mr-1 text-emerald-500"></i>Stagiaires du projet *
                    </label>
                    <p class="text-xs text-slate-500 mb-2">
                        Vous pouvez ajouter des stagiaires appartenant à n'importe quel mentor du projet.
                    </p>
                    <div class="space-y-2 max-h-56 overflow-y-auto p-3 bg-slate-50 rounded-xl border border-slate-200">
                        @forelse ($stagiaires as $stag)
                            <label
                                class="flex flex-col sm:flex-row items-start sm:items-center gap-3 p-2 rounded-lg hover:bg-white transition cursor-pointer">
                                <input type="checkbox" name="stagiaires[]" value="{{ $stag->id }}"
                                    {{ in_array($stag->id, old('stagiaires', $project->stagiaires->pluck('id')->toArray())) ? 'checked' : '' }}
                                    class="w-4 h-4 text-emerald-600 rounded mt-1 sm:mt-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-emerald-200 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                        {{ strtoupper(substr($stag->user->prenom, 0, 1)) }}
                                    </div>
                                    <span class="text-slate-700 text-sm font-medium">{{ $stag->user->nom_complet }}</span>
                                    <span class="text-slate-400 text-xs font-mono">{{ $stag->matricule }}</span>
                                    @if ($stag->mentor_id && $stag->mentor_id !== Auth::id())
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                                            Mentor : {{ $stag->mentor?->nom_complet }}
                                        </span>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <p class="text-slate-400 text-sm text-center py-3">Aucun stagiaire disponible</p>
                        @endforelse
                    </div>
                </div>

                {{-- Collaborateurs (créateur + autres mentors) --}}
                <div class="p-5 bg-emerald-50 rounded-2xl border border-emerald-100">
                    <label class="block text-xs text-emerald-600 font-semibold uppercase tracking-wide mb-2">
                        <i class="fas fa-users mr-1"></i>Mentors du projet (créateur + collaborateurs)
                    </label>
                    <p class="text-xs text-slate-500 mb-3">
                        Les mentors cochés ont les <strong>mêmes droits</strong> : ajouter/modifier/supprimer des tâches,
                        inviter d'autres mentors, gérer les stagiaires.
                    </p>

                    <div class="space-y-2 max-h-56 overflow-y-auto p-3 bg-white rounded-xl border border-emerald-100">
                        {{-- Créateur : coché et désactivé --}}
                        @php
                            $createur = \App\Models\User::find($project->mentor_id);
                        @endphp
                        @if ($createur)
                            <label class="flex items-center gap-3 p-2 rounded-lg bg-emerald-50/60 cursor-not-allowed">
                                <input type="checkbox" checked disabled
                                    class="w-4 h-4 text-emerald-600 rounded">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-emerald-500 flex items-center justify-center text-white font-bold text-xs">
                                        {{ strtoupper(substr($createur->prenom, 0, 1)) }}
                                    </div>
                                    <span class="text-slate-800 text-sm font-semibold">{{ $createur->nom_complet }}</span>
                                    <span
                                        class="text-xs bg-emerald-200 text-emerald-800 px-2 py-0.5 rounded-full font-semibold">
                                        Créateur
                                    </span>
                                </div>
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
                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-emerald-50 transition cursor-pointer">
                                <input type="checkbox" name="collaborateurs[]" value="{{ $m->id }}"
                                    {{ $isCurrent ? 'checked' : '' }}
                                    class="w-4 h-4 text-emerald-600 rounded">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-emerald-200 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                        {{ strtoupper(substr($m->prenom, 0, 1)) }}
                                    </div>
                                    <span class="text-slate-700 text-sm font-medium">{{ $m->nom_complet }}</span>
                                    @if (!$isCurrent)
                                        <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">
                                            Disponible
                                        </span>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <p class="text-slate-400 text-sm text-center py-3">Aucun autre mentor disponible</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Mettre à jour
                    </button>
                    <a href="{{ route('mentor.projects.index') }}"
                        class="flex-1 sm:flex-none px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">
                        Annuler
                    </a>
                </div>
            </form>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- SECTION INVITATION RAPIDE (hors formulaire principal)      --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            @if ($autresMentors->count() > 0)
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <h3 class="text-slate-800 font-bold text-lg mb-2">
                        <i class="fas fa-user-plus text-emerald-600 mr-2"></i>Invitation rapide
                    </h3>
                    <p class="text-xs text-slate-500 mb-3">
                        Alternative : invitez directement un mentor sans passer par le formulaire de modification.
                    </p>

                    <form action="{{ route('mentor.projects.invite', $project) }}" method="POST"
                        class="flex flex-col sm:flex-row gap-2">
                        @csrf
                        <select name="mentor_id" required
                            class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-400 transition">
                            <option value="">Sélectionner un mentor...</option>
                            @foreach ($autresMentors as $m)
                                <option value="{{ $m->id }}">{{ $m->nom_complet }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-medium transition">
                            <i class="fas fa-paper-plane mr-1"></i>Inviter
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection