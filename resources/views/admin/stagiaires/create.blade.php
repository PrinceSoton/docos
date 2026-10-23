@extends('layouts.admin')
@section('titre', 'Nouveau stagiaire')
@section('breadcrumb', 'Stagiaires > Créer')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champBase = 'w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2';
        $champOk = $champBase . ' border-slate-300 focus:border-brand-600 focus:ring-brand-600/25';
        $champErreur = $champBase . ' border-red-500 focus:border-red-600 focus:ring-red-600/20';
        // Classe du champ selon la présence d'une erreur de validation
        $champ = fn(string $nom) => $errors->has($nom) ? $champErreur : $champOk;
        $fichier = 'w-full text-sm text-slate-500 file:mr-3 file:rounded-md file:border-0 file:!bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium file:!text-slate-700 hover:file:!bg-slate-200';
        $label = 'block text-sm font-medium text-slate-700 mb-1';
        $requis = '<span class="text-red-700" aria-hidden="true">*</span>';
        $legende = 'text-base font-semibold text-slate-900';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl overflow-hidden">
            <div class="px-5 sm:px-6 py-4 bg-brand-50 border-b border-brand-100">
                <h2 class="text-base font-semibold text-slate-900">Nouveau stagiaire</h2>
                <p class="mt-0.5 text-sm text-slate-600">Enregistrez un nouveau stagiaire dans le système. Les champs
                    marqués <span class="text-red-700">*</span> sont obligatoires.</p>
            </div>

            <form action="{{ route('admin.stagiaires.store') }}" method="POST" enctype="multipart/form-data"
                class="p-5 sm:p-6 space-y-8">
                @csrf

                <!-- Compte utilisateur -->
                <fieldset>
                    <legend class="{{ $legende }}">Compte utilisateur</legend>
                    <p class="mt-0.5 text-sm text-slate-500">Identité et accès du stagiaire à la plateforme.</p>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="prenom" class="{{ $label }}">Prénom {!! $requis !!}</label>
                            <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required
                                autocomplete="given-name" class="{{ $champ('prenom') }}"
                                @error('prenom') aria-invalid="true" aria-describedby="prenom-erreur" @enderror>
                            @error('prenom')
                                <p id="prenom-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nom" class="{{ $label }}">Nom {!! $requis !!}</label>
                            <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                                autocomplete="family-name" class="{{ $champ('nom') }}"
                                @error('nom') aria-invalid="true" aria-describedby="nom-erreur" @enderror>
                            @error('nom')
                                <p id="nom-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="{{ $label }}">Email {!! $requis !!}</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email" class="{{ $champ('email') }}"
                                @error('email') aria-invalid="true" aria-describedby="email-erreur" @enderror>
                            @error('email')
                                <p id="email-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="telephone" class="{{ $label }}">Téléphone</label>
                            <input type="text" id="telephone" name="telephone" value="{{ old('telephone') }}"
                                autocomplete="tel" class="{{ $champ('telephone') }} tabular-nums"
                                @error('telephone') aria-invalid="true" aria-describedby="telephone-erreur" @enderror>
                            @error('telephone')
                                <p id="telephone-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="{{ $label }}">Mot de passe {!! $requis !!}</label>
                            <input type="password" id="password" name="password" required autocomplete="new-password"
                                class="{{ $champ('password') }}"
                                aria-describedby="password-aide @error('password') password-erreur @enderror"
                                @error('password') aria-invalid="true" @enderror>
                            <p id="password-aide" class="mt-1 text-xs text-slate-500">8 caractères minimum.</p>
                            @error('password')
                                <p id="password-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="{{ $label }}">Confirmation {!! $requis !!}</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                autocomplete="new-password" class="{{ $champOk }}">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="photo" class="{{ $label }}">Photo de profil</label>
                        <input type="file" id="photo" name="photo" accept="image/*" class="{{ $fichier }}"
                            @error('photo') aria-invalid="true" aria-describedby="photo-erreur" @enderror>
                        @error('photo')
                            <p id="photo-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <!-- Informations de stage -->
                <div class="pt-6 border-t border-brand-100">
                <fieldset>
                    <legend class="{{ $legende }}">Informations de stage</legend>
                    <p class="mt-0.5 text-sm text-slate-500">Formation, encadrement et période du stage.</p>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="ecole" class="{{ $label }}">École / Université</label>
                            <input type="text" id="ecole" name="ecole" value="{{ old('ecole') }}"
                                class="{{ $champ('ecole') }}"
                                @error('ecole') aria-invalid="true" aria-describedby="ecole-erreur" @enderror>
                            @error('ecole')
                                <p id="ecole-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="specialite" class="{{ $label }}">Spécialité</label>
                            <input type="text" id="specialite" name="specialite" value="{{ old('specialite') }}"
                                class="{{ $champ('specialite') }}"
                                @error('specialite') aria-invalid="true" aria-describedby="specialite-erreur" @enderror>
                            @error('specialite')
                                <p id="specialite-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="niveau_etude" class="{{ $label }}">Niveau d'étude</label>
                            <input type="text" id="niveau_etude" name="niveau_etude" value="{{ old('niveau_etude') }}"
                                class="{{ $champ('niveau_etude') }}" placeholder="Licence, Master, BTS..."
                                @error('niveau_etude') aria-invalid="true" aria-describedby="niveau_etude-erreur" @enderror>
                            @error('niveau_etude')
                                <p id="niveau_etude-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="mentor_id" class="{{ $label }}">Mentor / Tuteur</label>
                            <select id="mentor_id" name="mentor_id" class="{{ $champ('mentor_id') }}"
                                @error('mentor_id') aria-invalid="true" aria-describedby="mentor_id-erreur" @enderror>
                                <option value="">-- Assigner plus tard --</option>
                                @foreach ($mentors as $mentor)
                                    <option value="{{ $mentor->user_id }}"
                                        {{ old('mentor_id') == $mentor->user_id ? 'selected' : '' }}>
                                        {{ $mentor->user->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mentor_id')
                                <p id="mentor_id-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="date_debut" class="{{ $label }}">Date de début {!! $requis !!}</label>
                            <input type="date" id="date_debut" name="date_debut" value="{{ old('date_debut') }}" required
                                class="{{ $champ('date_debut') }} tabular-nums"
                                @error('date_debut') aria-invalid="true" aria-describedby="date_debut-erreur" @enderror>
                            @error('date_debut')
                                <p id="date_debut-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="date_fin" class="{{ $label }}">Date de fin {!! $requis !!}</label>
                            <input type="date" id="date_fin" name="date_fin" value="{{ old('date_fin') }}" required
                                class="{{ $champ('date_fin') }} tabular-nums"
                                @error('date_fin') aria-invalid="true" aria-describedby="date_fin-erreur" @enderror>
                            @error('date_fin')
                                <p id="date_fin-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="description" class="{{ $label }}">Description / Notes</label>
                        <textarea id="description" name="description" rows="3" class="{{ $champ('description') }} resize-y"
                            @error('description') aria-invalid="true" aria-describedby="description-erreur" @enderror>{{ old('description') }}</textarea>
                        @error('description')
                            <p id="description-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-4">
                        <label for="cv" class="{{ $label }}">CV (PDF, DOC)</label>
                        <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" class="{{ $fichier }}"
                            aria-describedby="cv-aide @error('cv') cv-erreur @enderror"
                            @error('cv') aria-invalid="true" @enderror>
                        <p id="cv-aide" class="mt-1 text-xs text-slate-500">PDF, DOC ou DOCX, 5 Mo maximum.</p>
                        @error('cv')
                            <p id="cv-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>
                </div>

                <!-- Actions -->
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-brand-100">
                    <a href="{{ route('admin.stagiaires.index') }}" class="{{ $btnSecondaire }}">
                        Annuler
                    </a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Enregistrer le stagiaire
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
