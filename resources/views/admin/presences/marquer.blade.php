@extends('layouts.admin')
@section('titre', 'Marquer les présences')
@section('breadcrumb', 'Présences > Marquage')
@section('content')
    <div class="space-y-4 sm:space-y-5">

        <div data-aos="fade-down">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800">Marquage des présences</h2>
            <p class="text-sm text-slate-500">Marquez ou corrigez les présences des stagiaires actifs</p>
        </div>

        {{-- Sélection de la date --}}
        <div class="card p-5" data-aos="fade-up">
            <form method="GET" action="{{ route('admin.presences.formMarquage') }}"
                  class="flex flex-wrap gap-3 items-end no-loader">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Date à marquer</label>
                    <input type="date" name="date" value="{{ $date->toDateString() }}"
                           max="{{ today()->toDateString() }}"
                           class="border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-medium">
                    <i class="fas fa-calendar-day mr-2"></i>Charger
                </button>
                <a href="{{ route('admin.presences.index') }}"
                   class="px-6 py-2.5 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </form>
        </div>

        {{-- Alerte si non travaillé --}}
        @if (!$estJourTravaille)
            <div class="card p-4 border-l-4 border-amber-400 bg-amber-50" data-aos="fade-up">
                <p class="text-amber-700 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>{{ $date->translatedFormat('l d F Y') }}</strong> n'est pas un jour travaillé.
                    Le marquage reste possible pour régularisation.
                </p>
            </div>
        @endif

        @if ($stagiaires->isEmpty())
            <div class="card p-12 text-center" data-aos="fade-up">
                <i class="fas fa-user-graduate text-5xl text-slate-200 mb-4"></i>
                <p class="text-slate-400">Aucun stagiaire actif pour cette date</p>
            </div>
        @else
            <form action="{{ route('admin.presences.marquerLot') }}" method="POST" class="no-loader">
                @csrf
                <input type="hidden" name="date" value="{{ $date->toDateString() }}">

                <div class="card overflow-hidden" data-aos="fade-up">
                    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <h3 class="font-bold text-slate-800">
                            {{ $stagiaires->count() }} stagiaire(s) — {{ $date->translatedFormat('l d F Y') }}
                        </h3>
                        <div class="flex gap-2">
                            <button type="button" onclick="toutMarquer('present')"
                                    class="text-xs px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg font-medium">
                                <i class="fas fa-check mr-1"></i>Tous présents
                            </button>
                            <button type="button" onclick="toutMarquer('absent')"
                                    class="text-xs px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg font-medium">
                                <i class="fas fa-times mr-1"></i>Tous absents
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Stagiaire</th>
                                    <th class="px-4 py-3 text-center font-semibold text-slate-600">Statut</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600 hidden sm:table-cell">Motif</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach ($stagiaires as $i => $stag)
                                    @php $p = $presencesExistantes->get($stag->id); @endphp
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3">
                                            <input type="hidden" name="presences[{{ $i }}][stagiaire_id]" value="{{ $stag->id }}">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                    @if ($stag->user->photo)
                                                        <img src="{{ asset('storage/' . $stag->user->photo) }}"
                                                             class="w-full h-full object-cover" loading="lazy">
                                                    @else
                                                        <span class="text-white font-bold text-xs">
                                                            {{ strtoupper(substr($stag->user->prenom, 0, 1)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-slate-800">{{ $stag->user->nom_complet }}</p>
                                                    <p class="text-xs text-slate-400 font-mono">{{ $stag->matricule }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-1 sm:gap-2 flex-wrap">
                                                @foreach (['present' => 'Présent', 'retard' => 'Retard', 'absent' => 'Absent'] as $val => $label)
                                                    <label class="cursor-pointer">
                                                        <input type="radio"
                                                               name="presences[{{ $i }}][statut]"
                                                               value="{{ $val }}"
                                                               class="peer sr-only"
                                                               onchange="toggleMotif(this, {{ $i }})"
                                                               {{ $p ? ($p->statut === $val ? 'checked' : '') : ($val === 'present' ? 'checked' : '') }}>
                                                        <span class="block px-2 sm:px-3 py-1.5 rounded-lg border-2 border-slate-200 text-xs font-medium
                                                                     peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition">
                                                            {{ $label }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 hidden sm:table-cell">
                                            <input type="text"
                                                   name="presences[{{ $i }}][motif]"
                                                   id="motif-{{ $i }}"
                                                   value="{{ $p->motif ?? '' }}"
                                                   placeholder="Motif si absent / retard..."
                                                   class="w-full sm:w-56 border border-slate-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-indigo-400">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-4">
                    <button type="submit"
                            class="flex-1 sm:flex-none bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Enregistrer les présences
                    </button>
                    <a href="{{ route('admin.presences.index') }}"
                       class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl text-center hover:bg-slate-50">
                        Annuler
                    </a>
                </div>
            </form>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    function toggleMotif(radio, i) {
        const input = document.getElementById('motif-' + i);
        if (!input) return;
        if (radio.value === 'absent' && !input.value) {
            input.focus();
        }
    }
    function toutMarquer(statut) {
        document.querySelectorAll('input[type="radio"][value="' + statut + '"]').forEach(r => {
            r.checked = true;
        });
    }
</script>
@endpush