@extends('layouts.stagiaire')
@section('titre', 'Mon rapport')
@section('breadcrumb', 'Rapports > Détail')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
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

    <div class="max-w-3xl mx-auto space-y-5">
        <!-- En-tête du rapport -->
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-lg font-semibold text-slate-900 leading-snug break-words">{{ $report->titre }}</h2>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 capitalize break-all">{{ $report->type_affiche }}</span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md {{ $clsStatut }}">{{ $libStatut }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 flex-shrink-0">
                    <a href="{{ route('stagiaire.reports.telecharger', $report) }}" download class="{{ $btnPrimaire }}">
                        <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger
                    </a>
                    @if ($report->statut === 'soumis')
                        <a href="{{ route('stagiaire.reports.edit', $report) }}" class="{{ $btnSecondaire }}">
                            <i class="fas fa-edit text-xs" aria-hidden="true"></i>Modifier
                        </a>
                    @endif
                </div>
            </div>

            <dl class="mt-5 rounded-lg bg-brand-50 border border-brand-100 p-4 grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs text-slate-600">Déposé le</dt>
                    <dd class="mt-0.5 font-medium text-slate-900 tabular-nums">{{ $report->created_at->format('d/m/Y à H:i') }}</dd>
                </div>
                @if ($report->note !== null)
                    <div>
                        <dt class="text-xs text-slate-600">Note</dt>
                        <dd class="mt-0.5 font-medium tabular-nums {{ $clsNote }}">{{ $report->note }}/20</dd>
                    </div>
                @endif
                @if ($report->project)
                    <div class="col-span-2 min-w-0">
                        <dt class="text-xs text-slate-600">Projet lié</dt>
                        <dd class="mt-0.5 font-medium text-slate-900 break-words">{{ $report->project->titre }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        @if ($report->description)
            <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
                <h3 class="text-base font-semibold text-slate-900">Description</h3>
                <p class="mt-2 text-sm text-slate-700 break-words whitespace-pre-line">{{ $report->description }}</p>
            </div>
        @endif

        @if ($report->commentaire_mentor)
            <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
                <h3 class="text-base font-semibold text-slate-900">Commentaire de mon mentor</h3>
                <div class="mt-3 rounded-lg bg-brand-50 border border-brand-100 p-4">
                    <p class="text-sm text-slate-800 break-words whitespace-pre-line">{{ $report->commentaire_mentor }}</p>
                    @if ($report->valide_par)
                        <p class="mt-2 text-xs text-slate-600">Par {{ $report->validePar->nom_complet }} —
                            <span class="tabular-nums">{{ $report->valide_le?->format('d/m/Y') }}</span></p>
                    @endif
                </div>
            </div>
        @endif

        <!-- ============================================================ -->
        <!-- VISUALISEUR INTÉGRÉ                                          -->
        <!-- ============================================================ -->
        <div class="bg-white border border-brand-100 rounded-xl overflow-hidden">
            <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
                <h3 class="text-base font-semibold text-slate-900">Aperçu du rapport</h3>
            </div>
            <div class="p-5 sm:p-6">

                @php
                    $ext = strtolower(pathinfo($report->fichier, PATHINFO_EXTENSION));
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico'];
                    $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv', 'wmv'];
                    $audioExtensions = ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a', 'wma'];
                    $pdfExtensions = ['pdf'];
                    $texteExtensions = ['txt', 'csv', 'json', 'xml', 'html', 'css', 'js', 'php', 'log', 'md', 'sql'];
                    $officeExtensions = ['docx', 'xlsx', 'pptx', 'doc', 'xls', 'ppt'];
                    $streamUrl = route('stagiaire.reports.stream', $report);
                @endphp

                @if (in_array($ext, $pdfExtensions))
                    <!-- PDF -->
                    <div class="w-full rounded-lg overflow-hidden border border-brand-100 bg-slate-100 h-[480px] sm:h-[600px]">
                        <iframe src="{{ $streamUrl }}" title="Aperçu du rapport {{ $report->titre }}"
                            class="w-full h-full border-0" allowfullscreen></iframe>
                    </div>
                @elseif (in_array($ext, $imageExtensions))
                    <!-- Image -->
                    <div class="w-full rounded-lg overflow-hidden border border-brand-100 bg-slate-50 flex items-center justify-center p-4">
                        <img src="{{ $streamUrl }}" alt="{{ $report->titre }}"
                            class="max-w-full max-h-[600px] object-contain rounded" loading="lazy">
                    </div>
                @elseif (in_array($ext, $videoExtensions))
                    <!-- Vidéo -->
                    <div class="w-full rounded-lg overflow-hidden border border-brand-100 bg-slate-100 p-2">
                        <video controls class="w-full rounded max-h-[600px]" src="{{ $streamUrl }}">
                            Votre navigateur ne supporte pas la lecture de vidéo.
                        </video>
                    </div>
                @elseif (in_array($ext, $audioExtensions))
                    <!-- Audio -->
                    <div class="w-full rounded-lg border border-brand-100 bg-slate-50 p-4 sm:p-6 flex items-center justify-center">
                        <audio controls class="w-full max-w-2xl" src="{{ $streamUrl }}">
                            Votre navigateur ne supporte pas la lecture audio.
                        </audio>
                    </div>
                @elseif (in_array($ext, $texteExtensions) && isset($contenuTexte))
                    <!-- Fichier texte -->
                    <div class="w-full rounded-lg border border-brand-100 bg-slate-50 text-slate-800 p-4 overflow-auto max-h-[600px]">
                        <pre class="text-sm font-mono whitespace-pre-wrap break-words">{{ $contenuTexte }}</pre>
                    </div>
                @elseif (in_array($ext, $officeExtensions))
                    <!-- Fichiers Office -->
                    @php
                        $fileUrl = Storage::url($report->fichier);
                        $encodedUrl = urlencode($fileUrl);
                        $officeViewerUrl = "https://view.officeapps.live.com/op/embed.aspx?src={$encodedUrl}";
                    @endphp
                    <div class="w-full rounded-lg overflow-hidden border border-brand-100 bg-slate-100 h-[480px] sm:h-[600px]">
                        <iframe src="{{ $officeViewerUrl }}" title="Aperçu du rapport {{ $report->titre }}"
                            class="w-full h-full border-0" allowfullscreen></iframe>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">
                        Aperçu généré via Microsoft Office Online. Assurez-vous que le fichier est accessible publiquement.
                    </p>
                @else
                    <!-- Format non pris en charge -->
                    <div class="rounded-lg border border-brand-100 bg-slate-50 p-6 sm:p-8 text-center">
                        <p class="text-sm font-medium text-slate-700">Ce type de fichier ne peut pas être visualisé en ligne.</p>
                        <p class="mt-1 text-sm text-slate-600">Extension :
                            <span class="font-medium text-slate-700">{{ strtoupper($ext) ?: 'inconnue' }}</span>
                        </p>
                        <a href="{{ route('stagiaire.reports.telecharger', $report) }}" class="mt-4 {{ $btnPrimaire }}">
                            <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger le fichier
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Commentaires -->
        <div class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <h3 class="text-base font-semibold text-slate-900 mb-4">
                Commentaires (<span class="tabular-nums">{{ $report->comments->count() }}</span>)
            </h3>
            @forelse($report->comments as $comment)
                @if ($loop->first)
                    <ul class="divide-y divide-slate-100">
                @endif
                <li class="py-3 first:pt-0 last:pb-0">
                    <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
                        <span class="text-sm font-medium text-slate-900 break-words">{{ $comment->user->nom_complet ?? '—' }}</span>
                        @if ($comment->user_id === Auth::id())
                            <span class="text-xs text-slate-500">(vous)</span>
                        @endif
                        <span class="text-xs text-slate-500 tabular-nums">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-700 break-words whitespace-pre-line">{{ $comment->contenu }}</p>
                </li>
                @if ($loop->last)
                    </ul>
                @endif
            @empty
                <p class="text-sm text-slate-500">Aucun commentaire pour ce rapport.</p>
            @endforelse
        </div>

        <a href="{{ route('stagiaire.reports.index') }}" class="inline-flex items-center gap-2 {{ $lien }}">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour à mes rapports
        </a>
    </div>
@endsection
