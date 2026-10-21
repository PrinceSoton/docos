@extends('layouts.stagiaire')
@section('titre', 'Mes Tâches')
@section('breadcrumb', 'Mon espace > Tâches')
@section('content')
    <div class="space-y-4 sm:space-y-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3" data-aos="fade-down">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-800">Mes Tâches</h2>
                <p class="text-sm text-slate-500">
                    {{ $aFaire->count() + $enCours->count() + $terminees->count() }} tâche(s) au total
                </p>
            </div>
            @php
                $totalT = $aFaire->count() + $enCours->count() + $terminees->count();
                $pctT = $totalT > 0 ? round(($terminees->count() / $totalT) * 100) : 0;
            @endphp
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-3 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full transition-all duration-1000"
                        style="width:{{ $pctT }}%"></div>
                </div>
                <span class="font-bold text-slate-700 text-sm">{{ $pctT }}%</span>
            </div>
        </div>

        <div class="card p-4" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher une tâche, projet..."
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 text-sm">
        </div>

        {{-- Kanban 3 colonnes --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
            @foreach ([['a_faire', $aFaire, 'À Faire', 'slate', 'clock'], ['en_cours', $enCours, 'En Cours', 'amber', 'spinner'], ['termine', $terminees, 'Terminé', 'green', 'check-circle']] as [$statut, $liste, $label, $color, $icon])
                <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-{{ $color }}-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-{{ $icon }} text-{{ $color }}-500 text-sm"></i>
                        </div>
                        <h3 class="font-bold text-slate-800">{{ $label }}</h3>
                        <span
                            class="ml-auto w-6 h-6 bg-{{ $color }}-100 text-{{ $color }}-700 text-xs font-bold rounded-full flex items-center justify-center">
                            {{ $liste->count() }}
                        </span>
                    </div>
                    <div class="space-y-3">
                        @forelse($liste as $tache)
                            <div
                                class="bg-white rounded-2xl p-4 shadow-sm border {{ $tache->estIndependante() ? 'border-amber-300 border-l-4' : 'border-slate-100' }} hover:shadow-md hover:-translate-y-0.5 transition-all tache-card">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h4 class="font-semibold text-slate-800 text-sm leading-tight">{{ $tache->titre }}</h4>
                                    @php $pc = ['faible'=>'text-slate-400','normale'=>'text-blue-500','haute'=>'text-amber-500','urgente'=>'text-red-600']; @endphp
                                    <i class="fas fa-flag {{ $pc[$tache->priorite] ?? '' }} text-xs flex-shrink-0"
                                        title="{{ ucfirst($tache->priorite) }}"></i>
                                </div>
                                @if ($tache->description)
                                    <p class="text-slate-400 text-xs mb-2 line-clamp-2">{{ $tache->description }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span
                                        class="text-xs px-2 py-0.5 rounded-full {{ $tache->difficulte === 'difficile' ? 'bg-red-50 text-red-600' : ($tache->difficulte === 'moyen' ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600') }}">
                                        {{ ucfirst($tache->difficulte) }}
                                    </span>
                                    @if ($tache->date_echeance)
                                        <span
                                            class="text-xs {{ $tache->date_echeance->isPast() && $tache->statut !== 'termine' ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                                            <i
                                                class="fas fa-calendar mr-0.5"></i>{{ $tache->date_echeance->format('d/m') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Contexte : projet OU indépendante --}}
                                @if ($tache->project)
                                    <p class="text-indigo-500 text-xs font-medium mb-3">
                                        <i class="fas fa-folder mr-1"></i>{{ $tache->project->titre }}
                                    </p>
                                @else
                                    <p class="text-amber-600 text-xs font-medium mb-3">
                                        <i class="fas fa-inbox mr-1"></i>Tâche indépendante
                                    </p>
                                @endif

                                {{-- Actions selon statut --}}
                                @if ($tache->statut === 'a_faire')
                                    <form action="{{ route('stagiaire.tasks.updateStatut', $tache) }}" method="POST"
                                        class="no-loader">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="en_cours">
                                        <button type="submit"
                                            class="w-full bg-amber-50 hover:bg-amber-100 text-amber-700 py-1.5 rounded-xl text-xs font-medium transition">
                                            <i class="fas fa-play mr-1"></i>Commencer
                                        </button>
                                    </form>
                                @elseif ($tache->statut === 'en_cours')
                                    <div class="grid grid-cols-2 gap-2">
                                        <form action="{{ route('stagiaire.tasks.updateStatut', $tache) }}" method="POST"
                                            class="no-loader">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="statut" value="a_faire">
                                            <button type="submit"
                                                class="w-full bg-slate-50 hover:bg-slate-100 text-slate-600 py-1.5 rounded-xl text-xs font-medium transition">
                                                <i class="fas fa-undo mr-1"></i>Retour
                                            </button>
                                        </form>
                                        <form action="{{ route('stagiaire.tasks.updateStatut', $tache) }}" method="POST"
                                            class="no-loader">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="statut" value="termine">
                                            <button type="submit"
                                                class="w-full bg-green-50 hover:bg-green-100 text-green-700 py-1.5 rounded-xl text-xs font-medium transition">
                                                <i class="fas fa-check mr-1"></i>Terminer
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <form action="{{ route('stagiaire.tasks.updateStatut', $tache) }}" method="POST"
                                        class="no-loader">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="en_cours">
                                        <button type="submit"
                                            class="w-full bg-slate-50 hover:bg-slate-100 text-slate-500 py-1.5 rounded-xl text-xs font-medium transition">
                                            <i class="fas fa-undo mr-1"></i>Rouvrir
                                        </button>
                                    </form>
                                @endif

                                {{-- Voir le détail --}}
                                <a href="{{ route('stagiaire.tasks.show', $tache) }}"
                                    class="mt-2 block w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-700 py-1.5 rounded-xl text-xs font-medium transition">
                                    <i class="fas fa-eye mr-1"></i>Voir le détail
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-300">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <p class="text-xs">Aucune tâche</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.tache-card').forEach(card => {
                card.style.display = card.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
@endpush
