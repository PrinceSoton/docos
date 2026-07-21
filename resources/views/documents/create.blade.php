@extends('layouts.' . Auth::user()->role)
@section('titre', 'Nouveau document')
@section('breadcrumb', 'Documents > Ajouter')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-upload text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Nouveau document</h2>
                    <p class="text-slate-400 text-sm">Téléversez et partagez un fichier</p>
                </div>
            </div>

            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Titre *</label>
                    <input type="text" name="titre" value="{{ old('titre') }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm resize-none">{{ old('description') }}</textarea>
                </div>

                <!-- Zone de fichier -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Fichier *</label>
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:border-indigo-400 transition-colors cursor-pointer"
                        id="dropZone">
                        <input type="file" name="fichier" id="fichierInput" required class="hidden">
                        <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-3"></i>
                        <p class="text-slate-500 text-sm font-medium" id="fileLabel">Glissez un fichier ici ou <span
                                class="text-indigo-600 hover:underline">parcourez</span></p>
                        <p class="text-slate-400 text-xs mt-1">Tous formats acceptés — Max 50 Mo</p>
                    </div>
                </div>

                <!-- Partage -->
                <div class="p-5 bg-slate-50 rounded-2xl">
                    <div class="flex items-center gap-3 mb-4">
                        <input type="checkbox" name="partage_tous" id="partageTous" value="1"
                            {{ old('partage_tous') ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
                        <label for="partageTous" class="text-sm font-medium text-slate-700 cursor-pointer">
                            <i class="fas fa-globe mr-1 text-green-500"></i>Partager avec tous les utilisateurs
                        </label>
                    </div>
                    <div id="partageSelectif" class="{{ old('partage_tous') ? 'hidden' : '' }}">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Ou sélectionner des utilisateurs
                            :</label>
                        <select name="partages[]" multiple
                            class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-indigo-400 transition"
                            size="5">
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}"
                                    {{ in_array($u->id, (array) old('partages', [])) ? 'selected' : '' }}>
                                    {{ $u->nom_complet }} ({{ ucfirst($u->role) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">Maintenez Ctrl/Cmd pour sélectionner plusieurs</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-upload mr-2"></i>Téléverser
                    </button>
                    <a href="{{ route('documents.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const dropZone = document.getElementById('dropZone');
        const input = document.getElementById('fichierInput');
        const label = document.getElementById('fileLabel');

        dropZone.addEventListener('click', () => input.click());
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('border-indigo-400', 'bg-indigo-50');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-indigo-400', 'bg-indigo-50'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('border-indigo-400', 'bg-indigo-50');
            if (e.dataTransfer.files[0]) {
                input.files = e.dataTransfer.files;
                updateLabel(e.dataTransfer.files[0].name);
            }
        });
        input.addEventListener('change', function() {
            if (this.files[0]) updateLabel(this.files[0].name);
        });

        function updateLabel(name) {
            label.innerHTML =
                `<i class="fas fa-check-circle text-green-500 mr-2"></i><span class="text-slate-700 font-medium">${name}</span>`;
        }

        document.getElementById('partageTous').addEventListener('change', function() {
            document.getElementById('partageSelectif').classList.toggle('hidden', this.checked);
        });
    </script>
@endpush
