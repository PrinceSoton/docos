@extends('layouts.admin')
@section('titre', 'Modifier archive')
@section('breadcrumb', 'Archives > Modifier')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $champErreur = 'w-full rounded-lg border border-red-400 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20';
        $fichier = 'w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:!bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:!text-slate-700 hover:file:!bg-slate-100 file:cursor-pointer';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $btnPetitSecondaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <p class="text-sm text-slate-500 mb-5 break-words">
                Archive : <span class="font-medium text-slate-900">{{ $archive->titre }}</span>
            </p>

            <form action="{{ route('admin.archives.update', $archive) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf @method('PUT')

                <fieldset class="space-y-4">
                    <legend class="text-base font-semibold text-slate-900">Informations</legend>

                    <div>
                        <label for="titre" class="block text-sm font-medium text-slate-700 mb-1">
                            Titre <span class="text-red-700" aria-hidden="true">*</span>
                        </label>
                        <input type="text" id="titre" name="titre" value="{{ old('titre', $archive->titre) }}" required
                            class="{{ $errors->has('titre') ? $champErreur : $champ }}"
                            @error('titre') aria-invalid="true" @enderror>
                        @error('titre')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3"
                            class="{{ $errors->has('description') ? $champErreur : $champ }} resize-y">{{ old('description', $archive->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                @if ($archive->fichiers->count())
                    <hr class="border-brand-100">

                    <div>
                        <h3 class="text-base font-semibold text-slate-900 mb-3">Fichiers existants</h3>
                        <ul class="rounded-lg border border-brand-100 divide-y divide-slate-100">
                            @foreach ($archive->fichiers as $f)
                                <li class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 px-4 py-3">
                                    <span class="flex-1 min-w-0 text-sm text-slate-700 truncate"
                                        title="{{ $f->nom_original }}">{{ $f->nom_original }}</span>
                                    <a href="{{ route('admin.archives.telecharger', $f) }}"
                                        class="{{ $btnPetitSecondaire }} whitespace-nowrap self-start sm:self-center">
                                        <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <hr class="border-brand-100">

                <fieldset>
                    <legend class="text-base font-semibold text-slate-900 mb-3">Ajouter des fichiers</legend>
                    <div class="rounded-xl border-2 border-dashed border-brand-200 bg-brand-50 p-5 sm:p-6 transition-colors cursor-pointer"
                        id="dropZone">
                        <label for="fichiersInput" class="block text-sm font-medium text-slate-900">
                            Sélectionnez un ou plusieurs fichiers à ajouter à l'archive
                        </label>
                        <p class="mt-1 text-xs text-slate-600">Les fichiers existants sont conservés.</p>
                        <input type="file" name="fichiers[]" id="fichiersInput" multiple class="mt-3 {{ $fichier }}">
                        <div id="fileList" class="mt-3 space-y-1.5" aria-live="polite"></div>
                    </div>
                    @error('fichiers')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                    @error('fichiers.*')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-brand-100">
                    <a href="{{ route('admin.archives.show', $archive) }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const dz = document.getElementById('dropZone');
        const fi = document.getElementById('fichiersInput');
        // Le champ natif est visible dans la zone : on évite d'ouvrir deux fois le sélecteur.
        dz.addEventListener('click', e => {
            if (e.target === fi || e.target.closest('label')) return;
            fi.click();
        });
        fi.addEventListener('change', function() {
            const list = document.getElementById('fileList');
            list.innerHTML = '';
            Array.from(this.files).forEach(f => {
                const ligne = document.createElement('div');
                ligne.className = 'text-xs text-slate-700 bg-white border border-brand-100 rounded-lg px-3 py-1.5 truncate';
                ligne.textContent = f.name;
                list.appendChild(ligne);
            });
        });
    </script>
@endpush
