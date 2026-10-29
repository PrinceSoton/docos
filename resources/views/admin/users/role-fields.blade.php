@php
    $profilMentor = isset($user) ? $user->mentor : null;
    $profilStage = isset($user) ? $user->stagiaire : null;
@endphp
                <!-- Informations du mentor (affichées si rôle = mentor) -->
                {{-- L'affichage conditionnel est appliqué par JavaScript ; les champs restent accessibles sans JS. --}}
                <fieldset id="mentor-fields" class="rounded-lg bg-brand-50 border border-brand-100 p-4 sm:p-5 space-y-4">
                    <legend class="sr-only">Informations professionnelles du mentor</legend>
                    <p class="text-sm font-semibold text-slate-900" aria-hidden="true">Informations professionnelles du mentor</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="departement" class="{{ $label }}">Département</label>
                            <input type="text" id="departement" name="departement" value="{{ old('departement', $profilMentor?->departement) }}"
                                class="{{ $champ }} {{ $bord('departement') }}"
                                @error('departement') aria-invalid="true" aria-describedby="departement-erreur" @enderror>
                            @error('departement')
                                <p id="departement-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="poste" class="{{ $label }}">Poste</label>
                            <input type="text" id="poste" name="poste" value="{{ old('poste', $profilMentor?->poste) }}"
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
                            @error('bio') aria-invalid="true" aria-describedby="bio-erreur" @enderror>{{ old('bio', $profilMentor?->bio) }}</textarea>
                        @error('bio')
                            <p id="bio-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </fieldset>

                <!-- Informations de stage (affichées si rôle = stagiaire) -->
                <fieldset id="stagiaire-fields" class="rounded-lg bg-brand-50 border border-brand-100 p-4 sm:p-5 space-y-4">
                    <legend class="sr-only">Informations de stage</legend>
                    <p class="text-sm font-semibold text-slate-900" aria-hidden="true">Informations de stage</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="ecole" class="{{ $label }}">École / Université</label>
                            <input type="text" id="ecole" name="ecole" value="{{ old('ecole', $profilStage?->ecole) }}"
                                class="{{ $champ }} {{ $bord('ecole') }}"
                                @error('ecole') aria-invalid="true" aria-describedby="ecole-erreur" @enderror>
                            @error('ecole')
                                <p id="ecole-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="specialite" class="{{ $label }}">Spécialité</label>
                            <input type="text" id="specialite" name="specialite" value="{{ old('specialite', $profilStage?->specialite) }}"
                                class="{{ $champ }} {{ $bord('specialite') }}"
                                @error('specialite') aria-invalid="true" aria-describedby="specialite-erreur" @enderror>
                            @error('specialite')
                                <p id="specialite-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="niveau_etude" class="{{ $label }}">Niveau d'étude</label>
                            <input type="text" id="niveau_etude" name="niveau_etude" value="{{ old('niveau_etude', $profilStage?->niveau_etude) }}"
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
                                    <option value="{{ $mentor->user_id }}" {{ old('mentor_id', $profilStage?->mentor_id) == $mentor->user_id ? 'selected' : '' }}>
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
                            <input type="date" id="date_debut" name="date_debut" value="{{ old('date_debut', $profilStage?->date_debut?->format('Y-m-d')) }}" required
                                class="{{ $champ }} {{ $bord('date_debut') }} tabular-nums"
                                @error('date_debut') aria-invalid="true" aria-describedby="date_debut-erreur" @enderror>
                            @error('date_debut')
                                <p id="date_debut-erreur" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="date_fin" class="{{ $label }}">Date de fin {!! $requis !!}</label>
                            <input type="date" id="date_fin" name="date_fin" value="{{ old('date_fin', $profilStage?->date_fin?->format('Y-m-d')) }}" required
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
                            @error('description') aria-invalid="true" aria-describedby="description-erreur" @enderror>{{ old('description', $profilStage?->description) }}</textarea>
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
