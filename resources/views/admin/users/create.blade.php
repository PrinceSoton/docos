@extends('layouts.admin')
@section('titre', 'Créer un utilisateur')
@section('breadcrumb', 'Utilisateurs > Créer')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2';
        // Bordure normale ou rouge selon la présence d'une erreur de validation sur le champ
        $bord = fn ($nom) => $errors->has($nom)
            ? 'border-red-600 focus:border-red-600 focus:ring-red-600/25'
            : 'border-slate-300 focus:border-brand-600 focus:ring-brand-600/25';
        $fichier = 'w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:!bg-brand-100 file:px-3 file:py-2 file:text-sm file:font-medium file:!text-brand-800 hover:file:!bg-brand-200';
        $label = 'block text-sm font-medium text-slate-700 mb-1';
        $legende = 'text-base font-semibold text-slate-900';
        $requis = '<span class="text-red-700" aria-hidden="true">*</span>';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <p class="text-sm text-slate-500 mb-5">Créez un compte utilisateur. Les champs marqués
                <span class="text-red-700">*</span> sont obligatoires.</p>

            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Identité -->
                <fieldset class="space-y-4">
                    <legend class="{{ $legende }} mb-3">Identité</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="prenom" class="{{ $label }}">Prénom {!! $requis !!}</label>
                            <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required
                                autocomplete="given-name" class="{{ $champ }} {{ $bord('prenom') }}"
                                @error('prenom') aria-invalid="true" aria-describedby="prenom-erreur" @enderror>
                            @error('prenom')
                                <p id="prenom-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nom" class="{{ $label }}">Nom {!! $requis !!}</label>
                            <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                                autocomplete="family-name" class="{{ $champ }} {{ $bord('nom') }}"
                                @error('nom') aria-invalid="true" aria-describedby="nom-erreur" @enderror>
                            @error('nom')
                                <p id="nom-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="photo" class="{{ $label }}">Photo</label>
                        <input type="file" id="photo" name="photo" accept="image/*" class="{{ $fichier }}"
                            @error('photo') aria-invalid="true" aria-describedby="photo-erreur" @enderror>
                        <p class="mt-1 text-xs text-slate-500">Image, 2 Mo maximum.</p>
                        @error('photo')
                            <p id="photo-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <!-- Coordonnées -->
                <div class="pt-5 border-t border-slate-100">
                    <fieldset class="space-y-4">
                        <legend class="{{ $legende }} mb-3">Coordonnées</legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="{{ $label }}">Email {!! $requis !!}</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    autocomplete="email" class="{{ $champ }} {{ $bord('email') }}"
                                    @error('email') aria-invalid="true" aria-describedby="email-erreur" @enderror>
                                @error('email')
                                    <p id="email-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="telephone" class="{{ $label }}">Téléphone</label>
                                <input type="text" id="telephone" name="telephone" value="{{ old('telephone') }}"
                                    autocomplete="tel" class="{{ $champ }} {{ $bord('telephone') }} tabular-nums"
                                    @error('telephone') aria-invalid="true" aria-describedby="telephone-erreur" @enderror>
                                @error('telephone')
                                    <p id="telephone-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- Accès -->
                <div class="pt-5 border-t border-slate-100">
                    <fieldset class="space-y-4">
                        <legend class="{{ $legende }} mb-3">Accès</legend>
                        <div>
                            <label for="role" class="{{ $label }}">Rôle {!! $requis !!}</label>
                            <select name="role" id="role" required class="{{ $champ }} {{ $bord('role') }} sm:max-w-xs"
                                @error('role') aria-invalid="true" aria-describedby="role-erreur" @enderror>
                                <option value="">Sélectionner un rôle</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrateur</option>
                                <option value="mentor" {{ old('role') === 'mentor' ? 'selected' : '' }}>Mentor</option>
                                <option value="stagiaire" {{ old('role') === 'stagiaire' ? 'selected' : '' }}>Stagiaire</option>
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Les informations complémentaires s'affichent selon le rôle choisi.</p>
                            @error('role')
                                <p id="role-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="{{ $label }}">Mot de passe {!! $requis !!}</label>
                                <input type="password" id="password" name="password" required autocomplete="new-password"
                                    class="{{ $champ }} {{ $bord('password') }}"
                                    @error('password') aria-invalid="true" aria-describedby="password-erreur" @enderror>
                                <p class="mt-1 text-xs text-slate-500">8 caractères minimum.</p>
                                @error('password')
                                    <p id="password-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="{{ $label }}">Confirmation {!! $requis !!}</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    autocomplete="new-password" class="{{ $champ }} {{ $bord('password_confirmation') }}">
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- Informations du mentor (affichées si rôle = mentor) -->
                {{-- Pas de classe d'affichage (grid/flex/block) sur ce fieldset : elle écraserait l'attribut hidden --}}
                <fieldset id="mentor-fields" class="rounded-lg bg-brand-50 border border-brand-100 p-4 sm:p-5 space-y-4"
                    @if (old('role') !== 'mentor') hidden disabled @endif>
                    <legend class="sr-only">Informations professionnelles du mentor</legend>
                    <p class="text-sm font-semibold text-slate-900" aria-hidden="true">Informations professionnelles du mentor</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="departement" class="{{ $label }}">Département</label>
                            <input type="text" id="departement" name="departement" value="{{ old('departement') }}"
                                class="{{ $champ }} {{ $bord('departement') }}"
                                @error('departement') aria-invalid="true" aria-describedby="departement-erreur" @enderror>
                            @error('departement')
                                <p id="departement-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="poste" class="{{ $label }}">Poste</label>
                            <input type="text" id="poste" name="poste" value="{{ old('poste') }}"
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
                            @error('bio') aria-invalid="true" aria-describedby="bio-erreur" @enderror>{{ old('bio') }}</textarea>
                        @error('bio')
                            <p id="bio-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <!-- Informations de stage (affichées si rôle = stagiaire) -->
                <fieldset id="stagiaire-fields" class="rounded-lg bg-brand-50 border border-brand-100 p-4 sm:p-5 space-y-4"
                    @if (old('role') !== 'stagiaire') hidden disabled @endif>
                    <legend class="sr-only">Informations de stage</legend>
                    <p class="text-sm font-semibold text-slate-900" aria-hidden="true">Informations de stage</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="ecole" class="{{ $label }}">École / Université</label>
                            <input type="text" id="ecole" name="ecole" value="{{ old('ecole') }}"
                                class="{{ $champ }} {{ $bord('ecole') }}"
                                @error('ecole') aria-invalid="true" aria-describedby="ecole-erreur" @enderror>
                            @error('ecole')
                                <p id="ecole-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="specialite" class="{{ $label }}">Spécialité</label>
                            <input type="text" id="specialite" name="specialite" value="{{ old('specialite') }}"
                                class="{{ $champ }} {{ $bord('specialite') }}"
                                @error('specialite') aria-invalid="true" aria-describedby="specialite-erreur" @enderror>
                            @error('specialite')
                                <p id="specialite-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="niveau_etude" class="{{ $label }}">Niveau d'étude</label>
                            <input type="text" id="niveau_etude" name="niveau_etude" value="{{ old('niveau_etude') }}"
                                placeholder="Licence, Master, BTS..." class="{{ $champ }} {{ $bord('niveau_etude') }}"
                                @error('niveau_etude') aria-invalid="true" aria-describedby="niveau_etude-erreur" @enderror>
                            @error('niveau_etude')
                                <p id="niveau_etude-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="mentor_id" class="{{ $label }}">Mentor / Tuteur</label>
                            <select id="mentor_id" name="mentor_id" class="{{ $champ }} {{ $bord('mentor_id') }}"
                                @error('mentor_id') aria-invalid="true" aria-describedby="mentor_id-erreur" @enderror>
                                <option value="">-- Assigner plus tard --</option>
                                @foreach ($mentors as $mentor)
                                    <option value="{{ $mentor->user_id }}" {{ old('mentor_id') == $mentor->user_id ? 'selected' : '' }}>
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
                                class="{{ $champ }} {{ $bord('date_debut') }} tabular-nums"
                                @error('date_debut') aria-invalid="true" aria-describedby="date_debut-erreur" @enderror>
                            @error('date_debut')
                                <p id="date_debut-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="date_fin" class="{{ $label }}">Date de fin {!! $requis !!}</label>
                            <input type="date" id="date_fin" name="date_fin" value="{{ old('date_fin') }}" required
                                class="{{ $champ }} {{ $bord('date_fin') }} tabular-nums"
                                @error('date_fin') aria-invalid="true" aria-describedby="date_fin-erreur" @enderror>
                            @error('date_fin')
                                <p id="date_fin-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="description" class="{{ $label }}">Description / Notes</label>
                        <textarea id="description" name="description" rows="3"
                            class="{{ $champ }} {{ $bord('description') }} resize-y"
                            @error('description') aria-invalid="true" aria-describedby="description-erreur" @enderror>{{ old('description') }}</textarea>
                        @error('description')
                            <p id="description-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="cv" class="{{ $label }}">CV (PDF, DOC)</label>
                        <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" class="{{ $fichier }}"
                            @error('cv') aria-invalid="true" aria-describedby="cv-erreur" @enderror>
                        <p class="mt-1 text-xs text-slate-600">PDF, DOC ou DOCX, 5 Mo maximum.</p>
                        @error('cv')
                            <p id="cv-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const roleSelect = document.getElementById('role');
        const roleFields = {
            mentor: document.getElementById('mentor-fields'),
            stagiaire: document.getElementById('stagiaire-fields'),
        };

        function updateRoleFields() {
            Object.entries(roleFields).forEach(([role, fields]) => {
                const active = roleSelect.value === role;
                fields.hidden = !active;
                fields.disabled = !active;
            });
        }

        roleSelect.addEventListener('change', updateRoleFields);
        updateRoleFields();
    </script>
@endpush
