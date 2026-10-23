@extends('layouts.stagiaire')
@section('titre', 'Mon Projet')
@section('breadcrumb', 'Mon espace > Projet')
@section('content')
    @php
        $statutsProjet = [
            'en_attente' => ['En attente', 'bg-slate-100 text-slate-700'],
            'en_cours' => ['En cours', 'bg-sky-50 text-sky-800'],
            'termine' => ['Terminé', 'bg-emerald-50 text-emerald-800'],
            'suspendu' => ['Suspendu', 'bg-red-50 text-red-800'],
        ];
        $priorites = [
            'faible' => ['Faible', 'bg-slate-400'],
            'normale' => ['Normale', 'bg-sky-500'],
            'haute' => ['Haute', 'bg-amber-500'],
            'urgente' => ['Urgente', 'bg-red-500'],
        ];

        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Résumé et recherche -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <p class="text-sm text-slate-600 tabular-nums">
                <span class="font-semibold text-slate-900">{{ $projets->total() }}</span>
                {{ $projets->total() > 1 ? 'projets assignés' : 'projet assigné' }}
            </p>
            @if ($projets->count() > 0)
                <input type="search" id="search" placeholder="Rechercher un projet..." aria-label="Rechercher un projet"
                    class="w-full sm:w-72 {{ $champ }}">
            @endif
        </div>

        <div class="space-y-4" id="projetGrid">
            @forelse($projets as $projet)
                @php
                    [$libStatut, $clsStatut] = $statutsProjet[$projet->statut] ?? [ucfirst(str_replace('_', ' ', $projet->statut)), 'bg-slate-100 text-slate-700'];
                    [$libPriorite, $dotPriorite] = $priorites[$projet->priorite] ?? [ucfirst((string) $projet->priorite), 'bg-slate-300'];
                    $pct = $projet->progressionPourcent();
                @endphp
                <article class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6 projet-card">
                    <!-- Titre et statut -->
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="text-base font-semibold text-slate-900 leading-snug min-w-0 break-words">{{ $projet->titre }}</h2>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsStatut }}">
                            {{ $libStatut }}
                        </span>
                    </div>
                    @if ($projet->description)
                        <p class="mt-1.5 text-sm text-slate-500 line-clamp-2 max-w-3xl">{{ $projet->description }}</p>
                    @endif

                    <!-- Mentor, période, priorité, progression -->
                    <dl class="mt-5 grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4 text-sm rounded-lg bg-brand-50 border border-brand-100 p-4">
                        <div class="order-1 lg:order-none">
                            <dt class="text-xs text-slate-600">Mentor</dt>
                            <dd class="mt-0.5 font-medium text-slate-900">{{ $projet->mentor->nom_complet ?? '—' }}</dd>
                        </div>
                        <div class="order-3 col-span-2 lg:order-none lg:col-span-1">
                            <dt class="text-xs text-slate-600">Période</dt>
                            <dd class="mt-0.5 font-medium text-slate-900 tabular-nums">
                                {{ $projet->date_debut->format('d/m/Y') }}
                                @if ($projet->date_fin)
                                    → {{ $projet->date_fin->format('d/m/Y') }}
                                @endif
                            </dd>
                        </div>
                        <div class="order-2 lg:order-none">
                            <dt class="text-xs text-slate-600">Priorité</dt>
                            <dd class="mt-0.5 flex items-center gap-2 font-medium text-slate-900">
                                <span class="h-2 w-2 rounded-full {{ $dotPriorite }}" aria-hidden="true"></span>
                                {{ $libPriorite }}
                            </dd>
                        </div>
                        <div class="order-4 col-span-2 lg:order-none lg:col-span-1">
                            <dt class="flex justify-between text-xs text-slate-600">
                                <span>Progression</span>
                                <span class="font-semibold text-slate-900 tabular-nums">{{ $pct }} %</span>
                            </dt>
                            <dd class="mt-2">
                                <div class="h-2 rounded-full bg-brand-100 overflow-hidden" role="progressbar"
                                    aria-label="Progression du projet {{ $projet->titre }}" aria-valuemin="0"
                                    aria-valuemax="100" aria-valuenow="{{ $pct }}">
                                    <div class="h-2 rounded-full bg-accent-500" style="width:{{ $pct }}%"></div>
                                </div>
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('stagiaire.projects.show', $projet) }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-900 hover:bg-slate-50 hover:border-slate-300 transition-colors {{ $focus }}">
                            Voir le détail<span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                    <p class="text-sm font-medium text-slate-900">Aucun projet assigné pour le moment</p>
                    <p class="mt-1 text-sm text-slate-600">Dès que votre mentor vous associe à un projet, il apparaîtra
                        ici avec sa progression.</p>
                </div>
            @endforelse
        </div>

        <p id="projetNoResult" class="hidden rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center text-sm text-slate-600">
            Aucun projet ne correspond à votre recherche.</p>

        <div>{{ $projets->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('search')?.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            const cartes = document.querySelectorAll('.projet-card');
            let visibles = 0;
            cartes.forEach(c => {
                const ok = c.textContent.toLowerCase().includes(val);
                c.style.display = ok ? '' : 'none';
                if (ok) visibles++;
            });
            document.getElementById('projetNoResult')?.classList.toggle('hidden', cartes.length === 0 || visibles > 0);
        });
    </script>
@endpush
