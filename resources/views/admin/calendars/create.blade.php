@extends('layouts.admin')
@section('titre', 'Ajouter un jour')
@section('breadcrumb', 'Calendrier > Ajouter')
@section('content')
    <div class="max-w-xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-plus text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Ajouter un jour spécial</h2>
                    <p class="text-slate-400 text-sm">Jour férié, séjour ou autre — bloque le marquage de présence</p>
                </div>
            </div>
            <form action="{{ route('admin.calendars.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Date *</label>
                    <input type="date" name="date" value="{{ old('date') }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Libellé *</label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm"
                        placeholder="Ex: Fête nationale, Noël...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Type *</label>
                    <select name="type" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                        <option value="ferie" {{ old('type') === 'ferie' ? 'selected' : '' }}>Jour férié</option>
                        <option value="sejour" {{ old('type') === 'sejour' ? 'selected' : '' }}>Jour de séjour</option>
                        <option value="autre" {{ old('type') === 'autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description (optionnel)</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm resize-none">{{ old('description') }}</textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-save mr-2"></i>Ajouter
                    </button>
                    <a href="{{ route('admin.calendars.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
