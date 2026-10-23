@extends('layouts.admin')
@section('titre', 'Utilisateurs')
@section('breadcrumb', 'Gestion > Utilisateurs')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors whitespace-nowrap ' . $focus;
        $btnPetitSecondaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors whitespace-nowrap ' . $focus;
        $btnIcone = 'inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-800 hover:border-brand-200 transition-colors ' . $focus;
        $btnIconeDanger = 'inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-red-200 bg-white text-red-700 hover:bg-red-50 transition-colors ' . $focus;
        // Rôle : information d'identité, pas un statut -> teintes de la marque
        $clsRole = fn ($role) => match ($role) {
            'admin' => 'bg-brand-800 text-white',
            'mentor' => 'bg-brand-100 text-brand-800',
            default => 'bg-slate-100 text-slate-700',
        };
        $clsActif = fn ($actif) => $actif
            ? 'border-accent-100 bg-accent-50 text-accent-700 hover:bg-accent-100'
            : 'border-red-200 bg-red-50 text-red-800 hover:bg-red-100';
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Barre d'outils : synthèse + recherche + action principale -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <p class="text-sm text-slate-600 tabular-nums">
                <span class="font-semibold text-slate-900">{{ $users->total() }}</span> utilisateur(s) au total
            </p>
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <input type="search" id="search" placeholder="Rechercher par nom, email, rôle..."
                    aria-label="Rechercher un utilisateur" class="sm:w-72 {{ $champ }}">
                <a href="{{ route('admin.users.create') }}" class="{{ $btnPrimaire }}">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>Nouvel utilisateur
                </a>
            </div>
        </div>

        @if ($users->count() > 0)
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden" aria-label="Liste des utilisateurs">

                <!-- Tableau (écrans larges) -->
                <div class="hidden xl:block">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col>
                            <col class="w-[26%]">
                            <col class="w-28">
                            <col class="w-28">
                            <col class="w-28">
                            <col class="w-40">
                        </colgroup>
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr class="text-left text-xs font-medium text-slate-600">
                                <th scope="col" class="px-5 py-2.5">Utilisateur</th>
                                <th scope="col" class="px-3 py-2.5">Email</th>
                                <th scope="col" class="px-3 py-2.5">Rôle</th>
                                <th scope="col" class="px-3 py-2.5">Statut</th>
                                <th scope="col" class="px-3 py-2.5">Créé le</th>
                                <th scope="col" class="px-4 py-2.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="userTable">
                            @foreach ($users as $user)
                                <tr class="hover:bg-brand-50/60 user-row">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div
                                                class="h-9 w-9 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                @if ($user->photo)
                                                    <img src="{{ asset('storage/' . $user->photo) }}" alt=""
                                                        class="h-full w-full object-cover" loading="lazy">
                                                @else
                                                    <span class="text-sm font-semibold text-brand-800"
                                                        aria-hidden="true">{{ strtoupper(substr($user->prenom, 0, 1)) }}</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-medium text-slate-900 truncate" title="{{ $user->nom_complet }}">
                                                    {{ $user->nom_complet }}</p>
                                                <p class="text-xs text-slate-500 tabular-nums truncate">{{ $user->telephone ?: '—' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-700">
                                        <p class="truncate" title="{{ $user->email }}">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span
                                            class="text-xs font-medium px-2 py-0.5 rounded-md whitespace-nowrap {{ $clsRole($user->role) }}">{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST"
                                            class="no-loader">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-md border px-2 py-0.5 text-xs font-medium whitespace-nowrap transition-colors {{ $clsActif($user->actif) }} {{ $focus }}"
                                                title="{{ $user->actif ? 'Désactiver ce compte' : 'Activer ce compte' }}"
                                                aria-label="{{ $user->actif ? 'Actif' : 'Inactif' }} — {{ $user->actif ? 'désactiver' : 'activer' }} le compte de {{ $user->nom_complet }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $user->actif ? 'bg-accent-500' : 'bg-red-500' }}"
                                                    aria-hidden="true"></span>
                                                {{ $user->actif ? 'Actif' : 'Inactif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-3 py-3 text-slate-600 tabular-nums whitespace-nowrap">
                                        {{ $user->created_at->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.users.show', $user) }}" class="{{ $btnPetitSecondaire }}"
                                                aria-label="Voir le profil de {{ $user->nom_complet }}">Voir</a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="{{ $btnIcone }}"
                                                title="Modifier" aria-label="Modifier {{ $user->nom_complet }}">
                                                <i class="fas fa-pen text-xs" aria-hidden="true"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                id="del-user-{{ $user->id }}">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                                    data-form="del-user-{{ $user->id }}" title="Supprimer"
                                                    aria-label="Supprimer {{ $user->nom_complet }}">
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

                <!-- Liste (téléphone et tablette) -->
                <ul class="xl:hidden divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <li class="px-4 sm:px-5 py-4 user-row">
                            <div class="flex items-start gap-3">
                                <div
                                    class="h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if ($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}" alt=""
                                            class="h-full w-full object-cover" loading="lazy">
                                    @else
                                        <span class="text-sm font-semibold text-brand-800"
                                            aria-hidden="true">{{ strtoupper(substr($user->prenom, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-slate-900 truncate" title="{{ $user->nom_complet }}">
                                                {{ $user->nom_complet }}</p>
                                            <p class="text-sm text-slate-600 truncate" title="{{ $user->email }}">{{ $user->email }}</p>
                                        </div>
                                        <span
                                            class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $clsRole($user->role) }}">{{ ucfirst($user->role) }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500 tabular-nums">
                                        {{ $user->telephone ?: '—' }} · Créé le {{ $user->created_at->format('d/m/Y') }}</p>

                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST"
                                            class="no-loader">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-md border px-2.5 py-1.5 text-xs font-medium whitespace-nowrap transition-colors {{ $clsActif($user->actif) }} {{ $focus }}"
                                                title="{{ $user->actif ? 'Désactiver ce compte' : 'Activer ce compte' }}"
                                                aria-label="{{ $user->actif ? 'Actif' : 'Inactif' }} — {{ $user->actif ? 'désactiver' : 'activer' }} le compte de {{ $user->nom_complet }}">
                                                <span class="h-1.5 w-1.5 rounded-full {{ $user->actif ? 'bg-accent-500' : 'bg-red-500' }}"
                                                    aria-hidden="true"></span>
                                                {{ $user->actif ? 'Actif' : 'Inactif' }}
                                            </button>
                                        </form>
                                        <div class="ml-auto flex items-center gap-1.5">
                                            <a href="{{ route('admin.users.show', $user) }}" class="{{ $btnPetitSecondaire }}"
                                                aria-label="Voir le profil de {{ $user->nom_complet }}">Voir</a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="{{ $btnIcone }}"
                                                title="Modifier" aria-label="Modifier {{ $user->nom_complet }}">
                                                <i class="fas fa-pen text-xs" aria-hidden="true"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                id="del-user-{{ $user->id }}-carte">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                                    data-form="del-user-{{ $user->id }}-carte" title="Supprimer"
                                                    aria-label="Supprimer {{ $user->nom_complet }}">
                                                    <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <p id="userNoResult" class="hidden px-5 sm:px-6 py-6 text-center text-sm text-slate-500">Aucun résultat pour
                    cette recherche.</p>
            </section>
        @else
            <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                <p class="text-sm font-medium text-slate-900">Aucun utilisateur trouvé</p>
                <p class="mt-1 text-sm text-slate-600">Créez un premier compte pour donner accès à la plateforme.</p>
                <a href="{{ route('admin.users.create') }}"
                    class="mt-3 inline-block text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded {{ $focus }}">Nouvel
                    utilisateur</a>
            </div>
        @endif

        <div>{{ $users->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.user-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
            // Message « aucun résultat » (le tableau sert de référence pour le comptage)
            const lignes = document.querySelectorAll('#userTable .user-row');
            const visibles = Array.from(lignes).filter(r => r.style.display !== 'none').length;
            document.getElementById('userNoResult')?.classList.toggle('hidden', lignes.length === 0 || visibles > 0);
        });
    </script>
@endpush
