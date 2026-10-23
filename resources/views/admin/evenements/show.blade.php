@extends('layouts.admin')
@section('titre', $evenement->titre)
@section('breadcrumb', 'Événements > Détail')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $btnDanger = 'inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 transition-colors ' . $focus;

        $tc = [
            'information' => ['Information', 'bg-sky-50 text-sky-800'],
            'evenement' => ['Événement', 'bg-brand-50 text-brand-800'],
            'note' => ['Note', 'bg-slate-100 text-slate-700'],
        ];
        [$libType, $clsType] = $tc[$evenement->type] ?? [ucfirst((string) $evenement->type), 'bg-slate-100 text-slate-700'];
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">
        <article class="bg-white border border-brand-100 rounded-xl overflow-hidden">
            @if ($evenement->image)
                <div class="h-44 sm:h-56 overflow-hidden bg-brand-50">
                    <img src="{{ asset('storage/' . $evenement->image) }}" alt="{{ $evenement->titre }}"
                        class="w-full h-full object-cover">
                </div>
            @endif
            <div class="p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="min-w-0">
                        <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-md {{ $clsType }}">{{ $libType }}</span>
                        <h2 class="mt-3 text-lg font-semibold text-slate-900 leading-snug break-words">{{ $evenement->titre }}</h2>
                    </div>
                    <div class="flex flex-wrap gap-2 flex-shrink-0">
                        <a href="{{ route('admin.evenements.edit', $evenement) }}" class="{{ $btnSecondaire }} whitespace-nowrap">
                            <i class="fas fa-pen text-xs" aria-hidden="true"></i>Modifier
                        </a>
                        <form action="{{ route('admin.evenements.destroy', $evenement) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-delete {{ $btnDanger }} whitespace-nowrap">
                                <i class="fas fa-trash text-xs" aria-hidden="true"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Métadonnées (bloc teinté) -->
                <dl class="mt-4 rounded-lg bg-brand-50 border border-brand-100 p-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                    <div class="min-w-0">
                        <dt class="text-xs text-slate-600">Publié par</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">{{ $evenement->creePar?->nom_complet }}
                            <span class="font-normal text-slate-600">• {{ $evenement->created_at->diffForHumans() }}</span>
                        </dd>
                    </div>
                    @if ($evenement->date_evenement)
                        <div>
                            <dt class="text-xs text-slate-600">Date de l'événement</dt>
                            <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">{{ $evenement->date_evenement->format('d/m/Y à H:i') }}</dd>
                        </div>
                    @endif
                </dl>

                @if ($evenement->contenu)
                    <div class="mt-5 pt-5 border-t border-brand-100 text-sm sm:text-base text-slate-700 leading-relaxed break-words">
                        {!! nl2br(e($evenement->contenu)) !!}
                    </div>
                @endif

                @if ($evenement->fichier)
                    <!-- Fichier joint (bloc teinté) -->
                    <div class="mt-5 rounded-lg bg-brand-50 border border-brand-100 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h3 class="text-sm font-semibold text-slate-900">Fichier joint</h3>
                        <a href="{{ asset('storage/' . $evenement->fichier) }}" download class="{{ $btnSecondaire }} w-full sm:w-auto">
                            <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger le fichier
                        </a>
                    </div>
                @endif

                <!-- Destinataires -->
                <div class="mt-5 pt-5 border-t border-brand-100">
                    <h3 class="text-sm font-semibold text-slate-900">Destinataires</h3>
                    @if ($evenement->partage_tous)
                        <p class="mt-2">
                            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-md bg-accent-50 text-accent-700">Tous les utilisateurs</span>
                        </p>
                    @else
                        <ul class="mt-2 flex flex-wrap gap-2">
                            @foreach ($evenement->utilisateurssCibles as $u)
                                <li class="bg-white border border-brand-100 text-slate-700 text-xs px-2.5 py-1 rounded-md">{{ $u->nom_complet }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </article>

        <a href="{{ route('admin.evenements.index') }}" class="{{ $lien }} inline-flex items-center gap-2">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour
        </a>
    </div>
@endsection
