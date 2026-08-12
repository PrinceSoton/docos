@extends('layouts.admin')
@section('titre', 'Détail attestation')
@section('breadcrumb', 'Attestations > Détail')
@section('content')
    <div class="max-w-2xl mx-auto space-y-5">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-certificate text-amber-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-slate-800 font-bold text-xl capitalize">{{ $attestation->type }}</h2>
                        <p class="text-slate-400 text-sm">Demande #{{ $attestation->id }}</p>
                    </div>
                </div>
                @php
                    $badges = [
                        'en_attente' => 'bg-amber-100 text-amber-700',
                        'valide_mentor' => 'bg-blue-100 text-blue-700',
                        'envoye' => 'bg-green-100 text-green-700',
                        'refuse' => 'bg-red-100 text-red-600',
                    ];
                    $labels = [
                        'en_attente' => 'En attente',
                        'valide_mentor' => 'Validé par mentor',
                        'envoye' => 'Envoyé',
                        'refuse' => 'Refusé',
                    ];
                @endphp
                <span
                    class="px-4 py-2 rounded-xl text-sm font-bold {{ $badges[$attestation->statut] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $labels[$attestation->statut] ?? $attestation->statut }}
                </span>
            </div>

            <div class="space-y-4">
                <!-- Stagiaire -->
                <div class="p-4 bg-indigo-50 rounded-2xl">
                    <p class="text-xs text-indigo-400 font-semibold uppercase mb-2">Stagiaire</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-200 flex items-center justify-center font-bold text-indigo-700">
                            {{ strtoupper(substr($attestation->stagiaire->user->prenom ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800">{{ $attestation->stagiaire->user->nom_complet ?? '—' }}</p>
                            <p class="text-slate-500 text-sm">{{ $attestation->stagiaire->matricule ?? '' }} —
                                {{ $attestation->stagiaire->ecole ?: '' }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 uppercase font-medium">Type</p>
                        <p class="font-bold text-slate-800 capitalize mt-1">{{ $attestation->type }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 uppercase font-medium">Demandé le</p>
                        <p class="font-bold text-slate-800 mt-1">{{ $attestation->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    @if ($attestation->valide_par_mentor)
                        <div class="p-4 bg-blue-50 rounded-xl">
                            <p class="text-xs text-blue-400 uppercase font-medium">Validé par mentor</p>
                            <p class="font-bold text-blue-800 mt-1">{{ $attestation->validePar?->nom_complet ?? '—' }}</p>
                            <p class="text-blue-500 text-xs">{{ $attestation->valide_le_mentor?->format('d/m/Y') }}</p>
                        </div>
                    @endif
                    @if ($attestation->envoye_par_admin)
                        <div class="p-4 bg-green-50 rounded-xl">
                            <p class="text-xs text-green-400 uppercase font-medium">Envoyé par admin</p>
                            <p class="font-bold text-green-800 mt-1">{{ $attestation->envoyePar?->nom_complet ?? '—' }}</p>
                            <p class="text-green-500 text-xs">{{ $attestation->envoye_le?->format('d/m/Y') }}</p>
                        </div>
                    @endif
                </div>

                @if ($attestation->motif_demande)
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 uppercase font-medium mb-1">Motif de la demande</p>
                        <p class="text-slate-700 text-sm">{{ $attestation->motif_demande }}</p>
                    </div>
                @endif

                @if ($attestation->commentaire)
                    <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                        <p class="text-xs text-amber-500 uppercase font-medium mb-1">Commentaire</p>
                        <p class="text-slate-700 text-sm">{{ $attestation->commentaire }}</p>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex gap-3 mt-6 pt-5 border-t border-slate-100 flex-wrap">
                @if (in_array($attestation->statut, ['valide_mentor', 'approuve_admin']))
                    <a href="{{ route('admin.attestations.uploadForm', $attestation) }}"
                        class="flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all hover:-translate-y-0.5">
                        <i class="fas fa-upload"></i>Envoyer le document
                    </a>

                    <!-- Bouton Supprimer -->
                    <form action="{{ route('admin.attestations.destroy', $attestation) }}" method="POST"
                        id="del-att-{{ $attestation->id }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            class="btn-delete flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl font-medium transition hover:shadow-lg"
                            data-form="del-att-{{ $attestation->id }}">
                            <i class="fas fa-trash"></i>Rejeter & Supprimer
                        </button>
                    </form>
                @endif
                @if ($attestation->fichier)
                    <a href="{{ route('admin.attestations.telecharger', $attestation) }}"
                        class="flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-blue-700 transition hover:shadow-lg">
                        <i class="fas fa-download"></i>Télécharger
                    </a>
                @endif

                <a href="{{ route('admin.attestations.index') }}"
                    class="flex items-center gap-2 border border-slate-200 text-slate-600 px-5 py-2.5 rounded-xl font-medium hover:bg-slate-50 transition ml-auto">
                    <i class="fas fa-arrow-left"></i>Retour
                </a>
            </div>
        </div>
    </div>
@endsection
