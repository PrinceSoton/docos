@extends('layouts.mentor')
@section('titre', 'Rapport')
@section('breadcrumb', 'Rapports > Détail')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;

        $statuts = [
            'soumis' => ['Soumis', 'bg-amber-50 text-amber-800'],
            'valide' => ['Validé', 'bg-emerald-50 text-emerald-800'],
            'rejete' => ['Rejeté', 'bg-red-50 text-red-800'],
            'en_revision' => ['En révision', 'bg-sky-50 text-sky-800'],
        ];
        [$libStatut, $clsStatut] = $statuts[$report->statut] ?? [ucfirst(str_replace('_', ' ', $report->statut)), 'bg-slate-100 text-slate-700'];
        $clsNote = $report->note !== null
            ? ($report->note >= 14 ? 'text-emerald-700' : ($report->note >= 10 ? 'text-amber-800' : 'text-red-700'))
            : '';
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- En-tête du rapport -->
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 capitalize">{{ $report->type_affiche }}</span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md {{ $clsStatut }}">{{ $libStatut }}</span>
                    </div>
                    <h2 class="mt-3 text-lg font-semibold text-slate-900 leading-snug break-words">{{ $report->titre }}</h2>
                    <p class="mt-1 text-sm text-slate-500 tabular-nums">Déposé le {{ $report->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 flex-shrink-0">
                    @if ($report->statut === 'soumis' || $report->statut === 'en_revision')
                        <a href="{{ route('mentor.reports.evaluate', $report) }}" class="{{ $btnPrimaire }}">
                            <i class="fas fa-star text-xs" aria-hidden="true"></i>Évaluer
                        </a>
                    @endif
                    <a href="{{ route('mentor.reports.telecharger', $report) }}" class="{{ $btnSecondaire }}">
                        <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger
                    </a>
                </div>
            </div>

            <!-- Synthèse : stagiaire, projet, note (bloc teinté) -->
            <dl class="mt-5 rounded-lg bg-brand-50 border border-brand-100 p-4 grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4">
                <div class="min-w-0">
                    <dt class="text-xs text-slate-600">Stagiaire</dt>
                    <dd class="mt-1 flex items-center gap-2.5 min-w-0">
                        <span class="h-8 w-8 rounded-full bg-brand-100 text-brand-800 text-xs font-semibold flex items-center justify-center flex-shrink-0"
                            aria-hidden="true">{{ strtoupper(substr($report->stagiaire->user->prenom ?? '', 0, 1)) }}</span>
                        <span class="min-w-0">
                            <span class="block text-sm font-medium text-slate-900 truncate">{{ $report->stagiaire->user->nom_complet ?? '—' }}</span>
                            <span class="block text-xs text-slate-600 tabular-nums">{{ $report->stagiaire->matricule }}</span>
                        </span>
                    </dd>
                </div>
                @if ($report->project)
                    @php $progression = $report->project->progressionPourcent(); @endphp
                    <div class="min-w-0">
                        <dt class="text-xs text-slate-600">Projet lié</dt>
                        <dd class="mt-1">
                            <span class="block text-sm font-medium text-slate-900 break-words">{{ $report->project->titre }}</span>
                            <span class="mt-2 flex items-center gap-2">
                                <span class="flex-1 h-2 rounded-full bg-white border border-brand-100 overflow-hidden" role="progressbar"
                                    aria-label="Progression du projet" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progression }}">
                                    <span class="block h-full rounded-full bg-accent-500" style="width: {{ $progression }}%"></span>
                                </span>
                                <span class="text-xs text-slate-600 tabular-nums">{{ $progression }}% terminé</span>
                            </span>
                        </dd>
                    </div>
                @endif
                <div>
                    <dt class="text-xs text-slate-600">Note</dt>
                    <dd class="mt-1 text-sm font-medium tabular-nums {{ $report->note !== null ? $clsNote : 'text-slate-900' }}">
                        {{ $report->note !== null ? $report->note . '/20' : 'Non noté' }}
                    </dd>
                </div>
            </dl>

            @if ($report->description)
                <div class="mt-5">
                    <h3 class="text-sm font-semibold text-slate-900">Description</h3>
                    <p class="mt-1 text-sm text-slate-700 break-words whitespace-pre-line">{{ $report->description }}</p>
                </div>
            @endif

            @if ($report->commentaire_mentor)
                <div class="mt-5 rounded-lg bg-brand-50 border border-brand-100 p-4">
                    <h3 class="text-xs text-slate-600">Mon commentaire d'évaluation</h3>
                    <p class="mt-0.5 text-sm text-slate-900 break-words whitespace-pre-line">{{ $report->commentaire_mentor }}</p>
                </div>
            @endif
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

            <!-- Visualiseur intégré -->
            <section class="lg:col-span-2 bg-white border border-brand-100 rounded-xl overflow-hidden min-w-0">
                <div class="flex items-center justify-between gap-3 px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
                    <h3 class="text-base font-semibold text-slate-900">Aperçu du rapport</h3>
                    <a href="{{ route('mentor.reports.telecharger', $report) }}" class="{{ $lien }}">Télécharger</a>
                </div>
                <div class="p-4 sm:p-5">
                    @php
                        $ext = strtolower(pathinfo($report->fichier, PATHINFO_EXTENSION));
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico'];
                        $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv', 'wmv'];
                        $audioExtensions = ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a', 'wma'];
                        $pdfExtensions = ['pdf'];
                        $texteExtensions = ['txt', 'csv', 'json', 'xml', 'html', 'css', 'js', 'php', 'log', 'md', 'sql'];
                        $officeExtensions = ['docx', 'xlsx', 'pptx', 'doc', 'xls', 'ppt'];
                        $streamUrl = route('mentor.reports.stream', $report);
                    @endphp

                    @if (in_array($ext, $pdfExtensions))
                        <div class="w-full rounded-lg overflow-hidden border border-brand-100 bg-slate-100" style="height: 600px;">
                            <iframe src="{{ $streamUrl }}" title="Aperçu PDF du rapport {{ $report->titre }}"
                                class="w-full h-full border-0" style="min-height: 400px; height: 100%;" allowfullscreen></iframe>
                        </div>
                    @elseif (in_array($ext, $imageExtensions))
                        <div class="w-full rounded-lg border border-brand-100 bg-slate-50 flex items-center justify-center p-4">
                            <img src="{{ $streamUrl }}" alt="{{ $report->titre }}"
                                class="max-w-full max-h-[600px] object-contain rounded-md" loading="lazy">
                        </div>
                    @elseif (in_array($ext, $videoExtensions))
                        <div class="w-full rounded-lg overflow-hidden bg-slate-900 p-2">
                            <video controls class="w-full rounded-md" style="max-height: 600px;" src="{{ $streamUrl }}">
                                Votre navigateur ne supporte pas la lecture de vidéo.
                            </video>
                        </div>
                    @elseif (in_array($ext, $audioExtensions))
                        <div class="w-full rounded-lg border border-brand-100 bg-brand-50 p-6 flex items-center justify-center">
                            <audio controls class="w-full max-w-2xl" src="{{ $streamUrl }}">
                                Votre navigateur ne supporte pas la lecture audio.
                            </audio>
                        </div>
                    @elseif (in_array($ext, $texteExtensions) && isset($contenuTexte))
                        <div class="w-full rounded-lg border border-brand-100 bg-slate-50 p-4 overflow-auto" style="max-height: 600px;">
                            <pre class="text-sm text-slate-800 font-mono whitespace-pre-wrap break-words">{{ $contenuTexte }}</pre>
                        </div>
                    @elseif (in_array($ext, $officeExtensions))
                        @php
                            $fileUrl = Storage::url($report->fichier);
                            $encodedUrl = urlencode($fileUrl);
                            $officeViewerUrl = "https://view.officeapps.live.com/op/embed.aspx?src={$encodedUrl}";
                        @endphp
                        <div class="w-full rounded-lg overflow-hidden border border-brand-100 bg-slate-100" style="height: 600px;">
                            <iframe src="{{ $officeViewerUrl }}" title="Aperçu Office du rapport {{ $report->titre }}"
                                class="w-full h-full border-0" style="min-height: 400px; height: 100%;" allowfullscreen></iframe>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Aperçu généré via Microsoft Office Online.</p>
                    @else
                        <div class="rounded-lg border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                            <p class="text-sm font-medium text-slate-900">Ce type de fichier ne peut pas être visualisé en ligne.</p>
                            <p class="mt-1 text-sm text-slate-600">Extension :
                                <span class="font-medium text-slate-900">{{ strtoupper($ext) ?: 'inconnue' }}</span>
                            </p>
                            <a href="{{ route('mentor.reports.telecharger', $report) }}" class="mt-4 {{ $btnPrimaire }}">
                                <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger le fichier
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            <!-- Commentaires -->
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden min-w-0">
                <div class="flex items-center justify-between gap-3 px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
                    <h3 class="text-base font-semibold text-slate-900">Commentaires</h3>
                    <span class="text-xs text-slate-600 tabular-nums">{{ $report->comments->count() }}</span>
                </div>
                <div class="p-5 sm:p-6">
                    <form action="{{ route('mentor.reports.commenter', $report) }}" method="POST">
                        @csrf
                        <label for="commentaire-contenu" class="block text-sm font-medium text-slate-700 mb-1">Ajouter un commentaire</label>
                        <textarea id="commentaire-contenu" name="contenu" rows="3" required class="{{ $champ }} resize-none"
                            placeholder="Votre message au stagiaire..."></textarea>
                        @error('contenu')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="mt-3 w-full sm:w-auto {{ $btnPrimaire }}">
                            <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>Commenter
                        </button>
                    </form>

                    <div class="mt-6 pt-5 border-t border-brand-100 space-y-4">
                        @forelse($report->comments as $comment)
                            @php $estMoi = $comment->user_id === Auth::id(); @endphp
                            <div class="flex gap-3 {{ $estMoi ? '' : 'flex-row-reverse' }}">
                                <span class="h-8 w-8 rounded-full {{ $estMoi ? 'bg-brand-800 text-white' : 'bg-brand-100 text-brand-800' }} text-xs font-semibold flex items-center justify-center flex-shrink-0"
                                    aria-hidden="true">{{ strtoupper(substr($comment->user->prenom ?? '', 0, 1)) }}</span>
                                <div class="{{ $estMoi ? '' : 'text-right' }} flex-1 min-w-0">
                                    <div class="inline-block max-w-full text-left rounded-lg px-4 py-3 {{ $estMoi ? 'bg-brand-50 border border-brand-100' : 'bg-slate-50 border border-slate-100' }}">
                                        <p class="text-xs font-medium text-slate-600">{{ $comment->user->nom_complet ?? '—' }}</p>
                                        <p class="mt-0.5 text-sm text-slate-900 break-words whitespace-pre-line">{{ $comment->contenu }}</p>
                                        <p class="mt-1 text-xs text-slate-600">{{ $comment->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 text-center py-4">Aucun commentaire pour le moment.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <a href="{{ route('mentor.reports.index') }}" class="{{ $lien }} inline-flex items-center gap-2">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour aux rapports
        </a>
    </div>
@endsection
