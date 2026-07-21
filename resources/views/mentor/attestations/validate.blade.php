@extends('layouts.mentor')
@section('titre', 'Valider la demande')
@section('breadcrumb', 'Attestations > Validation')
@section('content')
    <div class="max-w-xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Valider la demande</h2>
                    <p class="text-slate-400 text-sm capitalize">{{ $attestation->type }} —
                        {{ $attestation->stagiaire->user->nom_complet ?? '—' }}</p>
                </div>
            </div>

            <!-- Résumé -->
            <div class="p-4 bg-slate-50 rounded-2xl mb-5">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-slate-400 text-xs">Stagiaire</p>
                        <p class="font-semibold text-slate-800">{{ $attestation->stagiaire->user->nom_complet ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">Matricule</p>
                        <p class="font-semibold text-slate-800 font-mono">{{ $attestation->stagiaire->matricule }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">Type de document</p>
                        <p class="font-semibold text-slate-800 capitalize">{{ $attestation->type }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs">Demandé le</p>
                        <p class="font-semibold text-slate-800">{{ $attestation->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                @if ($attestation->motif_demande)
                    <div class="mt-3 pt-3 border-t border-slate-200">
                        <p class="text-slate-400 text-xs">Motif du stagiaire</p>
                        <p class="text-slate-700 text-sm mt-0.5">{{ $attestation->motif_demande }}</p>
                    </div>
                @endif
            </div>

            <form action="{{ route('mentor.attestations.doValidate', $attestation) }}" method="POST" class="space-y-5">
                @csrf @method('PUT')

                <!-- Décision -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-3">Votre décision *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="flex flex-col items-center gap-2 p-5 border-2 rounded-2xl cursor-pointer transition hover:border-green-400
                        {{ old('statut') === 'valide_mentor' ? 'border-green-400 bg-green-50' : 'border-slate-200' }}"
                            id="labelValide">
                            <input type="radio" name="statut" value="valide_mentor"
                                {{ old('statut') === 'valide_mentor' ? 'checked' : '' }} class="sr-only" required
                                id="radioValide">
                            <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                            <span class="font-bold text-slate-800">Valider</span>
                            <span class="text-slate-400 text-xs text-center">Transmettre à l'administration</span>
                        </label>
                        <label
                            class="flex flex-col items-center gap-2 p-5 border-2 rounded-2xl cursor-pointer transition hover:border-red-400
                        {{ old('statut') === 'refuse' ? 'border-red-400 bg-red-50' : 'border-slate-200' }}"
                            id="labelRefuse">
                            <input type="radio" name="statut" value="refuse"
                                {{ old('statut') === 'refuse' ? 'checked' : '' }} class="sr-only" id="radioRefuse">
                            <i class="fas fa-times-circle text-red-500 text-3xl"></i>
                            <span class="font-bold text-slate-800">Refuser</span>
                            <span class="text-slate-400 text-xs text-center">Notifier le stagiaire</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Commentaire (optionnel)</label>
                    <textarea name="commentaire" rows="3"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm resize-none"
                        placeholder="Expliquez votre décision au stagiaire...">{{ old('commentaire') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-check mr-2"></i>Confirmer ma décision
                    </button>
                    <a href="{{ route('mentor.attestations.index') }}"
                        class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition text-center">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('radioValide').addEventListener('change', function() {
            document.getElementById('labelValide').classList.add('border-green-400', 'bg-green-50');
            document.getElementById('labelValide').classList.remove('border-slate-200');
            document.getElementById('labelRefuse').classList.remove('border-red-400', 'bg-red-50');
            document.getElementById('labelRefuse').classList.add('border-slate-200');
        });
        document.getElementById('radioRefuse').addEventListener('change', function() {
            document.getElementById('labelRefuse').classList.add('border-red-400', 'bg-red-50');
            document.getElementById('labelRefuse').classList.remove('border-slate-200');
            document.getElementById('labelValide').classList.remove('border-green-400', 'bg-green-50');
            document.getElementById('labelValide').classList.add('border-slate-200');
        });
    </script>
@endpush
