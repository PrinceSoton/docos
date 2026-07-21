@extends('layouts.stagiaire')
@section('titre', $project->titre)
@section('breadcrumb', 'Projet > Détail')
@section('content')
    <div class="max-w-5xl mx-auto space-y-5">
        <!-- En-tête projet -->
        <div class="card p-6" data-aos="fade-down">
            <div class="flex items-start justify-between flex-wrap gap-4 mb-4">
                <div>
                    <h2 class="text-slate-800 font-black text-2xl">{{ $project->titre }}</h2>
                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                        @php $sc = ['en_attente'=>'bg-slate-100 text-slate-600','en_cours'=>'bg-blue-100 text-blue-700','termine'=>'bg-green-100 text-green-700','suspendu'=>'bg-red-100 text-red-600']; @endphp
                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$project->statut] ?? '' }}">{{ ucfirst(str_replace('_', ' ', $project->statut)) }}</span>
                        @php $pc = ['faible'=>'bg-slate-50 text-slate-500','normale'=>'bg-blue-50 text-blue-600','haute'=>'bg-amber-50 text-amber-700','urgente'=>'bg-red-50 text-red-700']; @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $pc[$project->priorite] ?? '' }}">
                            <i class="fas fa-flag mr-1"></i>{{ ucfirst($project->priorite) }}
                        </span>
                    </div>
                    @if ($project->description)
                        <p class="text-slate-500 text-sm mt-2">{{ $project->description }}</p>
                    @endif
                    <p class="text-slate-400 text-xs mt-2">
                        <i
                            class="fas fa-chalkboard-teacher mr-1 text-emerald-500"></i>{{ $project->mentor->nom_complet ?? '—' }}
                        •
                        {{ $project->date_debut->format('d/m/Y') }} →
                        {{ $project->date_fin?->format('d/m/Y') ?? 'Indéfini' }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-black text-amber-600">{{ $project->progressionPourcent() }}%</p>
                    <p class="text-slate-400 text-xs">terminé</p>
                </div>
            </div>
            <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-3 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full transition-all duration-1000"
                    style="width:{{ $project->progressionPourcent() }}%"></div>
            </div>
        </div>

        <!-- Kanban mes tâches -->
        <div class="card p-5" data-aos="fade-up">
            <h3 class="text-slate-800 font-bold text-lg mb-4"><i class="fas fa-tasks text-amber-500 mr-2"></i>Mes tâches
                dans ce projet</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ([['a_faire', $taches['a_faire'], 'À Faire', 'slate', 'clock'], ['en_cours', $taches['en_cours'], 'En Cours', 'amber', 'spinner'], ['termine', $taches['termine'], 'Terminé', 'green', 'check-circle']] as [$statut, $liste, $label, $color, $icon])
                    <div class="bg-{{ $color }}-50 rounded-xl p-4 border border-{{ $color }}-100">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-{{ $icon }} text-{{ $color }}-500 text-sm"></i>
                                <span class="font-semibold text-slate-700 text-sm">{{ $label }}</span>
                            </div>
                            <span
                                class="w-5 h-5 rounded-full bg-{{ $color }}-200 text-{{ $color }}-700 text-xs font-bold flex items-center justify-center">{{ $liste->count() }}</span>
                        </div>
                        <div class="space-y-2">
                            @forelse($liste as $tache)
                                <a href="{{ route('stagiaire.tasks.show', $tache) }}"
                                    class="block bg-white rounded-xl p-3 hover:shadow-md transition border border-{{ $color }}-100">
                                    <p class="font-semibold text-slate-800 text-xs">{{ $tache->titre }}</p>
                                    @if ($tache->date_echeance)
                                        <p
                                            class="text-xs mt-1 {{ $tache->date_echeance->isPast() && $statut !== 'termine' ? 'text-red-500' : 'text-slate-400' }}">
                                            <i
                                                class="fas fa-calendar mr-0.5"></i>{{ $tache->date_echeance->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </a>
                            @empty
                                <p class="text-{{ $color }}-400 text-xs text-center py-3">Aucune</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Mes rapports liés -->
        @php $rapportsLies = $project->reports->where('stagiaire_id', Auth::user()->stagiaire->id ?? 0); @endphp
        @if ($rapportsLies->count() > 0)
            <div class="card p-6" data-aos="fade-up">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-slate-800 font-bold text-lg"><i class="fas fa-file-alt text-purple-500 mr-2"></i>Mes
                        rapports liés</h3>
                    <a href="{{ route('stagiaire.reports.create') }}" class="text-amber-600 text-sm hover:underline">+
                        Rapport</a>
                </div>
                <div class="space-y-2">
                    @foreach ($rapportsLies as $rapport)
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-purple-50 transition">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-alt text-purple-500 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-slate-800 text-sm">{{ $rapport->titre }}</p>
                                <p class="text-slate-400 text-xs">{{ $rapport->created_at->format('d/m/Y') }}</p>
                            </div>
                            @php $sl = ['soumis'=>'bg-amber-100 text-amber-700','valide'=>'bg-green-100 text-green-700','rejete'=>'bg-red-100 text-red-600']; @endphp
                            <span
                                class="text-xs px-2 py-1 rounded-full {{ $sl[$rapport->statut] ?? '' }}">{{ ucfirst($rapport->statut) }}</span>
                            <a href="{{ route('stagiaire.reports.telecharger', $rapport) }}" download
                                class="w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg flex items-center justify-center transition">
                                <i class="fas fa-download text-xs"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <a href="{{ route('stagiaire.projects.index') }}"
            class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour
        </a>
    </div>
@endsection
