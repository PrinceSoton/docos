@extends('layouts.admin')
@section('titre', 'Événements & Informations')
@section('breadcrumb', 'Système > Événements')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnPetitSecondaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $btnIcone = 'inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900 transition-colors ' . $focus;
        $btnIconeDanger = 'inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-red-200 bg-white text-red-700 hover:bg-red-50 transition-colors ' . $focus;

        $tc = [
            'information' => ['Information', 'bg-sky-50 text-sky-800'],
            'evenement' => ['Événement', 'bg-brand-50 text-brand-800'],
            'note' => ['Note', 'bg-slate-100 text-slate-700'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Barre d'outils : résumé, filtres, action principale -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-slate-900">Événements & Communications</h2>
                <p class="text-sm text-slate-600 tabular-nums">{{ $evenements->total() }} publication(s)</p>
            </div>
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3">
                <input type="search" id="search" placeholder="Rechercher par titre, type..."
                    aria-label="Rechercher une publication" class="w-full sm:w-64 {{ $champ }}">
                <select id="filterType" aria-label="Filtrer par type" class="w-full sm:w-44 {{ $champ }}">
                    <option value="">Tous types</option>
                    <option value="information">Information</option>
                    <option value="evenement">Événement</option>
                    <option value="note">Note</option>
                </select>
                <a href="{{ route('admin.evenements.create') }}" class="{{ $btnPrimaire }} whitespace-nowrap">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>Nouvelle publication
                </a>
            </div>
        </div>

        <!-- Publications -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="evtGrid">
            @forelse($evenements as $evt)
                @php [$libType, $clsType] = $tc[$evt->type] ?? [ucfirst((string) $evt->type), 'bg-slate-100 text-slate-700']; @endphp
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
                            <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsType }}">{{ $libType }}</span><span hidden>{{ $evt->type }}</span>
                            <span class="text-xs text-slate-500 text-right">{{ $evt->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="mt-3 text-base font-semibold text-slate-900 leading-snug break-words">{{ $evt->titre }}</h3>
                        @if ($evt->contenu)
                            <p class="mt-1.5 text-sm text-slate-500 line-clamp-2 break-words">{{ Str::limit(strip_tags($evt->contenu), 100) }}</p>
                        @endif
                        @if ($evt->date_evenement)
                            <p class="mt-3 text-xs text-slate-700 tabular-nums">
                                <span class="text-slate-500">Date :</span>
                                <span class="font-medium">{{ $evt->date_evenement->format('d/m/Y à H:i') }}</span>
                            </p>
                        @endif
                        <div class="mt-auto pt-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                                <span class="text-xs text-slate-500">Destinataires :</span>
                                @if ($evt->partage_tous)
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-accent-50 text-accent-700">Tous</span>
                                @else
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 tabular-nums">{{ $evt->utilisateurssCibles->count() . ' destinataire(s)' }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 pt-3">
                            <a href="{{ route('admin.evenements.show', $evt) }}"
                                class="{{ $btnPetitSecondaire }} whitespace-nowrap">Voir</a>
                            <div class="flex items-center gap-1.5 ml-auto">
                                <a href="{{ route('admin.evenements.edit', $evt) }}" class="{{ $btnIcone }}"
                                    aria-label="Modifier {{ $evt->titre }}" title="Modifier">
                                    <i class="fas fa-pen text-xs" aria-hidden="true"></i>
                                </a>
                                <form action="{{ route('admin.evenements.destroy', $evt) }}" method="POST"
                                    id="del-evt-{{ $evt->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                        data-form="del-evt-{{ $evt->id }}"
                                        aria-label="Supprimer {{ $evt->titre }}" title="Supprimer">
                                        <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="md:col-span-2 xl:col-span-3 rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                    <p class="text-sm font-medium text-slate-900">Aucune publication</p>
                    <p class="mt-1 text-sm text-slate-600 max-w-md mx-auto">
                        Publiez une information, un événement ou une note pour la partager avec les utilisateurs.
                    </p>
                    <a href="{{ route('admin.evenements.create') }}"
                        class="mt-3 inline-block text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded {{ $focus }}">Nouvelle
                        publication</a>
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
        function filterEvt() {
            const s = document.getElementById('search').value.toLowerCase();
            const t = document.getElementById('filterType').value;
            let visibles = 0;
            document.querySelectorAll('.evt-card').forEach(card => {
                const visible = card.textContent.toLowerCase().includes(s) && (!t || card.dataset.type === t);
                card.style.display = visible ? '' : 'none';
                if (visible) visibles++;
            });
            const aucun = document.getElementById('evtAucun');
            if (aucun) aucun.classList.toggle('hidden', visibles > 0 || !document.querySelector('.evt-card'));
        }
        document.getElementById('search').addEventListener('input', filterEvt);
        document.getElementById('filterType').addEventListener('change', filterEvt);
    </script>
@endpush
