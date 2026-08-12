@extends('layouts.stagiaire')
@section('titre', 'Déposer un rapport')
@section('breadcrumb', 'Rapports > Nouveau')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-upload text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Déposer un rapport</h2>
                    <p class="text-slate-400 text-sm">Soumettez votre rapport à votre mentor</p>
                </div>
            </div>
            <form action="{{ route('stagiaire.reports.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Titre du rapport *</label>
                    <input type="text" name="titre" value="{{ old('titre') }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Type de rapport *</label>
                        <select name="type" id="type_select" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                            @foreach (['journalier' => 'Journalier', 'hebdomadaire' => 'Hebdomadaire', 'mensuel' => 'Mensuel', 'final' => 'Final', 'autre' => 'Autre'] as $v => $l)
                                <option value="{{ $v }}" {{ old('type') === $v ? 'selected' : '' }}>
                                    {{ $l }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="type_autre_div" style="{{ old('type') == 'autre' ? '' : 'display:none;' }}" class="mt-3">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Précisez le type *</label>
                        <input type="text" name="type_autre" value="{{ old('type_autre') }}"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm"
                            placeholder="Ex: Rapport technique, Bilan mi-parcours...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Projet lié</label>
                        <select name="project_id"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                            <option value="">-- Aucun projet --</option>
                            @foreach ($projets as $p)
                                <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->titre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm resize-none">{{ old('description') }}</textarea>
                </div>

                <!-- Zone de fichier obligatoire -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-paperclip mr-1 text-indigo-500"></i>Fichier du rapport *
                        <span class="text-slate-400 font-normal">(PDF, DOC, DOCX... — Max 20 Mo)</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:border-indigo-400 transition cursor-pointer"
                        id="dropZone">
                        <input type="file" name="fichier" id="fichierInput" required class="hidden">
                        <div id="dropContent">
                            <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-3"></i>
                            <p class="text-slate-500 text-sm font-medium">Glissez votre rapport ici ou
                                <span class="text-indigo-600 hover:underline">parcourez</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-paper-plane mr-2"></i>Déposer le rapport
                    </button>
                    <a href="{{ route('stagiaire.reports.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const dz = document.getElementById('dropZone');
        const fi = document.getElementById('fichierInput');
        dz.addEventListener('click', () => fi.click());
        dz.addEventListener('dragover', e => {
            e.preventDefault();
            dz.classList.add('border-indigo-400', 'bg-indigo-50/50');
        });
        dz.addEventListener('dragleave', () => dz.classList.remove('border-indigo-400', 'bg-indigo-50/50'));
        dz.addEventListener('drop', e => {
            e.preventDefault();
            dz.classList.remove('border-indigo-400', 'bg-indigo-50/50');
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
            document.getElementById('dropContent').innerHTML =
                `<i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i><p class="text-slate-700 font-semibold text-sm">${name}</p><p class="text-green-500 text-xs mt-1">Fichier prêt</p>`;
            dz.classList.add('border-green-400', 'bg-green-50/50');
        }
    </script>
@endpush
