@extends('layouts.admin')
@section('titre', 'Archive')
@section('breadcrumb', 'Archives > Détail')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $btnDanger = 'inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 transition-colors ' . $focus;
        $btnPetitSecondaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $btnIconeDanger = 'inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-red-200 bg-white text-red-700 hover:bg-red-50 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">

        <!-- En-tête de l'archive -->
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="min-w-0">
                    <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-md {{ $archive->type === 'stagiaire' ? 'bg-brand-50 text-brand-800' : 'bg-slate-100 text-slate-700' }}">
                        {{ ucfirst($archive->type) }}
                    </span>
                    <h2 class="mt-2 text-lg font-semibold text-slate-900 leading-snug break-words">{{ $archive->titre }}</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Créé le <span class="tabular-nums">{{ $archive->created_at->format('d/m/Y') }}</span> par
                        {{ $archive->creePar?->nom_complet }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 flex-shrink-0">
                    <a href="{{ route('admin.archives.edit', $archive) }}" class="{{ $btnSecondaire }} whitespace-nowrap">
                        <i class="fas fa-pen text-xs" aria-hidden="true"></i>Modifier
                    </a>
                    <form action="{{ route('admin.archives.destroy', $archive) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="button" class="btn-delete {{ $btnDanger }} whitespace-nowrap">
                            <i class="fas fa-trash text-xs" aria-hidden="true"></i>Supprimer
                        </button>
                    </form>
                </div>
            </div>

            @if ($archive->description)
                <div class="mt-5 pt-5 border-t border-brand-100">
                    <h3 class="text-xs font-medium text-slate-500">Description</h3>
                    <p class="mt-1 text-sm text-slate-700 leading-relaxed break-words">{{ $archive->description }}</p>
                </div>
            @endif

            @if ($archive->stagiaire)
                <!-- Stagiaire lié (bloc teinté) -->
                <div class="mt-5 rounded-lg bg-brand-50 border border-brand-100 p-4">
                    <h3 class="text-xs font-medium text-slate-600">Stagiaire</h3>
                    <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <span class="h-10 w-10 flex-shrink-0 rounded-full bg-brand-100 text-brand-800 flex items-center justify-center text-sm font-semibold" aria-hidden="true">
                                {{ strtoupper(substr($archive->stagiaire->user->prenom ?? '', 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900 break-words">{{ $archive->stagiaire->user->nom_complet ?? '—' }}</p>
                                <p class="text-sm text-slate-600 break-words">
                                    <span class="tabular-nums">{{ $archive->stagiaire->matricule }}</span> •
                                    {{ $archive->stagiaire->ecole ?: '' }}
                                </p>
                                <p class="text-xs text-slate-600 tabular-nums">
                                    {{ $archive->stagiaire->date_debut->format('d/m/Y') }} →
                                    {{ $archive->stagiaire->date_fin->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        @if ($archive->stagiaire->cv)
                            <a href="{{ asset('storage/' . $archive->stagiaire->cv) }}" download
                                class="{{ $btnPetitSecondaire }} whitespace-nowrap self-start sm:self-center">
                                <i class="fas fa-download text-xs" aria-hidden="true"></i>CV
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </section>

        <!-- Fichiers -->
        <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
            <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
                <h3 class="text-base font-semibold text-slate-900">
                    Fichiers <span class="font-normal text-slate-600 tabular-nums">({{ $archive->fichiers->count() }})</span>
                </h3>
            </div>
            @if ($archive->fichiers->isEmpty())
                <div class="px-6 py-8 text-center">
                    <p class="text-sm font-medium text-slate-700">Aucun fichier dans cette archive</p>
                    <p class="mt-1 text-sm text-slate-500">Ajoutez des fichiers depuis la page de modification.</p>
                </div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach ($archive->fichiers as $fichier)
                        <li class="flex flex-col sm:flex-row sm:items-center gap-3 px-5 sm:px-6 py-3.5 hover:bg-brand-50/60 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate" title="{{ $fichier->nom_original }}">{{ $fichier->nom_original }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 tabular-nums">{{ strtoupper($fichier->type_fichier) }} •
                                    {{ $fichier->taille ? round($fichier->taille / 1024, 1) . 'Ko' : '' }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <a href="{{ route('admin.archives.telecharger', $fichier) }}"
                                    class="{{ $btnPetitSecondaire }} whitespace-nowrap">
                                    <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger
                                </a>
                                <form action="{{ route('admin.archives.supprimerFichier', $fichier) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                        aria-label="Supprimer le fichier {{ $fichier->nom_original }}" title="Supprimer">
                                        <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <a href="{{ route('admin.archives.index') }}" class="{{ $lien }} inline-flex items-center gap-2">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour aux archives
        </a>
    </div>
@endsection
