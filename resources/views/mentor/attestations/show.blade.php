@extends('layouts.mentor')
@section('titre', 'Demande attestation')
@section('breadcrumb', 'Attestations > Détail')
@section('content')
    <div class="max-w-2xl mx-auto space-y-5">
        <div class="card p-8" data-aos="fade-up">
            <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-certificate text-amber-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-slate-800 font-bold text-xl capitalize">{{ $attestation->type }}</h2>
                        <p class="text-slate-400 text-sm">Demande #{{ $attestation->id }}</p>
                    </div>
                </div>
                @php $sc = ['en_attente'=>'bg-amber-100 text-amber-700','valide_mentor'=>'bg-green-100 text-green-700','refuse'=>'bg-red-100 text-red-600','envoye'=>'bg-indigo-100 text-indigo-700']; @endphp
                <span class="px-4 py-2 rounded-xl text-sm font-bold {{ $sc[$attestation->statut] ?? '' }}">
                    {{ ['en_attente' => 'En attente', 'valide_mentor' => 'Validé', 'refuse' => 'Refusé', 'envoye' => 'Envoyé'][$attestation->statut] ?? $attestation->statut }}
                </span>
            </div>

            <!-- Stagiaire -->
            <div class="p-4 bg-emerald-50 rounded-2xl mb-4">
                <p class="text-xs text-emerald-400 uppercase font-medium mb-2">Stagiaire</p>
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-200 flex items-center justify-center font-bold text-emerald-700 flex-shrink-0">
                        {{ strtoupper(substr($attestation->stagiaire->user->prenom ?? '', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $attestation->stagiaire->user->nom_complet ?? '—' }}</p>
                        <p class="text-slate-500 text-sm">{{ $attestation->stagiaire->matricule }} •
                            {{ $attestation->stagiaire->ecole ?: '' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="p-4 bg-slate-50 rounded-xl">
                    <p class="text-xs text-slate-400 uppercase font-medium">Type</p>
                    <p class="font-bold text-slate-800 capitalize mt-1">{{ $attestation->type }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl">
                    <p class="text-xs text-slate-400 uppercase font-medium">Demandé le</p>
                    <p class="font-bold text-slate-800 mt-1">{{ $attestation->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            @if ($attestation->motif_demande)
                <div class="p-4 bg-slate-50 rounded-xl mb-4">
                    <p class="text-xs text-slate-400 uppercase font-medium mb-1">Motif</p>
                    <p class="text-slate-700 text-sm">{{ $attestation->motif_demande }}</p>
                </div>
            @endif

            @if ($attestation->commentaire)
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-100 mb-4">
                    <p class="text-xs text-amber-500 uppercase font-medium mb-1">Commentaire</p>
                    <p class="text-slate-700 text-sm">{{ $attestation->commentaire }}</p>
                </div>
            @endif

            @if ($attestation->fichier)
                <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                    <p class="text-xs text-indigo-400 font-medium mb-2">Document envoyé</p>
                    <a href="{{ route('mentor.attestations.telecharger', $attestation) }}"
                        class="flex items-center gap-2 text-indigo-700 hover:text-indigo-900 font-medium text-sm">
                        <i class="fas fa-download"></i>Télécharger le document
                    </a>
                </div>
            @endif

            <div class="flex gap-3 mt-6 pt-5 border-t border-slate-100">
                @if ($attestation->statut === 'en_attente')
                    <a href="{{ route('mentor.attestations.validate', $attestation) }}"
                        class="flex items-center gap-2 bg-emerald-500 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-emerald-600 transition hover:shadow-md">
                        <i class="fas fa-check"></i>Valider / Refuser
                    </a>
                @endif
                <a href="{{ route('mentor.attestations.index') }}"
                    class="flex items-center gap-2 border border-slate-200 text-slate-600 px-5 py-2.5 rounded-xl font-medium hover:bg-slate-50 transition ml-auto">
                    <i class="fas fa-arrow-left"></i>Retour
                </a>
            </div>
        </div>
    </div>
@endsection
