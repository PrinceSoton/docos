@extends('layouts.stagiaire')
@section('titre', 'Modifier rapport')
@section('breadcrumb', 'Rapports > Modifier')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Modifier le rapport</h2>
                    <p class="text-slate-400 text-sm truncate">{{ $report->titre }}</p>
                </div>
            </div>
            <form action="{{ route('stagiaire.reports.update', $report) }}" method="POST" enctype="multipart/form-data"
                class="space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Titre *</label>
                    <input type="text" name="titre" value="{{ old('titre', $report->titre) }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Type *</label>
                        <select name="type" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                            @foreach (['journalier' => 'Journalier', 'hebdomadaire' => 'Hebdomadaire', 'mensuel' => 'Mensuel', 'final' => 'Final', 'autre' => 'Autre'] as $v => $l)
                                <option value="{{ $v }}" {{ $report->type === $v ? 'selected' : '' }}>
                                    {{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Projet lié</label>
                        <select name="project_id"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                            <option value="">-- Aucun --</option>
                            @foreach ($projets as $p)
                                <option value="{{ $p->id }}" {{ $report->project_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->titre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm resize-none">{{ old('description', $report->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fichier du rapport</label>
                    <div class="p-3 bg-slate-50 rounded-xl flex items-center gap-3 mb-2">
                        <i class="fas fa-file text-indigo-400"></i>
                        <span class="text-slate-600 text-sm flex-1">Fichier actuel</span>
                        <a href="{{ route('stagiaire.reports.telecharger', $report) }}" download
                            class="text-indigo-600 hover:underline text-xs font-medium">
                            <i class="fas fa-download mr-1"></i>Télécharger
                        </a>
                    </div>
                    <input type="file" name="fichier"
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-amber-50 file:text-amber-700 file:font-medium hover:file:bg-amber-100 transition">
                    <p class="text-xs text-slate-400 mt-1">Laisser vide pour conserver le fichier actuel</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Mettre à jour
                    </button>
                    <a href="{{ route('stagiaire.reports.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
