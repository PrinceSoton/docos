@extends('layouts.mentor')
@section('titre', 'Événements')
@section('breadcrumb', 'Mentor > Événements')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;

        $types = [
            'information' => ['Information', 'bg-sky-50 text-sky-800'],
            'evenement' => ['Événement', 'bg-brand-50 text-brand-800'],
            'note' => ['Note', 'bg-slate-100 text-slate-700'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Résumé et filtres -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-slate-900">Événements et informations</h2>
                <p class="text-sm text-slate-600">Publications partagées par l'administration</p>
            </div>
            @if ($evenements->count() > 0)
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="search" id="search" placeholder="Rechercher un événement..." aria-label="Rechercher un événement"
                        class="w-full sm:w-64 {{ $champ }}">
                    <select id="filterType" aria-label="Filtrer par type" class="w-full sm:w-44 {{ $champ }}">
                        <option value="">Tous types</option>
                        <option value="information">Information</option>
                        <option value="evenement">Événement</option>
                        <option value="note">Note</option>
                    </select>
                </div>
            @endif
        </div>

        <!-- Liste -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="evtGrid">
            @forelse($evenements as $evt)
                @php
                    [$libType, $clsType] = $types[$evt->type] ?? [ucfirst((string) $evt->type), 'bg-slate-100 text-slate-700'];
                @endphp
                <article class="evt-card flex flex-col min-w-0 overflow-hidden bg-white border border-brand-100 rounded-xl"
                    data-type="{{ $evt->type }}">
                    @if ($evt->image)
                        <div class="h-36 sm:h-40 overflow-hidden bg-brand-50">
                            <img src="{{ asset('storage/' . $evt->image) }}" alt="" class="w-full h-full object-cover"
                                loading="lazy">
                        </div>
                    @endif
                    <div class="flex flex-col flex-1 p-5">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsType }}">{{ $libType }}</span>
                            <span class="text-xs text-slate-500 text-right">{{ $evt->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="mt-3 text-base font-semibold text-slate-900 leading-snug break-words">{{ $evt->titre }}</h3>
                        @if ($evt->contenu)
                            <p class="mt-1.5 text-sm text-slate-500 line-clamp-2 break-words">{{ Str::limit(strip_tags($evt->contenu), 80) }}</p>
                        @endif
                        @if ($evt->date_evenement)
                            <p class="mt-3 text-xs text-slate-700 tabular-nums">
                                <span class="text-slate-500">Date :</span>
                                <span class="font-medium">{{ $evt->date_evenement->format('d/m/Y à H:i') }}</span>
                            </p>
                        @endif
                        <div class="flex flex-wrap items-center gap-2 mt-auto pt-4">
                            <a href="{{ route('mentor.evenements.show', $evt) }}" class="{{ $btnPrimaire }}">Voir</a>
                            @if ($evt->fichier)
                                <a href="{{ asset('storage/' . $evt->fichier) }}" download class="{{ $btnSecondaire }}">
                                    <i class="fas fa-download text-xs" aria-hidden="true"></i>Télécharger
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="md:col-span-2 lg:col-span-3 rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                    <p class="text-sm font-medium text-slate-900">Aucun événement pour le moment</p>
                    <p class="mt-1 text-sm text-slate-600 max-w-md mx-auto">
                        Les informations, événements et notes partagés par l'administration apparaîtront ici.
                    </p>
                </div>
            @endforelse
        </div>

        <p id="evtAucun" data-sans-revelation class="hidden rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-8 text-center text-sm text-slate-600">
            Aucun résultat pour cette recherche.
        </p>

        <div>{{ $evenements->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        (function() {
            const search = document.getElementById('search');
            const filterType = document.getElementById('filterType');
            if (!search || !filterType) return;

            function filterEvt() {
                const s = search.value.toLowerCase();
                const t = filterType.value;
                let visibles = 0;
                document.querySelectorAll('.evt-card').forEach(card => {
                    const visible = card.textContent.toLowerCase().includes(s) && (!t || card.dataset.type === t);
                    card.style.display = visible ? '' : 'none';
                    if (visible) visibles++;
                });
                document.getElementById('evtAucun').classList.toggle('hidden', visibles > 0);
            }
            search.addEventListener('input', filterEvt);
            filterType.addEventListener('change', filterEvt);
        })();
    </script>
@endpush
