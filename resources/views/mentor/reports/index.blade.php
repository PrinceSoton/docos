@extends('layouts.mentor')
@section('titre', 'Rapports')
@section('breadcrumb', 'Mentor > Rapports')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Rapports des stagiaires</h2>
                <p class="text-slate-500 text-sm">{{ $rapports->total() }} rapport(s) reçu(s)</p>
            </div>
        </div>

        <div class="card p-4 flex flex-wrap gap-3" data-aos="fade-up">
            <input type="text" id="search" placeholder="Titre, stagiaire, type..."
                class="flex-1 min-w-48 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 text-sm">
            <select id="filterStatut"
                class="border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-400">
                <option value="">Tous statuts</option>
                <option value="soumis">À évaluer</option>
                <option value="valide">Validé</option>
                <option value="rejete">Rejeté</option>
                <option value="en_revision">En révision</option>
            </select>
        </div>

        <div class="card overflow-hidden" data-aos="fade-up">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Rapport</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Stagiaire</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Type</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Projet</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Note</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Statut</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Date</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="rapportTable">
                        @forelse($rapports as $rapport)
                            <tr class="hover:bg-slate-50 transition rapport-row" data-statut="{{ $rapport->statut }}">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">{{ $rapport->titre }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                            <span
                                                class="text-emerald-700 font-bold text-xs">{{ strtoupper(substr($rapport->stagiaire->user->prenom ?? '', 0, 1)) }}</span>
                                        </div>
                                        <span
                                            class="text-slate-700 font-medium text-sm">{{ $rapport->stagiaire->user->nom_complet ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="px-2 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-600 capitalize">{{ $rapport->type_affiche }}</span>
                                </td>
                                <td class="px-5 py-4 text-slate-500 text-sm">{{ $rapport->project?->titre ?: '—' }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if ($rapport->note !== null)
                                        <span
                                            class="font-bold text-{{ $rapport->note >= 14 ? 'green' : ($rapport->note >= 10 ? 'amber' : 'red') }}-600">
                                            {{ $rapport->note }}/20
                                        </span>
                                    @else<span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @php $sc = ['soumis'=>'bg-amber-100 text-amber-700','valide'=>'bg-green-100 text-green-700','rejete'=>'bg-red-100 text-red-600','en_revision'=>'bg-blue-100 text-blue-700']; @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$rapport->statut] ?? '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $rapport->statut)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-400 text-xs">{{ $rapport->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('mentor.reports.telecharger', $rapport) }}"
                                            class="w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg flex items-center justify-center transition"
                                            title="Télécharger">
                                            <i class="fas fa-download text-xs"></i>
                                        </a>
                                        <a href="{{ route('mentor.reports.show', $rapport) }}"
                                            class="w-8 h-8 bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-700 rounded-lg flex items-center justify-center transition"
                                            title="Voir">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @if ($rapport->statut === 'soumis')
                                            <a href="{{ route('mentor.reports.evaluate', $rapport) }}"
                                                class="w-8 h-8 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg flex items-center justify-center transition"
                                                title="Évaluer">
                                                <i class="fas fa-star text-xs"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-16 text-slate-400">Aucun rapport reçu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100">{{ $rapports->links() }}</div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function filterRap() {
            const s = document.getElementById('search').value.toLowerCase();
            const st = document.getElementById('filterStatut').value;
            document.querySelectorAll('.rapport-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(s) && (!st || row.dataset.statut ===
                    st) ? '' : 'none';
            });
        }
        document.getElementById('search').addEventListener('input', filterRap);
        document.getElementById('filterStatut').addEventListener('change', filterRap);
    </script>
@endpush
