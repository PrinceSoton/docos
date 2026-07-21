@extends('layouts.admin')
@section('titre', 'Stagiaires')
@section('breadcrumb', 'Gestion > Stagiaires')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Stagiaires</h2>
                <p class="text-slate-500 text-sm">{{ $stagiaires->total() }} stagiaire(s) enregistré(s)</p>
            </div>
            <a href="{{ route('admin.stagiaires.create') }}"
                class="flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i>Nouveau stagiaire
            </a>
        </div>

        <!-- Filtres + recherche -->
        <div class="card p-4 flex flex-wrap gap-3 items-center" data-aos="fade-up">
            <input type="text" id="search" placeholder="Nom, matricule, école, spécialité..."
                class="flex-1 min-w-48 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm transition">
            <select id="filterStatut"
                class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm transition">
                <option value="">Tous les statuts</option>
                <option value="en_cours">En cours</option>
                <option value="termine">Terminé</option>
                <option value="suspendu">Suspendu</option>
            </select>
        </div>

        <div class="card overflow-hidden" data-aos="fade-up">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Stagiaire</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Matricule</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">École / Spécialité</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Mentor</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Période</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Statut</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="stagTable">
                        @forelse($stagiaires as $stag)
                            <tr class="hover:bg-slate-50 transition stag-row" data-statut="{{ $stag->statut }}">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center flex-shrink-0 overflow-hidden shadow-sm">
                                            @if ($stag->user->photo)
                                                <img src="{{ asset('storage/' . $stag->user->photo) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <span
                                                    class="text-white font-bold">{{ strtoupper(substr($stag->user->prenom, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $stag->user->nom_complet }}</p>
                                            <p class="text-slate-400 text-xs">{{ $stag->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="font-mono text-xs bg-indigo-50 text-indigo-700 px-2 py-1 rounded-lg font-bold">{{ $stag->matricule }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-slate-700 font-medium text-sm">{{ $stag->ecole ?: '—' }}</p>
                                    <p class="text-slate-400 text-xs">{{ $stag->specialite ?: '' }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-600 text-sm">
                                    {{ $stag->mentor?->nom_complet ?: '—' }}
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-slate-700 text-xs font-medium">{{ $stag->date_debut->format('d/m/Y') }}
                                    </p>
                                    <p class="text-slate-400 text-xs">→ {{ $stag->date_fin->format('d/m/Y') }}</p>
                                    <p class="text-indigo-500 text-xs font-semibold">{{ $stag->dureeStageDays() }}j</p>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $stag->statut === 'en_cours' ? 'bg-green-100 text-green-700' : ($stag->statut === 'termine' ? 'bg-slate-100 text-slate-600' : 'bg-red-100 text-red-600') }}">
                                        {{ $stag->statut === 'en_cours' ? 'En cours' : ($stag->statut === 'termine' ? 'Terminé' : 'Suspendu') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.stagiaires.show', $stag) }}"
                                            class="w-8 h-8 bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-700 rounded-lg flex items-center justify-center transition"
                                            title="Voir">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.stagiaires.edit', $stag) }}"
                                            class="w-8 h-8 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg flex items-center justify-center transition"
                                            title="Modifier">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        @if ($stag->cv)
                                            <a href="{{ asset('storage/' . $stag->cv) }}" download
                                                class="w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg flex items-center justify-center transition"
                                                title="Télécharger CV">
                                                <i class="fas fa-download text-xs"></i>
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.stagiaires.destroy', $stag) }}" method="POST"
                                            id="del-stag-{{ $stag->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                class="btn-delete w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition"
                                                data-form="del-stag-{{ $stag->id }}" title="Supprimer">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-16">
                                    <i class="fas fa-user-graduate text-5xl text-slate-200 mb-3"></i>
                                    <p class="text-slate-400">Aucun stagiaire enregistré</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100">{{ $stagiaires->links() }}</div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function filterTable() {
            const search = document.getElementById('search').value.toLowerCase();
            const statut = document.getElementById('filterStatut').value;
            document.querySelectorAll('.stag-row').forEach(row => {
                const matchSearch = row.textContent.toLowerCase().includes(search);
                const matchStatut = !statut || row.dataset.statut === statut;
                row.style.display = matchSearch && matchStatut ? '' : 'none';
            });
        }
        document.getElementById('search').addEventListener('input', filterTable);
        document.getElementById('filterStatut').addEventListener('change', filterTable);
    </script>
@endpush
