@extends('layouts.admin')
@section('titre', 'Modifier mentor')
@section('breadcrumb', 'Mentors > Modifier')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2';
        $bord = fn ($nom) => $errors->has($nom)
            ? 'border-red-600 focus:border-red-600 focus:ring-red-600/25'
            : 'border-slate-300 focus:border-brand-600 focus:ring-brand-600/25';
        $fichier = 'w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:!bg-brand-100 file:px-3 file:py-2 file:text-sm file:font-medium file:!text-brand-800 hover:file:!bg-brand-200';
        $label = 'block text-sm font-medium text-slate-700 mb-1';
        $legende = 'text-base font-semibold text-slate-900 mb-3';
        $requis = '<span class="text-red-700" aria-hidden="true">*</span>';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <div class="mb-5">
                <h2 class="text-base font-semibold text-slate-900 break-words">Modifier : {{ $mentor->user->nom_complet }}</h2>
                <p class="mt-0.5 text-sm text-slate-500">Mettre à jour les informations du mentor. Les champs marqués
                    <span class="text-red-700">*</span> sont obligatoires.</p>
            </div>

            <form action="{{ route('admin.mentors.update', $mentor) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf @method('PUT')

                <!-- Informations personnelles -->
                <fieldset class="space-y-4">
                    <legend class="{{ $legende }}">Informations personnelles</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="prenom" class="{{ $label }}">Prénom {!! $requis !!}</label>
                            <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $mentor->user->prenom) }}" required
                                autocomplete="given-name" class="{{ $champ }} {{ $bord('prenom') }}"
                                @error('prenom') aria-invalid="true" aria-describedby="prenom-erreur" @enderror>
                            @error('prenom')
                                <p id="prenom-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nom" class="{{ $label }}">Nom {!! $requis !!}</label>
                            <input type="text" id="nom" name="nom" value="{{ old('nom', $mentor->user->nom) }}" required
                                autocomplete="family-name" class="{{ $champ }} {{ $bord('nom') }}"
                                @error('nom') aria-invalid="true" aria-describedby="nom-erreur" @enderror>
                            @error('nom')
                                <p id="nom-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="{{ $label }}">Email {!! $requis !!}</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $mentor->user->email) }}" required
                                autocomplete="email" class="{{ $champ }} {{ $bord('email') }}"
                                @error('email') aria-invalid="true" aria-describedby="email-erreur" @enderror>
                            @error('email')
                                <p id="email-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="telephone" class="{{ $label }}">Téléphone</label>
                            <input type="text" id="telephone" name="telephone" value="{{ old('telephone', $mentor->user->telephone) }}"
                                autocomplete="tel" class="{{ $champ }} {{ $bord('telephone') }} tabular-nums"
                                @error('telephone') aria-invalid="true" aria-describedby="telephone-erreur" @enderror>
                            @error('telephone')
                                <p id="telephone-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="{{ $label }}">Nouveau mot de passe</label>
                            <input type="password" id="password" name="password" autocomplete="new-password"
                                class="{{ $champ }} {{ $bord('password') }}" placeholder="Laisser vide pour conserver"
                                @error('password') aria-invalid="true" aria-describedby="password-erreur" @enderror>
                            <p class="mt-1 text-xs text-slate-500">8 caractères minimum.</p>
                            @error('password')
                                <p id="password-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="{{ $label }}">Confirmation</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                autocomplete="new-password" class="{{ $champ }} {{ $bord('password_confirmation') }}">
                        </div>
                    </div>
                    <div>
                        <label for="photo" class="{{ $label }}">Photo</label>
                        <div class="flex items-center gap-4">
                            @if ($mentor->user->photo)
                                <img src="{{ asset('storage/' . $mentor->user->photo) }}" alt="Photo actuelle"
                                    class="h-12 w-12 rounded-full object-cover border border-brand-100 flex-shrink-0">
                            @endif
                            <div class="min-w-0 flex-1">
                                <input type="file" id="photo" name="photo" accept="image/*" class="{{ $fichier }}"
                                    @error('photo') aria-invalid="true" aria-describedby="photo-erreur" @enderror>
                                <p class="mt-1 text-xs text-slate-500">Image, 2 Mo maximum. Laisser vide pour conserver la photo actuelle.</p>
                            </div>
                        </div>
                        @error('photo')
                            <p id="photo-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <!-- Informations professionnelles -->
                <div class="pt-5 border-t border-slate-100">
                    <fieldset class="space-y-4">
                        <legend class="{{ $legende }}">Informations professionnelles</legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="departement" class="{{ $label }}">Département</label>
                                <input type="text" id="departement" name="departement" value="{{ old('departement', $mentor->departement) }}"
                                    class="{{ $champ }} {{ $bord('departement') }}"
                                    @error('departement') aria-invalid="true" aria-describedby="departement-erreur" @enderror>
                                @error('departement')
                                    <p id="departement-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="poste" class="{{ $label }}">Poste</label>
                                <input type="text" id="poste" name="poste" value="{{ old('poste', $mentor->poste) }}"
                                    class="{{ $champ }} {{ $bord('poste') }}"
                                    @error('poste') aria-invalid="true" aria-describedby="poste-erreur" @enderror>
                                @error('poste')
                                    <p id="poste-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label for="bio" class="{{ $label }}">Biographie</label>
                            <textarea id="bio" name="bio" rows="3" class="{{ $champ }} {{ $bord('bio') }} resize-y"
                                @error('bio') aria-invalid="true" aria-describedby="bio-erreur" @enderror>{{ old('bio', $mentor->bio) }}</textarea>
                            @error('bio')
                                <p id="bio-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </fieldset>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-slate-100">
                    <a href="{{ route('admin.mentors.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
