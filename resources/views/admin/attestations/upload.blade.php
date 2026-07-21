@extends('layouts.admin')
@section('titre', 'Envoyer l\'attestation')
@section('breadcrumb', 'Attestations > Envoi document')
@section('content')
    <div class="max-w-xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-upload text-green-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Envoyer le document</h2>
                    <p class="text-slate-400 text-sm capitalize">{{ $attestation->type }} pour
                        {{ $attestation->stagiaire->user->nom_complet ?? '—' }}</p>
                </div>
            </div>

            <!-- Info stagiaire -->
            <div class="p-4 bg-indigo-50 rounded-2xl mb-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-indigo-200 flex items-center justify-center font-bold text-indigo-700 flex-shrink-0">
                        {{ strtoupper(substr($attestation->stagiaire->user->prenom ?? '', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $attestation->stagiaire->user->nom_complet ?? '—' }}</p>
                        <p class="text-slate-500 text-sm">{{ $attestation->stagiaire->matricule }} — Demande
                            #{{ $attestation->id }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.attestations.upload', $attestation) }}" method="POST"
                enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')

                <!-- Zone de dépôt fichier -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-file mr-1 text-indigo-500"></i>Document à envoyer *
                        <span class="text-slate-400 font-normal">(PDF, DOC, DOCX, JPG, PNG...)</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:border-green-400 transition cursor-pointer"
                        id="dropZone">
                        <input type="file" name="fichier" id="fichierInput" required class="hidden">
                        <div id="dropContent">
                            <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-3"></i>
                            <p class="text-slate-500 text-sm font-medium">Glissez le document ici ou
                                <span class="text-green-600 hover:underline">parcourez</span>
                            </p>
                            <p class="text-slate-400 text-xs mt-1">Tous formats — Max 20 Mo</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Commentaire (optionnel)</label>
                    <textarea name="commentaire" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-green-400 transition text-sm resize-none"
                        placeholder="Message à destination du stagiaire...">{{ old('commentaire') }}</textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-paper-plane mr-2"></i>Envoyer le document
                    </button>
                    <a href="{{ route('admin.attestations.show', $attestation) }}"
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
        const fi = document.getElementById('fichierInput');
        dz.addEventListener('click', () => fi.click());
        dz.addEventListener('dragover', e => {
            e.preventDefault();
            dz.classList.add('border-green-400', 'bg-green-50');
        });
        dz.addEventListener('dragleave', () => dz.classList.remove('border-green-400', 'bg-green-50'));
        dz.addEventListener('drop', e => {
            e.preventDefault();
            dz.classList.remove('border-green-400', 'bg-green-50');
            if (e.dataTransfer.files[0]) {
                fi.files = e.dataTransfer.files;
                showFile(e.dataTransfer.files[0].name);
            }
        });
        fi.addEventListener('change', function() {
            if (this.files[0]) showFile(this.files[0].name);
        });

        function showFile(name) {
            document.getElementById('dropContent').innerHTML = `
            <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
            <p class="text-slate-700 font-semibold text-sm">${name}</p>
            <p class="text-green-500 text-xs mt-1">Fichier prêt à l'envoi</p>`;
            dz.classList.add('border-green-400', 'bg-green-50/50');
        }
    </script>
@endpush
