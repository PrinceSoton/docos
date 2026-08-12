@extends('layouts.mentor')
@section('titre', 'Gestion des Tâches')
@section('breadcrumb', 'Mentor > Tâches')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Tâches</h2>
                <p class="text-slate-500 text-sm">Vue Kanban de toutes les tâches de vos projets</p>
            </div>
            <a href="{{ route('mentor.tasks.create') }}"
                class="flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i>Nouvelle tâche
            </a>
        </div>

        <!-- Recherche -->
        <div class="card p-4" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher une tâche, stagiaire, projet..."
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 text-sm">
        </div>

        @forelse($projets as $projet)
            <div class="card p-5"> {{-- data-aos="fade-up" --}}
                <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
                    <div>
                        <h3 class="text-slate-800 font-bold text-lg">
                            <i class="fas fa-folder text-emerald-500 mr-2"></i>{{ $projet->titre }}
                        </h3>
                        <p class="text-slate-400 text-xs">Progression : {{ $projet->progressionPourcent() }}%</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 w-32 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-2 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full"
                                style="width:{{ $projet->progressionPourcent() }}%"></div>
                        </div>
                        <a href="{{ route('mentor.projects.show', $projet) }}"
                            class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-lg font-medium transition">
                            <i class="fas fa-eye mr-1"></i>Projet
                        </a>
                    </div>
                </div>

                <!-- Kanban par projet -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ([['a_faire', 'À Faire', 'slate', 'clock'], ['en_cours', 'En Cours', 'amber', 'spinner fa-spin'], ['termine', 'Terminé', 'green', 'check-circle']] as [$statut, $label, $color, $icon])
                        @php $taches = $projet->tasks->where('statut',$statut); @endphp
                        <div class="bg-{{ $color }}-50 rounded-xl p-4 border border-{{ $color }}-100">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-{{ $icon }} text-{{ $color }}-500 text-sm"></i>
                                    <span class="font-semibold text-slate-700 text-sm">{{ $label }}</span>
                                </div>
                                <span
                                    class="w-5 h-5 rounded-full bg-{{ $color }}-200 text-{{ $color }}-700 text-xs font-bold flex items-center justify-center">
                                    {{ $taches->count() }}
                                </span>
                            </div>
                            <div class="space-y-2 tache-col" data-statut="{{ $statut }}">
                                @forelse($taches as $tache)
                                    <div
                                        class="bg-white rounded-xl p-3 shadow-sm hover:shadow-md transition-all border border-{{ $color }}-100 tache-card">
                                        <div class="flex items-start justify-between gap-2 mb-1.5">
                                            <p class="font-semibold text-slate-800 text-xs leading-tight">
                                                {{ $tache->titre }}</p>
                                            @php $pc = ['faible'=>'text-slate-400','normale'=>'text-blue-500','haute'=>'text-amber-500','urgente'=>'text-red-600']; @endphp
                                            <i class="fas fa-flag {{ $pc[$tache->priorite] ?? '' }} text-xs flex-shrink-0"
                                                title="{{ ucfirst($tache->priorite) }}"></i>
                                        </div>
                                        <p class="text-xs text-slate-400 mb-2">
                                            <i
                                                class="fas fa-user mr-1"></i>{{ $tache->stagiaire->user->nom_complet ?? '—' }}
                                        </p>
                                        @if ($tache->date_echeance)
                                            <p
                                                class="text-xs {{ $tache->date_echeance->isPast() && $tache->statut !== 'termine' ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                                                <i
                                                    class="fas fa-calendar mr-1"></i>{{ $tache->date_echeance->format('d/m/Y') }}
                                            </p>
                                        @endif
                                        <div class="flex items-center gap-1 mt-2">
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full {{ $tache->difficulte === 'difficile' ? 'bg-red-50 text-red-600' : ($tache->difficulte === 'moyen' ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600') }}">
                                                {{ ucfirst($tache->difficulte) }}
                                            </span>
                                            <div class="ml-auto flex gap-1">
                                                <a href="{{ route('mentor.tasks.edit', $tache) }}"
                                                    class="w-6 h-6 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded flex items-center justify-center transition">
                                                    <i class="fas fa-edit" style="font-size:9px"></i>
                                                </a>
                                                <form action="{{ route('mentor.tasks.destroy', $tache) }}" method="POST"
                                                    id="del-t-{{ $tache->id }}">
                                                    @csrf @method('DELETE')
                                                    <button type="button"
                                                        class="btn-delete w-6 h-6 bg-red-50 hover:bg-red-100 text-red-600 rounded flex items-center justify-center transition"
                                                        data-form="del-t-{{ $tache->id }}">
                                                        <i class="fas fa-trash" style="font-size:9px"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-{{ $color }}-400 text-xs text-center py-3">Aucune tâche</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @empty
                <div class="card p-16 text-center" data-aos="fade-up">
                    <i class="fas fa-tasks text-5xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400">Aucun projet avec des tâches</p>
                </div>
            @endforelse
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
    {{-- @endsection --}}
