@extends('layouts.' . Auth::user()->role)
@section('titre', 'Modifier le document')
@section('breadcrumb', 'Documents > Modifier')
@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-0">
        <div class="card p-5 sm:p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Modifier le document</h2>
                    <p class="text-slate-400 text-sm truncate">{{ $document->titre }}</p>
                </div>
            </div>

            <form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data"
                class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Titre -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Titre *</label>
                    <input type="text" name="titre" value="{{ old('titre', $document->titre) }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition text-sm">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm resize-none">{{ old('description', $document->description) }}</textarea>
                </div>

                <!-- Fichier (optionnel) -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Fichier actuel</label>
                    <div class="p-3 bg-slate-50 rounded-xl flex items-center gap-3 mb-3">
                        <i class="fas fa-file text-indigo-400"></i>
                        <span class="text-slate-600 text-sm flex-1 truncate">
                            {{ basename($document->fichier) }}
                        </span>
                        <a href="{{ route('documents.telecharger', $document) }}" download
                            class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                            <i class="fas fa-download mr-1"></i>Télécharger
                        </a>
                    </div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-upload mr-1 text-amber-500"></i>Remplacer le fichier
                        <span class="text-slate-400 font-normal">(optionnel — tous formats, max 50 Mo)</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:border-amber-400 transition-colors cursor-pointer"
                        id="dropZone">
                        <input type="file" name="fichier" id="fichierInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.jpg,.jpeg,.png,.zip,.rar,.7z" class="hidden">
                        <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-3"></i>
                        <p class="text-slate-500 text-sm font-medium" id="fileLabel">
                            Glissez un nouveau fichier ici ou <span class="text-amber-600 hover:underline">parcourez</span>
                        </p>
                        <p class="text-slate-400 text-xs mt-1">Laissez vide pour conserver le fichier actuel</p>
                    </div>
                </div>

                <!-- Partage -->
                <div class="p-5 bg-slate-50 rounded-2xl">
                    <div class="flex items-center gap-3 mb-4">
                        <input type="checkbox" name="partage_tous" id="partageTous" value="1"
                            {{ old('partage_tous', $document->partage_tous) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 rounded">
                        <label for="partageTous" class="text-sm font-medium text-slate-700 cursor-pointer">
                            <i class="fas fa-globe mr-1 text-green-500"></i>Partager avec tous les utilisateurs
                        </label>
                    </div>
                    <div id="partageSelectif" class="{{ old('partage_tous', $document->partage_tous) ? 'hidden' : '' }}">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Ou sélectionner des utilisateurs
                            :</label>
                        <select name="partages[]" multiple
                            class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-amber-400 transition"
                            size="5">
                            @foreach ($users as $u)
                                @php
                                    $selected = in_array(
                                        $u->id,
                                        old('partages', $document->partagesAvec->pluck('id')->toArray()),
                                    );
                                @endphp
                                <option value="{{ $u->id }}" {{ $selected ? 'selected' : '' }}>
                                    {{ $u->nom_complet }} ({{ ucfirst($u->role) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">Maintenez Ctrl/Cmd pour sélectionner plusieurs</p>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>Mettre à jour
                    </button>
                    <a href="{{ route('documents.index') }}"
                        class="flex-1 sm:flex-none px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Gestion du drag & drop pour le fichier
        const dropZone = document.getElementById('dropZone');
        const input = document.getElementById('fichierInput');
        const label = document.getElementById('fileLabel');

        dropZone.addEventListener('click', () => input.click());

        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('border-amber-400', 'bg-amber-50');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-amber-400', 'bg-amber-50');
        });

        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('border-amber-400', 'bg-amber-50');
            if (e.dataTransfer.files[0]) {
                input.files = e.dataTransfer.files;
                updateLabel(e.dataTransfer.files[0].name);
            }
        });

        input.addEventListener('change', function() {
            if (this.files[0]) {
                updateLabel(this.files[0].name);
            } else {
                // Réinitialiser si aucun fichier sélectionné
                label.innerHTML =
                    `Glissez un nouveau fichier ici ou <span class="text-amber-600 hover:underline">parcourez</span>`;
            }
        });

        function updateLabel(name) {
            label.innerHTML =
                `<i class="fas fa-check-circle text-green-500 mr-2"></i><span class="text-slate-700 font-medium">${name}</span>`;
        }

        // Toggle du sélecteur d'utilisateurs en fonction de la case "partage_tous"
        document.getElementById('partageTous').addEventListener('change', function() {
            document.getElementById('partageSelectif').classList.toggle('hidden', this.checked);
        });
    </script>
@endpush
