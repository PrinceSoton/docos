@extends('layouts.stagiaire')
@section('titre', 'Détail tâche')
@section('breadcrumb', 'Tâches > Détail')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;

        $statuts = [
            'a_faire' => ['À faire', 'bg-slate-100 text-slate-700'],
            'en_cours' => ['En cours', 'bg-amber-50 text-amber-800'],
            'termine' => ['Terminé', 'bg-emerald-50 text-emerald-800'],
        ];
        $priorites = [
            'faible' => ['Faible', 'bg-slate-400'],
            'normale' => ['Normale', 'bg-sky-500'],
            'haute' => ['Haute', 'bg-amber-500'],
            'urgente' => ['Urgente', 'bg-red-500'],
        ];
        [$libStatut, $clsStatut] = $statuts[$task->statut] ?? [$task->statut, 'bg-slate-100 text-slate-700'];
        [$libPriorite, $dotPriorite] = $priorites[$task->priorite] ?? [ucfirst((string) $task->priorite), 'bg-slate-300'];
        $enRetard = $task->date_echeance && $task->date_echeance->isPast() && $task->statut !== 'termine';
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">
        <!-- En-tête -->
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <h2 class="min-w-0 text-lg font-semibold text-slate-900 leading-snug break-words">{{ $task->titre }}</h2>
                <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsStatut }}">{{ $libStatut }}</span>
            </div>

            <dl class="mt-5 rounded-lg bg-brand-50 border border-brand-100 p-4 grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div class="col-span-2 min-w-0">
                    @if ($task->project)
                        <dt class="text-xs text-slate-600">Projet</dt>
                        <dd class="mt-0.5 font-medium text-slate-900 break-words">{{ $task->project->titre }}</dd>
                    @else
                        <dt class="text-xs text-slate-600">Type de tâche</dt>
                        <dd class="mt-0.5 font-medium text-slate-900">Tâche indépendante</dd>
                        <dd class="mt-0.5 text-xs text-slate-600">Non rattachée à un projet</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-xs text-slate-600">Priorité</dt>
                    <dd class="mt-0.5 flex items-center gap-2 font-medium text-slate-900">
                        <span class="h-2 w-2 rounded-full {{ $dotPriorite }}" aria-hidden="true"></span>
                        {{ $libPriorite }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-600">Difficulté</dt>
                    <dd class="mt-0.5 font-medium text-slate-900">{{ ucfirst($task->difficulte) }}</dd>
                </div>
                @if ($task->date_echeance)
                    <div class="col-span-2">
                        <dt class="text-xs text-slate-600">Échéance</dt>
                        <dd class="mt-0.5 flex flex-wrap items-center gap-2 font-medium tabular-nums {{ $enRetard ? 'text-red-700' : 'text-slate-900' }}">
                            {{ $task->date_echeance->format('d/m/Y') }}
                            @if ($enRetard)
                                <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-red-50 text-red-800">En retard</span>
                            @endif
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        @if ($task->description)
            <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
                <h3 class="text-base font-semibold text-slate-900">Description</h3>
                <p class="mt-2 text-sm text-slate-700 break-words whitespace-pre-line">{{ $task->description }}</p>
            </div>
        @endif

        <!-- Actions de statut -->
        @if ($task->statut !== 'termine')
            <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row gap-3">
                    @if ($task->statut === 'a_faire')
                        <form action="{{ route('stagiaire.tasks.updateStatut', $task) }}" method="POST" class="no-loader w-full sm:w-auto">
                            @csrf @method('PATCH')
                            <input type="hidden" name="statut" value="en_cours">
                            <button type="submit" class="w-full {{ $btnPrimaire }}">
                                <i class="fas fa-play text-xs" aria-hidden="true"></i>Commencer cette tâche
                            </button>
                        </form>
                    @elseif ($task->statut === 'en_cours')
                        <form action="{{ route('stagiaire.tasks.updateStatut', $task) }}" method="POST" class="no-loader w-full sm:w-auto">
                            @csrf @method('PATCH')
                            <input type="hidden" name="statut" value="a_faire">
                            <button type="submit" class="w-full {{ $btnSecondaire }}">
                                <i class="fas fa-undo text-xs" aria-hidden="true"></i>Retour
                            </button>
                        </form>
                        <form action="{{ route('stagiaire.tasks.updateStatut', $task) }}" method="POST" class="no-loader w-full sm:w-auto">
                            @csrf @method('PATCH')
                            <input type="hidden" name="statut" value="termine">
                            <button type="submit" class="w-full {{ $btnPrimaire }}">
                                <i class="fas fa-check text-xs" aria-hidden="true"></i>Marquer terminée
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @else
            <div class="rounded-xl bg-accent-50 border border-accent-100 p-5 sm:p-6">
                <p class="flex items-center gap-2 text-sm font-semibold text-slate-900">
                    <span class="h-2 w-2 rounded-full bg-accent-500" aria-hidden="true"></span>
                    Tâche terminée
                </p>
                <p class="mt-1 text-sm text-accent-700">Aucune action n'est plus requise sur cette tâche.</p>
            </div>
        @endif

        <a href="{{ route('stagiaire.tasks.index') }}" class="inline-flex items-center gap-2 {{ $lien }}">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour à mes tâches
        </a>
    </div>
@endsection
