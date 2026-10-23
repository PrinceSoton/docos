@extends('layouts.admin')
@section('titre', 'Ajouter un jour')
@section('breadcrumb', 'Calendrier > Ajouter')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $champErreur = 'w-full rounded-lg border border-red-400 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    @endphp

    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <div class="mb-5 rounded-lg bg-brand-50 border border-brand-100 p-4">
                <h2 class="text-sm font-semibold text-slate-900">Ajouter un jour spécial</h2>
                <p class="mt-1 text-sm text-slate-600">Jour férié, séjour ou autre — bloque le marquage de présence.</p>
            </div>

            <form action="{{ route('admin.calendars.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-slate-700 mb-1">
                            Date <span class="text-red-700" aria-hidden="true">*</span>
                        </label>
                        <input type="date" id="date" name="date" value="{{ old('date') }}" required
                            class="{{ $errors->has('date') ? $champErreur : $champ }} tabular-nums"
                            @error('date') aria-invalid="true" @enderror>
                        @error('date')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-slate-700 mb-1">
                            Type <span class="text-red-700" aria-hidden="true">*</span>
                        </label>
                        <select id="type" name="type" required
                            class="{{ $errors->has('type') ? $champErreur : $champ }}"
                            @error('type') aria-invalid="true" @enderror>
                            <option value="ferie" {{ old('type') === 'ferie' ? 'selected' : '' }}>Jour férié</option>
                            <option value="sejour" {{ old('type') === 'sejour' ? 'selected' : '' }}>Jour de séjour</option>
                            <option value="autre" {{ old('type') === 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="libelle" class="block text-sm font-medium text-slate-700 mb-1">
                        Libellé <span class="text-red-700" aria-hidden="true">*</span>
                    </label>
                    <input type="text" id="libelle" name="libelle" value="{{ old('libelle') }}" required
                        class="{{ $errors->has('libelle') ? $champErreur : $champ }}"
                        placeholder="Ex: Fête nationale, Noël..."
                        @error('libelle') aria-invalid="true" @enderror>
                    @error('libelle')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description <span
                            class="font-normal text-slate-500">(optionnel)</span></label>
                    <textarea id="description" name="description" rows="3"
                        class="{{ $errors->has('description') ? $champErreur : $champ }} resize-y">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-5 border-t border-brand-100">
                    <a href="{{ route('admin.calendars.index') }}" class="{{ $btnSecondaire }}">Annuler</a>
                    <button type="submit" class="{{ $btnPrimaire }}">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
