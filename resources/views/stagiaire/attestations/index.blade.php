@extends('layouts.stagiaire')
@section('titre', 'Mes Attestations')
@section('breadcrumb', 'Mon espace > Attestations')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;

        $statuts = [
            'en_attente' => ['En attente', 'bg-amber-50 text-amber-800'],
            'valide_mentor' => ['Validé par le mentor', 'bg-sky-50 text-sky-800'],
            'approuve_admin' => ['Approuvé', 'bg-sky-50 text-sky-800'],
            'envoye' => ['Envoyé', 'bg-emerald-50 text-emerald-800'],
            'refuse' => ['Refusé', 'bg-red-50 text-red-800'],
        ];
        $types = [
            'attestation' => 'Attestation de stage',
            'convention' => 'Convention de stage',
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Résumé et action (sur le fond de page) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-slate-900">Attestations et conventions</h2>
                <p class="text-sm text-slate-600">Suivi de vos demandes officielles</p>
            </div>
            <a href="{{ route('stagiaire.attestations.request') }}" class="{{ $btnPrimaire }} w-full sm:w-auto">
                <i class="fas fa-plus text-xs" aria-hidden="true"></i>Nouvelle demande
            </a>
        </div>

        <!-- Liste des demandes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">
            @forelse($attestations as $att)
                @php
                    [$libStatut, $clsStatut] = $statuts[$att->statut] ?? [ucfirst(str_replace('_', ' ', (string) $att->statut)), 'bg-slate-100 text-slate-700'];
                    $libType = $types[$att->type] ?? ucfirst((string) $att->type);
                    $refuse = $att->statut === 'refuse';
                @endphp
                <article class="bg-white border border-brand-100 rounded-xl overflow-hidden min-w-0">
                    <!-- En-tête teinté : type, numéro et statut -->
                    <div class="flex items-start justify-between gap-3 px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-slate-900 break-words">{{ $libType }}</h3>
                            <p class="text-xs text-slate-600 tabular-nums">Demande n° {{ $att->id }}</p>
                        </div>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsStatut }}">
                            {{ $libStatut }}
                        </span>
                    </div>

                    <div class="p-5 sm:p-6">
                        <!-- Étapes de suivi -->
                        <ol class="grid grid-cols-3 gap-2" aria-label="Étapes de la demande">
                            @foreach ([['Demande', 0], ['Mentor', 1], ['Reçu', 2]] as [$etapeLabel, $i])
                                @php
                                    $done =
                                        (in_array($att->statut, ['valide_mentor', 'approuve_admin', 'envoye']) && $i <= 1) ||
                                        ($att->statut === 'envoye' && $i <= 2);
                                    $echec = $refuse && $i === 0;
                                    $dot = $echec ? 'bg-red-500' : ($done ? 'bg-emerald-500' : 'bg-slate-300');
                                    $etat = $echec ? 'refusée' : ($done ? 'terminée' : 'à venir');
                                @endphp
                                <li>
                                    <div class="h-1 rounded-full {{ $dot }}" aria-hidden="true"></div>
                                    <p class="mt-1.5 text-xs {{ $done || $echec ? 'font-medium text-slate-900' : 'text-slate-500' }}">
                                        {{ $etapeLabel }}<span class="sr-only"> : étape {{ $etat }}</span>
                                    </p>
                                </li>
                            @endforeach
                        </ol>

                        @if ($att->motif_demande || $att->commentaire)
                            <dl class="mt-5 space-y-3">
                                @if ($att->motif_demande)
                                    <div class="rounded-lg bg-brand-50 border border-brand-100 p-4">
                                        <dt class="text-xs text-slate-600">Votre motif</dt>
                                        <dd class="mt-0.5 text-sm text-slate-900 break-words whitespace-pre-line">{{ $att->motif_demande }}</dd>
                                    </div>
                                @endif
                                @if ($att->commentaire)
                                    <div class="rounded-lg bg-brand-50 border border-brand-100 p-4">
                                        <dt class="text-xs text-slate-600">Commentaire</dt>
                                        <dd class="mt-0.5 text-sm text-slate-900 break-words whitespace-pre-line">{{ $att->commentaire }}</dd>
                                    </div>
                                @endif
                            </dl>
                        @endif

                        <div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-xs text-slate-500 tabular-nums">
                            <span>Demandé le {{ $att->created_at->format('d/m/Y') }}</span>
                            @if ($att->envoye_le)
                                <span>Envoyé le {{ $att->envoye_le->format('d/m/Y') }}</span>
                            @endif
                        </div>

                        @if ($att->fichier)
                            <!-- Document disponible : accent vert -->
                            <div class="mt-4 rounded-lg bg-accent-50 border border-accent-100 p-4">
                                <p class="text-sm font-medium text-accent-700">Votre document est disponible</p>
                                <a href="{{ route('stagiaire.attestations.telecharger', $att) }}" download
                                    class="{{ $btnPrimaire }} mt-3 w-full">
                                    <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger mon document
                                </a>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="md:col-span-2 rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                    <p class="text-sm font-medium text-slate-900">Aucune demande effectuée</p>
                    <p class="mt-1 text-sm text-slate-600 max-w-md mx-auto">
                        Vous pouvez demander une attestation de stage ou une convention de stage. Chaque type de document ne peut être demandé qu'une seule fois.
                    </p>
                    <a href="{{ route('stagiaire.attestations.request') }}" class="{{ $lien }} mt-4 inline-block">
                        Faire ma première demande
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection
