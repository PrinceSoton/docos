@extends('layouts.admin')
@section('titre', 'Modifier utilisateur')
@section('breadcrumb', 'Utilisateurs > Modifier')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2';
        $bord = fn ($nom) => $errors->has($nom)
            ? 'border-red-600 focus:border-red-600 focus:ring-red-600/25'
            : 'border-slate-300 focus:border-brand-600 focus:ring-brand-600/25';
        $fichier = 'w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:!bg-brand-100 file:px-3 file:py-2 file:text-sm file:font-medium file:!text-brand-800 hover:file:!bg-brand-200';
        $case = 'h-4 w-4 flex-shrink-0 rounded border-slate-300 accent-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $label = 'block text-sm font-medium text-slate-700 mb-1';
        $legende = 'text-base font-semibold text-slate-900 mb-3';
        $requis = '<span class="text-red-700" aria-hidden="true">*</span>';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <div class="mb-5">
                <h2 class="text-base font-semibold text-slate-900 break-words">Modifier : {{ $user->nom_complet }}</h2>
                <p class="mt-0.5 text-sm text-slate-500">Mettre à jour les informations. Les champs marqués
                    <span class="text-red-700">*</span> sont obligatoires.</p>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf @method('PUT')

                <!-- Identité -->
                <fieldset class="space-y-4">
                    <legend class="{{ $legende }}">Identité</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="prenom" class="{{ $label }}">Prénom {!! $requis !!}</label>
                            <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required
                                autocomplete="given-name" class="{{ $champ }} {{ $bord('prenom') }}"
                                @error('prenom') aria-invalid="true" aria-describedby="prenom-erreur" @enderror>
                            @error('prenom')
                                <p id="prenom-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="nom" class="{{ $label }}">Nom {!! $requis !!}</label>
                            <input type="text" id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required
                                autocomplete="family-name" class="{{ $champ }} {{ $bord('nom') }}"
                                @error('nom') aria-invalid="true" aria-describedby="nom-erreur" @enderror>
                            @error('nom')
                                <p id="nom-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="photo" class="{{ $label }}">Photo</label>
                        <div class="flex items-center gap-4">
                            @if ($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo actuelle"
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

                <!-- Coordonnées -->
                <div class="pt-5 border-t border-slate-100">
                    <fieldset class="space-y-4">
                        <legend class="{{ $legende }}">Coordonnées</legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="{{ $label }}">Email {!! $requis !!}</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                    autocomplete="email" class="{{ $champ }} {{ $bord('email') }}"
                                    @error('email') aria-invalid="true" aria-describedby="email-erreur" @enderror>
                                @error('email')
                                    <p id="email-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="telephone" class="{{ $label }}">Téléphone</label>
                                <input type="text" id="telephone" name="telephone" value="{{ old('telephone', $user->telephone) }}"
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
                        <legend class="{{ $legende }}">Accès</legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:items-end">
                            <div>
                                <label for="role" class="{{ $label }}">Rôle {!! $requis !!}</label>
                                <select id="role" name="role" required class="{{ $champ }} {{ $bord('role') }}"
                                    @error('role') aria-invalid="true" aria-describedby="role-erreur" @enderror>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrateur</option>
                                    <option value="mentor" {{ $user->role === 'mentor' ? 'selected' : '' }}>Mentor</option>
                                    <option value="stagiaire" {{ $user->role === 'stagiaire' ? 'selected' : '' }}>Stagiaire</option>
                                </select>
                                @error('role')
                                    <p id="role-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2.5">
                                <input type="checkbox" name="actif" id="actif" value="1"
                                    {{ $user->actif ? 'checked' : '' }} class="{{ $case }}">
                                <label for="actif" class="text-sm font-medium text-slate-700">Compte actif</label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="{{ $label }}">Nouveau mot de passe</label>
                                <input type="password" id="password" name="password" autocomplete="new-password"
                                    class="{{ $champ }} {{ $bord('password') }}" placeholder="Laisser vide pour ne pas changer"
                                    @error('password') aria-invalid="true" aria-describedby="password-erreur" @enderror>
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
                    </fieldset>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
