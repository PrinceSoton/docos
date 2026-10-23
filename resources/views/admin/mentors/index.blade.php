@extends('layouts.admin')
@section('titre', 'Mentors')
@section('breadcrumb', 'Gestion > Mentors')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors whitespace-nowrap ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors whitespace-nowrap ' . $focus;
        $btnPetitSecondaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors whitespace-nowrap ' . $focus;
        $btnIconeDanger = 'inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-red-200 bg-white text-red-700 hover:bg-red-50 transition-colors ' . $focus;
        $puce = 'inline-flex max-w-full items-center gap-2 rounded-md border border-brand-100 bg-white py-1 pl-2.5 pr-1';
        $btnRetirer = 'inline-flex flex-shrink-0 items-center justify-center rounded-md text-slate-500 hover:bg-red-50 hover:text-red-700 transition-colors ' . $focus;
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Barre d'outils : synthèse + recherche + actions -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <p class="text-sm text-slate-600 tabular-nums">
                <span class="font-semibold text-slate-900">{{ $mentors->total() }}</span> mentor(s) enregistré(s)
            </p>
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <input type="search" id="search" placeholder="Nom, département, poste..."
                    aria-label="Rechercher un mentor" class="sm:w-72 {{ $champ }}">
                <div class="grid grid-cols-2 sm:flex gap-3">
                    <a href="{{ route('admin.mentors.assign') }}" class="{{ $btnSecondaire }}">
                        <i class="fas fa-link text-xs" aria-hidden="true"></i>Affecter
                    </a>
                    <a href="{{ route('admin.mentors.create') }}" class="{{ $btnPrimaire }}">
                        <i class="fas fa-plus text-xs" aria-hidden="true"></i>Nouveau
                    </a>
                </div>
            </div>
        </div>

        @if ($mentors->count() > 0)
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden" aria-label="Liste des mentors">

                <!-- Tableau (écrans larges) -->
                <div class="hidden xl:block">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col>
                            <col class="w-[24%]">
                            <col class="w-[15%]">
                            <col class="w-[15%]">
                            <col class="w-28">
                            <col class="w-36">
                        </colgroup>
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr class="text-left text-xs font-medium text-slate-600">
                                <th scope="col" class="px-5 py-2.5">Mentor</th>
                                <th scope="col" class="px-3 py-2.5">Email</th>
                                <th scope="col" class="px-3 py-2.5">Département</th>
                                <th scope="col" class="px-3 py-2.5">Poste</th>
                                <th scope="col" class="px-3 py-2.5 text-center">Stagiaires</th>
                                <th scope="col" class="px-4 py-2.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="mentorTable">
                            @foreach ($mentors as $mentor)
                                <tr class="hover:bg-brand-50/60 mentor-row">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="h-9 w-9 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                @if ($mentor->user->photo)
                                                    <img src="{{ asset('storage/' . $mentor->user->photo) }}" alt=""
                                                        class="h-full w-full object-cover" loading="lazy">
                                                @else
                                                    <span class="text-sm font-semibold text-brand-800"
                                                        aria-hidden="true">{{ strtoupper(substr($mentor->user->prenom, 0, 1)) }}</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-medium text-slate-900 truncate" title="{{ $mentor->user->nom_complet }}">
                                                    {{ $mentor->user->nom_complet }}</p>
                                                <p class="text-xs text-slate-500 tabular-nums truncate">{{ $mentor->user->telephone ?: '—' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-700">
                                        <p class="truncate" title="{{ $mentor->user->email }}">{{ $mentor->user->email }}</p>
                                    </td>
                                    <td class="px-3 py-3 text-slate-700">
                                        <p class="truncate" title="{{ $mentor->departement }}">{{ $mentor->departement ?: '—' }}</p>
                                    </td>
                                    <td class="px-3 py-3 text-slate-700">
                                        <p class="truncate" title="{{ $mentor->poste }}">{{ $mentor->poste ?: '—' }}</p>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span
                                            class="inline-block min-w-[2rem] text-xs font-semibold px-2 py-0.5 rounded-md tabular-nums {{ $mentor->stagiaires->count() > 0 ? 'bg-accent-50 text-accent-700' : 'bg-slate-100 text-slate-700' }}">{{ $mentor->stagiaires->count() }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.mentors.edit', $mentor) }}" class="{{ $btnPetitSecondaire }}"
                                                aria-label="Modifier {{ $mentor->user->nom_complet }}">
                                                <i class="fas fa-pen text-[10px]" aria-hidden="true"></i>Modifier
                                            </a>
                                            <form action="{{ route('admin.mentors.destroy', $mentor) }}" method="POST"
                                                id="del-mentor-{{ $mentor->id }}">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                                    data-form="del-mentor-{{ $mentor->id }}" title="Supprimer"
                                                    aria-label="Supprimer {{ $mentor->user->nom_complet }}">
                                                    <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @if ($mentor->stagiaires->count() > 0)
                                    <tr class="mentor-stagiaires !border-t-0">
                                        <td colspan="6" class="px-5 pb-3 pt-0">
                                            <div class="flex flex-wrap items-center gap-2 pl-12">
                                                <span class="text-xs font-medium text-slate-600 mr-1">Stagiaires :</span>
                                                @foreach ($mentor->stagiaires as $stag)
                                                    <div class="{{ $puce }}">
                                                        <span class="text-sm text-slate-900 font-medium truncate">{{ $stag->user->nom_complet }}</span>
                                                        <span class="text-xs text-slate-500 font-mono">{{ $stag->matricule }}</span>
                                                        <form action="{{ route('admin.mentors.removeAssign', $stag) }}"
                                                            method="POST" class="no-loader">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="{{ $btnRetirer }} h-7 w-7"
                                                                title="Retirer l'affectation"
                                                                aria-label="Retirer l'affectation de {{ $stag->user->nom_complet }}">
                                                                <i class="fas fa-times text-xs" aria-hidden="true"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Liste (téléphone et tablette) -->
                <ul class="xl:hidden divide-y divide-slate-100">
                    @foreach ($mentors as $mentor)
                        <li class="px-4 sm:px-5 py-4 mentor-row">
                            <div class="flex items-start gap-3">
                                <div
                                    class="h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if ($mentor->user->photo)
                                        <img src="{{ asset('storage/' . $mentor->user->photo) }}" alt=""
                                            class="h-full w-full object-cover" loading="lazy">
                                    @else
                                        <span class="text-sm font-semibold text-brand-800"
                                            aria-hidden="true">{{ strtoupper(substr($mentor->user->prenom, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="mentor-infos">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-slate-900 truncate" title="{{ $mentor->user->nom_complet }}">
                                                    {{ $mentor->user->nom_complet }}</p>
                                                <p class="text-sm text-slate-600 truncate" title="{{ $mentor->user->email }}">
                                                    {{ $mentor->user->email }}</p>
                                            </div>
                                            <span
                                                class="flex-shrink-0 text-xs font-semibold px-2 py-0.5 rounded-md tabular-nums whitespace-nowrap {{ $mentor->stagiaires->count() > 0 ? 'bg-accent-50 text-accent-700' : 'bg-slate-100 text-slate-700' }}">{{ $mentor->stagiaires->count() }}
                                                stagiaire(s)</span>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500 break-words">
                                            <span class="tabular-nums">{{ $mentor->user->telephone ?: '—' }}</span> ·
                                            {{ $mentor->departement ?: '—' }} · {{ $mentor->poste ?: '—' }}</p>
                                    </div>

                                    @if ($mentor->stagiaires->count() > 0)
                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                            @foreach ($mentor->stagiaires as $stag)
                                                <div class="{{ $puce }}">
                                                    <span class="text-sm text-slate-900 font-medium truncate">{{ $stag->user->nom_complet }}</span>
                                                    <span class="text-xs text-slate-500 font-mono">{{ $stag->matricule }}</span>
                                                    <form action="{{ route('admin.mentors.removeAssign', $stag) }}" method="POST"
                                                        class="no-loader">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="{{ $btnRetirer }} h-8 w-8"
                                                            title="Retirer l'affectation"
                                                            aria-label="Retirer l'affectation de {{ $stag->user->nom_complet }}">
                                                            <i class="fas fa-times text-xs" aria-hidden="true"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="mt-3 flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.mentors.edit', $mentor) }}" class="{{ $btnPetitSecondaire }}"
                                            aria-label="Modifier {{ $mentor->user->nom_complet }}">
                                            <i class="fas fa-pen text-[10px]" aria-hidden="true"></i>Modifier
                                        </a>
                                        <form action="{{ route('admin.mentors.destroy', $mentor) }}" method="POST"
                                            id="del-mentor-{{ $mentor->id }}-carte">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                                data-form="del-mentor-{{ $mentor->id }}-carte" title="Supprimer"
                                                aria-label="Supprimer {{ $mentor->user->nom_complet }}">
                                                <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <p id="mentorNoResult" class="hidden px-5 sm:px-6 py-6 text-center text-sm text-slate-500">Aucun résultat
                    pour cette recherche.</p>
            </section>
        @else
            <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                <p class="text-sm font-medium text-slate-900">Aucun mentor enregistré</p>
                <p class="mt-1 text-sm text-slate-600">Ajoutez un mentor pour pouvoir lui affecter des stagiaires.</p>
                <a href="{{ route('admin.mentors.create') }}"
                    class="mt-3 inline-block text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded {{ $focus }}">Nouveau
                    mentor</a>
            </div>
        @endif

        <div>{{ $mentors->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.mentor-row').forEach(row => {
                // Même critère que la liste d'origine : informations du mentor (nom, téléphone, email, département, poste)
                const cible = row.querySelector('.mentor-infos') || row;
                const affiche = cible.textContent.toLowerCase().includes(val);
                row.style.display = affiche ? '' : 'none';
                // Dans le tableau, la ligne des stagiaires affectés suit la ligne du mentor
                const suivante = row.nextElementSibling;
                if (suivante && suivante.classList.contains('mentor-stagiaires')) {
                    suivante.style.display = affiche ? '' : 'none';
                }
            });
            const lignes = document.querySelectorAll('#mentorTable .mentor-row');
            const visibles = Array.from(lignes).filter(r => r.style.display !== 'none').length;
            document.getElementById('mentorNoResult')?.classList.toggle('hidden', lignes.length === 0 || visibles > 0);
        });
    </script>
@endpush
