{{-- Présentation mentor de la vue partagée documents/create (incluse uniquement pour le rôle mentor) --}}
@php
    $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
    $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
    // Variante du champ fichier pour un fond teinté brand-50 (texte secondaire slate-600 minimum)
    $fichier = 'w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border file:border-solid file:border-brand-200 file:!bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:!text-brand-800 hover:file:!bg-brand-100';
    $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
    $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
@endphp

<div class="max-w-3xl mx-auto">
    <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
        <h2 class="text-base font-semibold text-slate-900">Nouveau document</h2>
        <p class="mt-1 text-sm text-slate-500">Téléversez et partagez un fichier.</p>

        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-5">
            @csrf
            <div>
                <label for="doc-titre" class="block text-sm font-medium text-slate-700 mb-1">Titre *</label>
                <input type="text" name="titre" id="doc-titre" value="{{ old('titre') }}" required class="{{ $champ }}">
                @error('titre')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="doc-description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" id="doc-description" rows="3" class="{{ $champ }} resize-none">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fichier : zone de dépôt -->
            <div>
                <label for="fichierInput" class="block text-sm font-medium text-slate-700 mb-1">Fichier *</label>
                <div id="dropZone"
                    class="rounded-xl border-2 border-dashed border-brand-200 bg-brand-50 p-5 sm:p-6 transition-colors">
                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        <img src="{{ asset('images/illustrations/document-upload.svg') }}" alt=""
                            class="w-28 sm:w-32 h-auto flex-shrink-0">
                        <div class="w-full min-w-0 text-center sm:text-left">
                            <p class="text-sm font-medium text-slate-900">Glissez votre fichier dans ce cadre ou
                                choisissez-le ci-dessous.</p>
                            <p class="mt-1 text-xs text-slate-600">Tous formats acceptés — Max 50 Mo</p>
                            <input type="file" name="fichier" id="fichierInput" required class="mt-3 {{ $fichier }}">
                            <p class="mt-2 text-xs text-slate-600" id="fileLabel" aria-live="polite">Aucun fichier
                                sélectionné pour l'instant.</p>
                        </div>
                    </div>
                </div>
                @error('fichier')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <!-- Partage -->
            <fieldset class="rounded-lg bg-brand-50 border border-brand-100 p-4">
                <legend class="sr-only">Partage du document</legend>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="partage_tous" id="partageTous" value="1"
                        {{ old('partage_tous') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-slate-300 accent-brand-800 {{ $focus }}">
                    <label for="partageTous" class="text-sm font-medium text-slate-900 cursor-pointer">Partager avec tous
                        les utilisateurs</label>
                </div>
                <div id="partageSelectif" class="mt-4 {{ old('partage_tous') ? 'hidden' : '' }}">
                    <label for="doc-partages" class="block text-sm font-medium text-slate-700 mb-1">Ou sélectionner des
                        utilisateurs</label>
                    <select name="partages[]" id="doc-partages" multiple size="5" class="{{ $champ }}">
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}"
                                {{ in_array($u->id, (array) old('partages', [])) ? 'selected' : '' }}>
                                {{ $u->nom_complet }} ({{ ucfirst($u->role) }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-600">Maintenez Ctrl/Cmd pour sélectionner plusieurs</p>
                    @error('partages')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </fieldset>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-1">
                <a href="{{ route('documents.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                <button type="submit" class="{{ $btnPrimaire }}">
                    <i class="fas fa-upload text-xs" aria-hidden="true"></i>Téléverser
                </button>
            </div>
        </form>
    </section>
</div>

@push('scripts')
    <script>
        (function() {
            const dropZone = document.getElementById('dropZone');
            const input = document.getElementById('fichierInput');
            const label = document.getElementById('fileLabel');
            const repos = ['border-brand-200', 'bg-brand-50'];
            const actif = ['border-accent-500', 'bg-accent-50'];

            function activer() {
                dropZone.classList.remove(...repos);
                dropZone.classList.add(...actif);
            }

            function desactiver() {
                dropZone.classList.remove(...actif);
                dropZone.classList.add(...repos);
            }

            dropZone.addEventListener('dragover', e => {
                e.preventDefault();
                activer();
            });
            dropZone.addEventListener('dragleave', e => {
                // Ignore le passage du pointeur sur les éléments internes de la zone
                if (!dropZone.contains(e.relatedTarget)) desactiver();
            });
            dropZone.addEventListener('drop', e => {
                e.preventDefault();
                desactiver();
                if (e.dataTransfer.files[0]) {
                    input.files = e.dataTransfer.files;
                    updateLabel(e.dataTransfer.files[0].name);
                }
            });
            input.addEventListener('change', function() {
                if (this.files[0]) updateLabel(this.files[0].name);
            });

            function updateLabel(name) {
                label.textContent = 'Fichier prêt : ' + name;
                label.classList.remove('text-slate-600');
                label.classList.add('text-slate-900', 'font-medium');
            }

            document.getElementById('partageTous').addEventListener('change', function() {
                document.getElementById('partageSelectif').classList.toggle('hidden', this.checked);
            });
        })();
    </script>
@endpush
