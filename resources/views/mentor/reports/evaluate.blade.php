@extends('layouts.mentor')
@section('titre', 'Évaluer le rapport')
@section('breadcrumb', 'Rapports > Évaluation')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;

        // Choix de décision : [libellé, aide, pastille, classes à l'état coché]
        $decisions = [
            'valide' => ['Valider', 'Le rapport est accepté', 'bg-emerald-500', 'has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50'],
            'en_revision' => ['Révision', 'Le stagiaire doit le reprendre', 'bg-sky-500', 'has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50'],
            'rejete' => ['Rejeter', 'Le rapport est refusé', 'bg-red-500', 'has-[:checked]:border-red-600 has-[:checked]:bg-red-50'],
        ];
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">

        <!-- Rapport évalué (bloc de synthèse) -->
        <section class="rounded-xl bg-brand-50 border border-brand-100 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs text-slate-600">Rapport</p>
                    <h2 class="mt-0.5 text-base font-semibold text-slate-900 break-words">{{ $report->titre }}</h2>
                </div>
                <a href="{{ route('mentor.reports.telecharger', $report) }}" download class="{{ $btnSecondaire }} flex-shrink-0">
                    <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger
                </a>
            </div>
            <dl class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-3">
                <div class="min-w-0">
                    <dt class="text-xs text-slate-600">Stagiaire</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">{{ $report->stagiaire->user->nom_complet ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-600">Type</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 capitalize">{{ $report->type_affiche }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-600">Déposé le</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">{{ $report->created_at->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </section>

        <!-- Formulaire d'évaluation -->
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <h3 class="text-base font-semibold text-slate-900">Votre évaluation</h3>
            <p class="mt-1 text-sm text-slate-500">Le stagiaire verra votre décision, la note et votre commentaire.</p>

            <form action="{{ route('mentor.reports.doEvaluate', $report) }}" method="POST" class="mt-5 space-y-6">
                @csrf @method('PUT')

                <fieldset>
                    <legend class="block text-sm font-medium text-slate-700 mb-2">Décision <span class="text-red-700" aria-hidden="true">*</span></legend>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach ($decisions as $val => [$label, $aide, $pastille, $coche])
                            <label
                                class="flex items-start gap-3 rounded-lg border border-slate-300 bg-white p-4 cursor-pointer transition-colors hover:border-brand-600 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand-600 has-[:focus-visible]:ring-offset-2 {{ $coche }}">
                                <input type="radio" name="statut" value="{{ $val }}"
                                    {{ old('statut') === $val ? 'checked' : '' }} class="sr-only" required>
                                <span class="mt-1.5 h-2 w-2 rounded-full flex-shrink-0 {{ $pastille }}" aria-hidden="true"></span>
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-slate-900">{{ $label }}</span>
                                    <span class="block mt-0.5 text-xs text-slate-600">{{ $aide }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('statut')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div>
                    <label for="noteRange" class="block text-sm font-medium text-slate-700 mb-1">Note sur 20 <span class="font-normal text-slate-500">(optionnel)</span></label>
                    <div class="flex items-center gap-4 rounded-lg bg-brand-50 border border-brand-100 px-4 py-3">
                        <input type="number" name="note" min="0" max="20" step="1"
                            value="{{ old('note', $report->note) }}" id="noteRange"
                            aria-describedby="note-aide"
                            class="w-24 rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 {{ $focus }}">
                        <p id="note-aide" class="text-sm text-slate-600">Sur 20. Laissez vide pour ne pas attribuer de note.</p>
                    </div>
                    @error('note')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="commentaire_mentor" class="block text-sm font-medium text-slate-700 mb-1">Commentaire pour le stagiaire</label>
                    <textarea id="commentaire_mentor" name="commentaire_mentor" rows="5" class="{{ $champ }} resize-y"
                        placeholder="Vos remarques, points positifs, axes d'amélioration...">{{ old('commentaire_mentor', $report->commentaire_mentor) }}</textarea>
                    @error('commentaire_mentor')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-brand-100">
                    <a href="{{ route('mentor.reports.show', $report) }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-check text-xs" aria-hidden="true"></i>Soumettre l'évaluation
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
