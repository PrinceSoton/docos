@extends('layouts.stagiaire')
@section('titre', 'Mes Rapports')
@section('breadcrumb', 'Mon espace > Rapports')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnPetit = 'inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium transition-colors ' . $focus;
        $btnPetitSecondaire = $btnPetit . ' border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300';
        $btnPetitDanger = $btnPetit . ' border border-red-200 bg-white text-red-700 hover:bg-red-50';

        $statuts = [
            'soumis' => ['Soumis', 'bg-amber-50 text-amber-800'],
            'valide' => ['Validé', 'bg-emerald-50 text-emerald-800'],
            'rejete' => ['Rejeté', 'bg-red-50 text-red-800'],
            'en_revision' => ['En révision', 'bg-sky-50 text-sky-800'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">
        <!-- Résumé et action principale -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <p class="text-sm text-slate-600 tabular-nums">
                <span class="font-semibold text-slate-900">{{ $rapports->total() }}</span> rapport(s) déposé(s)
            </p>
            <a href="{{ route('stagiaire.reports.create') }}" class="w-full sm:w-auto {{ $btnPrimaire }}">
                <i class="fas fa-plus text-xs" aria-hidden="true"></i>Nouveau rapport
            </a>
        </div>

        @if ($rapports->count() > 0)
            <!-- Filtres -->
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="search" id="search" placeholder="Rechercher un rapport..." aria-label="Rechercher un rapport"
                    class="w-full sm:w-72 {{ $champ }}">
                <select id="filterStatut" aria-label="Filtrer par statut" class="w-full sm:w-48 {{ $champ }}">
                    <option value="">Tous statuts</option>
                    <option value="soumis">Soumis</option>
                    <option value="valide">Validé</option>
                    <option value="rejete">Rejeté</option>
                    <option value="en_revision">En révision</option>
                </select>
            </div>
        @endif

        <!-- Liste -->
        <div class="space-y-4" id="rapportGrid">
            @forelse($rapports as $rapport)
                @php
                    [$libStatut, $clsStatut] = $statuts[$rapport->statut] ?? [ucfirst(str_replace('_', ' ', $rapport->statut)), 'bg-slate-100 text-slate-700'];
                    $clsNote = $rapport->note !== null
                        ? ($rapport->note >= 14 ? 'text-emerald-700' : ($rapport->note >= 10 ? 'text-amber-800' : 'text-red-700'))
                        : '';
                @endphp
                <article class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6 rapport-card"
                    data-statut="{{ $rapport->statut }}">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="min-w-0 text-base font-semibold text-slate-900 leading-snug break-words">{{ $rapport->titre }}</h3>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsStatut }}">{{ $libStatut }}</span>
                    </div>
                    @if ($rapport->description)
                        <p class="mt-1.5 text-sm text-slate-500 line-clamp-2 break-words max-w-3xl">{{ $rapport->description }}</p>
                    @endif

                    <dl class="mt-4 rounded-lg bg-brand-50 border border-brand-100 px-4 py-3 flex flex-wrap gap-x-8 gap-y-3 text-sm">
                        @if ($rapport->project)
                            <div class="min-w-0 max-w-full">
                                <dt class="text-xs text-slate-600">Projet</dt>
                                <dd class="mt-0.5 font-medium text-slate-900 break-words">{{ $rapport->project->titre }}</dd>
                            </div>
                        @endif
                        @if ($rapport->note !== null)
                            <div>
                                <dt class="text-xs text-slate-600">Note</dt>
                                <dd class="mt-0.5 font-medium tabular-nums {{ $clsNote }}">{{ $rapport->note }}/20</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs text-slate-600">Déposé le</dt>
                            <dd class="mt-0.5 font-medium text-slate-900 tabular-nums">{{ $rapport->created_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ route('stagiaire.reports.show', $rapport) }}" class="{{ $btnPetitSecondaire }}">
                            <i class="fas fa-eye text-xs" aria-hidden="true"></i>Voir
                        </a>
                        <a href="{{ route('stagiaire.reports.telecharger', $rapport) }}" class="{{ $btnPetitSecondaire }}">
                            <i class="fas fa-download text-xs" aria-hidden="true"></i>Fichier
                        </a>
                        @if ($rapport->statut === 'soumis')
                            <a href="{{ route('stagiaire.reports.edit', $rapport) }}" class="{{ $btnPetitSecondaire }}">
                                <i class="fas fa-edit text-xs" aria-hidden="true"></i>Modifier
                            </a>
                            <form action="{{ route('stagiaire.reports.destroy', $rapport) }}" method="POST"
                                id="del-r-{{ $rapport->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete {{ $btnPetitDanger }}"
                                    data-form="del-r-{{ $rapport->id }}">
                                    <i class="fas fa-trash text-xs" aria-hidden="true"></i>Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                    <img src="{{ asset('images/illustrations/documents-empty.svg') }}" alt="" class="mx-auto w-32 h-auto mb-4">
                    <p class="text-sm font-medium text-slate-900">Aucun rapport déposé</p>
                    <p class="mt-1 text-sm text-slate-600">Déposez votre premier rapport pour le soumettre à votre mentor.</p>
                    <a href="{{ route('stagiaire.reports.create') }}" class="mt-5 {{ $btnPrimaire }}">
                        <i class="fas fa-plus text-xs" aria-hidden="true"></i>Déposer mon premier rapport
                    </a>
                </div>
            @endforelse
        </div>

        @if ($rapports->count() > 0)
            <p id="aucunResultat" class="hidden text-sm text-slate-600 text-center py-2">Aucun résultat pour cette recherche.</p>
        @endif

        <div>{{ $rapports->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        (function() {
            const champ = document.getElementById('search');
            const statut = document.getElementById('filterStatut');
            if (!champ || !statut) return;
            const vide = document.getElementById('aucunResultat');

            function filter() {
                const s = champ.value.toLowerCase();
                const st = statut.value;
                let visibles = 0;
                document.querySelectorAll('.rapport-card').forEach(c => {
                    const ok = c.textContent.toLowerCase().includes(s) && (!st || c.dataset.statut === st);
                    c.style.display = ok ? '' : 'none';
                    if (ok) visibles++;
                });
                if (vide) vide.classList.toggle('hidden', visibles > 0);
            }
            champ.addEventListener('input', filter);
            statut.addEventListener('change', filter);
        })();
    </script>
@endpush
