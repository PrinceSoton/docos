@extends('layouts.' . Auth::user()->role)
@section('titre', 'Modifier le profil')
@section('breadcrumb', 'Profil > Modifier')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-edit text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Modifier mon profil</h2>
                    <p class="text-slate-400 text-sm">Mettez à jour vos informations personnelles</p>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')
                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl">
                    <div
                        class="w-16 h-16 rounded-xl overflow-hidden bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        @if ($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" class="w-full h-full object-cover"
                                id="photoPreview">
                        @else
                            <i class="fas fa-user text-indigo-400 text-2xl" id="photoIcon"></i>
                            <img src="" class="hidden w-full h-full object-cover" id="photoPreview">
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Photo de profil</label>
                        <input type="file" name="photo" accept="image/*" id="photoInput"
                            class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Prénom *</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nom *</label>
                        <input type="text" name="nom" value="{{ old('nom', $user->nom) }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition text-sm">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                    <a href="{{ route('profile.index') }}"
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
        document.getElementById('photoInput')?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    const preview = document.getElementById('photoPreview');
                    const icon = document.getElementById('photoIcon');
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (icon) icon.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
