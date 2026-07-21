@extends('layouts.stagiaire')
@section('titre', 'Détail présence')
@section('breadcrumb', 'Présence > Détail')
@section('content')
    <div class="max-w-xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                @php $sc = ['present' => ['bg-green-100', 'text-green-600', 'user-check'], 'retard' => ['bg-amber-100', 'text-amber-600', 'clock'], 'absent' => ['bg-red-100', 'text-red-600', 'user-times']]; @endphp
                <div
                    class="w-12 h-12 {{ $sc[$presence->statut][0] ?? 'bg-slate-100' }} rounded-2xl flex items-center justify-center">
                    <i
                        class="fas fa-{{ $sc[$presence->statut][2] ?? 'circle' }} {{ $sc[$presence->statut][1] ?? '' }} text-xl"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl capitalize">{{ $presence->statut }}</h2>
                    <p class="text-slate-400 text-sm">{{ $presence->date->translatedFormat('l d F Y') }}</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="p-4 bg-slate-50 rounded-xl">
                    <p class="text-xs text-slate-400 uppercase font-medium">Heure d'arrivée</p>
                    <p class="font-bold text-slate-800 mt-1">
                        {{ $presence->heure_arrivee ? substr($presence->heure_arrivee, 0, 5) : '—' }}</p>
                </div>
                @if ($presence->motif)
                    <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                        <p class="text-xs text-amber-400 uppercase font-medium">Motif</p>
                        <p class="text-slate-700 text-sm mt-1">{{ $presence->motif }}</p>
                    </div>
                @endif
                @if ($presence->justificatif)
                    <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                        <p class="text-xs text-indigo-400 uppercase font-medium mb-2">Justificatif</p>
                        <a href="{{ route('stagiaire.presence.telechargerJustificatif', $presence) }}" download
                            class="flex items-center gap-2 text-indigo-700 hover:text-indigo-900 font-medium text-sm">
                            <i class="fas fa-download"></i>Télécharger le justificatif
                        </a>
                    </div>
                @endif
            </div>

            <a href="{{ route('stagiaire.presence.index') }}"
                class="mt-6 inline-flex items-center gap-2 text-amber-600 hover:text-amber-800 font-medium text-sm">
                <i class="fas fa-arrow-left"></i>Retour à mes présences
            </a>
        </div>
    </div>
@endsection
