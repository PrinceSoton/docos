@extends('layouts.admin')
@section('titre', 'Nouvelle archive')
@section('breadcrumb', 'Archives > Créer')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $champErreur = 'w-full rounded-lg border border-red-400 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20';
        $fichier = 'w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:!bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:!text-slate-700 hover:file:!bg-slate-100 file:cursor-pointer';
        $radio = 'h-4 w-4 flex-shrink-0 accent-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $carteChoix = 'flex items-start gap-3 rounded-lg border border-slate-300 bg-white p-4 cursor-pointer transition-colors hover:border-brand-600 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand-600/25';
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <p class="text-sm text-slate-500 mb-5">Archivez des documents et informations.</p>

            <form action="{{ route('admin.archives.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Type et rattachement -->
                <fieldset class="space-y-4">
                    <legend class="text-base font-semibold text-slate-900">Type d'archive</legend>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label for="typeStagiaire" class="{{ $carteChoix }}">
                            <input type="radio" name="type" value="stagiaire"
                                {{ old('type') === 'stagiaire' ? 'checked' : '' }} class="mt-0.5 {{ $radio }}"
                                id="typeStagiaire">
                            <span class="min-w-0">
                                <span class="block text-sm font-medium text-slate-900">Stagiaire</span>
                                <span class="block text-xs text-slate-600">Archive liée à un stagiaire</span>
                            </span>
                        </label>
                        <label for="typeAutre" class="{{ $carteChoix }}">
                            <input type="radio" name="type" value="autre"
                                {{ old('type', 'autre') === 'autre' ? 'checked' : '' }} class="mt-0.5 {{ $radio }}"
                                id="typeAutre">
                            <span class="min-w-0">
                                <span class="block text-sm font-medium text-slate-900">Autre</span>
                                <span class="block text-xs text-slate-600">Archive générale</span>
                            </span>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror

                    <div id="stagiaireSelect" class="{{ old('type') !== 'stagiaire' ? 'hidden' : '' }}">
                        <label for="stagiaire_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Stagiaire <span class="text-red-700" aria-hidden="true">*</span>
                        </label>
                        <select name="stagiaire_id" id="stagiaire_id" aria-describedby="stagiaire_id-aide"
                            class="{{ $errors->has('stagiaire_id') ? $champErreur : $champ }}"
                            @error('stagiaire_id') aria-invalid="true" @enderror>
                            <option value="">-- Sélectionner un stagiaire --</option>
                            @foreach ($stagiaires as $stag)
                                <option value="{{ $stag->id }}" {{ old('stagiaire_id') == $stag->id ? 'selected' : '' }}>
                                    {{ $stag->user->nom_complet }} — {{ $stag->matricule }}
                                </option>
                            @endforeach
                        </select>
                        <p id="stagiaire_id-aide" class="mt-1 text-xs text-slate-500">Les informations du stagiaire seront
                            automatiquement intégrées.</p>
                        @error('stagiaire_id')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <!-- Contenu -->
                <hr class="border-brand-100">

                <fieldset class="space-y-4">
                    <legend class="text-base font-semibold text-slate-900">Informations</legend>

                    <div>
                        <label for="titre" class="block text-sm font-medium text-slate-700 mb-1">
                            Titre <span class="text-red-700" aria-hidden="true">*</span>
                        </label>
                        <input type="text" id="titre" name="titre" value="{{ old('titre') }}" required
                            class="{{ $errors->has('titre') ? $champErreur : $champ }}"
                            @error('titre') aria-invalid="true" @enderror>
                        @error('titre')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3"
                            class="{{ $errors->has('description') ? $champErreur : $champ }} resize-y">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <!-- Fichiers multiples -->
                <hr class="border-brand-100">

                <fieldset>
                    <legend class="text-base font-semibold text-slate-900 mb-3">Fichiers</legend>
                    <div class="rounded-xl border-2 border-dashed border-brand-200 bg-brand-50 p-5 sm:p-6 transition-colors cursor-pointer"
                        id="dropZone">
                        <div class="flex flex-col lg:flex-row items-center gap-5">
                            <img src="{{ asset('images/illustrations/document-upload.svg') }}" alt=""
                                class="w-28 lg:w-32 h-auto flex-shrink-0">
                            <div class="w-full min-w-0 text-center lg:text-left">
                                <label for="fichiersInput" class="block text-sm font-medium text-slate-900">
                                    Glissez vos fichiers ici ou parcourez votre ordinateur
                                </label>
                                <p class="mt-1 text-xs text-slate-600">Tous formats, sans limite. Plusieurs fichiers possibles.</p>
                                <input type="file" name="fichiers[]" id="fichiersInput" multiple class="mt-3 {{ $fichier }}">
                                <div id="fileList" class="mt-3 space-y-1.5" aria-live="polite"></div>
                            </div>
                        </div>
                    </div>
                    @error('fichiers')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                    @error('fichiers.*')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-brand-100">
                    <a href="{{ route('admin.archives.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Créer l'archive
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.querySelectorAll('input[name=type]').forEach(r => {
            r.addEventListener('change', function() {
                document.getElementById('stagiaireSelect').classList.toggle('hidden', this.value !==
                    'stagiaire');
            });
        });

        const dz = document.getElementById('dropZone');
        const fi = document.getElementById('fichiersInput');
        // Le champ natif est visible dans la zone : on évite d'ouvrir deux fois le sélecteur.
        dz.addEventListener('click', e => {
            if (e.target === fi || e.target.closest('label')) return;
            fi.click();
        });
        const actifOn = () => {
            dz.classList.remove('border-brand-200', 'bg-brand-50');
            dz.classList.add('border-accent-500', 'bg-accent-50');
        };
        const actifOff = () => {
            dz.classList.remove('border-accent-500', 'bg-accent-50');
            dz.classList.add('border-brand-200', 'bg-brand-50');
        };
        dz.addEventListener('dragover', e => {
            e.preventDefault();
            actifOn();
        });
        dz.addEventListener('dragleave', actifOff);
        dz.addEventListener('drop', e => {
            e.preventDefault();
            actifOff();
            fi.files = e.dataTransfer.files;
            showFiles();
        });
        fi.addEventListener('change', showFiles);

        function showFiles() {
            const list = document.getElementById('fileList');
            list.innerHTML = '';
            Array.from(fi.files).forEach(f => {
                const ligne = document.createElement('div');
                ligne.className = 'flex items-center gap-2 text-xs text-slate-700 bg-white border border-brand-100 rounded-lg px-3 py-1.5 min-w-0';
                const nom = document.createElement('span');
                nom.className = 'truncate';
                nom.textContent = f.name;
                const taille = document.createElement('span');
                taille.className = 'ml-auto flex-shrink-0 text-slate-500 tabular-nums';
                taille.textContent = (f.size / 1024).toFixed(1) + 'Ko';
                ligne.append(nom, taille);
                list.appendChild(ligne);
            });
        }
    </script>
@endpush
