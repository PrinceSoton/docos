@extends('layouts.admin')
@section('titre', 'Attestations & Conventions')
@section('breadcrumb', 'Suivi > Attestations')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Attestations & Conventions</h2>
                <p class="text-slate-500 text-sm">{{ $attestations->total() }} demande(s) au total</p>
            </div>
        </div>

        <div class="card p-4 flex flex-wrap gap-3" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher stagiaire, type, statut..."
                class="flex-1 min-w-48 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm transition">
            <select id="filterType"
                class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm transition">
                <option value="">Tous types</option>
                <option value="attestation">Attestation</option>
                <option value="convention">Convention</option>
            </select>
            <select id="filterStatut"
                class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm transition">
                <option value="">Tous statuts</option>
                <option value="en_attente">En attente</option>
                <option value="valide_mentor">Validé mentor</option>
                <option value="envoye">Envoyé</option>
                <option value="refuse">Refusé</option>
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
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Validé mentor</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="attTable">
                        @forelse($attestations as $att)
                            <tr class="hover:bg-slate-50 transition att-row" data-type="{{ $att->type }}"
                                data-statut="{{ $att->statut }}">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
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
                                        class="px-3 py-1 rounded-full text-xs font-semibold capitalize
                                {{ $att->type === 'attestation' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                        <i
                                            class="fas fa-{{ $att->type === 'attestation' ? 'certificate' : 'file-contract' }} mr-1"></i>
                                        {{ $att->type }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $badges = [
                                            'en_attente' => 'bg-amber-100 text-amber-700',
                                            'valide_mentor' => 'bg-blue-100 text-blue-700',
                                            'approuve_admin' => 'bg-indigo-100 text-indigo-700',
                                            'envoye' => 'bg-green-100 text-green-700',
                                            'refuse' => 'bg-red-100 text-red-600',
                                        ];
                                        $labels = [
                                            'en_attente' => 'En attente',
                                            'valide_mentor' => 'Validé mentor',
                                            'approuve_admin' => 'Approuvé',
                                            'envoye' => 'Envoyé',
                                            'refuse' => 'Refusé',
                                        ];
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $badges[$att->statut] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $labels[$att->statut] ?? $att->statut }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-500 text-xs">{{ $att->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500">
                                    {{ $att->valide_le_mentor ? $att->valide_le_mentor->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.attestations.show', $att) }}"
                                            class="w-8 h-8 bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-700 rounded-lg flex items-center justify-center transition">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @if (in_array($att->statut, ['valide_mentor', 'approuve_admin']))
                                            <a href="{{ route('admin.attestations.uploadForm', $att) }}"
                                                class="w-8 h-8 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg flex items-center justify-center transition"
                                                title="Envoyer document">
                                                <i class="fas fa-upload text-xs"></i>
                                            </a>
                                        @endif
                                        @if ($att->fichier)
                                            <a href="{{ route('admin.attestations.telecharger', $att) }}"
                                                class="w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg flex items-center justify-center transition"
                                                title="Télécharger">
                                                <i class="fas fa-download text-xs"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.attestations.destroy', $att) }}" method="POST"
                                            id="del-att-{{ $att->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                class="btn-delete w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition"
                                                data-form="del-att-{{ $att->id }}">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-16">
                                    <i class="fas fa-certificate text-5xl text-slate-200 mb-3"></i>
                                    <p class="text-slate-400">Aucune demande d'attestation</p>
                                </td>
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
            const t = document.getElementById('filterType').value;
            const st = document.getElementById('filterStatut').value;
            document.querySelectorAll('.att-row').forEach(row => {
                const ms = row.textContent.toLowerCase().includes(s);
                const mt = !t || row.dataset.type === t;
                const mst = !st || row.dataset.statut === st;
                row.style.display = ms && mt && mst ? '' : 'none';
            });
        }
        ['search', 'filterType', 'filterStatut'].forEach(id => document.getElementById(id).addEventListener('input',
            filterAtt));
    </script>
@endpush
