@extends('layouts.mentor')
@section('titre', 'Présences & Permissions')
@section('breadcrumb', 'Mentor > Présences')
@section('content')
    <div class="space-y-5">
        <div data-aos="fade-down">
            <h2 class="text-slate-800 font-bold text-2xl">Présences & Permissions</h2>
            <p class="text-slate-500 text-sm">Suivi des présences et validation des permissions</p>
        </div>

        <!-- Permissions en attente -->
        @if ($permissionsEnAttente->count() > 0)
            <div class="card p-6 border-l-4 border-amber-400" data-aos="fade-down">
                <h3 class="text-slate-800 font-bold text-lg mb-4">
                    <i class="fas fa-bell text-amber-500 mr-2"></i>Permissions en attente de validation
                    ({{ $permissionsEnAttente->count() }})
                </h3>
                <div class="space-y-3">
                    @foreach ($permissionsEnAttente as $perm)
                        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
                            <div class="flex items-start justify-between gap-4 flex-wrap">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-amber-200 flex items-center justify-center flex-shrink-0">
                                        <span
                                            class="font-bold text-amber-700">{{ strtoupper(substr($perm->stagiaire->user->prenom ?? '', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $perm->stagiaire->user->nom_complet ?? '—' }}
                                        </p>
                                        <p class="text-slate-500 text-sm">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $perm->date_debut->format('d/m/Y') }} →
                                            {{ $perm->date_fin->format('d/m/Y') }}
                                            @php $jours = $perm->date_debut->diffInDays($perm->date_fin)+1; @endphp
                                            ({{ $jours }} jour(s))
                                        </p>
                                        <p class="text-slate-600 text-sm mt-1"><i
                                                class="fas fa-comment mr-1"></i>{{ $perm->motif }}</p>
                                        @if ($perm->justificatif)
                                            <a href="{{ asset('storage/' . $perm->justificatif) }}" download
                                                class="text-indigo-600 text-xs hover:underline font-medium mt-1 inline-flex items-center gap-1">
                                                <i class="fas fa-paperclip"></i>Justificatif joint
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form action="{{ route('mentor.presences.validerPermission', $perm) }}" method="POST"
                                        class="no-loader">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="valide">
                                        <button type="submit"
                                            class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition hover:shadow-md">
                                            <i class="fas fa-check"></i>Valider
                                        </button>
                                    </form>
                                    <button type="button" onclick="ouvrirRefus({{ $perm->id }})"
                                        class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition hover:shadow-md">
                                        <i class="fas fa-times"></i>Refuser
                                    </button>
                                    <form action="{{ route('mentor.presences.validerPermission', $perm) }}" method="POST"
                                        id="refusForm-{{ $perm->id }}" class="hidden no-loader">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="refuse">
                                        <input type="hidden" name="commentaire_mentor"
                                            id="commentaireRefus-{{ $perm->id }}">
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Filtre stagiaire -->
        <div class="card p-5" data-aos="fade-up">
            <form action="{{ route('mentor.presences.index') }}" method="GET"
                class="flex flex-wrap gap-3 items-end no-loader">
                <div class="flex-1 min-w-48">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Stagiaire</label>
                    <select name="stagiaire_id"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 text-sm">
                        <option value="">Tous mes stagiaires</option>
                        @foreach ($stagiaires as $stag)
                            <option value="{{ $stag->id }}"
                                {{ request('stagiaire_id') == $stag->id ? 'selected' : '' }}>
                                {{ $stag->user->nom_complet }} — {{ $stag->matricule }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-emerald-700 transition hover:shadow-md">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
                @if (request('stagiaire_id'))
                    <a href="{{ route('mentor.presences.show', request('stagiaire_id')) }}"
                        class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-indigo-700 transition hover:shadow-md">
                        <i class="fas fa-eye mr-2"></i>Fiche complète
                    </a>
                @endif
            </form>
        </div>

        <!-- Tableau présences -->
        @if ($presences->count() > 0)
            <div class="card overflow-hidden" data-aos="fade-up">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
                    <h3 class="font-bold text-slate-800">
                        @if ($stagiaire)
                            Présences de {{ $stagiaire->user->nom_complet }}
                        @else
                            Toutes les présences
                        @endif
                    </h3>
                    <input type="text" id="search" placeholder="Rechercher..."
                        class="border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-emerald-400 transition">
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold text-slate-600">Date</th>
                                @if (!$stagiaire)
                                    <th class="px-5 py-3 text-left font-semibold text-slate-600">Stagiaire</th>
                                @endif
                                <th class="px-5 py-3 text-left font-semibold text-slate-600">Statut</th>
                                <th class="px-5 py-3 text-left font-semibold text-slate-600">Heure</th>
                                <th class="px-5 py-3 text-left font-semibold text-slate-600">Motif</th>
                                <th class="px-5 py-3 text-center font-semibold text-slate-600">Justificatif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50" id="presTable">
                            @foreach ($presences as $p)
                                <tr class="hover:bg-slate-50 transition pres-row">
                                    <td class="px-5 py-3 font-medium text-slate-800">
                                        {{ $p->date->translatedFormat('D d/m/Y') }}</td>
                                    @if (!$stagiaire)
                                        <td class="px-5 py-3 text-slate-600">{{ $p->stagiaire->user->nom_complet ?? '—' }}
                                        </td>
                                    @endif
                                    <td class="px-5 py-3">
                                        @php $sc = ['present'=>'bg-green-100 text-green-700','retard'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-600']; @endphp
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$p->statut] ?? '' }}">{{ ucfirst($p->statut) }}</span>
                                    </td>
                                    <td class="px-5 py-3 font-mono text-slate-600">
                                        {{ $p->heure_arrivee ? substr($p->heure_arrivee, 0, 5) : '—' }}</td>
                                    <td class="px-5 py-3 text-slate-500 text-xs max-w-xs truncate">{{ $p->motif ?: '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        @if ($p->justificatif)
                                            <a href="{{ asset('storage/' . $p->justificatif) }}" download
                                                class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-2 py-1 rounded-lg text-xs font-medium transition">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else<span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-slate-100">{{ $presences->links() }}</div>
            </div>
        @else
            <div class="card p-12 text-center" data-aos="fade-up">
                <i class="fas fa-clipboard-list text-5xl text-slate-200 mb-4"></i>
                <p class="text-slate-400">Sélectionnez un stagiaire pour voir ses présences</p>
            </div>
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('search')?.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.pres-row').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });

        function ouvrirRefus(id) {
            Swal.fire({
                title: 'Motif du refus',
                input: 'textarea',
                inputPlaceholder: 'Expliquez le motif du refus (optionnel)...',
                showCancelButton: true,
                confirmButtonText: 'Confirmer le refus',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#ef4444',
                inputAttributes: {
                    rows: 3
                }
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('commentaireRefus-' + id).value = result.value || '';
                    document.getElementById('refusForm-' + id).submit();
                }
            });
        }
    </script>
@endpush
