@extends('layouts.stagiaire')
@section('titre', 'Mon Projet')
@section('breadcrumb', 'Mon espace > Projet')
@section('content')
    <div class="space-y-5">
        <div data-aos="fade-down">
            <h2 class="text-slate-800 font-bold text-2xl">Mon Projet</h2>
            <p class="text-slate-500 text-sm">{{ $projets->total() }} projet(s) assigné(s)</p>
        </div>

        <div class="card p-4" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher un projet..."
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 text-sm">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5" id="projetGrid">
            @forelse($projets as $projet)
                <div class="card overflow-hidden projet-card hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                    data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                    @php
                        $gradients = [
                            'faible' => 'from-slate-400 to-slate-500',
                            'normale' => 'from-blue-400 to-indigo-500',
                            'haute' => 'from-amber-400 to-orange-500',
                            'urgente' => 'from-red-400 to-pink-500',
                        ];
                    @endphp
                    <div class="h-2 bg-gradient-to-r {{ $gradients[$projet->priorite] ?? 'from-slate-300 to-slate-400' }}">
                    </div>
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h3 class="font-bold text-slate-800 text-xl">{{ $projet->titre }}</h3>
                            @php $sc = ['en_attente'=>'bg-slate-100 text-slate-600','en_cours'=>'bg-blue-100 text-blue-700','termine'=>'bg-green-100 text-green-700','suspendu'=>'bg-red-100 text-red-600']; @endphp
                            <span
                                class="px-2 py-1 rounded-lg text-xs font-semibold {{ $sc[$projet->statut] ?? '' }} flex-shrink-0">
                                {{ ucfirst(str_replace('_', ' ', $projet->statut)) }}
                            </span>
                        </div>
                        @if ($projet->description)
                            <p class="text-slate-500 text-sm mb-4 line-clamp-2">{{ $projet->description }}</p>
                        @endif

                        <div class="flex items-center gap-2 text-sm text-slate-500 mb-4">
                            <i class="fas fa-chalkboard-teacher text-emerald-500"></i>
                            <span>{{ $projet->mentor->nom_complet ?? '—' }}</span>
                        </div>

                        <!-- Progression -->
                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-slate-500 mb-1">
                                <span>Progression</span>
                                <span class="font-bold text-amber-600">{{ $projet->progressionPourcent() }}%</span>
                            </div>
                            <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-2.5 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full"
                                    style="width:{{ $projet->progressionPourcent() }}%"></div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-xs text-slate-400 mb-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $projet->date_debut->format('d/m/Y') }}</span>
                            @if ($projet->date_fin)
                                <span><i
                                        class="fas fa-flag-checkered mr-1"></i>{{ $projet->date_fin->format('d/m/Y') }}</span>
                            @endif
                        </div>

                        <a href="{{ route('stagiaire.projects.show', $projet) }}"
                            class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all text-sm">
                            <i class="fas fa-eye"></i>Voir le détail
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 card p-16 text-center">
                    <i class="fas fa-project-diagram text-5xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400">Aucun projet assigné pour le moment</p>
                </div>
            @endforelse
        </div>
        <div>{{ $projets->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.projet-card').forEach(c => {
                c.style.display = c.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
@endpush
