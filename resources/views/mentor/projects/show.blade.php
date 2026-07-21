@extends('layouts.mentor')
@section('titre', $project->titre)
@section('breadcrumb', 'Projets > Détail')
@section('content')
    <div class="max-w-5xl mx-auto space-y-5">
        <!-- En-tête projet -->
        <div class="card p-6" data-aos="fade-down">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-3 flex-wrap mb-2">
                        <h2 class="text-slate-800 font-black text-2xl">{{ $project->titre }}</h2>
                        @php
                            $sc = [
                                'en_attente' => 'bg-slate-100 text-slate-600',
                                'en_cours' => 'bg-blue-100 text-blue-700',
                                'termine' => 'bg-green-100 text-green-700',
                                'suspendu' => 'bg-red-100 text-red-600',
                            ];
                            $pc = [
                                'faible' => 'bg-slate-100 text-slate-500',
                                'normale' => 'bg-blue-100 text-blue-600',
                                'haute' => 'bg-amber-100 text-amber-700',
                                'urgente' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$project->statut] ?? '' }}">{{ ucfirst(str_replace('_', ' ', $project->statut)) }}</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $pc[$project->priorite] ?? '' }}">
                            <i class="fas fa-flag mr-1"></i>{{ ucfirst($project->priorite) }}
                        </span>
                    </div>
                    @if ($project->description)
                        <p class="text-slate-500 text-sm">{{ $project->description }}</p>
                    @endif
                    <p class="text-slate-400 text-xs mt-2">
                        <i class="fas fa-calendar mr-1"></i>{{ $project->date_debut->format('d/m/Y') }}
                        @if ($project->date_fin)
                            → {{ $project->date_fin->format('d/m/Y') }}
                        @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('mentor.projects.edit', $project) }}"
                        class="flex items-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-amber-600 transition">
                        <i class="fas fa-edit"></i>Modifier
                    </a>
                    <a href="{{ route('mentor.tasks.create') }}"
                        class="flex items-center gap-2 bg-emerald-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-emerald-600 transition">
                        <i class="fas fa-plus"></i>Tâche
                    </a>
                </div>
            </div>

            <!-- Progression globale -->
            <div class="mt-5">
                <div class="flex justify-between text-sm text-slate-600 mb-2">
                    <span>Progression globale</span>
                    <span class="font-bold text-emerald-600">{{ $project->progressionPourcent() }}%</span>
                </div>
                <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-3 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full transition-all duration-1000"
                        style="width:{{ $project->progressionPourcent() }}%"></div>
                </div>
            </div>

            <!-- Stagiaires -->
            <div class="flex flex-wrap gap-2 mt-4">
                <span class="text-xs text-slate-500 font-medium">Stagiaires :</span>
                @foreach ($project->stagiaires as $stag)
                    <a href="{{ route('mentor.stagiaires.show', $stag) }}"
                        class="flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-xl text-xs font-medium transition">
                        <i class="fas fa-user-graduate text-xs"></i>{{ $stag->user->nom_complet }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Kanban tâches -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5" data-aos="fade-up">
            @foreach ([['a_faire', 'À Faire', 'slate', 'clock'], ['en_cours', 'En Cours', 'amber', 'spinner'], ['termine', 'Terminé', 'green', 'check-circle']] as [$statut, $label, $color, $icon])
                @php $tachesFiltrees = $project->tasks->where('statut',$statut); @endphp
                <div class="bg-{{ $color }}-50 rounded-2xl p-4 border border-{{ $color }}-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-{{ $icon }} text-{{ $color }}-600"></i>
                            <h3 class="font-bold text-slate-800">{{ $label }}</h3>
                        </div>
                        <span
                            class="w-6 h-6 rounded-full bg-{{ $color }}-200 text-{{ $color }}-700 text-xs font-bold flex items-center justify-center">
                            {{ $tachesFiltrees->count() }}
                        </span>
                    </div>
                    <div class="space-y-3">
                        @forelse($tachesFiltrees as $tache)
                            <div
                                class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all border border-{{ $color }}-100">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <p class="font-semibold text-slate-800 text-sm">{{ $tache->titre }}</p>
                                    @php
                                        $pc2 = [
                                            'faible' => 'bg-slate-100 text-slate-500',
                                            'normale' => 'bg-blue-100 text-blue-600',
                                            'haute' => 'bg-amber-100 text-amber-700',
                                            'urgente' => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span
                                        class="text-xs px-2 py-0.5 rounded-full {{ $pc2[$tache->priorite] ?? '' }} flex-shrink-0">{{ ucfirst($tache->priorite) }}</span>
                                </div>
                                @if ($tache->description)
                                    <p class="text-slate-400 text-xs mb-2 line-clamp-2">{{ $tache->description }}</p>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-400">
                                        <i class="fas fa-user mr-1"></i>{{ $tache->stagiaire->user->prenom ?? '—' }}
                                    </span>
                                    @if ($tache->date_echeance)
                                        <span
                                            class="text-xs {{ $tache->date_echeance->isPast() && $tache->statut !== 'termine' ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                                            <i class="fas fa-calendar mr-1"></i>{{ $tache->date_echeance->format('d/m') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex gap-1 mt-2">
                                    <span
                                        class="text-xs px-2 py-0.5 rounded-full {{ $tache->difficulte === 'difficile' ? 'bg-red-50 text-red-600' : ($tache->difficulte === 'moyen' ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600') }}">
                                        {{ ucfirst($tache->difficulte) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-{{ $color }}-400 text-xs text-center py-4">Aucune tâche</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Rapports liés -->
        @if ($project->reports->count() > 0)
            <div class="card p-6" data-aos="fade-up">
                <h3 class="text-slate-800 font-bold text-lg mb-4"><i
                        class="fas fa-file-alt text-purple-500 mr-2"></i>Rapports liés ({{ $project->reports->count() }})
                </h3>
                <div class="space-y-2">
                    @foreach ($project->reports as $rapport)
                        <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-xl hover:bg-purple-50 transition">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-alt text-purple-500 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-slate-800 text-sm">{{ $rapport->titre }}</p>
                                <p class="text-slate-400 text-xs">{{ $rapport->stagiaire->user->nom_complet ?? '' }} •
                                    {{ $rapport->created_at->format('d/m/Y') }}</p>
                            </div>
                            @php $sr = ['soumis'=>'bg-amber-100 text-amber-700','valide'=>'bg-green-100 text-green-700','rejete'=>'bg-red-100 text-red-600']; @endphp
                            <span
                                class="text-xs px-2 py-1 rounded-full {{ $sr[$rapport->statut] ?? '' }}">{{ ucfirst($rapport->statut) }}</span>
                            <a href="{{ route('mentor.reports.show', $rapport) }}"
                                class="w-8 h-8 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg flex items-center justify-center transition">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <a href="{{ route('mentor.projects.index') }}"
            class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour aux projets
        </a>
    </div>
@endsection
