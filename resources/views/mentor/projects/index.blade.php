@extends('layouts.mentor')
@section('titre', 'Mes Projets')
@section('breadcrumb', 'Mentor > Projets')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Mes Projets</h2>
                <p class="text-slate-500 text-sm">{{ $projets->total() }} projet(s) créé(s)</p>
            </div>
            <a href="{{ route('mentor.projects.create') }}"
                class="flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i>Nouveau projet
            </a>
        </div>

        <!-- Recherche + Filtre -->
        <div class="card p-4 flex flex-wrap gap-3" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher un projet..."
                class="flex-1 min-w-48 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 text-sm">
            <select id="filterStatut"
                class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 text-sm">
                <option value="">Tous statuts</option>
                <option value="en_attente">En attente</option>
                <option value="en_cours">En cours</option>
                <option value="termine">Terminé</option>
                <option value="suspendu">Suspendu</option>
            </select>
        </div>

        <!-- Grille projets -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="projetGrid">
            @forelse($projets as $projet)
                <div class="card overflow-hidden projet-card hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                    data-statut="{{ $projet->statut }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                    <!-- Barre de couleur selon priorité -->
                    <div
                        class="h-1.5 {{ $projet->priorite === 'urgente' ? 'bg-red-500' : ($projet->priorite === 'haute' ? 'bg-amber-500' : ($projet->priorite === 'normale' ? 'bg-blue-400' : 'bg-slate-300')) }}">
                    </div>

                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h3 class="font-bold text-slate-800 text-lg leading-tight">{{ $projet->titre }}</h3>
                            @php
                                $sc = [
                                    'en_attente' => 'bg-slate-100 text-slate-600',
                                    'en_cours' => 'bg-blue-100 text-blue-700',
                                    'termine' => 'bg-green-100 text-green-700',
                                    'suspendu' => 'bg-red-100 text-red-600',
                                ];
                            @endphp
                            <span
                                class="px-2 py-1 rounded-lg text-xs font-semibold flex-shrink-0 {{ $sc[$projet->statut] ?? '' }}">
                                {{ ucfirst(str_replace('_', ' ', $projet->statut)) }}
                            </span>
                        </div>

                        @if ($projet->description)
                            <p class="text-slate-500 text-sm line-clamp-2 mb-3">{{ $projet->description }}</p>
                        @endif

                        <!-- Stagiaires -->
                        <div class="flex items-center gap-1 mb-3 flex-wrap">
                            @foreach ($projet->stagiaires->take(3) as $stag)
                                <span
                                    class="flex items-center gap-1 bg-emerald-50 text-emerald-700 px-2 py-1 rounded-lg text-xs font-medium">
                                    <i class="fas fa-user-graduate text-xs"></i>{{ $stag->user->prenom ?? '' }}
                                </span>
                            @endforeach
                            @if ($projet->stagiaires->count() > 3)
                                <span class="text-slate-400 text-xs">+{{ $projet->stagiaires->count() - 3 }}</span>
                            @endif
                        </div>

                        <!-- Progression -->
                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-slate-500 mb-1">
                                <span>Progression
                                    ({{ $projet->tasks->where('statut', 'termine')->count() }}/{{ $projet->tasks->count() }}
                                    tâches)</span>
                                <span class="font-bold text-emerald-600">{{ $projet->progressionPourcent() }}%</span>
                            </div>
                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-2 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full"
                                    style="width:{{ $projet->progressionPourcent() }}%"></div>
                            </div>
                        </div>

                        <!-- Infos -->
                        <div class="flex items-center gap-3 text-xs text-slate-400 mb-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $projet->date_debut->format('d/m/Y') }}</span>
                            @if ($projet->date_fin)
                                <span><i
                                        class="fas fa-flag-checkered mr-1"></i>{{ $projet->date_fin->format('d/m/Y') }}</span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="{{ route('mentor.projects.show', $projet) }}"
                                class="flex-1 text-center bg-emerald-50 hover:bg-emerald-100 text-emerald-700 py-2 rounded-xl text-sm font-medium transition">
                                <i class="fas fa-eye mr-1"></i>Voir
                            </a>
                            <a href="{{ route('mentor.projects.edit', $projet) }}"
                                class="flex-1 text-center bg-amber-50 hover:bg-amber-100 text-amber-700 py-2 rounded-xl text-sm font-medium transition">
                                <i class="fas fa-edit mr-1"></i>Modifier
                            </a>
                            <form action="{{ route('mentor.projects.destroy', $projet) }}" method="POST"
                                id="del-proj-{{ $projet->id }}">
                                @csrf @method('DELETE')
                                <button type="button"
                                    class="btn-delete w-9 h-9 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl flex items-center justify-center transition"
                                    data-form="del-proj-{{ $projet->id }}">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 card p-16 text-center">
                    <i class="fas fa-project-diagram text-5xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400 mb-4">Aucun projet créé</p>
                    <a href="{{ route('mentor.projects.create') }}"
                        class="inline-flex items-center gap-2 bg-emerald-500 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-emerald-600 transition">
                        <i class="fas fa-plus"></i>Créer un projet
                    </a>
                </div>
            @endforelse
        </div>
        <div>{{ $projets->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        function filterProjets() {
            const s = document.getElementById('search').value.toLowerCase();
            const st = document.getElementById('filterStatut').value;
            document.querySelectorAll('.projet-card').forEach(card => {
                card.style.display = card.textContent.toLowerCase().includes(s) && (!st || card.dataset.statut ===
                    st) ? '' : 'none';
            });
        }
        document.getElementById('search').addEventListener('input', filterProjets);
        document.getElementById('filterStatut').addEventListener('change', filterProjets);
    </script>
@endpush
