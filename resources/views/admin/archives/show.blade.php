@extends('layouts.admin')
@section('titre', 'Archive')
@section('breadcrumb', 'Archives > Détail')
@section('content')
    <div class="max-w-3xl mx-auto space-y-5">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-archive text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-slate-800 font-bold text-xl">{{ $archive->titre }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span
                                class="px-2 py-0.5 rounded-lg text-xs font-semibold {{ $archive->type === 'stagiaire' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($archive->type) }}
                            </span>
                            <span class="text-slate-400 text-xs">Créé le {{ $archive->created_at->format('d/m/Y') }} par
                                {{ $archive->creePar?->nom_complet }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.archives.edit', $archive) }}"
                        class="flex items-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-amber-600 transition">
                        <i class="fas fa-edit"></i>Modifier
                    </a>
                    <form action="{{ route('admin.archives.destroy', $archive) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="button"
                            class="btn-delete flex items-center gap-2 bg-red-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-red-600 transition">
                            <i class="fas fa-trash"></i>Supprimer
                        </button>
                    </form>
                </div>
            </div>

            @if ($archive->description)
                <div class="p-4 bg-slate-50 rounded-xl mb-5">
                    <p class="text-xs text-slate-400 uppercase font-medium mb-1">Description</p>
                    <p class="text-slate-700 text-sm">{{ $archive->description }}</p>
                </div>
            @endif

            <!-- Stagiaire lié -->
            @if ($archive->stagiaire)
                <div class="p-5 bg-indigo-50 rounded-2xl mb-5">
                    <p class="text-xs text-indigo-400 uppercase font-medium mb-3">Stagiaire</p>
                    <div class="flex items-center gap-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-200 flex items-center justify-center font-bold text-indigo-700 flex-shrink-0">
                            {{ strtoupper(substr($archive->stagiaire->user->prenom ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800">{{ $archive->stagiaire->user->nom_complet ?? '—' }}</p>
                            <p class="text-slate-500 text-sm">{{ $archive->stagiaire->matricule }} •
                                {{ $archive->stagiaire->ecole ?: '' }}</p>
                            <p class="text-slate-400 text-xs">{{ $archive->stagiaire->date_debut->format('d/m/Y') }} →
                                {{ $archive->stagiaire->date_fin->format('d/m/Y') }}</p>
                        </div>
                        @if ($archive->stagiaire->cv)
                            <a href="{{ asset('storage/' . $archive->stagiaire->cv) }}" download
                                class="ml-auto flex items-center gap-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-3 py-2 rounded-xl text-xs font-medium transition">
                                <i class="fas fa-download"></i>CV
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Fichiers -->
            <div>
                <h3 class="text-slate-800 font-bold text-lg mb-4">
                    <i class="fas fa-paperclip text-amber-500 mr-2"></i>Fichiers ({{ $archive->fichiers->count() }})
                </h3>
                @if ($archive->fichiers->isEmpty())
                    <p class="text-slate-400 text-sm text-center py-6">Aucun fichier dans cette archive</p>
                @else
                    <div class="space-y-2">
                        @foreach ($archive->fichiers as $fichier)
                            <div
                                class="flex items-center gap-4 p-4 bg-slate-50 hover:bg-indigo-50 rounded-xl transition group">
                                <div
                                    class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-file text-indigo-500"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-slate-800 font-medium text-sm truncate">{{ $fichier->nom_original }}</p>
                                    <p class="text-slate-400 text-xs">{{ strtoupper($fichier->type_fichier) }} •
                                        {{ $fichier->taille ? round($fichier->taille / 1024, 1) . 'Ko' : '' }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.archives.telecharger', $fichier) }}"
                                        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-xl text-xs font-medium transition hover:shadow-md">
                                        <i class="fas fa-download"></i>Télécharger
                                    </a>
                                    <form action="{{ route('admin.archives.supprimerFichier', $fichier) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            class="btn-delete w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <a href="{{ route('admin.archives.index') }}"
            class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour aux archives
        </a>
    </div>
@endsection
