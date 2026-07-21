@extends('layouts.admin')
@section('titre', 'Nouvelle archive')
@section('breadcrumb', 'Archives > Créer')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-archive text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Nouvelle archive</h2>
                    <p class="text-slate-400 text-sm">Archivez des documents et informations</p>
                </div>
            </div>

            <form action="{{ route('admin.archives.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Type d'archive *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition hover:border-indigo-400
                        {{ old('type', 'autre') === 'stagiaire' ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200' }}">
                            <input type="radio" name="type" value="stagiaire"
                                {{ old('type') === 'stagiaire' ? 'checked' : '' }} class="text-indigo-600"
                                id="typeStagiaire">
                            <div>
                                <p class="font-semibold text-slate-800 text-sm"><i
                                        class="fas fa-user-graduate text-indigo-500 mr-1"></i>Stagiaire</p>
                                <p class="text-xs text-slate-400">Archive liée à un stagiaire</p>
                            </div>
                        </label>
                        <label
                            class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition hover:border-indigo-400
                        {{ old('type', 'autre') === 'autre' ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200' }}">
                            <input type="radio" name="type" value="autre"
                                {{ old('type', 'autre') === 'autre' ? 'checked' : '' }} class="text-indigo-600"
                                id="typeAutre">
                            <div>
                                <p class="font-semibold text-slate-800 text-sm"><i
                                        class="fas fa-folder text-amber-500 mr-1"></i>Autre</p>
                                <p class="text-xs text-slate-400">Archive générale</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="stagiaireSelect" class="{{ old('type') !== 'stagiaire' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Stagiaire *</label>
                    <select name="stagiaire_id"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        <option value="">-- Sélectionner un stagiaire --</option>
                        @foreach ($stagiaires as $stag)
                            <option value="{{ $stag->id }}" {{ old('stagiaire_id') == $stag->id ? 'selected' : '' }}>
                                {{ $stag->user->nom_complet }} — {{ $stag->matricule }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Les informations du stagiaire seront automatiquement intégrées.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Titre *</label>
                    <input type="text" name="titre" value="{{ old('titre') }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm resize-none">{{ old('description') }}</textarea>
                </div>

                <!-- Fichiers multiples -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-paperclip mr-1 text-indigo-500"></i>Fichiers
                        <span class="text-slate-400 font-normal">(Tous formats, sans limite)</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-indigo-400 transition cursor-pointer"
                        id="dropZone">
                        <input type="file" name="fichiers[]" id="fichiersInput" multiple class="hidden">
                        <i class="fas fa-cloud-upload-alt text-3xl text-slate-300 mb-2"></i>
                        <p class="text-slate-500 text-sm">Glissez vos fichiers ou <span
                                class="text-indigo-600">parcourez</span></p>
                        <div id="fileList" class="mt-3 space-y-1"></div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Créer l'archive
                    </button>
                    <a href="{{ route('admin.archives.index') }}"
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
        document.querySelectorAll('input[name=type]').forEach(r => {
            r.addEventListener('change', function() {
                document.getElementById('stagiaireSelect').classList.toggle('hidden', this.value !==
                    'stagiaire');
            });
        });

        const dz = document.getElementById('dropZone');
        const fi = document.getElementById('fichiersInput');
        dz.addEventListener('click', () => fi.click());
        dz.addEventListener('dragover', e => {
            e.preventDefault();
            dz.classList.add('border-indigo-400', 'bg-indigo-50/50');
        });
        dz.addEventListener('dragleave', () => dz.classList.remove('border-indigo-400', 'bg-indigo-50/50'));
        dz.addEventListener('drop', e => {
            e.preventDefault();
            dz.classList.remove('border-indigo-400', 'bg-indigo-50/50');
            fi.files = e.dataTransfer.files;
            showFiles();
        });
        fi.addEventListener('change', showFiles);

        function showFiles() {
            const list = document.getElementById('fileList');
            list.innerHTML = '';
            Array.from(fi.files).forEach(f => {
                list.innerHTML += `<div class="flex items-center gap-2 text-xs text-slate-600 bg-white border border-slate-200 rounded-lg px-3 py-1.5">
                <i class="fas fa-file text-indigo-400"></i>${f.name} <span class="text-slate-400">${(f.size/1024).toFixed(1)}Ko</span>
            </div>`;
            });
        }
    </script>
@endpush
