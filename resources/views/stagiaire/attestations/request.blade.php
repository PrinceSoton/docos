@extends('layouts.stagiaire')
@section('titre', "Demande d'attestation")
@section('breadcrumb', 'Attestations > Demande')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;

        // Carte d'option (radio) : l'entrée réelle est masquée visuellement mais reste focusable ;
        // l'état sélectionné/focus/désactivé est porté par les classes "peer-*".
        // Non sélectionnée : blanche, bordure brand-100 ; sélectionnée : border-brand-600 bg-brand-50 ;
        // désactivée : fond slate-50, texte inchangé (lisible).
        $carte = 'block h-full rounded-lg border border-brand-100 bg-white p-4 pr-11 cursor-pointer transition-colors hover:border-brand-200 peer-checked:border-brand-600 peer-checked:bg-brand-50 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-600 peer-focus-visible:ring-offset-2 peer-disabled:cursor-not-allowed peer-disabled:border-slate-200 peer-disabled:bg-slate-50 peer-disabled:hover:border-slate-200';
        $pastille = 'pointer-events-none absolute right-4 top-4 h-4 w-4 rounded-full border border-slate-400 bg-white peer-checked:border-[5px] peer-checked:border-brand-600 peer-disabled:border-slate-300 peer-disabled:bg-slate-100';
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <h3 class="text-base font-semibold text-slate-900">Demande officielle</h3>
            <p class="mt-0.5 text-sm text-slate-500">Attestation de stage ou convention de stage</p>

            <!-- Processus (bloc teinté) -->
            <div class="mt-5 rounded-lg bg-brand-50 border border-brand-100 p-4">
                <p class="text-xs font-medium text-slate-900">Processus de validation</p>
                <ol class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-1.5 sm:gap-3 text-sm text-slate-600">
                    <li><span class="font-medium text-slate-900 tabular-nums">1.</span> Votre demande</li>
                    <li><span class="font-medium text-slate-900 tabular-nums">2.</span> Validation mentor</li>
                    <li><span class="font-medium text-slate-900 tabular-nums">3.</span> Envoi admin</li>
                </ol>
            </div>

            <form action="{{ route('stagiaire.attestations.store') }}" method="POST" class="mt-6 space-y-6">
                @csrf
                <!-- Choix du type -->
                <fieldset>
                    <legend class="block text-sm font-medium text-slate-700 mb-2">Type de document <span class="text-red-700" aria-hidden="true">*</span></legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="relative">
                            <input type="radio" id="type_attestation" name="type" value="attestation"
                                {{ $attestationExiste ? 'disabled' : '' }} @checked(old('type') === 'attestation') class="peer sr-only" required>
                            <span class="{{ $pastille }}" aria-hidden="true"></span>
                            <label for="type_attestation" class="{{ $carte }}">
                                <span class="block text-sm font-medium text-slate-900">Attestation de stage</span>
                                @if ($attestationExiste)
                                    <span class="mt-1.5 inline-block text-xs font-medium px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800">Déjà demandée</span>
                                @else
                                    <span class="mt-0.5 block text-xs text-slate-600">Atteste votre présence en stage</span>
                                @endif
                            </label>
                        </div>
                        <div class="relative">
                            <input type="radio" id="type_convention" name="type" value="convention"
                                {{ $conventionExiste ? 'disabled' : '' }} @checked(old('type') === 'convention') class="peer sr-only">
                            <span class="{{ $pastille }}" aria-hidden="true"></span>
                            <label for="type_convention" class="{{ $carte }}">
                                <span class="block text-sm font-medium text-slate-900">Convention de stage</span>
                                @if ($conventionExiste)
                                    <span class="mt-1.5 inline-block text-xs font-medium px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800">Déjà demandée</span>
                                @else
                                    <span class="mt-0.5 block text-xs text-slate-600">Document officiel de convention</span>
                                @endif
                            </label>
                        </div>
                    </div>
                    @error('type')
                        <p class="mt-2 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                    @if ($attestationExiste && $conventionExiste)
                        <div class="mt-4 rounded-lg bg-accent-50 border border-accent-100 p-4 text-sm font-medium text-accent-700">
                            Vous avez déjà effectué toutes vos demandes
                        </div>
                    @endif
                </fieldset>

                @if (!$attestationExiste || !$conventionExiste)
                    <!-- Infos stagiaire (bloc teinté) -->
                    <section aria-labelledby="infos-stage" class="rounded-lg bg-brand-50 border border-brand-100 p-4">
                        <h4 id="infos-stage" class="text-xs font-medium text-slate-900">Vos informations de stage</h4>
                        <dl class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="min-w-0">
                                <dt class="text-xs text-slate-600">Matricule</dt>
                                <dd class="mt-0.5 text-sm font-medium text-slate-900 font-mono break-words">{{ $stagiaire->matricule }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-xs text-slate-600">École</dt>
                                <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">{{ $stagiaire->ecole ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-600">Début du stage</dt>
                                <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">{{ $stagiaire->date_debut->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-600">Fin du stage</dt>
                                <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">{{ $stagiaire->date_fin->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </section>

                    <div>
                        <label for="motif_demande" class="block text-sm font-medium text-slate-700 mb-1">Motif de la demande <span
                                class="text-slate-500 font-normal">(optionnel)</span></label>
                        <textarea id="motif_demande" name="motif_demande" rows="3" aria-describedby="motif_aide"
                            class="{{ $champ }} resize-none"
                            placeholder="Expliquez pourquoi vous avez besoin de ce document...">{{ old('motif_demande') }}</textarea>
                        @error('motif_demande')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                        <p id="motif_aide" class="mt-1 text-xs text-slate-500">500 caractères maximum.</p>
                    </div>

                    <p class="rounded-lg bg-brand-50 border border-brand-100 p-4 text-sm text-slate-700">
                        <strong class="font-semibold text-slate-900">Important :</strong> Une seule demande par type de document est autorisée.
                    </p>

                    <button type="submit" class="{{ $btnPrimaire }} w-full sm:w-auto">
                        <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>Soumettre ma demande
                    </button>
                @endif
            </form>

            <div class="mt-6 pt-4 border-t border-brand-100">
                <a href="{{ route('stagiaire.attestations.index') }}" class="{{ $lien }} inline-flex items-center gap-2">
                    <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Voir mes demandes
                </a>
            </div>
        </div>
    </div>
@endsection
