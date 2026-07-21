@extends('layouts.admin')
@section('titre', 'Mentors')
@section('breadcrumb', 'Gestion > Mentors')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Mentors</h2>
                <p class="text-slate-500 text-sm">{{ $mentors->total() }} mentor(s) enregistré(s)</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.mentors.assign') }}"
                    class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <i class="fas fa-link"></i>Affecter un stagiaire
                </a>
                <a href="{{ route('admin.mentors.create') }}"
                    class="flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <i class="fas fa-plus"></i>Nouveau mentor
                </a>
            </div>
        </div>

        <!-- Recherche -->
        <div class="card p-4" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher par nom, département, poste..."
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm transition">
        </div>

        <!-- Tableau -->
        <div class="card overflow-hidden" data-aos="fade-up">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Mentor</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Email</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Département</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Poste</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Stagiaires</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="mentorTable">
                        @forelse($mentors as $mentor)
                            <tr class="hover:bg-slate-50 transition mentor-row">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center flex-shrink-0 shadow-sm overflow-hidden">
                                            @if ($mentor->user->photo)
                                                <img src="{{ asset('storage/' . $mentor->user->photo) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <span
                                                    class="text-white font-bold">{{ strtoupper(substr($mentor->user->prenom, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $mentor->user->nom_complet }}</p>
                                            <p class="text-slate-400 text-xs">{{ $mentor->user->telephone ?: '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $mentor->user->email }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $mentor->departement ?: '—' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $mentor->poste ?: '—' }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                                        {{ $mentor->stagiaires->count() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.mentors.edit', $mentor) }}"
                                            class="w-8 h-8 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg flex items-center justify-center transition"
                                            title="Modifier">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.mentors.destroy', $mentor) }}" method="POST"
                                            id="del-mentor-{{ $mentor->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                class="btn-delete w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition"
                                                data-form="del-mentor-{{ $mentor->id }}" title="Supprimer">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Sous-ligne : liste des stagiaires affectés -->
                            @if ($mentor->stagiaires->count() > 0)
                                <tr class="bg-emerald-50/40">
                                    <td colspan="6" class="px-8 py-3">
                                        <div class="flex flex-wrap gap-2 items-center">
                                            <span class="text-xs text-emerald-600 font-semibold mr-2"><i
                                                    class="fas fa-users mr-1"></i>Stagiaires :</span>
                                            @foreach ($mentor->stagiaires as $stag)
                                                <div
                                                    class="flex items-center gap-2 bg-white border border-emerald-200 px-3 py-1.5 rounded-xl">
                                                    <span
                                                        class="text-sm text-slate-700 font-medium">{{ $stag->user->nom_complet }}</span>
                                                    <span class="text-xs text-slate-400">{{ $stag->matricule }}</span>
                                                    <form action="{{ route('admin.mentors.removeAssign', $stag) }}"
                                                        method="POST" class="no-loader">
                                                        @csrf @method('PATCH')
                                                        <button type="submit"
                                                            class="text-red-400 hover:text-red-600 transition ml-1"
                                                            title="Retirer l'affectation">
                                                            <i class="fas fa-times text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-16">
                                    <i class="fas fa-chalkboard-teacher text-5xl text-slate-200 mb-3"></i>
                                    <p class="text-slate-400">Aucun mentor enregistré</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $mentors->links() }}
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.mentor-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
@endpush
