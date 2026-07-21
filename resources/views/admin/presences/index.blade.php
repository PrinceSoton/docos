@extends('layouts.admin')
@section('titre', 'Présences')
@section('breadcrumb', 'Suivi > Présences')
@section('content')
    <div class="space-y-5">
        <div data-aos="fade-down">
            <h2 class="text-slate-800 font-bold text-2xl">Suivi des présences</h2>
            <p class="text-slate-500 text-sm">Historique complet de présence de chaque stagiaire</p>
        </div>

        <!-- Filtres -->
        <div class="card p-5" data-aos="fade-up">
            <form action="{{ route('admin.presences.index') }}" method="GET"
                class="flex flex-wrap gap-3 items-end no-loader">
                <div class="flex-1 min-w-48">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Stagiaire</label>
                    <select name="stagiaire_id"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm transition">
                        <option value="">Tous les stagiaires</option>
                        @foreach ($stagiaires as $stag)
                            <option value="{{ $stag->id }}"
                                {{ request('stagiaire_id') == $stag->id ? 'selected' : '' }}>
                                {{ $stag->user->nom_complet }} — {{ $stag->matricule }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Mois</label>
                    <input type="month" name="mois" value="{{ request('mois', date('Y-m')) }}"
                        class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm transition">
                </div>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all hover:shadow-md">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
                @if (request('stagiaire_id'))
                    <a href="{{ route('admin.presences.show', request('stagiaire_id')) }}"
                        class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-medium transition-all hover:shadow-md">
                        <i class="fas fa-eye mr-2"></i>Fiche complète
                    </a>
                @endif
            </form>
        </div>

        @if ($stagiaire)
            <!-- Stats du stagiaire -->
            <div class="grid grid-cols-3 gap-4" data-aos="fade-up">
                @foreach ([['present', 'Présent', 'green', 'user-check'], ['retard', 'Retard', 'amber', 'clock'], ['absent', 'Absent', 'red', 'user-times']] as [$statut, $label, $color, $icon])
                    @php
                        $count = \App\Models\Presence::where('stagiaire_id', $stagiaire->id)
                            ->where('statut', $statut)
                            ->count();
                    @endphp
                    <div class="card p-5 text-center">
                        <div
                            class="w-12 h-12 bg-{{ $color }}-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-{{ $icon }} text-{{ $color }}-600 text-xl"></i>
                        </div>
                        <p class="text-2xl font-black text-slate-800">{{ $count }}</p>
                        <p class="text-slate-400 text-xs mt-1">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Tableau -->
        @if ($presences->count() > 0)
            <div class="card overflow-hidden" data-aos="fade-up">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">
                        @if ($stagiaire)
                            Présences de {{ $stagiaire->user->nom_complet }}
                        @else
                            Toutes les présences
                        @endif
                    </h3>
                    <input type="text" id="search" placeholder="Rechercher..."
                        class="border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-indigo-400 transition w-48">
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold text-slate-600">Date</th>
                                @if (!$stagiaire)
                                    <th class="px-5 py-3 text-left font-semibold text-slate-600">Stagiaire</th>
                                @endif
                                <th class="px-5 py-3 text-left font-semibold text-slate-600">Statut</th>
                                <th class="px-5 py-3 text-left font-semibold text-slate-600">Heure d'arrivée</th>
                                <th class="px-5 py-3 text-left font-semibold text-slate-600">Motif</th>
                                <th class="px-5 py-3 text-center font-semibold text-slate-600">Justificatif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50" id="presenceTable">
                            @foreach ($presences as $presence)
                                <tr class="hover:bg-slate-50 transition pres-row">
                                    <td class="px-5 py-3 font-medium text-slate-800">
                                        {{ $presence->date->translatedFormat('l d/m/Y') }}
                                    </td>
                                    @if (!$stagiaire)
                                        <td class="px-5 py-3 text-slate-600">
                                            {{ $presence->stagiaire->user->nom_complet ?? '—' }}</td>
                                    @endif
                                    <td class="px-5 py-3">
                                        @php
                                            $sc = [
                                                'present' => 'bg-green-100 text-green-700',
                                                'retard' => 'bg-amber-100 text-amber-700',
                                                'absent' => 'bg-red-100 text-red-600',
                                            ];
                                            $si = [
                                                'present' => 'check-circle',
                                                'retard' => 'clock',
                                                'absent' => 'times-circle',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$presence->statut] ?? '' }}">
                                            <i class="fas fa-{{ $si[$presence->statut] ?? '' }} mr-1"></i>
                                            {{ ucfirst($presence->statut) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600 font-mono text-sm">
                                        {{ $presence->heure_arrivee ? substr($presence->heure_arrivee, 0, 5) : '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-500 text-xs max-w-xs truncate">
                                        {{ $presence->motif ?: '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        @if ($presence->justificatif)
                                            <a href="{{ asset('storage/' . $presence->justificatif) }}" download
                                                class="inline-flex items-center gap-1 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-3 py-1 rounded-lg text-xs font-medium transition">
                                                <i class="fas fa-download"></i>Fichier
                                            </a>
                                        @else
                                            <span class="text-slate-300 text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-slate-100">{{ $presences->links() }}</div>
            </div>
        @else
            <div class="card p-12 text-center" data-aos="fade-up">
                <i class="fas fa-clipboard-list text-5xl text-slate-200 mb-4"></i>
                <p class="text-slate-400">Sélectionnez un stagiaire pour voir ses présences</p>
            </div>
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('search')?.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.pres-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
@endpush
