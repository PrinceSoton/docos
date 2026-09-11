@extends('layouts.stagiaire')
@section('titre', 'Mon rapport')
@section('breadcrumb', 'Rapports > Détail')
@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-0 space-y-5">
        <div class="card p-5 sm:p-6" data-aos="fade-up">
            <div class="flex flex-col sm:flex-row items-start justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-800">{{ $report->titre }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span
                            class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-lg capitalize">{{ $report->type_affiche }}</span>
                        @php $sl = ['soumis'=>'bg-amber-100 text-amber-700','valide'=>'bg-green-100 text-green-700','rejete'=>'bg-red-100 text-red-600','en_revision'=>'bg-blue-100 text-blue-700']; @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sl[$report->statut] ?? '' }}">
                            {{ ucfirst(str_replace('_', ' ', $report->statut)) }}
                        </span>
                        @if ($report->note !== null)
                            <span
                                class="font-bold text-{{ $report->note >= 14 ? 'green' : ($report->note >= 10 ? 'amber' : 'red') }}-600">
                                <i class="fas fa-star text-amber-400 mr-1"></i>{{ $report->note }}/20
                            </span>
                        @endif
                    </div>
                    <p class="text-slate-400 text-xs mt-1">Déposé le {{ $report->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('stagiaire.reports.telecharger', $report) }}" download
                        class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-blue-700 transition">
                        <i class="fas fa-download"></i>Télécharger
                    </a>
                    @if ($report->statut === 'soumis')
                        <a href="{{ route('stagiaire.reports.edit', $report) }}"
                            class="flex items-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-amber-600 transition">
                            <i class="fas fa-edit"></i>Modifier
                        </a>
                    @endif
                </div>
            </div>

            @if ($report->project)
                <div class="p-4 bg-indigo-50 rounded-xl mb-4">
                    <p class="text-xs text-indigo-400 uppercase font-medium mb-1">Projet lié</p>
                    <p class="font-semibold text-slate-800">{{ $report->project->titre }}</p>
                </div>
            @endif

            @if ($report->description)
                <div class="p-4 bg-slate-50 rounded-xl mb-4">
                    <p class="text-xs text-slate-400 uppercase font-medium mb-1">Description</p>
                    <p class="text-slate-700 text-sm">{{ $report->description }}</p>
                </div>
            @endif

            @if ($report->commentaire_mentor)
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-100 mb-4">
                    <p class="text-xs text-amber-500 uppercase font-medium mb-1"><i
                            class="fas fa-comment mr-1"></i>Commentaire de mon mentor</p>
                    <p class="text-slate-700 text-sm">{{ $report->commentaire_mentor }}</p>
                    @if ($report->valide_par)
                        <p class="text-slate-400 text-xs mt-1">Par {{ $report->validePar->nom_complet }} —
                            {{ $report->valide_le?->format('d/m/Y') }}</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- ============================================================ -->
        <!-- VISUALISEUR INTÉGRÉ                                          -->
        <!-- ============================================================ -->
        <div class="card p-5 sm:p-6" data-aos="fade-up">
            <h3 class="text-slate-800 font-bold text-lg mb-4">
                <i class="fas fa-eye text-indigo-500 mr-2"></i>Aperçu du rapport
            </h3>

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
                <div class="w-full rounded-xl overflow-hidden bg-slate-100" style="height: 600px;">
                    <iframe src="{{ $streamUrl }}" class="w-full h-full border-0"
                        style="min-height: 400px; height: 100%;" allowfullscreen></iframe>
                </div>
            @elseif (in_array($ext, $imageExtensions))
                <!-- Image -->
                <div class="w-full rounded-xl overflow-hidden bg-slate-50 flex items-center justify-center p-4">
                    <img src="{{ $streamUrl }}" alt="{{ $report->titre }}"
                        class="max-w-full max-h-[600px] object-contain rounded-lg shadow-sm" loading="lazy">
                </div>
            @elseif (in_array($ext, $videoExtensions))
                <!-- Vidéo -->
                <div class="w-full rounded-xl overflow-hidden bg-slate-900 p-2">
                    <video controls class="w-full rounded-lg" style="max-height: 600px;" src="{{ $streamUrl }}">
                        Votre navigateur ne supporte pas la lecture de vidéo.
                    </video>
                </div>
            @elseif (in_array($ext, $audioExtensions))
                <!-- Audio -->
                <div class="w-full rounded-xl bg-slate-50 p-6 flex items-center justify-center">
                    <audio controls class="w-full max-w-2xl" src="{{ $streamUrl }}">
                        Votre navigateur ne supporte pas la lecture audio.
                    </audio>
                </div>
            @elseif (in_array($ext, $texteExtensions) && isset($contenuTexte))
                <!-- Fichier texte -->
                <div class="w-full rounded-xl bg-slate-900 text-white p-4 overflow-auto" style="max-height: 600px;">
                    <pre class="text-sm font-mono whitespace-pre-wrap break-words">{{ $contenuTexte }}</pre>
                </div>
            @elseif (in_array($ext, $officeExtensions))
                <!-- Fichiers Office -->
                @php
                    $fileUrl = Storage::url($report->fichier);
                    $encodedUrl = urlencode($fileUrl);
                    $officeViewerUrl = "https://view.officeapps.live.com/op/embed.aspx?src={$encodedUrl}";
                @endphp
                <div class="w-full rounded-xl overflow-hidden bg-slate-100" style="height: 600px;">
                    <iframe src="{{ $officeViewerUrl }}" class="w-full h-full border-0"
                        style="min-height: 400px; height: 100%;" allowfullscreen></iframe>
                </div>
                <p class="text-xs text-slate-400 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Aperçu généré via Microsoft Office Online. Assurez-vous que le fichier est accessible publiquement.
                </p>
            @else
                <!-- Format non pris en charge -->
                <div class="p-8 bg-amber-50 border border-amber-200 rounded-xl text-center">
                    <i class="fas fa-file-alt text-4xl text-amber-400 mb-3"></i>
                    <p class="text-amber-700 font-medium">Ce type de fichier ne peut pas être visualisé en ligne.</p>
                    <p class="text-amber-600 text-sm mt-1">Extension :
                        <strong>{{ strtoupper($ext) ?: 'inconnue' }}</strong>
                    </p>
                    <a href="{{ route('stagiaire.reports.telecharger', $report) }}"
                        class="mt-4 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition">
                        <i class="fas fa-download"></i>Télécharger le fichier
                    </a>
                </div>
            @endif
        </div>

        <!-- Commentaires -->
        <div class="card p-5 sm:p-6" data-aos="fade-up">
            <h3 class="text-slate-800 font-bold text-lg mb-4">
                <i class="fas fa-comments text-indigo-500 mr-2"></i>Commentaires ({{ $report->comments->count() }})
            </h3>
            <div class="space-y-3 mb-5">
                @forelse($report->comments as $comment)
                    <div class="flex gap-3 {{ $comment->user_id === Auth::id() ? '' : 'flex-row-reverse' }}">
                        <div
                            class="w-8 h-8 rounded-xl {{ $comment->user_id === Auth::id() ? 'bg-indigo-400' : 'bg-emerald-400' }} flex items-center justify-center flex-shrink-0">
                            <span
                                class="text-white font-bold text-xs">{{ strtoupper(substr($comment->user->prenom ?? '', 0, 1)) }}</span>
                        </div>
                        <div class="{{ $comment->user_id === Auth::id() ? '' : 'text-right' }} flex-1">
                            <div
                                class="inline-block max-w-xs {{ $comment->user_id === Auth::id() ? 'bg-indigo-50' : 'bg-emerald-50' }} rounded-2xl px-4 py-3">
                                <p class="text-slate-500 text-xs font-medium mb-0.5">
                                    {{ $comment->user->nom_complet ?? '—' }}</p>
                                <p class="text-slate-700 text-sm">{{ $comment->contenu }}</p>
                                <p class="text-slate-400 text-xs mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-4">Aucun commentaire</p>
                @endforelse
            </div>
        </div>

        <a href="{{ route('stagiaire.reports.index') }}"
            class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour à mes rapports
        </a>
    </div>
@endsection
