@extends('layouts.stagiaire')
@section('titre', 'Mes Attestations')
@section('breadcrumb', 'Mon espace > Attestations')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Attestations & Conventions</h2>
                <p class="text-slate-500 text-sm">Suivi de vos demandes officielles</p>
            </div>
            <a href="{{ route('stagiaire.attestations.request') }}"
                class="flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i>Nouvelle demande
            </a>
        </div>

        <!-- Status des demandes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse($attestations as $att)
                <div class="card p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5" data-aos="fade-up"
                    data-aos-delay="{{ $loop->index * 80 }}">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-2xl {{ $att->type === 'attestation' ? 'bg-blue-100' : 'bg-purple-100' }} flex items-center justify-center flex-shrink-0">
                                <i
                                    class="fas fa-{{ $att->type === 'attestation' ? 'certificate' : 'file-contract' }} {{ $att->type === 'attestation' ? 'text-blue-600' : 'text-purple-600' }} text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 capitalize text-lg">{{ $att->type }}</h3>
                                <p class="text-slate-400 text-xs">Demande #{{ $att->id }}</p>
                            </div>
                        </div>
                        @php
                            $sb = [
                                'en_attente' => 'bg-amber-100 text-amber-700',
                                'valide_mentor' => 'bg-blue-100 text-blue-700',
                                'approuve_admin' => 'bg-indigo-100 text-indigo-700',
                                'envoye' => 'bg-green-100 text-green-700',
                                'refuse' => 'bg-red-100 text-red-600',
                            ];
                            $sl = [
                                'en_attente' => 'En attente',
                                'valide_mentor' => 'Validé mentor',
                                'approuve_admin' => 'Approuvé',
                                'envoye' => 'Envoyé',
                                'refuse' => 'Refusé',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sb[$att->statut] ?? '' }}">
                            {{ $sl[$att->statut] ?? $att->statut }}
                        </span>
                    </div>

                    <!-- Étapes de progression -->
                    <div class="flex items-center gap-2 mb-4">
                        @foreach ([['en_attente', 'Demande', 'check'], ['valide_mentor', 'Mentor', 'chalkboard-teacher'], ['envoye', 'Reçu', 'download']] as $i => [$etapeStatut, $etapeLabel, $etapeIcon])
                            @php
                                $done =
                                    (in_array($att->statut, ['valide_mentor', 'approuve_admin', 'envoye']) &&
                                        $i <= 1) ||
                                    ($att->statut === 'envoye' && $i <= 2);
                                $current = $att->statut === $etapeStatut;
                            @endphp
                            <div class="flex-1 flex flex-col items-center">
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-xs
                        {{ $att->statut === 'refuse' && $i === 0 ? 'bg-red-100 text-red-600' : ($done ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-400') }}">
                                    <i
                                        class="fas fa-{{ $att->statut === 'refuse' && $i === 0 ? 'times' : ($done ? 'check' : $etapeIcon) }}"></i>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">{{ $etapeLabel }}</p>
                            </div>
                            @if ($i < 2)
                                <div class="flex-1 h-0.5 {{ $done ? 'bg-green-200' : 'bg-slate-100' }}"></div>
                            @endif
                        @endforeach
                    </div>

                    @if ($att->motif_demande)
                        <p class="text-slate-500 text-xs mb-3 bg-slate-50 rounded-xl p-2">
                            <i class="fas fa-comment mr-1 text-indigo-400"></i>{{ $att->motif_demande }}
                        </p>
                    @endif

                    @if ($att->commentaire)
                        <p class="text-slate-500 text-xs mb-3 bg-amber-50 rounded-xl p-2 border border-amber-100">
                            <i class="fas fa-reply mr-1 text-amber-500"></i>{{ $att->commentaire }}
                        </p>
                    @endif

                    <div class="flex items-center justify-between text-xs text-slate-400 mb-3">
                        <span>Demandé le {{ $att->created_at->format('d/m/Y') }}</span>
                        @if ($att->envoye_le)
                            <span>Envoyé le {{ $att->envoye_le->format('d/m/Y') }}</span>
                        @endif
                    </div>

                    @if ($att->fichier)
                        <a href="{{ route('stagiaire.attestations.telecharger', $att) }}" download
                            class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white py-2.5 rounded-xl font-medium hover:shadow-lg transition-all hover:-translate-y-0.5">
                            <i class="fas fa-download"></i>Télécharger mon document
                        </a>
                    @endif
                </div>
            @empty
                <div class="col-span-2 card p-16 text-center">
                    <i class="fas fa-certificate text-5xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400 mb-4">Aucune demande effectuée</p>
                    <a href="{{ route('stagiaire.attestations.request') }}"
                        class="inline-flex items-center gap-2 bg-amber-500 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-amber-600 transition">
                        <i class="fas fa-plus"></i>Faire ma première demande
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection
