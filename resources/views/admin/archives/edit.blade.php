@extends('layouts.admin')
@section('titre', 'Modifier archive')
@section('breadcrumb', 'Archives > Modifier')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Modifier l'archive</h2>
                    <p class="text-slate-400 text-sm">{{ $archive->titre }}</p>
                </div>
            </div>

            <form action="{{ route('admin.archives.update', $archive) }}" method="POST" enctype="multipart/form-data"
                class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Titre *</label>
                    <input type="text" name="titre" value="{{ old('titre', $archive->titre) }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm resize-none">{{ old('description', $archive->description) }}</textarea>
                </div>

                <!-- Fichiers existants -->
                @if ($archive->fichiers->count())
                    <div>
                        <p class="text-sm font-medium text-slate-700 mb-2">Fichiers existants</p>
                        <div class="space-y-2">
                            @foreach ($archive->fichiers as $f)
                                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                    <i class="fas fa-file text-indigo-400"></i>
                                    <span class="flex-1 text-sm text-slate-700 truncate">{{ $f->nom_original }}</span>
                                    <a href="{{ route('admin.archives.telecharger', $f) }}"
                                        class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                        <i class="fas fa-download mr-1"></i>Télécharger
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Nouveaux fichiers -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-plus mr-1 text-indigo-500"></i>Ajouter des fichiers
                    </label>
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-amber-400 transition cursor-pointer"
                        id="dropZone">
                        <input type="file" name="fichiers[]" id="fichiersInput" multiple class="hidden">
                        <i class="fas fa-cloud-upload-alt text-3xl text-slate-300 mb-2"></i>
                        <p class="text-slate-500 text-sm">Glissez ou <span class="text-amber-600">parcourez</span></p>
                        <div id="fileList" class="mt-3 space-y-1"></div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Mettre à jour
                    </button>
                    <a href="{{ route('admin.archives.show', $archive) }}"
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
        const dz = document.getElementById('dropZone');
        const fi = document.getElementById('fichiersInput');
        dz.addEventListener('click', () => fi.click());
        fi.addEventListener('change', function() {
            const list = document.getElementById('fileList');
            list.innerHTML = Array.from(this.files).map(f =>
                `<div class="text-xs text-slate-600 bg-white border rounded-lg px-3 py-1.5 flex items-center gap-2"><i class="fas fa-file text-amber-400"></i>${f.name}</div>`
            ).join('');
        });
    </script>
@endpush
