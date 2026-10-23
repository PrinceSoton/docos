@extends('layouts.mentor')
@section('titre', $evenement->titre)
@section('breadcrumb', 'Événements > Détail')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;

        $types = [
            'information' => ['Information', 'bg-sky-50 text-sky-800'],
            'evenement' => ['Événement', 'bg-brand-50 text-brand-800'],
            'note' => ['Note', 'bg-slate-100 text-slate-700'],
        ];
        [$libType, $clsType] = $types[$evenement->type] ?? [ucfirst((string) $evenement->type), 'bg-slate-100 text-slate-700'];
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">
        <article class="bg-white border border-brand-100 rounded-xl overflow-hidden">
            @if ($evenement->image)
                <div class="h-44 sm:h-56 overflow-hidden bg-brand-50">
                    <img src="{{ asset('storage/' . $evenement->image) }}" alt="" class="w-full h-full object-cover"
                        loading="lazy">
                </div>
            @endif
            <div class="p-5 sm:p-6">
                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-md {{ $clsType }}">{{ $libType }}</span>
                <h2 class="mt-3 text-lg font-semibold text-slate-900 leading-snug break-words">{{ $evenement->titre }}</h2>

                <!-- Métadonnées (bloc teinté) -->
                <dl class="mt-4 rounded-lg bg-brand-50 border border-brand-100 p-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                    @if ($evenement->date_evenement)
                        <div>
                            <dt class="text-xs text-slate-600">Date de l'événement</dt>
                            <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">{{ $evenement->date_evenement->format('d/m/Y à H:i') }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs text-slate-600">Publié</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">
                            {{ $evenement->created_at->format('d/m/Y à H:i') }}
                            <span class="font-normal text-slate-600">({{ $evenement->created_at->diffForHumans() }})</span>
                        </dd>
                    </div>
                    @if ($evenement->creePar)
                        <div class="min-w-0">
                            <dt class="text-xs text-slate-600">Publié par</dt>
                            <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">{{ $evenement->creePar?->nom_complet }}</dd>
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
                            <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger le fichier joint
                        </a>
                    </div>
                @endif
            </div>
        </article>

        <a href="{{ route('mentor.evenements.index') }}" class="{{ $lien }} inline-flex items-center gap-2">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour
        </a>
    </div>
@endsection
