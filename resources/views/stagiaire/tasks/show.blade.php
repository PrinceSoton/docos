@extends('layouts.stagiaire')
@section('titre', 'Détail tâche')
@section('breadcrumb', 'Tâches > Détail')
@section('content')
    <div class="max-w-xl mx-auto space-y-5">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-start justify-between gap-4 mb-5 flex-wrap">
                <div>
                    <h2 class="text-slate-800 font-black text-2xl">{{ $task->titre }}</h2>
                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                        @php $sc = ['a_faire'=>'bg-slate-100 text-slate-600','en_cours'=>'bg-amber-100 text-amber-700','termine'=>'bg-green-100 text-green-700']; @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$task->statut] ?? '' }}">
                            {{ ['a_faire' => 'À faire', 'en_cours' => 'En cours', 'termine' => 'Terminé'][$task->statut] ?? $task->statut }}
                        </span>
                        @php $pc = ['faible'=>'bg-slate-50 text-slate-500','normale'=>'bg-blue-50 text-blue-600','haute'=>'bg-amber-50 text-amber-700','urgente'=>'bg-red-50 text-red-700']; @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $pc[$task->priorite] ?? '' }}">
                            <i class="fas fa-flag mr-1"></i>{{ ucfirst($task->priorite) }}
                        </span>
                        @php $dc = ['facile'=>'bg-green-50 text-green-700','moyen'=>'bg-amber-50 text-amber-700','difficile'=>'bg-red-50 text-red-700']; @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $dc[$task->difficulte] ?? '' }}">
                            {{ ucfirst($task->difficulte) }}
                        </span>
                    </div>
                </div>
            </div>

            @if ($task->description)
                <div class="p-4 bg-slate-50 rounded-xl mb-4">
                    <p class="text-xs text-slate-400 uppercase font-medium mb-1">Description</p>
                    <p class="text-slate-700 text-sm">{{ $task->description }}</p>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div class="p-4 bg-indigo-50 rounded-xl">
                    <p class="text-xs text-indigo-400 uppercase font-medium">Projet</p>
                    <p class="font-semibold text-slate-800 text-sm mt-1">{{ $task->project->titre ?? '—' }}</p>
                </div>
                @if ($task->date_echeance)
                    <div
                        class="p-4 {{ $task->date_echeance->isPast() && $task->statut !== 'termine' ? 'bg-red-50' : 'bg-slate-50' }} rounded-xl">
                        <p
                            class="text-xs {{ $task->date_echeance->isPast() && $task->statut !== 'termine' ? 'text-red-400' : 'text-slate-400' }} uppercase font-medium">
                            Échéance</p>
                        <p
                            class="font-semibold {{ $task->date_echeance->isPast() && $task->statut !== 'termine' ? 'text-red-700' : 'text-slate-800' }} text-sm mt-1">
                            {{ $task->date_echeance->format('d/m/Y') }}
                            @if ($task->date_echeance->isPast() && $task->statut !== 'termine')
                                <span class="text-xs text-red-500">(En retard)</span>
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            <!-- Actions statut -->
            @if ($task->statut !== 'termine')
                <div class="grid grid-cols-2 gap-3">
                    @if ($task->statut === 'a_faire')
                        <form action="{{ route('stagiaire.tasks.updateStatut', $task) }}" method="POST"
                            class="no-loader col-span-2">
                            @csrf @method('PATCH')
                            <input type="hidden" name="statut" value="en_cours">
                            <button type="submit"
                                class="w-full bg-amber-500 hover:bg-amber-600 text-white py-3 rounded-xl font-semibold transition hover:shadow-md">
                                <i class="fas fa-play mr-2"></i>Commencer cette tâche
                            </button>
                        </form>
                    @elseif($task->statut === 'en_cours')
                        <form action="{{ route('stagiaire.tasks.updateStatut', $task) }}" method="POST" class="no-loader">
                            @csrf @method('PATCH')
                            <input type="hidden" name="statut" value="a_faire">
                            <button type="submit"
                                class="w-full bg-slate-200 hover:bg-slate-300 text-slate-700 py-3 rounded-xl font-semibold transition">
                                <i class="fas fa-undo mr-2"></i>Retour
                            </button>
                        </form>
                        <form action="{{ route('stagiaire.tasks.updateStatut', $task) }}" method="POST" class="no-loader">
                            @csrf @method('PATCH')
                            <input type="hidden" name="statut" value="termine">
                            <button type="submit"
                                class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl font-semibold transition hover:shadow-md">
                                <i class="fas fa-check mr-2"></i>Marquer terminée
                            </button>
                        </form>
                    @endif
                </div>
            @else
                <div class="p-4 bg-green-50 rounded-xl border border-green-100 text-center">
                    <i class="fas fa-trophy text-green-500 text-2xl mb-2"></i>
                    <p class="text-green-700 font-bold">Tâche terminée !</p>
                </div>
            @endif
        </div>

        <a href="{{ route('stagiaire.tasks.index') }}"
            class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour à mes tâches
        </a>
    </div>
@endsection
