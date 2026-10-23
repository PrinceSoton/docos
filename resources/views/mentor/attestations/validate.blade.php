@extends('layouts.mentor')
@section('titre', 'Valider la demande')
@section('breadcrumb', 'Attestations > Validation')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $choix = 'flex items-start gap-3 rounded-lg border border-slate-300 bg-white p-4 cursor-pointer transition-colors hover:border-brand-600 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand-600 has-[:focus-visible]:ring-offset-2';
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">

        <!-- Résumé de la demande (bloc de synthèse) -->
        <section class="rounded-xl bg-brand-50 border border-brand-100 p-5 sm:p-6">
            <p class="text-xs text-slate-600">Demande à traiter</p>
            <h2 class="mt-0.5 text-base font-semibold text-slate-900 break-words">
                <span class="capitalize">{{ $attestation->type }}</span> — {{ $attestation->stagiaire->user->nom_complet ?? '—' }}
            </h2>
            <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                <div class="min-w-0">
                    <dt class="text-xs text-slate-600">Stagiaire</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">{{ $attestation->stagiaire->user->nom_complet ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-600">Matricule</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">{{ $attestation->stagiaire->matricule }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-600">Type de document</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 capitalize">{{ $attestation->type }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-600">Demandé le</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">{{ $attestation->created_at->format('d/m/Y') }}</dd>
                </div>
                @if ($attestation->motif_demande)
                    <div class="sm:col-span-2 pt-3 border-t border-brand-100">
                        <dt class="text-xs text-slate-600">Motif du stagiaire</dt>
                        <dd class="mt-0.5 text-sm text-slate-900 break-words whitespace-pre-line">{{ $attestation->motif_demande }}</dd>
                    </div>
                @endif
            </dl>
        </section>

        <!-- Formulaire de décision -->
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <h3 class="text-base font-semibold text-slate-900">Votre décision</h3>
            <p class="mt-1 text-sm text-slate-500">Une demande validée est transmise à l'administration ; une demande refusée est notifiée au stagiaire.</p>

            <form action="{{ route('mentor.attestations.doValidate', $attestation) }}" method="POST" class="mt-5 space-y-6">
                @csrf @method('PUT')

                <fieldset>
                    <legend class="block text-sm font-medium text-slate-700 mb-2">Décision <span class="text-red-700" aria-hidden="true">*</span></legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="{{ $choix }} has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50" id="labelValide">
                            <input type="radio" name="statut" value="valide_mentor"
                                {{ old('statut') === 'valide_mentor' ? 'checked' : '' }} class="sr-only" required
                                id="radioValide">
                            <span class="mt-1.5 h-2 w-2 rounded-full bg-emerald-500 flex-shrink-0" aria-hidden="true"></span>
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-slate-900">Valider</span>
                                <span class="block mt-0.5 text-xs text-slate-600">Transmettre à l'administration</span>
                            </span>
                        </label>
                        <label class="{{ $choix }} has-[:checked]:border-red-600 has-[:checked]:bg-red-50" id="labelRefuse">
                            <input type="radio" name="statut" value="refuse"
                                {{ old('statut') === 'refuse' ? 'checked' : '' }} class="sr-only" id="radioRefuse">
                            <span class="mt-1.5 h-2 w-2 rounded-full bg-red-500 flex-shrink-0" aria-hidden="true"></span>
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-slate-900">Refuser</span>
                                <span class="block mt-0.5 text-xs text-slate-600">Notifier le stagiaire</span>
                            </span>
                        </label>
                    </div>
                    @error('statut')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div>
                    <label for="commentaire" class="block text-sm font-medium text-slate-700 mb-1">Commentaire <span class="font-normal text-slate-500">(optionnel)</span></label>
                    <textarea id="commentaire" name="commentaire" rows="4" class="{{ $champ }} resize-y"
                        placeholder="Expliquez votre décision au stagiaire...">{{ old('commentaire') }}</textarea>
                    @error('commentaire')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-brand-100">
                    <a href="{{ route('mentor.attestations.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-check text-xs" aria-hidden="true"></i>Confirmer ma décision
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
