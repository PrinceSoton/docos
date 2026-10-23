{{-- Présentation mentor de la vue partagée profile/edit (incluse uniquement pour le rôle mentor) --}}
@php
    $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
    $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
    // Variante du champ fichier pour un fond teinté brand-50 (texte secondaire slate-600 minimum)
    $fichier = 'w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border file:border-solid file:border-brand-200 file:!bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:!text-brand-800 hover:file:!bg-brand-100';
    $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
    $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
@endphp

<div class="max-w-3xl mx-auto">
    <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
        <h2 class="text-base font-semibold text-slate-900">Modifier mon profil</h2>
        <p class="mt-1 text-sm text-slate-500">Mettez à jour vos informations personnelles.</p>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-5">
            @csrf @method('PUT')

            <!-- Photo -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 rounded-lg bg-brand-50 border border-brand-100 p-4">
                <div class="w-16 h-16 rounded-full overflow-hidden bg-brand-800 ring-2 ring-white flex items-center justify-center flex-shrink-0">
                    @if ($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Aperçu de la photo de profil"
                            class="w-full h-full object-cover" id="photoPreview">
                    @else
                        <span class="text-white font-semibold text-xl" id="photoIcon"
                            aria-hidden="true">{{ strtoupper(substr($user->prenom, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <label for="photoInput" class="block text-sm font-medium text-slate-900 mb-1">Photo de profil</label>
                    <input type="file" name="photo" accept="image/*" id="photoInput" class="{{ $fichier }}">
                    @error('photo')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="profil-prenom" class="block text-sm font-medium text-slate-700 mb-1">Prénom *</label>
                    <input type="text" name="prenom" id="profil-prenom" value="{{ old('prenom', $user->prenom) }}" required
                        autocomplete="given-name" class="{{ $champ }}">
                    @error('prenom')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="profil-nom" class="block text-sm font-medium text-slate-700 mb-1">Nom *</label>
                    <input type="text" name="nom" id="profil-nom" value="{{ old('nom', $user->nom) }}" required
                        autocomplete="family-name" class="{{ $champ }}">
                    @error('nom')
                        <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="profil-email" class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
                <input type="email" name="email" id="profil-email" value="{{ old('email', $user->email) }}" required
                    autocomplete="email" class="{{ $champ }}">
                @error('email')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="profil-telephone" class="block text-sm font-medium text-slate-700 mb-1">Téléphone</label>
                <input type="text" name="telephone" id="profil-telephone" value="{{ old('telephone', $user->telephone) }}"
                    autocomplete="tel" class="{{ $champ }}">
                @error('telephone')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-1">
                <a href="{{ route('profile.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                <button type="submit" class="{{ $btnPrimaire }}">
                    <i class="fas fa-save text-xs" aria-hidden="true"></i>Enregistrer
                </button>
            </div>
        </form>
    </section>
</div>

@push('scripts')
    <script>
        document.getElementById('photoInput')?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    const icon = document.getElementById('photoIcon');
                    let preview = document.getElementById('photoPreview');
                    // Sans photo existante, l'image d'aperçu est créée au premier choix de fichier
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'photoPreview';
                        preview.alt = 'Aperçu de la photo de profil';
                        preview.className = 'w-full h-full object-cover';
                        icon.after(preview);
                    }
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (icon) icon.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
