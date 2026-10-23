@extends('layouts.admin')
@section('titre', 'Modifier publication')
@section('breadcrumb', 'Événements > Modifier')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $champErreur = 'w-full rounded-lg border border-red-400 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20';
        $fichier = 'w-full text-sm text-slate-500 file:mr-3 file:rounded-md file:border-0 file:!bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium file:!text-slate-700 hover:file:!bg-slate-200 file:cursor-pointer';
        $case = 'h-4 w-4 flex-shrink-0 rounded border-slate-300 accent-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <p class="text-sm text-slate-500 mb-5 truncate">
                Publication : <span class="font-medium text-slate-900">{{ $evenement->titre }}</span>
            </p>

            <form action="{{ route('admin.evenements.update', $evenement) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf @method('PUT')

                <!-- Publication -->
                <fieldset class="space-y-4">
                    <legend class="text-base font-semibold text-slate-900">Publication</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-slate-700 mb-1">
                                Type <span class="text-red-700" aria-hidden="true">*</span>
                            </label>
                            <select id="type" name="type" required
                                class="{{ $errors->has('type') ? $champErreur : $champ }}"
                                @error('type') aria-invalid="true" @enderror>
                                <option value="information" {{ $evenement->type === 'information' ? 'selected' : '' }}>
                                    Information</option>
                                <option value="evenement" {{ $evenement->type === 'evenement' ? 'selected' : '' }}>Événement
                                </option>
                                <option value="note" {{ $evenement->type === 'note' ? 'selected' : '' }}>Note</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="date_evenement" class="block text-sm font-medium text-slate-700 mb-1">Date de l'événement</label>
                            <input type="datetime-local" id="date_evenement" name="date_evenement"
                                value="{{ old('date_evenement', $evenement->date_evenement?->format('Y-m-d\TH:i')) }}"
                                class="{{ $errors->has('date_evenement') ? $champErreur : $champ }} tabular-nums"
                                @error('date_evenement') aria-invalid="true" @enderror>
                            @error('date_evenement')
                                <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="titre" class="block text-sm font-medium text-slate-700 mb-1">
                            Titre <span class="text-red-700" aria-hidden="true">*</span>
                        </label>
                        <input type="text" id="titre" name="titre" value="{{ old('titre', $evenement->titre) }}" required
                            class="{{ $errors->has('titre') ? $champErreur : $champ }}"
                            @error('titre') aria-invalid="true" @enderror>
                        @error('titre')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="evenement-contenu" class="block text-sm font-medium text-slate-700 mb-1">Contenu</label>
                        <textarea id="evenement-contenu" name="contenu" rows="5"
                            class="{{ $errors->has('contenu') ? $champErreur : $champ }} resize-y">{{ old('contenu', $evenement->contenu) }}</textarea>
                        @error('contenu')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <hr class="border-brand-100">

                <!-- Pièces jointes -->
                <fieldset>
                    <legend class="text-base font-semibold text-slate-900 mb-4">Pièces jointes</legend>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="min-w-0">
                            <label for="image" class="block text-sm font-medium text-slate-700 mb-1">Image</label>
                            @if ($evenement->image)
                                <img src="{{ asset('storage/' . $evenement->image) }}" alt="Image actuelle"
                                    class="h-20 rounded-lg border border-brand-100 object-cover mb-2">
                            @endif
                            <input type="file" id="image" name="image" accept="image/*" class="{{ $fichier }}">
                            @error('image')
                                <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="min-w-0">
                            <label for="fichier" class="block text-sm font-medium text-slate-700 mb-1">Fichier joint</label>
                            @if ($evenement->fichier)
                                <a href="{{ asset('storage/' . $evenement->fichier) }}" download
                                    class="{{ $lien }} inline-flex items-center gap-1.5 mb-2">
                                    <i class="fas fa-download text-xs" aria-hidden="true"></i>Fichier actuel
                                </a>
                            @endif
                            <input type="file" id="fichier" name="fichier" class="{{ $fichier }}">
                            @error('fichier')
                                <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </fieldset>

                <!-- Destinataires (bloc teinté) -->
                <fieldset class="rounded-lg bg-brand-50 border border-brand-100 p-4 sm:p-5">
                    <legend class="sr-only">Destinataires</legend>
                    <p class="text-sm font-semibold text-slate-900 mb-3">Destinataires</p>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="partage_tous" id="partageTous" value="1"
                            {{ $evenement->partage_tous ? 'checked' : '' }} class="{{ $case }}">
                        <label for="partageTous" class="text-sm font-medium text-slate-900 cursor-pointer">
                            Partager avec tous
                        </label>
                    </div>
                    <div id="destinatairesSelect" class="mt-4 {{ $evenement->partage_tous ? 'hidden' : '' }}">
                        <label for="destinataires" class="block text-sm font-medium text-slate-700 mb-1">Destinataires spécifiques</label>
                        <select id="destinataires" name="destinataires[]" multiple aria-describedby="destinataires-aide"
                            class="{{ $champ }}" size="5">
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}"
                                    {{ $evenement->utilisateurssCibles->contains($u->id) ? 'selected' : '' }}>
                                    {{ $u->nom_complet }} ({{ ucfirst($u->role) }})
                                </option>
                            @endforeach
                        </select>
                        <p id="destinataires-aide" class="mt-1 text-xs text-slate-600">Ctrl/Cmd pour sélection multiple</p>
                        @error('destinataires')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-brand-100">
                    <a href="{{ route('admin.evenements.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('partageTous').addEventListener('change', function() {
            document.getElementById('destinatairesSelect').classList.toggle('hidden', this.checked);
        });
    </script>
@endpush
