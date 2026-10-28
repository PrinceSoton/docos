@extends('layouts.mentor')
@section('titre', 'Marquer les présences')
@section('breadcrumb', 'Présences > Marquage')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $btnPetitSecondaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        // Pastilles de statut sélectionnables (radio natif masqué + libellé)
        $choix = [
            'present' => ['Présent', 'bg-emerald-500', 'peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-800'],
            'retard' => ['Retard', 'bg-amber-500', 'peer-checked:border-amber-600 peer-checked:bg-amber-50 peer-checked:text-amber-800'],
            'absent' => ['Absent', 'bg-red-500', 'peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:text-red-800'],
        ];
    @endphp

    <div class="max-w-5xl mx-auto space-y-5">

        <!-- Barre d'outils -->
        <p class="text-sm text-slate-600">Marquez ou corrigez les présences de vos stagiaires pour une date donnée.</p>

        <!-- Choix de la date -->
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <form method="GET" action="{{ route('mentor.presences.formMarquage') }}"
                class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3 no-loader">
                <div class="sm:w-56">
                    <label for="marquage-date" class="block text-sm font-medium text-slate-700 mb-1">Date à marquer</label>
                    <input type="date" name="date" id="marquage-date" value="{{ $date->toDateString() }}"
                        max="{{ today()->toDateString() }}" class="{{ $champ }} tabular-nums">
                </div>
                <button type="submit" class="{{ $btnPrimaire }}">
                    <i class="fas fa-calendar-day text-xs" aria-hidden="true"></i>Charger
                </button>
                <a href="{{ route('mentor.presences.index') }}" class="{{ $btnSecondaire }}">
                    <i class="fas fa-arrow-left text-xs text-slate-400" aria-hidden="true"></i>Retour
                </a>
            </form>
        </section>

        @if (!$estJourTravaille)
            <p class="flex items-start gap-2 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900"
                role="status">
                <span class="mt-1.5 h-2 w-2 rounded-full bg-amber-500 flex-shrink-0" aria-hidden="true"></span>
                <span><strong class="font-semibold">{{ ucfirst($date->translatedFormat('l d F Y')) }}</strong> n'est pas
                    un jour travaillé.</span>
            </p>
        @endif

        @if ($stagiaires->isEmpty())
            <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                <p class="text-sm font-medium text-slate-900">Aucun stagiaire actif pour cette date</p>
                <p class="mt-1 text-sm text-slate-600">Seuls les stagiaires en cours dont la période de stage couvre cette
                    date peuvent être marqués. Choisissez une autre date.</p>
            </div>
        @else
            <form action="{{ route('mentor.presences.marquerLot') }}" method="POST" class="no-loader">
                @csrf
                <input type="hidden" name="date" value="{{ $date->toDateString() }}">

                <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
                    <div
                        class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                        <h3 class="text-base font-semibold text-slate-900">
                            {{ $stagiaires->count() }} stagiaire(s)
                            <span class="font-normal text-slate-600">— {{ $date->translatedFormat('l d F Y') }}</span>
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="toutMarquer('present')" class="flex-1 sm:flex-none {{ $btnPetitSecondaire }}">
                                <i class="fas fa-check text-slate-400" aria-hidden="true"></i>Tous présents
                            </button>
                            <button type="button" onclick="toutMarquer('absent')" class="flex-1 sm:flex-none {{ $btnPetitSecondaire }}">
                                <i class="fas fa-times text-slate-400" aria-hidden="true"></i>Tous absents
                            </button>
                        </div>
                    </div>

                    <!-- En-têtes de colonnes (écrans moyens et plus) -->
                    <div class="hidden xl:grid grid-cols-[minmax(0,1fr)_auto_16rem] gap-4 px-5 sm:px-6 py-2.5 bg-brand-50 border-b border-brand-100 text-xs font-medium text-slate-600"
                        aria-hidden="true">
                        <span>Stagiaire</span>
                        <span class="w-[15.5rem] text-center">Statut</span>
                        <span>Motif</span>
                    </div>

                    <ul class="divide-y divide-slate-100">
                        @foreach ($stagiaires as $i => $stag)
                            @php $p = $presencesExistantes->get($stag->id); @endphp
                            <li class="px-5 sm:px-6 py-4 hover:bg-slate-50 grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_auto_16rem] gap-3 md:gap-4 md:items-center">
                                <input type="hidden" name="presences[{{ $i }}][stagiaire_id]" value="{{ $stag->id }}">

                                <div class="flex items-center gap-3 min-w-0">
                                    <div
                                        class="h-9 w-9 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                        @if ($stag->user->photo)
                                            <img src="{{ asset('storage/' . $stag->user->photo) }}" alt=""
                                                class="h-full w-full object-cover" loading="lazy">
                                        @else
                                            <span class="text-sm font-semibold text-brand-800"
                                                aria-hidden="true">{{ strtoupper(substr($stag->user->prenom, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-900 truncate">{{ $stag->user->nom_complet }}</p>
                                        <p class="text-xs text-slate-500 font-mono">{{ $stag->matricule }}</p>
                                    </div>
                                </div>

                                <fieldset>
                                    <legend class="sr-only">Statut de {{ $stag->user->nom_complet }}</legend>
                                    <div class="grid grid-cols-3 gap-2 xl:w-[15.5rem]">
                                        @foreach (['present' => 'Présent', 'retard' => 'Retard', 'absent' => 'Absent'] as $val => $label)
                                            @php [, $dot, $actif] = $choix[$val]; @endphp
                                            <label class="cursor-pointer">
                                                <input type="radio" name="presences[{{ $i }}][statut]"
                                                    value="{{ $val }}" class="peer sr-only"
                                                    {{ $p ? ($p->statut === $val ? 'checked' : '') : ($val === 'present' ? 'checked' : '') }}>
                                                <span
                                                    class="flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs font-medium text-slate-600 hover:border-slate-300 transition-colors peer-focus-visible:ring-2 peer-focus-visible:ring-brand-600 peer-focus-visible:ring-offset-2 {{ $actif }}">
                                                    <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                                                    {{ $label }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>

                                <div>
                                    <label for="motif-{{ $i }}" class="sr-only">Motif pour
                                        {{ $stag->user->nom_complet }}</label>
                                    <input type="text" name="presences[{{ $i }}][motif]" maxlength="255" id="motif-{{ $i }}"
                                        value="{{ $p->motif ?? '' }}" placeholder="Motif si absent / retard..."
                                        class="{{ $champ }}">
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <div class="flex flex-col-reverse sm:flex-row gap-2 mt-5">
                    <a href="{{ route('mentor.presences.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Enregistrer les présences
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toutMarquer(statut) {
            document.querySelectorAll('input[type="radio"][value="' + statut + '"]').forEach(r => {
                r.checked = true;
            });
        }
    </script>
@endpush
