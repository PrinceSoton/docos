@extends('layouts.mentor')
@section('titre', 'Évaluer le rapport')
@section('breadcrumb', 'Rapports > Évaluation')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-emerald-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Évaluer le rapport</h2>
                    <p class="text-slate-400 text-sm truncate">{{ $report->titre }}</p>
                </div>
            </div>

            <!-- Info rapport -->
            <div class="p-4 bg-slate-50 rounded-2xl mb-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-200 flex items-center justify-center font-bold text-emerald-700 flex-shrink-0">
                        {{ strtoupper(substr($report->stagiaire->user->prenom ?? '', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $report->stagiaire->user->nom_complet ?? '—' }}</p>
                        <p class="text-slate-500 text-sm capitalize">{{ $report->type }} • Déposé le
                            {{ $report->created_at->format('d/m/Y') }}</p>
                    </div>
                    <a href="{{ route('mentor.reports.telecharger', $report) }}" download
                        class="ml-auto flex items-center gap-2 bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-xl text-xs font-medium transition">
                        <i class="fas fa-download"></i>Télécharger
                    </a>
                </div>
            </div>

            <form action="{{ route('mentor.reports.doEvaluate', $report) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Décision *</label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach (['valide' => ['Valider', 'green', 'check-circle'], 'en_revision' => ['Révision', 'blue', 'edit'], 'rejete' => ['Rejeter', 'red', 'times-circle']] as $val => [$label, $color, $icon])
                            <label
                                class="flex flex-col items-center gap-2 p-4 border-2 rounded-xl cursor-pointer transition hover:border-{{ $color }}-400
                        {{ old('statut') === $val ? 'border-' . $color . '-400 bg-' . $color . '-50' : 'border-slate-200' }}">
                                <input type="radio" name="statut" value="{{ $val }}"
                                    {{ old('statut') === $val ? 'checked' : '' }} class="sr-only" required>
                                <i class="fas fa-{{ $icon }} text-{{ $color }}-500 text-2xl"></i>
                                <span class="font-semibold text-slate-800 text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Note -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        <i class="fas fa-star mr-1 text-amber-500"></i>Note (sur 20) — optionnel
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" name="note" min="0" max="20"
                            value="{{ old('note', $report->note ?? 10) }}" id="noteRange"
                            oninput="document.getElementById('noteVal').textContent=this.value"
                            class="flex-1 h-2 bg-slate-200 rounded-full accent-emerald-500">
                        <span id="noteVal"
                            class="w-10 text-center font-black text-xl text-emerald-600">{{ old('note', $report->note ?? 10) }}</span>
                        <span class="text-slate-400 text-sm">/20</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        <i class="fas fa-comment mr-1 text-indigo-500"></i>Commentaire pour le stagiaire
                    </label>
                    <textarea name="commentaire_mentor" rows="4"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm resize-none"
                        placeholder="Vos remarques, points positifs, axes d'amélioration...">{{ old('commentaire_mentor', $report->commentaire_mentor) }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-check mr-2"></i>Soumettre l'évaluation
                    </button>
                    <a href="{{ route('mentor.reports.show', $report) }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.querySelectorAll('input[name=statut]').forEach(r => {
            r.addEventListener('change', function() {
                document.querySelectorAll('label:has(input[name=statut])').forEach(l => {
                    l.classList.remove('border-green-400', 'bg-green-50', 'border-blue-400',
                        'bg-blue-50', 'border-red-400', 'bg-red-50');
                    l.classList.add('border-slate-200');
                });
                const colors = {
                    valide: 'green',
                    en_revision: 'blue',
                    rejete: 'red'
                };
                const c = colors[this.value];
                if (c) {
                    this.closest('label').classList.add('border-' + c + '-400', 'bg-' + c + '-50');
                    this.closest('label').classList.remove('border-slate-200');
                }
            });
        });
    </script>
@endpush
