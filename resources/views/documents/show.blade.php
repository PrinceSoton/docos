@extends('layouts.' . Auth::user()->role)
@section('titre', 'Document')
@section('breadcrumb', 'Documents > Détail')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-file text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-slate-800 font-bold text-xl">{{ $document->titre }}</h2>
                        <p class="text-slate-400 text-sm">Par {{ $document->user->nom_complet }} •
                            {{ $document->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                <a href="{{ route('documents.telecharger', $document) }}"
                    class="flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all">
                    <i class="fas fa-download"></i>Télécharger
                </a>
            </div>

            <div class="space-y-4">
                @if ($document->description)
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 font-medium uppercase mb-1">Description</p>
                        <p class="text-slate-700 text-sm">{{ $document->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-3 gap-4">
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 font-medium uppercase mb-1">Format</p>
                        <p class="text-slate-700 font-semibold text-sm">
                            {{ $document->type_fichier ? strtoupper($document->type_fichier) : '—' }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 font-medium uppercase mb-1">Taille</p>
                        <p class="text-slate-700 font-semibold text-sm">
                            {{ $document->taille ? round($document->taille / 1024, 1) . ' Ko' : '—' }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 font-medium uppercase mb-1">Partage</p>
                        <p
                            class="font-semibold text-sm {{ $document->partage_tous ? 'text-green-600' : 'text-amber-600' }}">
                            {{ $document->partage_tous ? 'Tous' : 'Limité' }}
                        </p>
                    </div>
                </div>

                @if (!$document->partage_tous && $document->partagesAvec->count())
                    <div class="p-4 bg-indigo-50 rounded-xl">
                        <p class="text-xs text-indigo-400 font-medium uppercase mb-2">Partagé avec</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($document->partagesAvec as $u)
                                <span
                                    class="bg-white border border-indigo-200 text-indigo-700 text-xs px-3 py-1 rounded-full">{{ $u->nom_complet }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex gap-3 mt-6 pt-6 border-t border-slate-100">
                @if ($document->user_id === Auth::id())
                    <a href="{{ route('documents.edit', $document) }}"
                        class="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-medium transition">
                        <i class="fas fa-edit"></i>Modifier
                    </a>
                    <form action="{{ route('documents.destroy', $document) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="button"
                            class="btn-delete flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl font-medium transition">
                            <i class="fas fa-trash"></i>Supprimer
                        </button>
                    </form>
                @endif
                <a href="{{ route('documents.index') }}"
                    class="flex items-center gap-2 border border-slate-200 text-slate-600 px-5 py-2.5 rounded-xl font-medium hover:bg-slate-50 transition ml-auto">
                    <i class="fas fa-arrow-left"></i>Retour
                </a>
            </div>
        </div>
    </div>
@endsection
