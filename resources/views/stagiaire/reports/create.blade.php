@extends('layouts.stagiaire')
@section('titre', 'Déposer un rapport')
@section('breadcrumb', 'Rapports > Nouveau')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $fichier = 'w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border file:border-solid file:border-brand-200 file:!bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:!text-brand-800 hover:file:!bg-brand-100';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <p class="text-sm text-slate-500 mb-5">Soumettez votre rapport à votre mentor.</p>

            <form action="{{ route('stagiaire.reports.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-5">
                @csrf
                <div>
                    <label for="titre" class="block text-sm font-medium text-slate-700 mb-1">Titre du rapport *</label>
                    <input type="text" id="titre" name="titre" value="{{ old('titre') }}" required
                        class="{{ $champ }}">
                    @error('titre')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="type_select" class="block text-sm font-medium text-slate-700 mb-1">Type de rapport *</label>
                        <select name="type" id="type_select" required class="{{ $champ }}">
                            @foreach (['journalier' => 'Journalier', 'hebdomadaire' => 'Hebdomadaire', 'mensuel' => 'Mensuel', 'final' => 'Final', 'autre' => 'Autre'] as $v => $l)
                                <option value="{{ $v }}" {{ old('type') === $v ? 'selected' : '' }}>
                                    {{ $l }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="project_id" class="block text-sm font-medium text-slate-700 mb-1">Projet lié</label>
                        <select name="project_id" id="project_id" class="{{ $champ }}">
                            <option value="">-- Aucun projet --</option>
                            @foreach ($projets as $p)
                                <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->titre }}</option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div id="type_autre_div" style="{{ old('type') == 'autre' ? '' : 'display:none;' }}">
                    <label for="type_autre" class="block text-sm font-medium text-slate-700 mb-1">Précisez le type *</label>
                    <input type="text" id="type_autre" name="type_autre" value="{{ old('type_autre') }}"
                        class="{{ $champ }}"
                        placeholder="Ex: Rapport technique, Bilan mi-parcours...">
                    @error('type_autre')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="4" class="{{ $champ }} resize-y">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fichier obligatoire : zone de dépôt -->
                <div>
                    <div id="dropZone"
                        class="rounded-xl border-2 border-dashed border-brand-200 bg-brand-50 p-5 sm:p-6 transition-colors">
                        <div class="flex flex-col sm:flex-row items-center gap-5">
                            <img src="{{ asset('images/illustrations/document-upload.svg') }}" alt=""
                                class="w-28 sm:w-32 h-auto flex-shrink-0">
                            <div class="w-full min-w-0 text-center sm:text-left">
                                <label for="fichierInput" class="block text-sm font-medium text-slate-900">Fichier du rapport *</label>
                                <p class="mt-1 text-xs text-slate-600">PDF, DOC, DOCX... — Max 20 Mo</p>
                                <input type="file" name="fichier" id="fichierInput" required class="mt-3 {{ $fichier }}">
                                <p id="dropContent" class="mt-2 text-xs text-slate-600" aria-live="polite">
                                    Vous pouvez aussi glisser votre fichier dans ce cadre.
                                </p>
                            </div>
                        </div>
                    </div>
                    @error('fichier')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
                    <a href="{{ route('stagiaire.reports.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>Déposer le rapport
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const dz = document.getElementById('dropZone');
        const fi = document.getElementById('fichierInput');
        // États visuels de la zone : repos (marine clair), survol actif (vert), fichier prêt (bordure verte)
        const repos = ['border-brand-200', 'bg-brand-50'];
        const survol = ['border-accent-500', 'bg-accent-50'];
        let fichierPret = false;

        function etatZone(actif) {
            dz.classList.remove(...repos, ...survol);
            if (actif) dz.classList.add(...survol);
            else if (fichierPret) dz.classList.add('border-accent-500', 'bg-brand-50');
            else dz.classList.add(...repos);
        }

        dz.addEventListener('dragover', e => {
            e.preventDefault();
            etatZone(true);
        });
        dz.addEventListener('dragleave', () => etatZone(false));
        dz.addEventListener('drop', e => {
            e.preventDefault();
            etatZone(false);
            if (e.dataTransfer.files[0]) {
                fi.files = e.dataTransfer.files;
                showFile(e.dataTransfer.files[0].name);
            }
        });

        document.getElementById('type_select').addEventListener('change', function() {
            var div = document.getElementById('type_autre_div');
            div.style.display = this.value === 'autre' ? 'block' : 'none';
        });

        fi.addEventListener('change', function() {
            if (this.files[0]) showFile(this.files[0].name);
        });

        function showFile(name) {
            const info = document.getElementById('dropContent');
            info.className = 'mt-2 text-xs font-medium text-accent-700 break-all';
            info.textContent = 'Fichier prêt : ' + name;
            fichierPret = true;
            etatZone(false);
        }
    </script>
@endpush
