@extends('layouts.' . Auth::user()->role)
@section('titre', 'Document')
@section('breadcrumb', 'Documents > Détail')
@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-0">
        <div class="card p-5 sm:p-8" data-aos="fade-up">
            <!-- En-tête -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-slate-800 font-bold text-xl">{{ $document->titre }}</h2>
                        <p class="text-slate-400 text-sm">Par {{ $document->user->nom_complet }} •
                            {{ $document->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('documents.telecharger', $document) }}"
                        class="flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all">
                        <i class="fas fa-download"></i>Télécharger
                    </a>
                </div>
            </div>

            <!-- Métadonnées -->
            <div class="space-y-4">
                @if ($document->description)
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 font-medium uppercase mb-1">Description</p>
                        <p class="text-slate-700 text-sm">{{ $document->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 font-medium uppercase mb-1">Format</p>
                        <p class="text-slate-700 font-semibold text-sm">
                            {{ $document->type_fichier ? strtoupper($document->type_fichier) : '—' }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 font-medium uppercase mb-1">Taille</p>
                        <p class="text-slate-700 font-semibold text-sm">
                            {{ $document->taille ? round($document->taille / 1024, 1) . ' Ko' : '—' }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs text-slate-400 font-medium uppercase mb-1">Partage</p>
                        <p
                            class="font-semibold text-sm {{ $document->partage_tous ? 'text-green-600' : 'text-amber-600' }}">
                            {{ $document->partage_tous ? 'Tous' : 'Limité' }}
                        </p>
                    </div>
                </div>

                @if (!$document->partage_tous && $document->partagesAvec->count())
                    <div class="p-4 bg-indigo-50 rounded-xl">
                        <p class="text-xs text-indigo-400 font-medium uppercase mb-2">Partagé avec</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($document->partagesAvec as $u)
                                <span
                                    class="bg-white border border-indigo-200 text-indigo-700 text-xs px-3 py-1 rounded-full">{{ $u->nom_complet }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- ======================================================== -->
            <!-- VISUALISEUR INTÉGRÉ                                     -->
            <!-- ======================================================== -->
            <div class="mt-6 pt-4 border-t border-slate-200">
                <h3 class="text-slate-800 font-bold text-lg mb-4">
                    <i class="fas fa-eye text-indigo-500 mr-2"></i>Aperçu
                </h3>

                @php
                    $ext = strtolower($document->type_fichier);
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico'];
                    $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv', 'wmv'];
                    $audioExtensions = ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a', 'wma'];
                    $pdfExtensions = ['pdf'];
                    $texteExtensions = ['txt', 'csv', 'json', 'xml', 'html', 'css', 'js', 'php', 'log', 'md', 'sql'];
                    $officeExtensions = ['docx', 'xlsx', 'pptx', 'doc', 'xls', 'ppt'];
                    $streamUrl = route('documents.stream', $document);
                @endphp

                @if (in_array($ext, $pdfExtensions))
                    <!-- PDF -->
                    <div class="w-full rounded-xl overflow-hidden bg-slate-100" style="height: 600px;">
                        <iframe src="{{ $streamUrl }}" class="w-full h-full border-0"
                            style="min-height: 400px; height: 100%;" allowfullscreen>
                        </iframe>
                    </div>
                @elseif (in_array($ext, $imageExtensions))
                    <!-- Image -->
                    <div class="w-full rounded-xl overflow-hidden bg-slate-50 flex items-center justify-center p-4">
                        <img src="{{ $streamUrl }}" alt="{{ $document->titre }}"
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
                    <!-- Fichiers Office (DOCX, XLSX, PPTX...) via Microsoft Office Online -->
                    @php
                        // Générer l'URL absolue du fichier
$fileUrl = Storage::url($document->fichier);
// Encoder l'URL pour l'API Office
                        $encodedUrl = urlencode($fileUrl);
                        $officeViewerUrl = "https://view.officeapps.live.com/op/embed.aspx?src={$encodedUrl}";
                    @endphp
                    <div class="w-full rounded-xl overflow-hidden bg-slate-100" style="height: 600px;">
                        <iframe src="{{ $officeViewerUrl }}" class="w-full h-full border-0"
                            style="min-height: 400px; height: 100%;" allowfullscreen>
                        </iframe>
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
                        <a href="{{ route('documents.telecharger', $document) }}"
                            class="mt-4 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition">
                            <i class="fas fa-download"></i>Télécharger le fichier
                        </a>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-3 mt-6 pt-6 border-t border-slate-100">
                @if ($document->user_id === Auth::id())
                    <a href="{{ route('documents.edit', $document) }}"
                        class="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-medium transition">
                        <i class="fas fa-edit"></i>Modifier
                    </a>
                    <form action="{{ route('documents.destroy', $document) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="button"
                            class="btn-delete flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl font-medium transition">
                            <i class="fas fa-trash"></i>Supprimer
                        </button>
                    </form>
                @endif
                <a href="{{ route('documents.index') }}"
                    class="flex items-center gap-2 border border-slate-200 text-slate-600 px-5 py-2.5 rounded-xl font-medium hover:bg-slate-50 transition ml-auto">
                    <i class="fas fa-arrow-left"></i>Retour
                </a>
            </div>
        </div>
    </div>
@endsection
