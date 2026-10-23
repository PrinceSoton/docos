@extends('layouts.admin')
@section('titre', 'Archives')
@section('breadcrumb', 'Système > Archives')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnPetitSecondaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $btnIcone = 'inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900 transition-colors ' . $focus;
        $btnIconeDanger = 'inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-red-200 bg-white text-red-700 hover:bg-red-50 transition-colors ' . $focus;

        $typesArchive = [
            'stagiaire' => 'bg-brand-50 text-brand-800',
            'autre' => 'bg-slate-100 text-slate-700',
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Barre d'outils : résumé, filtres, action principale -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <p class="text-sm text-slate-600 tabular-nums">
                <span class="font-semibold text-slate-900">{{ $archives->total() }}</span> archive(s)
            </p>
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3">
                <input type="search" id="search" placeholder="Rechercher par titre, stagiaire..."
                    aria-label="Rechercher une archive" class="w-full sm:w-64 {{ $champ }}">
                <select id="filterType" aria-label="Filtrer par type" class="w-full sm:w-40 {{ $champ }}">
                    <option value="">Tous types</option>
                    <option value="stagiaire">Stagiaire</option>
                    <option value="autre">Autre</option>
                </select>
                <a href="{{ route('admin.archives.create') }}" class="{{ $btnPrimaire }} whitespace-nowrap">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>Nouvelle archive
                </a>
            </div>
        </div>

        @if ($archives->count())
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden" aria-label="Liste des archives">

                <!-- Tableau (écrans larges) -->
                <div class="hidden xl:block">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col>
                            <col class="w-28">
                            <col class="w-[18%]">
                            <col class="w-24">
                            <col class="w-[14%]">
                            <col class="w-28">
                            <col class="w-44">
                        </colgroup>
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr class="text-left text-xs font-medium text-slate-600">
                                <th scope="col" class="px-5 py-3">Titre</th>
                                <th scope="col" class="px-3 py-3">Type</th>
                                <th scope="col" class="px-3 py-3">Stagiaire</th>
                                <th scope="col" class="px-3 py-3 text-center">Fichiers</th>
                                <th scope="col" class="px-3 py-3">Créé par</th>
                                <th scope="col" class="px-3 py-3">Date</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="archiveTable">
                            @foreach ($archives as $archive)
                                <tr class="hover:bg-brand-50/60 transition-colors archive-row" data-type="{{ $archive->type }}">
                                    <td class="px-5 py-3.5 min-w-0">
                                        <p class="font-medium text-slate-900 truncate" title="{{ $archive->titre }}">{{ $archive->titre }}</p>
                                        @if ($archive->description)
                                            <p class="mt-0.5 text-xs text-slate-500 truncate" title="{{ $archive->description }}">{{ $archive->description }}</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3.5">
                                        <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-md {{ $typesArchive[$archive->type] ?? 'bg-slate-100 text-slate-700' }}">{{ ucfirst($archive->type) }}</span>
                                    </td>
                                    <td class="px-3 py-3.5 min-w-0">
                                        <p class="text-slate-700 truncate" title="{{ $archive->stagiaire?->user?->nom_complet ?? '—' }}">{{ $archive->stagiaire?->user?->nom_complet ?? '—' }}</p>
                                        @if ($archive->stagiaire)
                                            <p class="mt-0.5 text-xs text-slate-500 tabular-nums truncate">{{ $archive->stagiaire->matricule }}</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3.5 text-center">
                                        <span class="inline-block min-w-[2rem] text-xs font-medium px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 tabular-nums">{{ $archive->fichiers->count() }}</span>
                                    </td>
                                    <td class="px-3 py-3.5 min-w-0">
                                        <p class="text-slate-700 truncate" title="{{ $archive->creePar?->nom_complet ?? '—' }}">{{ $archive->creePar?->nom_complet ?? '—' }}</p>
                                    </td>
                                    <td class="px-3 py-3.5 text-slate-600 tabular-nums whitespace-nowrap">
                                        {{ $archive->created_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.archives.show', $archive) }}"
                                                class="{{ $btnPetitSecondaire }} whitespace-nowrap">Voir</a>
                                            <a href="{{ route('admin.archives.edit', $archive) }}" class="{{ $btnIcone }}"
                                                aria-label="Modifier {{ $archive->titre }}" title="Modifier">
                                                <i class="fas fa-pen text-xs" aria-hidden="true"></i>
                                            </a>
                                            <form action="{{ route('admin.archives.destroy', $archive) }}" method="POST"
                                                id="del-arch-{{ $archive->id }}">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                                    data-form="del-arch-{{ $archive->id }}"
                                                    aria-label="Supprimer {{ $archive->titre }}" title="Supprimer">
                                                    <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Liste empilée (téléphone, tablette, petit écran) -->
                <ul class="xl:hidden divide-y divide-slate-100">
                    @foreach ($archives as $archive)
                        <li class="archive-row p-4 sm:p-5" data-type="{{ $archive->type }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-medium text-slate-900 break-words">{{ $archive->titre }}</p>
                                    @if ($archive->description)
                                        <p class="mt-0.5 text-xs text-slate-500 line-clamp-2 break-words">{{ $archive->description }}</p>
                                    @endif
                                </div>
                                <span class="flex-shrink-0 text-xs font-medium px-2 py-0.5 rounded-md {{ $typesArchive[$archive->type] ?? 'bg-slate-100 text-slate-700' }}">{{ ucfirst($archive->type) }}</span>
                            </div>
                            <dl class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-2 text-sm">
                                <div class="min-w-0">
                                    <dt class="text-xs text-slate-500">Stagiaire</dt>
                                    <dd class="text-slate-900 truncate">{{ $archive->stagiaire?->user?->nom_complet ?? '—' }}
                                        @if ($archive->stagiaire)
                                            <span class="block text-xs text-slate-500 tabular-nums">{{ $archive->stagiaire->matricule }}</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Fichiers</dt>
                                    <dd class="text-slate-900 tabular-nums">{{ $archive->fichiers->count() }}</dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="text-xs text-slate-500">Créé par</dt>
                                    <dd class="text-slate-900 truncate">{{ $archive->creePar?->nom_complet ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-slate-500">Date</dt>
                                    <dd class="text-slate-900 tabular-nums">{{ $archive->created_at->format('d/m/Y') }}</dd>
                                </div>
                            </dl>
                            <div class="mt-3 flex items-center gap-1.5">
                                <a href="{{ route('admin.archives.show', $archive) }}"
                                    class="{{ $btnPetitSecondaire }} whitespace-nowrap">Voir</a>
                                <a href="{{ route('admin.archives.edit', $archive) }}" class="{{ $btnIcone }}"
                                    aria-label="Modifier {{ $archive->titre }}" title="Modifier">
                                    <i class="fas fa-pen text-xs" aria-hidden="true"></i>
                                </a>
                                <form action="{{ route('admin.archives.destroy', $archive) }}" method="POST"
                                    id="del-arch-{{ $archive->id }}-carte">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                        data-form="del-arch-{{ $archive->id }}-carte"
                                        aria-label="Supprimer {{ $archive->titre }}" title="Supprimer">
                                        <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <p id="archAucun" data-sans-revelation class="hidden border-t border-slate-100 px-6 py-8 text-center text-sm text-slate-600">
                    Aucun résultat pour cette recherche.
                </p>
            </section>
        @else
            <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                <img src="{{ asset('images/illustrations/documents-empty.svg') }}" alt="" class="mx-auto w-32 h-auto mb-4">
                <p class="text-sm font-medium text-slate-900">Aucune archive</p>
                <p class="mt-1 text-sm text-slate-600 max-w-md mx-auto">
                    Archivez les documents d'un stagiaire ou des documents généraux pour les retrouver ici.
                </p>
                <a href="{{ route('admin.archives.create') }}"
                    class="mt-3 inline-block text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded {{ $focus }}">Créer
                    une archive</a>
            </div>
        @endif

        <div>{{ $archives->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        function filterArch() {
            const s = document.getElementById('search').value.toLowerCase();
            const t = document.getElementById('filterType').value;
            let visibles = 0;
            document.querySelectorAll('.archive-row').forEach(row => {
                const visible = row.textContent.toLowerCase().includes(s) && (!t || row.dataset.type === t);
                row.style.display = visible ? '' : 'none';
                if (visible) visibles++;
            });
            const aucun = document.getElementById('archAucun');
            if (aucun) aucun.classList.toggle('hidden', visibles > 0);
        }
        document.getElementById('search').addEventListener('input', filterArch);
        document.getElementById('filterType').addEventListener('change', filterArch);
    </script>
@endpush
