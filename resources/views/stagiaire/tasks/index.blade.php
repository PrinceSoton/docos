@extends('layouts.stagiaire')
@section('titre', 'Mes Tâches')
@section('breadcrumb', 'Mon espace > Tâches')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Mes Tâches</h2>
                <p class="text-slate-500 text-sm">Vue des tâches —
                    {{ $aFaire->count() + $enCours->count() + $terminees->count() }} tâche(s) au total</p>
            </div>
            <!-- Barre de progression globale -->
            @php
                $totalT = $aFaire->count() + $enCours->count() + $terminees->count();
                $pctT = $totalT > 0 ? round(($terminees->count() / $totalT) * 100) : 0;
            @endphp
            <div class="flex items-center gap-3 min-w-48">
                <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-3 {{-- bg-gradient-to-rfrom-amber-400to-green-500 --}} bg-gradient-to-r from-green-500 to-emerald-600 rounded-full transition-all duration-1000"
                        style="width:{{ $pctT }}%"></div>
                </div>
                <span class="font-bold text-slate-700 text-sm">{{ $pctT }}%</span>
            </div>
        </div>

        <!-- Recherche -->
        <div class="card p-4" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher une tâche, projet..."
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 text-sm">
        </div>

        <!-- Kanban 3 colonnes -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            <!-- À FAIRE -->
            <div data-aos="fade-up" data-aos-delay="0">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-slate-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-slate-500 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-slate-800">À Faire</h3>
                    <span
                        class="ml-auto w-6 h-6 bg-slate-200 text-slate-600 text-xs font-bold rounded-full flex items-center justify-center">{{ $aFaire->count() }}</span>
                </div>
                <div class="space-y-3 tache-col" data-statut="a_faire">
                    @forelse($aFaire as $tache)
                        <div
                            class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:shadow-md hover:-translate-y-0.5 transition-all tache-card">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h4 class="font-semibold text-slate-800 text-sm leading-tight">{{ $tache->titre }}</h4>
                                @php $pc = ['faible'=>'text-slate-400','normale'=>'text-blue-500','haute'=>'text-amber-500','urgente'=>'text-red-600']; @endphp
                                <i class="fas fa-flag {{ $pc[$tache->priorite] ?? '' }} text-xs flex-shrink-0"
                                    title="{{ ucfirst($tache->priorite) }}"></i>
                            </div>
                            @if ($tache->description)
                                <p class="text-slate-400 text-xs mb-2 line-clamp-2">{{ $tache->description }}</p>
                            @endif
                            <div class="flex items-center gap-2 mb-3">
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full {{ $tache->difficulte === 'difficile' ? 'bg-red-50 text-red-600' : ($tache->difficulte === 'moyen' ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600') }}">
                                    {{ ucfirst($tache->difficulte) }}
                                </span>
                                @if ($tache->date_echeance)
                                    <span
                                        class="text-xs {{ $tache->date_echeance->isPast() ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                                        <i class="fas fa-calendar mr-0.5"></i>{{ $tache->date_echeance->format('d/m') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-indigo-500 text-xs font-medium mb-3">
                                <i class="fas fa-folder mr-1"></i>{{ $tache->project->titre ?? '—' }}
                            </p>
                            <form action="{{ route('stagiaire.tasks.updateStatut', $tache) }}" method="POST"
                                class="no-loader">
                                @csrf @method('PATCH')
                                <input type="hidden" name="statut" value="en_cours">
                                <button type="submit"
                                    class="w-full bg-amber-50 hover:bg-amber-100 text-amber-700 py-1.5 rounded-xl text-xs font-medium transition">
                                    <i class="fas fa-play mr-1"></i>Commencer
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-300">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p class="text-xs">Aucune tâche</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- EN COURS -->
            <div data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-spinner text-amber-500 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-slate-800">En Cours</h3>
                    <span
                        class="ml-auto w-6 h-6 bg-amber-100 text-amber-700 text-xs font-bold rounded-full flex items-center justify-center">{{ $enCours->count() }}</span>
                </div>
                <div class="space-y-3 tache-col" data-statut="en_cours">
                    @forelse($enCours as $tache)
                        <div
                            class="bg-white rounded-2xl p-4 shadow-sm border-l-4 border-amber-400 hover:shadow-md hover:-translate-y-0.5 transition-all tache-card">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h4 class="font-semibold text-slate-800 text-sm leading-tight">{{ $tache->titre }}</h4>
                                @php $pc2 = ['faible'=>'text-slate-400','normale'=>'text-blue-500','haute'=>'text-amber-500','urgente'=>'text-red-600']; @endphp
                                <i class="fas fa-flag {{ $pc2[$tache->priorite] ?? '' }} text-xs flex-shrink-0"></i>
                            </div>
                            @if ($tache->description)
                                <p class="text-slate-400 text-xs mb-2 line-clamp-2">{{ $tache->description }}</p>
                            @endif
                            <div class="flex items-center gap-2 mb-3">
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full {{ $tache->difficulte === 'difficile' ? 'bg-red-50 text-red-600' : ($tache->difficulte === 'moyen' ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600') }}">
                                    {{ ucfirst($tache->difficulte) }}
                                </span>
                                @if ($tache->date_echeance)
                                    <span
                                        class="text-xs {{ $tache->date_echeance->isPast() ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                                        <i class="fas fa-calendar mr-0.5"></i>{{ $tache->date_echeance->format('d/m') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-indigo-500 text-xs font-medium mb-3">
                                <i class="fas fa-folder mr-1"></i>{{ $tache->project->titre ?? '—' }}
                            </p>
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
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-300">
                            <i class="fas fa-tasks text-3xl mb-2"></i>
                            <p class="text-xs">Aucune tâche</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- TERMINÉ -->
            <div data-aos="fade-up" data-aos-delay="200">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-500 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-slate-800">Terminé</h3>
                    <span
                        class="ml-auto w-6 h-6 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center justify-center">{{ $terminees->count() }}</span>
                </div>
                <div class="space-y-3 tache-col" data-statut="termine">
                    @forelse($terminees as $tache)
                        <div
                            class="bg-white rounded-2xl p-4 shadow-sm border-l-4 border-green-400 opacity-80 hover:opacity-100 hover:shadow-md transition-all tache-card">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h4 class="font-semibold text-slate-600 text-sm leading-tight line-through">
                                    {{ $tache->titre }}</h4>
                                <i class="fas fa-check-circle text-green-500 text-sm flex-shrink-0"></i>
                            </div>
                            <p class="text-indigo-400 text-xs mb-2">
                                <i class="fas fa-folder mr-1"></i>{{ $tache->project->titre ?? '—' }}
                            </p>
                            <form action="{{ route('stagiaire.tasks.updateStatut', $tache) }}" method="POST"
                                class="no-loader">
                                @csrf @method('PATCH')
                                <input type="hidden" name="statut" value="en_cours">
                                <button type="submit"
                                    class="w-full bg-slate-50 hover:bg-slate-100 text-slate-500 py-1.5 rounded-xl text-xs font-medium transition">
                                    <i class="fas fa-undo mr-1"></i>Rouvrir
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-300">
                            <i class="fas fa-trophy text-3xl mb-2"></i>
                            <p class="text-xs">Pas encore de tâche terminée</p>
                        </div>
                    @endforelse
                </div>
            </div>
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
