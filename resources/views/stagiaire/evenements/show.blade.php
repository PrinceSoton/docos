@extends('layouts.stagiaire')
@section('titre', $evenement->titre)
@section('breadcrumb', 'Événements > Détail')
@section('content')
    <div class="max-w-3xl mx-auto space-y-5">
        <div class="card overflow-hidden" data-aos="fade-up">
            @if ($evenement->image)
                <div class="h-52 overflow-hidden">
                    <img src="{{ asset('storage/' . $evenement->image) }}" class="w-full h-full object-cover">
                </div>
            @endif
            <div class="p-8">
                @php $tc = ['information'=>'bg-blue-100 text-blue-700','evenement'=>'bg-purple-100 text-purple-700','note'=>'bg-amber-100 text-amber-700']; @endphp
                <span
                    class="px-3 py-1 rounded-full text-xs font-semibold {{ $tc[$evenement->type] ?? '' }} mb-3 inline-block">{{ ucfirst($evenement->type) }}</span>
                <h2 class="text-slate-800 font-black text-2xl mb-2">{{ $evenement->titre }}</h2>
                <p class="text-slate-400 text-sm mb-4">
                    Publié le {{ $evenement->created_at->format('d/m/Y à H:i') }}
                    @if ($evenement->date_evenement)
                        • <i class="fas fa-calendar mx-1"></i>{{ $evenement->date_evenement->format('d/m/Y à H:i') }}
                    @endif
                </p>
                @if ($evenement->contenu)
                    <div class="text-slate-700 leading-relaxed text-sm">
                        {!! nl2br(e($evenement->contenu)) !!}
                    </div>
                @endif
                @if ($evenement->fichier)
                    <div class="mt-5 p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                        <a href="{{ route('stagiaire.evenements.telecharger', $evenement) }}" download
                            class="flex items-center gap-2 text-indigo-700 hover:text-indigo-900 font-medium">
                            <i class="fas fa-download"></i>Télécharger le fichier joint
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <a href="{{ route('stagiaire.evenements.index') }}"
            class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour
        </a>
    </div>
@endsection
