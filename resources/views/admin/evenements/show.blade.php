@extends('layouts.admin')
@section('titre', $evenement->titre)
@section('breadcrumb', 'Événements > Détail')
@section('content')
    <div class="max-w-3xl mx-auto space-y-5">
        <div class="card overflow-hidden" data-aos="fade-up">
            @if ($evenement->image)
                <div class="h-56 overflow-hidden">
                    <img src="{{ asset('storage/' . $evenement->image) }}" alt="{{ $evenement->titre }}"
                        class="w-full h-full object-cover">
                </div>
            @endif
            <div class="p-8">
                <div class="flex items-start justify-between flex-wrap gap-4 mb-5">
                    <div>
                        @php $tc = ['information'=>'bg-blue-100 text-blue-700','evenement'=>'bg-purple-100 text-purple-700','note'=>'bg-amber-100 text-amber-700']; @endphp
                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $tc[$evenement->type] ?? '' }} mb-3 inline-block">
                            {{ ucfirst($evenement->type) }}
                        </span>
                        <h2 class="text-slate-800 font-black text-2xl">{{ $evenement->titre }}</h2>
                        <p class="text-slate-400 text-sm mt-1">
                            Par {{ $evenement->creePar?->nom_complet }} • {{ $evenement->created_at->diffForHumans() }}
                        </p>
                        @if ($evenement->date_evenement)
                            <p class="text-indigo-600 font-medium text-sm mt-1">
                                <i class="fas fa-calendar mr-1"></i>{{ $evenement->date_evenement->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.evenements.edit', $evenement) }}"
                            class="flex items-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-amber-600 transition">
                            <i class="fas fa-edit"></i>Modifier
                        </a>
                        <form action="{{ route('admin.evenements.destroy', $evenement) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button"
                                class="btn-delete flex items-center gap-2 bg-red-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-red-600 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                @if ($evenement->contenu)
                    <div class="prose prose-sm max-w-none text-slate-700 mb-5">
                        {!! nl2br(e($evenement->contenu)) !!}
                    </div>
                @endif

                @if ($evenement->fichier)
                    <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100 mb-5">
                        <p class="text-xs text-indigo-400 font-medium mb-2">Fichier joint</p>
                        <a href="{{ asset('storage/' . $evenement->fichier) }}" download
                            class="flex items-center gap-2 text-indigo-700 hover:text-indigo-900 font-medium text-sm">
                            <i class="fas fa-download"></i>Télécharger le fichier
                        </a>
                    </div>
                @endif

                <div class="p-4 bg-slate-50 rounded-xl">
                    <p class="text-xs text-slate-400 font-medium uppercase mb-2">Destinataires</p>
                    @if ($evenement->partage_tous)
                        <span class="text-green-600 font-medium text-sm"><i class="fas fa-globe mr-2"></i>Tous les
                            utilisateurs</span>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach ($evenement->utilisateurssCibles as $u)
                                <span
                                    class="bg-white border border-slate-200 text-slate-700 text-xs px-3 py-1 rounded-full">{{ $u->nom_complet }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <a href="{{ route('admin.evenements.index') }}"
            class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour
        </a>
    </div>
@endsection
