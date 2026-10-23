@extends('layouts.stagiaire')
@section('titre', 'Détail présence')
@section('breadcrumb', 'Présence > Détail')
@section('content')
    @php
        // Libellé, badge et pastille par statut
        $statutsPresence = [
            'present' => ['Présent', 'bg-emerald-50 text-emerald-800', 'bg-emerald-500'],
            'retard' => ['Retard', 'bg-amber-50 text-amber-800', 'bg-amber-500'],
            'absent' => ['Absent', 'bg-red-50 text-red-800', 'bg-red-500'],
        ];
        [$libelle, $cls, $dot] = $statutsPresence[$presence->statut] ?? [ucfirst($presence->statut), 'bg-slate-100 text-slate-700', 'bg-slate-400'];

        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-slate-900">{{ $presence->date->translatedFormat('l d F Y') }}</h2>
                    <p class="mt-1 flex items-center gap-2 text-sm text-slate-500">
                        <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                        Présence du jour
                    </p>
                </div>
                <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $cls }}">{{ $libelle }}</span>
            </div>

            <dl class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 rounded-lg bg-brand-50 border border-brand-100 p-4">
                <div>
                    <dt class="text-xs text-slate-600">Heure d'arrivée</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">
                        {{ $presence->heure_arrivee ? substr($presence->heure_arrivee, 0, 5) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-600">Heure de départ</dt>
                    <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">
                        {{ $presence->heure_depart ? substr($presence->heure_depart, 0, 5) : 'Non marqué' }}</dd>
                </div>
                @if ($presence->motif)
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-slate-600">Motif</dt>
                        <dd class="mt-0.5 text-sm text-slate-900 break-words">{{ $presence->motif }}</dd>
                    </div>
                @endif
                @if ($presence->justificatif)
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-slate-600">Justificatif</dt>
                        <dd class="mt-1">
                            <a href="{{ route('stagiaire.presence.telechargerJustificatif', $presence) }}" download
                                class="inline-flex items-center gap-1.5 {{ $lien }}">
                                <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger le justificatif
                            </a>
                        </dd>
                    </div>
                @endif
            </dl>
        </section>

        <a href="{{ route('stagiaire.presence.index') }}" class="inline-flex items-center gap-2 {{ $lien }}">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour à mes présences
        </a>
    </div>
@endsection
