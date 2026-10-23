{{-- Présentation stagiaire de la vue partagée documents/show (incluse uniquement pour le rôle stagiaire) --}}
@php
    $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
    $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
    $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
    $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    $btnDanger = 'inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-50 transition-colors ' . $focus;

    $ext = strtolower($document->type_fichier);
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico'];
    $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv', 'wmv'];
    $audioExtensions = ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a', 'wma'];
    $pdfExtensions = ['pdf'];
    $texteExtensions = ['txt', 'csv', 'json', 'xml', 'html', 'css', 'js', 'php', 'log', 'md', 'sql'];
    $officeExtensions = ['docx', 'xlsx', 'pptx', 'doc', 'xls', 'ppt'];
    $streamUrl = route('documents.stream', $document);
@endphp

<div class="max-w-4xl mx-auto space-y-5">

    <!-- En-tête du document -->
    <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="min-w-0">
                <h2 class="text-lg font-semibold text-slate-900 break-words">{{ $document->titre }}</h2>
                <p class="mt-1 text-sm text-slate-500 tabular-nums">Par {{ $document->user->nom_complet }} ·
                    {{ $document->created_at->format('d/m/Y') }}</p>
            </div>
            <a href="{{ route('documents.telecharger', $document) }}" class="flex-shrink-0 {{ $btnPrimaire }}">
                <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger
            </a>
        </div>

        <!-- Métadonnées -->
        <dl class="mt-5 rounded-lg bg-brand-50 border border-brand-100 p-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <dt class="text-xs text-slate-600">Format</dt>
                <dd class="mt-0.5 text-sm font-medium text-slate-900">
                    {{ $document->type_fichier ? strtoupper($document->type_fichier) : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-600">Taille</dt>
                <dd class="mt-0.5 text-sm font-medium text-slate-900 tabular-nums">
                    {{ $document->taille ? round($document->taille / 1024, 1) . ' Ko' : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-600">Partage</dt>
                <dd class="mt-0.5">
                    @if ($document->partage_tous)
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800">Tous</span>
                    @else
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-white border border-brand-100 text-slate-700">Limité</span>
                    @endif
                </dd>
            </div>
        </dl>

        @if (!$document->partage_tous && $document->partagesAvec->count())
            <div class="mt-3 rounded-lg bg-brand-50 border border-brand-100 p-4">
                <p class="text-xs text-slate-600">Partagé avec</p>
                <ul class="mt-1.5 flex flex-wrap gap-2">
                    @foreach ($document->partagesAvec as $u)
                        <li class="text-xs font-medium px-2 py-0.5 rounded-md bg-white border border-brand-100 text-slate-700">{{ $u->nom_complet }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>

    @if ($document->description)
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <h3 class="text-base font-semibold text-slate-900">Description</h3>
            <p class="mt-2 text-sm text-slate-700 break-words">{{ $document->description }}</p>
        </section>
    @endif

    <!-- Aperçu -->
    <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
        <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
            <h3 class="text-base font-semibold text-slate-900">Aperçu</h3>
        </div>
        <div class="p-5 sm:p-6">
            @if (in_array($ext, $pdfExtensions))
                <div class="w-full rounded-lg overflow-hidden border border-brand-100 bg-slate-100 h-[480px] sm:h-[600px]">
                    <iframe src="{{ $streamUrl }}" title="Aperçu du document {{ $document->titre }}"
                        class="w-full h-full border-0" allowfullscreen></iframe>
                </div>
            @elseif (in_array($ext, $imageExtensions))
                <div class="w-full rounded-lg border border-brand-100 bg-slate-50 flex items-center justify-center p-4">
                    <img src="{{ $streamUrl }}" alt="{{ $document->titre }}"
                        class="max-w-full max-h-[600px] object-contain rounded" loading="lazy">
                </div>
            @elseif (in_array($ext, $videoExtensions))
                <div class="w-full rounded-lg overflow-hidden border border-brand-100 bg-slate-900 p-2">
                    <video controls class="w-full rounded max-h-[600px]" src="{{ $streamUrl }}">
                        Votre navigateur ne supporte pas la lecture de vidéo.
                    </video>
                </div>
            @elseif (in_array($ext, $audioExtensions))
                <div class="w-full rounded-lg border border-brand-100 bg-slate-50 p-6 flex items-center justify-center">
                    <audio controls class="w-full max-w-2xl" src="{{ $streamUrl }}">
                        Votre navigateur ne supporte pas la lecture audio.
                    </audio>
                </div>
            @elseif (in_array($ext, $texteExtensions) && isset($contenuTexte))
                <div class="w-full rounded-lg border border-brand-100 bg-slate-50 p-4 overflow-auto max-h-[600px]">
                    <pre class="text-sm text-slate-800 font-mono whitespace-pre-wrap break-words">{{ $contenuTexte }}</pre>
                </div>
            @elseif (in_array($ext, $officeExtensions))
                @php
                    // URL absolue du fichier pour la visionneuse Microsoft Office Online
                    $fileUrl = Storage::url($document->fichier);
                    $encodedUrl = urlencode($fileUrl);
                    $officeViewerUrl = "https://view.officeapps.live.com/op/embed.aspx?src={$encodedUrl}";
                @endphp
                <div class="w-full rounded-lg overflow-hidden border border-brand-100 bg-slate-100 h-[480px] sm:h-[600px]">
                    <iframe src="{{ $officeViewerUrl }}" title="Aperçu du document {{ $document->titre }}"
                        class="w-full h-full border-0" allowfullscreen></iframe>
                </div>
                <p class="mt-2 text-xs text-slate-500">Aperçu généré via Microsoft Office Online. Assurez-vous que le
                    fichier est accessible publiquement.</p>
            @else
                <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                    <p class="text-sm font-medium text-slate-900">Ce type de fichier ne peut pas être visualisé en ligne.</p>
                    <p class="mt-1 text-sm text-slate-600">Extension :
                        <span class="font-medium text-slate-900">{{ strtoupper($ext) ?: 'inconnue' }}</span>
                    </p>
                    <a href="{{ route('documents.telecharger', $document) }}" class="mt-4 {{ $btnSecondaire }}">
                        <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger le fichier
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Actions -->
    <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-3">
        <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-2 {{ $lien }}">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour aux documents
        </a>
        @if ($document->user_id === Auth::id())
            <div class="flex flex-col sm:flex-row gap-2 sm:ml-auto">
                <a href="{{ route('documents.edit', $document) }}" class="{{ $btnSecondaire }}">
                    <i class="fas fa-edit text-xs" aria-hidden="true"></i>Modifier
                </a>
                <form action="{{ route('documents.destroy', $document) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="button" class="btn-delete w-full {{ $btnDanger }}">
                        <i class="fas fa-trash text-xs" aria-hidden="true"></i>Supprimer
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
