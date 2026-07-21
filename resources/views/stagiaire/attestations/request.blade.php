@extends('layouts.stagiaire')
@section('titre', 'Demande d\'attestation')
@section('breadcrumb', 'Attestations > Demande')
@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-certificate text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-slate-800 font-bold text-xl">Demande officielle</h2>
                    <p class="text-slate-400 text-sm">Attestation de stage ou convention de stage</p>
                </div>
            </div>

            <!-- Info processus -->
            <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 mb-6">
                <p class="text-sm text-blue-700 font-medium mb-2"><i class="fas fa-info-circle mr-1"></i>Processus de
                    validation</p>
                <div class="flex items-center gap-2 text-xs text-blue-600">
                    <span class="bg-blue-200 px-2 py-1 rounded-lg">1. Votre demande</span>
                    <i class="fas fa-arrow-right"></i>
                    <span class="bg-blue-200 px-2 py-1 rounded-lg">2. Validation mentor</span>
                    <i class="fas fa-arrow-right"></i>
                    <span class="bg-blue-200 px-2 py-1 rounded-lg">3. Envoi admin</span>
                </div>
            </div>

            <form action="{{ route('stagiaire.attestations.store') }}" method="POST" class="space-y-5">
                @csrf
                <!-- Choix du type -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-3">Type de document *</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label
                            class="flex flex-col items-center gap-3 p-5 border-2 rounded-2xl cursor-pointer transition hover:border-blue-400
                        {{ $attestationExiste ? 'opacity-50 cursor-not-allowed border-slate-200' : 'border-slate-200' }}">
                            <input type="radio" name="type" value="attestation"
                                {{ $attestationExiste ? 'disabled' : '' }} class="sr-only" required>
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-certificate text-blue-600 text-xl"></i>
                            </div>
                            <div class="text-center">
                                <p class="font-bold text-slate-800 text-sm">Attestation de stage</p>
                                @if ($attestationExiste)
                                    <span class="text-xs text-green-600 font-medium"><i class="fas fa-check mr-1"></i>Déjà
                                        demandée</span>
                                @else
                                    <p class="text-slate-400 text-xs">Atteste votre présence en stage</p>
                                @endif
                            </div>
                        </label>
                        <label
                            class="flex flex-col items-center gap-3 p-5 border-2 rounded-2xl cursor-pointer transition hover:border-purple-400
                        {{ $conventionExiste ? 'opacity-50 cursor-not-allowed border-slate-200' : 'border-slate-200' }}">
                            <input type="radio" name="type" value="convention"
                                {{ $conventionExiste ? 'disabled' : '' }} class="sr-only">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-contract text-purple-600 text-xl"></i>
                            </div>
                            <div class="text-center">
                                <p class="font-bold text-slate-800 text-sm">Convention de stage</p>
                                @if ($conventionExiste)
                                    <span class="text-xs text-green-600 font-medium"><i class="fas fa-check mr-1"></i>Déjà
                                        demandée</span>
                                @else
                                    <p class="text-slate-400 text-xs">Document officiel de convention</p>
                                @endif
                            </div>
                        </label>
                    </div>
                    @if ($attestationExiste && $conventionExiste)
                        <div class="mt-4 p-4 bg-green-50 rounded-xl border border-green-100 text-center">
                            <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i>
                            <p class="text-green-700 font-semibold text-sm">Vous avez déjà effectué toutes vos demandes</p>
                        </div>
                    @endif
                </div>

                @if (!$attestationExiste || !$conventionExiste)
                    <!-- Informations du stagiaire -->
                    <div class="p-5 bg-slate-50 rounded-2xl">
                        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wide mb-3">Vos informations de
                            stage</p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-slate-400 text-xs">Matricule</p>
                                <p class="font-bold text-slate-800 font-mono">{{ $stagiaire->matricule }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs">École</p>
                                <p class="font-semibold text-slate-700">{{ $stagiaire->ecole ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs">Début du stage</p>
                                <p class="font-semibold text-slate-700">{{ $stagiaire->date_debut->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-xs">Fin du stage</p>
                                <p class="font-semibold text-slate-700">{{ $stagiaire->date_fin->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Motif de la demande <span
                                class="text-slate-400 font-normal">(optionnel)</span></label>
                        <textarea name="motif_demande" rows="3"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm resize-none"
                            placeholder="Expliquez pourquoi vous avez besoin de ce document...">{{ old('motif_demande') }}</textarea>
                    </div>

                    <div class="p-4 bg-amber-50 rounded-xl border border-amber-100 text-sm">
                        <i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>
                        <strong>Important :</strong> Une seule demande par type de document est autorisée.
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-paper-plane mr-2"></i>Soumettre ma demande
                    </button>
                @endif
            </form>

            <a href="{{ route('stagiaire.attestations.index') }}"
                class="mt-4 inline-flex items-center gap-2 text-amber-600 hover:text-amber-800 font-medium text-sm">
                <i class="fas fa-arrow-left"></i>Voir mes demandes
            </a>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.querySelectorAll('input[name=type]').forEach(r => {
            r.addEventListener('change', function() {
                document.querySelectorAll('label:has(input[name=type])').forEach(l => {
                    l.classList.remove('border-blue-400', 'bg-blue-50', 'border-purple-400',
                        'bg-purple-50');
                    l.classList.add('border-slate-200');
                });
                const c = this.value === 'attestation' ? 'blue' : 'purple';
                this.closest('label').classList.add('border-' + c + '-400', 'bg-' + c + '-50');
                this.closest('label').classList.remove('border-slate-200');
            });
        });
    </script>
@endpush
