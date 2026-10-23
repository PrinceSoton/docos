@extends('layouts.admin')
@section('titre', 'Envoyer l\'attestation')
@section('breadcrumb', 'Attestations > Envoi document')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $fichier = 'w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:!bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:!text-slate-700 hover:file:!bg-slate-100 file:cursor-pointer';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors whitespace-nowrap ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors whitespace-nowrap ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">

        <!-- Demande concernée (bloc de synthèse) -->
        <section class="rounded-xl bg-brand-50 border border-brand-100 p-5 sm:p-6">
            <p class="text-xs text-slate-600">Envoyer le document</p>
            <h2 class="mt-0.5 text-base font-semibold text-slate-900 break-words">
                <span class="capitalize">{{ $attestation->type }}</span> pour
                {{ $attestation->stagiaire->user->nom_complet ?? '—' }}
            </h2>
            <div class="mt-4 flex items-center gap-3 min-w-0">
                <span
                    class="h-10 w-10 rounded-full bg-brand-100 text-brand-800 text-sm font-semibold flex items-center justify-center flex-shrink-0"
                    aria-hidden="true">{{ strtoupper(substr($attestation->stagiaire->user->prenom ?? '', 0, 1)) }}</span>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-900 break-words">
                        {{ $attestation->stagiaire->user->nom_complet ?? '—' }}</p>
                    <p class="text-xs text-slate-600 tabular-nums">{{ $attestation->stagiaire->matricule }} — Demande
                        #{{ $attestation->id }}</p>
                </div>
            </div>
        </section>

        <!-- Formulaire d'envoi -->
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <form action="{{ route('admin.attestations.upload', $attestation) }}" method="POST"
                enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label for="fichierInput" class="block text-sm font-medium text-slate-700 mb-1">Document à envoyer
                        <span class="text-red-700" aria-hidden="true">*</span>
                        <span class="font-normal text-slate-500">(PDF, DOC, DOCX, JPG, PNG...)</span></label>
                    <div id="dropZone"
                        class="rounded-xl border-2 border-dashed border-brand-200 bg-brand-50 p-5 sm:p-6 transition-colors cursor-pointer">
                        <div class="flex flex-col lg:flex-row items-center gap-5">
                            <img src="{{ asset('images/illustrations/document-upload.svg') }}" alt=""
                                class="w-28 lg:w-32 h-auto flex-shrink-0">
                            <div class="w-full min-w-0 text-center lg:text-left">
                                <div id="dropContent" aria-live="polite">
                                    <p class="text-sm font-medium text-slate-900">Glissez le document ici ou choisissez un
                                        fichier</p>
                                    <p class="mt-1 text-xs text-slate-600">Tous formats — Max 20 Mo</p>
                                </div>
                                <input type="file" name="fichier" id="fichierInput" required
                                    class="mt-3 {{ $fichier }}">
                            </div>
                        </div>
                    </div>
                    @error('fichier')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="commentaire" class="block text-sm font-medium text-slate-700 mb-1">Commentaire
                        <span class="font-normal text-slate-500">(optionnel)</span></label>
                    <textarea name="commentaire" id="commentaire" rows="3" class="{{ $champ }} resize-y"
                        placeholder="Message à destination du stagiaire...">{{ old('commentaire') }}</textarea>
                    @error('commentaire')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-brand-100">
                    <a href="{{ route('admin.attestations.show', $attestation) }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>Envoyer le document
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        const dz = document.getElementById('dropZone');
        const fi = document.getElementById('fichierInput');
        const repos = ['border-brand-200', 'bg-brand-50'];
        const actif = ['border-accent-500', 'bg-accent-50'];
        let fichierChoisi = false;

        function etatActif() {
            dz.classList.remove(...repos);
            dz.classList.add(...actif);
        }

        function etatRepos() {
            if (fichierChoisi) return;
            dz.classList.remove(...actif);
            dz.classList.add(...repos);
        }

        // Un clic dans la zone ouvre le sélecteur (le champ natif reste utilisable directement)
        dz.addEventListener('click', e => {
            if (e.target !== fi) fi.click();
        });
        dz.addEventListener('dragover', e => {
            e.preventDefault();
            etatActif();
        });
        dz.addEventListener('dragleave', () => etatRepos());
        dz.addEventListener('drop', e => {
            e.preventDefault();
            if (e.dataTransfer.files[0]) {
                fi.files = e.dataTransfer.files;
                showFile(e.dataTransfer.files[0].name);
            } else {
                etatRepos();
            }
        });
        fi.addEventListener('change', function() {
            if (this.files[0]) showFile(this.files[0].name);
        });

        function showFile(name) {
            const contenu = document.getElementById('dropContent');
            contenu.innerHTML = '';
            const nom = document.createElement('p');
            nom.className = 'text-sm font-medium text-slate-900 break-all';
            nom.textContent = name;
            const etat = document.createElement('p');
            etat.className = 'mt-1 text-xs font-medium text-accent-700';
            etat.textContent = "Fichier prêt à l'envoi";
            contenu.append(nom, etat);
            fichierChoisi = true;
            etatActif();
        }
    </script>
@endpush
