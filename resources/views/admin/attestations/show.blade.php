@extends('layouts.admin')
@section('titre', 'Détail attestation')
@section('breadcrumb', 'Attestations > Détail')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors whitespace-nowrap ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors whitespace-nowrap ' . $focus;
        $btnDanger = 'inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-50 transition-colors whitespace-nowrap ' . $focus;

        $statuts = [
            'en_attente' => ['En attente', 'bg-amber-50 text-amber-800'],
            'valide_mentor' => ['Validé par mentor', 'bg-sky-50 text-sky-800'],
            'approuve_admin' => ['Approuvé', 'bg-sky-50 text-sky-800'],
            'envoye' => ['Envoyé', 'bg-emerald-50 text-emerald-800'],
            'refuse' => ['Refusé', 'bg-red-50 text-red-800'],
        ];
        [$libStatut, $clsStatut] = $statuts[$attestation->statut] ?? [$attestation->statut, 'bg-slate-100 text-slate-700'];
    @endphp

    <div class="max-w-3xl mx-auto space-y-5">
        <article class="bg-white border border-brand-100 rounded-xl overflow-hidden">
            <!-- En-tête teinté : type, numéro, statut -->
            <div class="flex items-start justify-between gap-3 px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-slate-900 capitalize break-words">{{ $attestation->type }}</h2>
                    <p class="text-xs text-slate-600 tabular-nums">Demande #{{ $attestation->id }}</p>
                </div>
                <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsStatut }}">{{ $libStatut }}</span>
            </div>

            <div class="p-5 sm:p-6 space-y-5">
                <!-- Stagiaire et demande (bloc teinté) -->
                <dl class="rounded-lg bg-brand-50 border border-brand-100 p-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div class="sm:col-span-2 min-w-0">
                        <dt class="text-xs text-slate-600">Stagiaire</dt>
                        <dd class="mt-1 flex items-center gap-2.5 min-w-0">
                            <span
                                class="h-9 w-9 rounded-full bg-brand-100 text-brand-800 text-sm font-semibold flex items-center justify-center flex-shrink-0"
                                aria-hidden="true">{{ strtoupper(substr($attestation->stagiaire->user->prenom ?? '', 0, 1)) }}</span>
                            <span class="min-w-0">
                                <span
                                    class="block text-sm font-medium text-slate-900 break-words">{{ $attestation->stagiaire->user->nom_complet ?? '—' }}</span>
                                <span class="block text-xs text-slate-600 tabular-nums break-words">{{ $attestation->stagiaire->matricule ?? '' }}
                                    — {{ $attestation->stagiaire->ecole ?: '' }}</span>
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-600">Type</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900 capitalize">{{ $attestation->type }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-600">Demandé le</dt>
                        <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">
                            {{ $attestation->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    @if ($attestation->valide_par_mentor)
                        <div class="min-w-0">
                            <dt class="text-xs text-slate-600">Validé par mentor</dt>
                            <dd class="mt-0.5 text-sm font-medium text-slate-900 break-words">
                                {{ $attestation->validePar?->nom_complet ?? '—' }}</dd>
                            <dd class="text-xs text-slate-600 tabular-nums">{{ $attestation->valide_le_mentor?->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                </dl>

                @if ($attestation->envoye_par_admin)
                    <!-- Envoi effectué : accent vert -->
                    <div class="rounded-lg bg-accent-50 border border-accent-100 p-4">
                        <p class="text-xs font-medium text-accent-700">Envoyé par admin</p>
                        <p class="mt-0.5 text-sm font-medium text-slate-900 break-words">
                            {{ $attestation->envoyePar?->nom_complet ?? '—' }}</p>
                        <p class="text-xs text-accent-700 tabular-nums">{{ $attestation->envoye_le?->format('d/m/Y') }}</p>
                    </div>
                @endif

                @if ($attestation->motif_demande)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Motif de la demande</h3>
                        <p class="mt-1 text-sm text-slate-700 break-words whitespace-pre-line">{{ $attestation->motif_demande }}</p>
                    </div>
                @endif

                @if ($attestation->commentaire)
                    <div class="rounded-lg bg-brand-50 border border-brand-100 p-4">
                        <h3 class="text-xs text-slate-600">Commentaire</h3>
                        <p class="mt-0.5 text-sm text-slate-900 break-words whitespace-pre-line">{{ $attestation->commentaire }}</p>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 pt-5 border-t border-brand-100">
                    @if (in_array($attestation->statut, ['valide_mentor', 'approuve_admin']))
                        <a href="{{ route('admin.attestations.uploadForm', $attestation) }}" class="{{ $btnPrimaire }}">
                            <i class="fas fa-upload text-xs" aria-hidden="true"></i>Envoyer le document
                        </a>
                        <form action="{{ route('admin.attestations.destroy', $attestation) }}" method="POST"
                            id="del-att-{{ $attestation->id }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-delete w-full sm:w-auto {{ $btnDanger }}"
                                data-form="del-att-{{ $attestation->id }}">
                                <i class="fas fa-trash text-xs" aria-hidden="true"></i>Rejeter &amp; Supprimer
                            </button>
                        </form>
                    @endif
                    @if ($attestation->fichier)
                        <a href="{{ route('admin.attestations.telecharger', $attestation) }}" class="{{ $btnSecondaire }}">
                            <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger
                        </a>
                    @endif
                    <a href="{{ route('admin.attestations.index') }}" class="sm:ml-auto {{ $btnSecondaire }}">
                        <i class="fas fa-arrow-left text-xs text-slate-400" aria-hidden="true"></i>Retour
                    </a>
                </div>
            </div>
        </article>
    </div>
@endsection
