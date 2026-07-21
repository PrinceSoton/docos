@extends('layouts.admin')
@section('titre', 'Nouvelle publication')
@section('breadcrumb', 'Événements > Créer')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-bullhorn text-purple-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Nouvelle publication</h2>
                    <p class="text-slate-400 text-sm">Information, événement ou note</p>
                </div>
            </div>
            <form action="{{ route('admin.evenements.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Type *</label>
                        <select name="type" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                            <option value="information" {{ old('type') === 'information' ? 'selected' : '' }}>Information
                            </option>
                            <option value="evenement" {{ old('type') === 'evenement' ? 'selected' : '' }}>Événement</option>
                            <option value="note" {{ old('type') === 'note' ? 'selected' : '' }}>Note</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date de l'événement</label>
                        <input type="datetime-local" name="date_evenement" value="{{ old('date_evenement') }}"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Titre *</label>
                    <input type="text" name="titre" value="{{ old('titre') }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Contenu</label>
                    <textarea name="contenu" rows="5"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm resize-none">{{ old('contenu') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Image</label>
                        <input type="file" name="image" accept="image/*"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-purple-50 file:text-purple-700 file:font-medium hover:file:bg-purple-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Fichier joint</label>
                        <input type="file" name="fichier"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-50 file:text-slate-700 file:font-medium hover:file:bg-slate-100">
                    </div>
                </div>

                <!-- Destinataires -->
                <div class="p-5 bg-slate-50 rounded-2xl">
                    <div class="flex items-center gap-3 mb-3">
                        <input type="checkbox" name="partage_tous" id="partageTous" value="1"
                            {{ old('partage_tous') ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded">
                        <label for="partageTous" class="text-sm font-medium text-slate-700 cursor-pointer">
                            <i class="fas fa-globe mr-1 text-green-500"></i>Partager avec tous les utilisateurs
                        </label>
                    </div>
                    <div id="destinatairesSelect" class="{{ old('partage_tous') ? 'hidden' : '' }}">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Destinataires spécifiques :</label>
                        <select name="destinataires[]" multiple
                            class="w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-indigo-400 transition"
                            size="6">
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}"
                                    {{ in_array($u->id, (array) old('destinataires', [])) ? 'selected' : '' }}>
                                    {{ $u->nom_complet }} ({{ ucfirst($u->role) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">Ctrl/Cmd pour sélection multiple</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-paper-plane mr-2"></i>Publier
                    </button>
                    <a href="{{ route('admin.evenements.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">Annuler</a>
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
