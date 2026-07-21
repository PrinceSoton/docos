@extends('layouts.admin')
@section('titre', 'Affecter un stagiaire')
@section('breadcrumb', 'Mentors > Affectation')
@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-link text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Affecter un stagiaire à un mentor</h2>
                    <p class="text-slate-400 text-sm">Établissez le lien mentor–stagiaire</p>
                </div>
            </div>

            <form action="{{ route('admin.mentors.doAssign') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-user-graduate mr-1 text-indigo-500"></i>Stagiaire *
                    </label>
                    <select name="stagiaire_id" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition text-sm">
                        <option value="">-- Sélectionner un stagiaire --</option>
                        @foreach ($stagiaires as $stag)
                            <option value="{{ $stag->id }}" {{ old('stagiaire_id') == $stag->id ? 'selected' : '' }}>
                                {{ $stag->user->nom_complet }} — {{ $stag->matricule }}
                                @if ($stag->mentor_id)
                                    (Actuel : {{ $stag->mentor?->nom_complet }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="fas fa-chalkboard-teacher mr-1 text-emerald-500"></i>Mentor *
                    </label>
                    <select name="mentor_id" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition text-sm">
                        <option value="">-- Sélectionner un mentor --</option>
                        @foreach ($mentors as $mentor)
                            <option value="{{ $mentor->user_id }}"
                                {{ old('mentor_id') == $mentor->user_id ? 'selected' : '' }}>
                                {{ $mentor->user->nom_complet }}
                                @if ($mentor->departement)
                                    — {{ $mentor->departement }}
                                @endif
                                ({{ $mentor->stagiaires->count() }} stagiaire(s))
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <i class="fas fa-check mr-2"></i>Confirmer l'affectation
                </button>
            </form>
        </div>

        <!-- Vue des affectations actuelles -->
        <div class="card p-6" data-aos="fade-up" data-aos-delay="100">
            <h3 class="text-slate-800 font-bold text-lg mb-4">
                <i class="fas fa-sitemap text-indigo-500 mr-2"></i>Affectations actuelles
            </h3>
            <div class="space-y-3">
                @foreach ($mentors as $mentor)
                    @if ($mentor->stagiaires->count() > 0)
                        <div class="p-4 bg-gradient-to-r from-indigo-50 to-emerald-50 rounded-2xl border border-indigo-100">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center">
                                    <i class="fas fa-chalkboard-teacher text-white text-xs"></i>
                                </div>
                                <p class="font-bold text-slate-800">{{ $mentor->user->nom_complet }}</p>
                                <span class="text-xs text-slate-400">{{ $mentor->departement ?: '' }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2 ml-11">
                                @foreach ($mentor->stagiaires as $stag)
                                    <div
                                        class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-indigo-200 text-sm">
                                        <i class="fas fa-user-graduate text-indigo-400 text-xs"></i>
                                        <span class="text-slate-700 font-medium">{{ $stag->user->nom_complet }}</span>
                                        <span class="text-slate-400 text-xs">{{ $stag->matricule }}</span>
                                        <form action="{{ route('admin.mentors.removeAssign', $stag) }}" method="POST"
                                            class="no-loader">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-red-400 hover:text-red-600 transition"
                                                title="Retirer">
                                                <i class="fas fa-times-circle text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
