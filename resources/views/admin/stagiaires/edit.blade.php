@extends('layouts.admin')
@section('titre', 'Modifier stagiaire')
@section('breadcrumb', 'Stagiaires > Modifier')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
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
                <h2 class="text-base font-semibold text-slate-900 break-words">Modifier : {{ $stagiaire->user->nom_complet }}</h2>
                <p class="mt-0.5 text-sm text-slate-600">Matricule : <span
                        class="font-mono font-medium text-slate-900">{{ $stagiaire->matricule }}</span></p>
            </div>

            <form action="{{ route('admin.stagiaires.update', $stagiaire) }}" method="POST" enctype="multipart/form-data"
                class="p-5 sm:p-6 space-y-8">
                @csrf @method('PUT')

                <!-- Compte utilisateur -->
                <fieldset>
                    <legend class="{{ $legende }}">Compte utilisateur</legend>
                    <p class="mt-0.5 text-sm text-slate-500">Identité et accès du stagiaire à la plateforme.</p>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="prenom" class="{{ $label }}">Prénom {!! $requis !!}</label>
                            <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $stagiaire->user->prenom) }}"
                                required autocomplete="given-name" class="{{ $champ('prenom') }}"
                                @error('prenom') aria-invalid="true" aria-describedby="prenom-erreur" @enderror>
                            @error('prenom')
                                <p id="prenom-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nom" class="{{ $label }}">Nom {!! $requis !!}</label>
                            <input type="text" id="nom" name="nom" value="{{ old('nom', $stagiaire->user->nom) }}" required
                                autocomplete="family-name" class="{{ $champ('nom') }}"
                                @error('nom') aria-invalid="true" aria-describedby="nom-erreur" @enderror>
                            @error('nom')
                                <p id="nom-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="{{ $label }}">Email {!! $requis !!}</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $stagiaire->user->email) }}"
                                required autocomplete="email" class="{{ $champ('email') }}"
                                @error('email') aria-invalid="true" aria-describedby="email-erreur" @enderror>
                            @error('email')
                                <p id="email-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="telephone" class="{{ $label }}">Téléphone</label>
                            <input type="text" id="telephone" name="telephone"
                                value="{{ old('telephone', $stagiaire->user->telephone) }}" autocomplete="tel"
                                class="{{ $champ('telephone') }} tabular-nums"
                                @error('telephone') aria-invalid="true" aria-describedby="telephone-erreur" @enderror>
                            @error('telephone')
                                <p id="telephone-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="{{ $label }}">Nouveau mot de passe</label>
                            <input type="password" id="password" name="password" autocomplete="new-password"
                                class="{{ $champ('password') }}" placeholder="Laisser vide pour ne pas changer"
                                aria-describedby="password-aide @error('password') password-erreur @enderror"
                                @error('password') aria-invalid="true" @enderror>
                            <p id="password-aide" class="mt-1 text-xs text-slate-500">8 caractères minimum. Laisser vide
                                pour conserver le mot de passe actuel.</p>
                            @error('password')
                                <p id="password-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="{{ $label }}">Confirmation</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                autocomplete="new-password" class="{{ $champOk }}">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="photo" class="{{ $label }}">Photo</label>
                        <div class="flex items-center gap-4">
                            @if ($stagiaire->user->photo)
                                <img src="{{ asset('storage/' . $stagiaire->user->photo) }}" alt="Photo actuelle"
                                    class="h-12 w-12 flex-shrink-0 rounded-full object-cover border border-brand-100">
                            @endif
                            <div class="flex-1 min-w-0">
                                <input type="file" id="photo" name="photo" accept="image/*" class="{{ $fichier }}"
                                    @error('photo') aria-invalid="true" aria-describedby="photo-erreur" @enderror>
                            </div>
                        </div>
                        @error('photo')
                            <p id="photo-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <!-- Informations de stage -->
                <div class="pt-6 border-t border-brand-100">
                    <fieldset>
                        <legend class="{{ $legende }}">Informations de stage</legend>
                        <p class="mt-0.5 text-sm text-slate-500">Formation, encadrement, statut et période du stage.</p>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ecole" class="{{ $label }}">École</label>
                                <input type="text" id="ecole" name="ecole" value="{{ old('ecole', $stagiaire->ecole) }}"
                                    class="{{ $champ('ecole') }}"
                                    @error('ecole') aria-invalid="true" aria-describedby="ecole-erreur" @enderror>
                                @error('ecole')
                                    <p id="ecole-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="specialite" class="{{ $label }}">Spécialité</label>
                                <input type="text" id="specialite" name="specialite"
                                    value="{{ old('specialite', $stagiaire->specialite) }}" class="{{ $champ('specialite') }}"
                                    @error('specialite') aria-invalid="true" aria-describedby="specialite-erreur" @enderror>
                                @error('specialite')
                                    <p id="specialite-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="niveau_etude" class="{{ $label }}">Niveau d'étude</label>
                                <input type="text" id="niveau_etude" name="niveau_etude"
                                    value="{{ old('niveau_etude', $stagiaire->niveau_etude) }}"
                                    class="{{ $champ('niveau_etude') }}"
                                    @error('niveau_etude') aria-invalid="true" aria-describedby="niveau_etude-erreur" @enderror>
                                @error('niveau_etude')
                                    <p id="niveau_etude-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="statut" class="{{ $label }}">Statut {!! $requis !!}</label>
                                <select id="statut" name="statut" required class="{{ $champ('statut') }}"
                                    @error('statut') aria-invalid="true" aria-describedby="statut-erreur" @enderror>
                                    <option value="en_cours" {{ $stagiaire->statut === 'en_cours' ? 'selected' : '' }}>En cours
                                    </option>
                                    <option value="termine" {{ $stagiaire->statut === 'termine' ? 'selected' : '' }}>Terminé
                                    </option>
                                    <option value="suspendu" {{ $stagiaire->statut === 'suspendu' ? 'selected' : '' }}>Suspendu
                                    </option>
                                </select>
                                @error('statut')
                                    <p id="statut-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="mentor_id" class="{{ $label }}">Mentor</label>
                                <select id="mentor_id" name="mentor_id" class="{{ $champ('mentor_id') }}"
                                    @error('mentor_id') aria-invalid="true" aria-describedby="mentor_id-erreur" @enderror>
                                    <option value="">-- Aucun --</option>
                                    @foreach ($mentors as $mentor)
                                        <option value="{{ $mentor->user_id }}"
                                            {{ $stagiaire->mentor_id == $mentor->user_id ? 'selected' : '' }}>
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
                                <input type="date" id="date_debut" name="date_debut"
                                    value="{{ old('date_debut', $stagiaire->date_debut->format('Y-m-d')) }}" required
                                    class="{{ $champ('date_debut') }} tabular-nums"
                                    @error('date_debut') aria-invalid="true" aria-describedby="date_debut-erreur" @enderror>
                                @error('date_debut')
                                    <p id="date_debut-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="date_fin" class="{{ $label }}">Date de fin {!! $requis !!}</label>
                                <input type="date" id="date_fin" name="date_fin"
                                    value="{{ old('date_fin', $stagiaire->date_fin->format('Y-m-d')) }}" required
                                    class="{{ $champ('date_fin') }} tabular-nums"
                                    @error('date_fin') aria-invalid="true" aria-describedby="date_fin-erreur" @enderror>
                                @error('date_fin')
                                    <p id="date_fin-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="description" class="{{ $label }}">Description</label>
                            <textarea id="description" name="description" rows="3" class="{{ $champ('description') }} resize-y"
                                @error('description') aria-invalid="true" aria-describedby="description-erreur" @enderror>{{ old('description', $stagiaire->description) }}</textarea>
                            @error('description')
                                <p id="description-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-4">
                            <label for="cv" class="{{ $label }}">CV</label>
                            @if ($stagiaire->cv)
                                <div
                                    class="mb-3 flex flex-wrap items-center justify-between gap-2 rounded-lg bg-brand-50 border border-brand-100 px-3 py-2.5">
                                    <span class="flex items-center gap-2 text-sm text-slate-900">
                                        <i class="fas fa-file-pdf text-xs text-slate-600" aria-hidden="true"></i>CV actuel
                                    </span>
                                    <a href="{{ asset('storage/' . $stagiaire->cv) }}" download
                                        class="inline-flex items-center gap-1.5 whitespace-nowrap {{ $lien }}">
                                        <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger
                                    </a>
                                </div>
                            @endif
                            <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" class="{{ $fichier }}"
                                aria-describedby="cv-aide @error('cv') cv-erreur @enderror"
                                @error('cv') aria-invalid="true" @enderror>
                            <p id="cv-aide" class="mt-1 text-xs text-slate-500">PDF, DOC ou DOCX, 5 Mo maximum. Un nouveau
                                fichier remplace le CV actuel.</p>
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
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
