{{-- Présentation stagiaire de la vue partagée documents/index (incluse uniquement pour le rôle stagiaire) --}}
@php
    $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
    $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
    $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
    $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
    $btnPetit = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
    $btnPetitDanger = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50 transition-colors ' . $focus;
    $badgeFormat = 'mt-0.5 w-12 flex-shrink-0 rounded-md bg-brand-50 py-1 text-center text-[11px] font-semibold text-brand-800 uppercase';
    $enTete = 'flex items-center justify-between gap-3 px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100';
    $taille = fn($octets) => $octets ? number_format($octets / 1024, 1, ',', ' ') . ' Ko' : null;
    $nbMesDocs = $mesDocs->count();
    $nbPartages = $docsPartages->count();
@endphp

<div class="max-w-6xl mx-auto space-y-5">

    <!-- Résumé, recherche et ajout (sur le fond de page) -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <p class="text-sm text-slate-600">Gérez et partagez vos fichiers.</p>
        <div class="flex flex-col sm:flex-row gap-2">
            @if ($mesDocs->isNotEmpty())
                <input type="search" id="searchDocs" placeholder="Rechercher un document..."
                    aria-label="Rechercher dans mes documents" class="w-full sm:w-64 {{ $champ }}">
            @endif
            <a href="{{ route('documents.create') }}" class="{{ $btnPrimaire }} whitespace-nowrap">
                <i class="fas fa-plus text-xs" aria-hidden="true"></i>Nouveau document
            </a>
        </div>
    </div>

    <!-- Mes documents -->
    <section aria-labelledby="titre-mes-documents" class="bg-white border border-brand-100 rounded-xl overflow-hidden">
        <div class="{{ $enTete }}">
            <h2 id="titre-mes-documents" class="text-base font-semibold text-slate-900">Mes documents</h2>
            <span class="text-sm text-slate-600 tabular-nums">{{ $nbMesDocs }}
                {{ $nbMesDocs > 1 ? 'documents' : 'document' }}</span>
        </div>

        @if ($mesDocs->isEmpty())
            <div class="p-5 sm:p-6">
                <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                    <img src="{{ asset('images/illustrations/documents-empty.svg') }}" alt=""
                        class="mx-auto w-32 h-auto mb-4">
                    <p class="text-sm font-medium text-slate-900">Aucun document pour l'instant</p>
                    <p class="mt-1 text-sm text-slate-600">Ajoutez votre convention, vos justificatifs ou tout fichier
                        utile à votre stage.</p>
                    <a href="{{ route('documents.create') }}" class="mt-3 inline-block {{ $lien }}">Ajouter un
                        document</a>
                </div>
            </div>
        @else
            <ul class="divide-y divide-slate-100" id="mesDocs">
                @foreach ($mesDocs as $doc)
                    <li class="doc-item p-4 sm:px-6 sm:py-5" data-nom="{{ strtolower($doc->titre) }}">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-3 lg:gap-6">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <span class="{{ $badgeFormat }}">
                                    {{ $doc->type_fichier ?: 'Fichier' }}
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('documents.show', $doc) }}"
                                        class="block text-sm font-medium text-slate-900 hover:text-brand-700 break-words rounded {{ $focus }}">
                                        {{ $doc->titre }}
                                    </a>
                                    <p class="mt-0.5 text-xs text-slate-500 tabular-nums">
                                        {{ $doc->type_fichier ? strtoupper($doc->type_fichier) : 'Fichier' }}
                                        @if ($taille($doc->taille))
                                            · {{ $taille($doc->taille) }}
                                        @endif
                                        · {{ $doc->created_at->diffForHumans() }}
                                    </p>
                                    @if ($doc->description)
                                        <p class="mt-1 text-sm text-slate-500 line-clamp-2 break-words">{{ $doc->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 lg:justify-end lg:flex-shrink-0">
                                @if ($doc->partage_tous)
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800">Public</span>
                                @else
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">Limité</span>
                                @endif
                                <a href="{{ route('documents.telecharger', $doc) }}" class="{{ $btnPetit }}">
                                    <i class="fas fa-download" aria-hidden="true"></i>Télécharger
                                </a>
                                <a href="{{ route('documents.show', $doc) }}" class="{{ $btnPetit }}">
                                    <i class="fas fa-eye" aria-hidden="true"></i>Voir
                                </a>
                                @if ($doc->user_id === Auth::id())
                                    <a href="{{ route('documents.edit', $doc) }}" class="{{ $btnPetit }}">
                                        <i class="fas fa-edit" aria-hidden="true"></i>Modifier
                                    </a>
                                    <form action="{{ route('documents.destroy', $doc) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-delete {{ $btnPetitDanger }}">
                                            <i class="fas fa-trash" aria-hidden="true"></i>Supprimer
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <p id="docsAucun" class="hidden px-6 py-8 text-center text-sm text-slate-500">
                Aucun résultat pour cette recherche.</p>
        @endif
    </section>

    <!-- Documents partagés avec moi -->
    @if ($docsPartages->count() > 0)
        <section aria-labelledby="titre-partages" class="bg-white border border-brand-100 rounded-xl overflow-hidden">
            <div class="{{ $enTete }}">
                <h2 id="titre-partages" class="text-base font-semibold text-slate-900">Partagés avec moi</h2>
                <span class="text-sm text-slate-600 tabular-nums">{{ $nbPartages }}
                    {{ $nbPartages > 1 ? 'documents' : 'document' }}</span>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach ($docsPartages as $doc)
                    <li class="p-4 sm:px-6 sm:py-5 flex flex-col lg:flex-row lg:items-center gap-3 lg:gap-6">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <span class="{{ $badgeFormat }}">
                                {{ $doc->type_fichier ?: 'Fichier' }}
                            </span>
                            <div class="min-w-0">
                                <a href="{{ route('documents.show', $doc) }}"
                                    class="block text-sm font-medium text-slate-900 hover:text-brand-700 break-words rounded {{ $focus }}">
                                    {{ $doc->titre }}
                                </a>
                                <p class="mt-0.5 text-xs text-slate-500">Par {{ $doc->user->nom_complet }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 lg:justify-end lg:flex-shrink-0">
                            <a href="{{ route('documents.telecharger', $doc) }}" class="{{ $btnPetit }}">
                                <i class="fas fa-download" aria-hidden="true"></i>Télécharger
                            </a>
                            <a href="{{ route('documents.show', $doc) }}" class="{{ $btnPetit }}">
                                <i class="fas fa-eye" aria-hidden="true"></i>Voir
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>

@push('scripts')
    <script>
        document.getElementById('searchDocs')?.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            const items = document.querySelectorAll('.doc-item');
            let visibles = 0;
            items.forEach(item => {
                const ok = item.dataset.nom?.includes(val);
                item.style.display = ok ? '' : 'none';
                if (ok) visibles++;
            });
            document.getElementById('docsAucun')?.classList.toggle('hidden', items.length === 0 || visibles > 0);
        });
    </script>
@endpush
