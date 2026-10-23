@extends('layouts.admin')
@section('titre', 'Affecter un stagiaire')
@section('breadcrumb', 'Mentors > Affectation')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2';
        $bord = fn ($nom) => $errors->has($nom)
            ? 'border-red-600 focus:border-red-600 focus:ring-red-600/25'
            : 'border-slate-300 focus:border-brand-600 focus:ring-brand-600/25';
        $label = 'block text-sm font-medium text-slate-700 mb-1';
        $requis = '<span class="text-red-700" aria-hidden="true">*</span>';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $puce = 'inline-flex max-w-full items-center gap-2 rounded-md border border-brand-100 bg-white py-1 pl-2.5 pr-1';
        $btnRetirer = 'inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md text-slate-500 hover:bg-red-50 hover:text-red-700 transition-colors ' . $focus;
        $aDesAffectations = $mentors->contains(fn ($m) => $m->stagiaires->count() > 0);
    @endphp

    <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-5 gap-5 items-start">

        <!-- Formulaire d'affectation -->
        <section class="lg:col-span-2 bg-white border border-brand-100 rounded-xl p-5 sm:p-6" aria-labelledby="titre-affecter">
            <h2 id="titre-affecter" class="text-base font-semibold text-slate-900">Affecter un stagiaire à un mentor</h2>
            <p class="mt-0.5 text-sm text-slate-500">Établissez le lien mentor–stagiaire. Une nouvelle affectation remplace
                le mentor actuel.</p>

            <form action="{{ route('admin.mentors.doAssign') }}" method="POST" class="mt-5 space-y-5">
                @csrf
                <div>
                    <label for="stagiaire_id" class="{{ $label }}">Stagiaire {!! $requis !!}</label>
                    <select id="stagiaire_id" name="stagiaire_id" required class="{{ $champ }} {{ $bord('stagiaire_id') }}"
                        @error('stagiaire_id') aria-invalid="true" aria-describedby="stagiaire_id-erreur" @enderror>
                        <option value="">-- Sélectionner un stagiaire --</option>
                        @foreach ($stagiaires as $stag)
                            <option value="{{ $stag->id }}" {{ old('stagiaire_id') == $stag->id ? 'selected' : '' }}>
                                {{ $stag->user->nom_complet }} — {{ $stag->matricule }} @if ($stag->mentor_id)
                                    (Actuel : {{ $stag->mentor?->nom_complet }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Seuls les stagiaires en cours de stage sont proposés.</p>
                    @error('stagiaire_id')
                        <p id="stagiaire_id-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mentor_id" class="{{ $label }}">Mentor {!! $requis !!}</label>
                    <select id="mentor_id" name="mentor_id" required class="{{ $champ }} {{ $bord('mentor_id') }}"
                        @error('mentor_id') aria-invalid="true" aria-describedby="mentor_id-erreur" @enderror>
                        <option value="">-- Sélectionner un mentor --</option>
                        @foreach ($mentors as $mentor)
                            <option value="{{ $mentor->user_id }}"
                                {{ old('mentor_id') == $mentor->user_id ? 'selected' : '' }}>
                                {{ $mentor->user->nom_complet }} @if ($mentor->departement)
                                    — {{ $mentor->departement }}
                                @endif ({{ $mentor->stagiaires->count() }} stagiaire(s))</option>
                        @endforeach
                    </select>
                    @error('mentor_id')
                        <p id="mentor_id-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full {{ $btnPrimaire }}">
                    <i class="fas fa-check text-xs" aria-hidden="true"></i>Confirmer l'affectation
                </button>
            </form>
        </section>

        <!-- Affectations actuelles -->
        <section class="lg:col-span-3 bg-white border border-brand-100 rounded-xl overflow-hidden"
            aria-labelledby="titre-affectations">
            <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex items-center justify-between gap-3">
                <h3 id="titre-affectations" class="text-base font-semibold text-slate-900">Affectations actuelles</h3>
                <a href="{{ route('admin.mentors.index') }}" class="{{ $lien }} whitespace-nowrap">Voir les mentors</a>
            </div>

            @if ($aDesAffectations)
                <ul class="divide-y divide-slate-100">
                    @foreach ($mentors as $mentor)
                        @if ($mentor->stagiaires->count() > 0)
                            <li class="px-5 sm:px-6 py-4">
                                <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-900 truncate">{{ $mentor->user->nom_complet }}</p>
                                        <p class="text-xs text-slate-500 truncate">{{ $mentor->departement ?: '' }}</p>
                                    </div>
                                    <span
                                        class="flex-shrink-0 text-xs font-semibold px-2 py-0.5 rounded-md bg-accent-50 text-accent-700 tabular-nums">{{ $mentor->stagiaires->count() }}
                                        stagiaire(s)</span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($mentor->stagiaires as $stag)
                                        <div class="{{ $puce }}">
                                            <span class="text-sm text-slate-900 font-medium truncate">{{ $stag->user->nom_complet }}</span>
                                            <span class="text-xs text-slate-500 font-mono">{{ $stag->matricule }}</span>
                                            <form action="{{ route('admin.mentors.removeAssign', $stag) }}" method="POST"
                                                class="no-loader">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="{{ $btnRetirer }}" title="Retirer"
                                                    aria-label="Retirer l'affectation de {{ $stag->user->nom_complet }}">
                                                    <i class="fas fa-times text-xs" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <div class="p-5 sm:p-6">
                    <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-8 text-center">
                        <p class="text-sm font-medium text-slate-900">Aucune affectation pour le moment</p>
                        <p class="mt-1 text-sm text-slate-600">Utilisez le formulaire pour lier un stagiaire à un mentor.</p>
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection
