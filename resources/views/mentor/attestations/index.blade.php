@extends('layouts.mentor')
@section('titre', 'Attestations & Conventions')
@section('breadcrumb', 'Mentor > Attestations')
@section('content')
    <div class="space-y-5">
        <div data-aos="fade-down">
            <h2 class="text-slate-800 font-bold text-2xl">Attestations & Conventions</h2>
            <p class="text-slate-500 text-sm">{{ $attestations->total() }} demande(s) de vos stagiaires</p>
        </div>

        <div class="card p-4 flex flex-wrap gap-3" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher stagiaire, type..."
                class="flex-1 min-w-48 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 text-sm">
            <select id="filterStatut"
                class="border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-400">
                <option value="">Tous statuts</option>
                <option value="en_attente">En attente</option>
                <option value="valide_mentor">Validé</option>
                <option value="refuse">Refusé</option>
                <option value="envoye">Envoyé</option>
            </select>
        </div>

        <div class="card overflow-hidden" data-aos="fade-up">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Stagiaire</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Type</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Statut</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Demandé le</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="attTable">
                        @forelse($attestations as $att)
                            <tr class="hover:bg-slate-50 transition att-row" data-statut="{{ $att->statut }}">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                            {{ strtoupper(substr($att->stagiaire->user->prenom ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">
                                                {{ $att->stagiaire->user->nom_complet ?? '—' }}</p>
                                            <p class="text-slate-400 text-xs">{{ $att->stagiaire->matricule ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold capitalize {{ $att->type === 'attestation' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ $att->type }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @php $sc = ['en_attente'=>'bg-amber-100 text-amber-700','valide_mentor'=>'bg-green-100 text-green-700','refuse'=>'bg-red-100 text-red-600','envoye'=>'bg-indigo-100 text-indigo-700']; @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$att->statut] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ ['en_attente' => 'En attente', 'valide_mentor' => 'Validé', 'refuse' => 'Refusé', 'envoye' => 'Envoyé'][$att->statut] ?? $att->statut }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-400 text-xs">{{ $att->created_at->format('d/m/Y') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('mentor.attestations.show', $att) }}"
                                            class="w-8 h-8 bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-700 rounded-lg flex items-center justify-center transition">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @if ($att->statut === 'en_attente')
                                            <a href="{{ route('mentor.attestations.validate', $att) }}"
                                                class="w-8 h-8 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg flex items-center justify-center transition"
                                                title="Valider/Refuser">
                                                <i class="fas fa-check text-xs"></i>
                                            </a>
                                        @endif
                                        @if ($att->fichier)
                                            <a href="{{ route('mentor.attestations.telecharger', $att) }}"
                                                class="w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg flex items-center justify-center transition">
                                                <i class="fas fa-download text-xs"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-16 text-slate-400">Aucune demande</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100">{{ $attestations->links() }}</div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function filterAtt() {
            const s = document.getElementById('search').value.toLowerCase();
            const st = document.getElementById('filterStatut').value;
            document.querySelectorAll('.att-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(s) && (!st || row.dataset.statut ===
                    st) ? '' : 'none';
            });
        }
        document.getElementById('search').addEventListener('input', filterAtt);
        document.getElementById('filterStatut').addEventListener('change', filterAtt);
    </script>
@endpush
