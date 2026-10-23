@extends('layouts.admin')
@section('titre', 'Présences - ' . $stagiaire->user->nom_complet)
@section('breadcrumb', 'Présences > Fiche complète')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $lienFichier = 'inline-flex items-center gap-1.5 text-xs font-medium text-brand-700 hover:underline underline-offset-4 rounded whitespace-nowrap ' . $focus;
        $focusMarine = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-800';
        $panneau = 'bg-white border border-brand-100 rounded-xl overflow-hidden';
        $enTete = 'px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100';

        $statutsPresence = [
            'present' => ['Présent', 'bg-emerald-50 text-emerald-800'],
            'retard' => ['Retard', 'bg-amber-50 text-amber-800'],
            'absent' => ['Absent', 'bg-red-50 text-red-800'],
        ];
        $statutsPermission = [
            'en_attente' => ['En attente', 'bg-amber-50 text-amber-800'],
            'valide' => ['Validée', 'bg-emerald-50 text-emerald-800'],
            'refuse' => ['Refusée', 'bg-red-50 text-red-800'],
        ];
    @endphp

    <div class="max-w-5xl mx-auto space-y-5">

        <!-- Identité, stage et synthèse (bandeau marine) -->
        <section class="rounded-xl bg-brand-800 text-white p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5">
                <div
                    class="h-16 w-16 rounded-full bg-white/15 flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if ($stagiaire->user->photo)
                        <img src="{{ asset('storage/' . $stagiaire->user->photo) }}" alt=""
                            class="h-full w-full object-cover">
                    @else
                        <span class="text-2xl font-semibold text-white"
                            aria-hidden="true">{{ strtoupper(substr($stagiaire->user->prenom, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-semibold text-white tracking-tight break-words">
                        {{ $stagiaire->user->nom_complet }}</h2>
                    <p class="mt-1 text-sm text-brand-100 break-words">
                        <span class="font-mono">{{ $stagiaire->matricule }}</span> · {{ $stagiaire->ecole ?: '—' }}</p>
                    <p class="mt-0.5 text-sm text-brand-100 tabular-nums">Stage : {{ $stagiaire->date_debut->format('d/m/Y') }}
                        → {{ $stagiaire->date_fin->format('d/m/Y') }} ({{ $stagiaire->dureeStageDays() }} jours)</p>
                </div>
                <div class="grid grid-cols-1 sm:flex sm:flex-wrap gap-2 flex-shrink-0">
                    <a href="{{ route('admin.presences.formMarquage', ['date' => now()->toDateString()]) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-brand-800 hover:bg-brand-50 transition-colors whitespace-nowrap {{ $focusMarine }}">
                        <i class="fas fa-user-check text-xs" aria-hidden="true"></i>Marquer
                    </a>
                    <a href="{{ route('admin.presences.index', ['stagiaire_id' => $stagiaire->id]) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/30 px-4 py-2.5 text-sm font-medium text-white hover:bg-white/10 transition-colors whitespace-nowrap {{ $focusMarine }}">
                        <i class="fas fa-filter text-xs text-brand-100" aria-hidden="true"></i>Filtrer par mois
                    </a>
                </div>
            </div>

            <dl class="mt-6 pt-5 border-t border-white/15 grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4">
                @foreach ([[$stats['present'], 'Présent'], [$stats['retard'], 'Retard'], [$stats['absent'], 'Absent'], [$permissions->count(), 'Permissions']] as [$val, $label])
                    <div>
                        <dd class="text-2xl font-semibold text-white tabular-nums">{{ $val }}</dd>
                        <dt class="mt-0.5 text-xs text-brand-100">{{ $label }}</dt>
                    </div>
                @endforeach
            </dl>
        </section>

        <!-- Permissions -->
        @if ($permissions->count() > 0)
            <section class="{{ $panneau }}">
                <div class="{{ $enTete }}">
                    <h3 class="text-base font-semibold text-slate-900">Permissions</h3>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach ($permissions as $perm)
                        @php [$libelle, $cls] = $statutsPermission[$perm->statut] ?? [ucfirst($perm->statut), 'bg-slate-100 text-slate-700']; @endphp
                        <li class="px-5 sm:px-6 py-4 flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-medium text-slate-900 tabular-nums">
                                        {{ $perm->date_debut->format('d/m/Y') }} → {{ $perm->date_fin->format('d/m/Y') }}</p>
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-md {{ $cls }}">{{ $libelle }}</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-600 break-words">{{ $perm->motif }}</p>
                            </div>
                            @if ($perm->justificatif)
                                <a href="{{ asset('storage/' . $perm->justificatif) }}" download
                                    class="flex-shrink-0 {{ $lienFichier }}"
                                    aria-label="Télécharger le justificatif de la permission du {{ $perm->date_debut->format('d/m/Y') }}">
                                    <i class="fas fa-download" aria-hidden="true"></i>Justificatif
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <!-- Historique des présences -->
        <section class="{{ $panneau }}">
            <div class="{{ $enTete }}">
                <h3 class="text-base font-semibold text-slate-900">Historique complet des présences
                    <span class="font-normal text-slate-600 tabular-nums">({{ $presences->total() }})</span>
                </h3>
            </div>

            @if ($presences->count() > 0)
                <!-- Tableau (grands écrans) -->
                <div class="hidden xl:block">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col class="w-40">
                            <col class="w-28">
                            <col class="w-20">
                            <col class="w-20">
                            <col>
                            <col class="w-44">
                            <col class="w-32">
                        </colgroup>
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr class="text-left text-xs font-medium text-slate-600">
                                <th scope="col" class="px-5 py-2.5">Date</th>
                                <th scope="col" class="px-3 py-2.5">Statut</th>
                                <th scope="col" class="px-3 py-2.5">Arrivée</th>
                                <th scope="col" class="px-3 py-2.5">Départ</th>
                                <th scope="col" class="px-3 py-2.5">Motif</th>
                                <th scope="col" class="px-3 py-2.5">Marqué par</th>
                                <th scope="col" class="px-5 py-2.5 text-right">Justificatif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($presences as $p)
                                @php [$libelle, $cls] = $statutsPresence[$p->statut] ?? [ucfirst($p->statut), 'bg-slate-100 text-slate-700']; @endphp
                                <tr class="hover:bg-brand-50/60">
                                    <td class="px-5 py-3 font-medium text-slate-900 whitespace-nowrap tabular-nums">
                                        {{ $p->date->translatedFormat('D d M Y') }}</td>
                                    <td class="px-3 py-3">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-md whitespace-nowrap {{ $cls }}">{{ $libelle }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-slate-700 tabular-nums">
                                        {{ $p->heure_arrivee ? substr($p->heure_arrivee, 0, 5) : '—' }}</td>
                                    <td class="px-3 py-3 text-slate-700 tabular-nums">
                                        {{ $p->heure_depart ? substr($p->heure_depart, 0, 5) : '—' }}</td>
                                    <td class="px-3 py-3 text-slate-500 truncate" title="{{ $p->motif }}">
                                        {{ $p->motif ?: '—' }}</td>
                                    <td class="px-3 py-3 text-slate-700 truncate"
                                        title="{{ $p->marquePar ? $p->marquePar->nom_complet : '' }}">
                                        {{ $p->marquePar ? $p->marquePar->nom_complet : '—' }}</td>
                                    <td class="px-5 py-3 text-right">
                                        @if ($p->justificatif)
                                            <a href="{{ asset('storage/' . $p->justificatif) }}" download
                                                class="{{ $lienFichier }}"
                                                aria-label="Télécharger le justificatif du {{ $p->date->format('d/m/Y') }}">
                                                <i class="fas fa-download" aria-hidden="true"></i>Télécharger
                                            </a>
                                        @else
                                            <span class="text-slate-500">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Liste (téléphone et tablette) -->
                <ul class="xl:hidden divide-y divide-slate-100">
                    @foreach ($presences as $p)
                        @php [$libelle, $cls] = $statutsPresence[$p->statut] ?? [ucfirst($p->statut), 'bg-slate-100 text-slate-700']; @endphp
                        <li class="px-5 sm:px-6 py-3">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-medium text-slate-900 tabular-nums">
                                    {{ $p->date->translatedFormat('D d M Y') }}</p>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $cls }}">{{ $libelle }}</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-600 tabular-nums">
                                Arrivée {{ $p->heure_arrivee ? substr($p->heure_arrivee, 0, 5) : '—' }}
                                · Départ {{ $p->heure_depart ? substr($p->heure_depart, 0, 5) : '—' }}
                            </p>
                            @if ($p->motif)
                                <p class="mt-1 text-sm text-slate-500 break-words">{{ $p->motif }}</p>
                            @endif
                            @if ($p->marquePar)
                                <p class="mt-1 text-xs text-slate-500">Marqué par {{ $p->marquePar->nom_complet }}</p>
                            @endif
                            @if ($p->justificatif)
                                <a href="{{ asset('storage/' . $p->justificatif) }}" download class="mt-2 {{ $lienFichier }}">
                                    <i class="fas fa-download" aria-hidden="true"></i>Télécharger le justificatif
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-6 py-10 text-center">
                    <p class="text-sm font-medium text-slate-700">Aucune présence enregistrée</p>
                    <p class="mt-1 text-sm text-slate-500">L'historique apparaîtra ici dès la première présence marquée.</p>
                </div>
            @endif

            <div class="px-5 sm:px-6 py-4 border-t border-brand-100">{{ $presences->links() }}</div>
        </section>

        <div>
            <a href="{{ route('admin.presences.index') }}" class="inline-flex items-center gap-2 {{ $lien }}">
                <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>Retour
            </a>
        </div>
    </div>
@endsection
