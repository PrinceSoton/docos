@extends('layouts.stagiaire')
@section('titre', 'Ma Présence')
@section('breadcrumb', 'Mon espace > Présence')
@section('content')
    @php
        $hDebut = \Carbon\Carbon::parse($heureDebut)->format('H:i');
        $hFin = \Carbon\Carbon::parse($heureFin)->format('H:i');

        // Libellé, badge et pastille par statut
        $statutsPresence = [
            'present' => ['Présent', 'bg-emerald-50 text-emerald-800', 'bg-emerald-500'],
            'retard' => ['Retard', 'bg-amber-50 text-amber-800', 'bg-amber-500'],
            'absent' => ['Absent', 'bg-red-50 text-red-800', 'bg-red-500'],
        ];
        $statutsPermission = [
            'en_attente' => ['En attente', 'bg-amber-50 text-amber-800'],
            'valide' => ['Validée', 'bg-emerald-50 text-emerald-800'],
            'refuse' => ['Refusée', 'bg-red-50 text-red-800'],
        ];

        $nbPresent = \App\Models\Presence::where('stagiaire_id', Auth::user()->stagiaire->id)
            ->where('statut', 'present')
            ->count();
        $nbRetard = \App\Models\Presence::where('stagiaire_id', Auth::user()->stagiaire->id)
            ->where('statut', 'retard')
            ->count();
        $nbAbsent = \App\Models\Presence::where('stagiaire_id', Auth::user()->stagiaire->id)
            ->where('statut', 'absent')
            ->count();

        $permissionsEnAttente = $permissions->where('statut', 'en_attente')->count();

        // Réouvre le bon formulaire si la validation a échoué
        $permissionOuverte = old('date_debut') !== null;
        $arriveeOuverte = $peutMarquer && !$permissionOuverte && $errors->any();

        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $lien = 'text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded ' . $focus;
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $fichier = 'w-full text-sm text-slate-500 file:mr-3 file:rounded-md file:border-0 file:!bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium file:!text-slate-700 hover:file:!bg-slate-200';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnSecondaire = 'inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $lienFichier = 'inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 hover:underline underline-offset-4 rounded ' . $focus;
        // Champ fichier posé sur fond teinté (brand-50) : texte secondaire en slate-600 pour le contraste
        $fichierTeinte = str_replace('text-slate-500', 'text-slate-600', $fichier);
        $enTete = 'px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100';
    @endphp

    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

        <!-- Aujourd'hui : arrivée et départ -->
        <section class="bg-white border border-brand-100 rounded-xl lg:col-span-2">
            <div class="px-5 sm:px-6 pt-5 sm:pt-6">
                <h3 class="text-base font-semibold text-slate-900">Aujourd'hui</h3>
                <p class="text-sm text-slate-500">{{ ucfirst(now()->translatedFormat('l d F Y')) }}</p>
            </div>

            <div
                class="mt-4 grid grid-cols-1 xl:grid-cols-2 border-t border-slate-100 divide-y divide-slate-100 xl:divide-y-0 xl:divide-x">

                <!-- Arrivée -->
                <div class="p-5 sm:p-6">
                    <p class="text-sm font-medium text-slate-500">Arrivée</p>
                    @if ($presenceAujourdhui)
                        @php [$libelle, $cls, $dot] = $statutsPresence[$presenceAujourdhui->statut] ?? [ucfirst($presenceAujourdhui->statut), 'bg-slate-100 text-slate-700', 'bg-slate-400']; @endphp
                        <p class="mt-1 flex items-center gap-2 text-lg font-semibold text-slate-900">
                            <span class="h-2.5 w-2.5 rounded-full {{ $dot }}" aria-hidden="true"></span>
                            Arrivée enregistrée
                        </p>
                        <p class="mt-1 text-sm text-slate-600 tabular-nums">
                            {{ $libelle }}{{ $presenceAujourdhui->heure_arrivee ? ', à ' . substr($presenceAujourdhui->heure_arrivee, 0, 5) : '' }}
                        </p>
                    @elseif ($peutMarquer)
                        <p class="mt-1 flex items-center gap-2 text-lg font-semibold text-slate-900">
                            <span class="h-2.5 w-2.5 rounded-full border-2 border-amber-500" aria-hidden="true"></span>
                            Arrivée à marquer
                        </p>
                        <p class="mt-1 text-sm text-slate-600">Vous n'avez pas encore marqué votre arrivée aujourd'hui.
                        </p>
                        <button type="button" onclick="marquerPresence()" aria-controls="presenceForm"
                            class="mt-4 w-full sm:w-auto {{ $btnPrimaire }}">
                            <i class="fas fa-user-check text-xs" aria-hidden="true"></i>Marquer mon arrivée
                        </button>
                    @else
                        <p class="mt-1 flex items-center gap-2 text-lg font-semibold text-slate-900">
                            <span class="h-2.5 w-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                            Jour non travaillé
                        </p>
                        <p class="mt-1 text-sm text-slate-600">Pas de service aujourd'hui : rien à marquer.</p>
                    @endif
                </div>

                <!-- Départ -->
                <div class="p-5 sm:p-6">
                    <p class="text-sm font-medium text-slate-500">Départ</p>
                    @if ($presenceAujourdhui)
                        @if ($presenceAujourdhui->heure_depart)
                            <p class="mt-1 flex items-center gap-2 text-lg font-semibold text-slate-900">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                Départ enregistré
                            </p>
                            <p class="mt-1 text-sm text-slate-600 tabular-nums">À
                                {{ substr($presenceAujourdhui->heure_depart, 0, 5) }}</p>
                        @elseif ($presenceAujourdhui->statut === 'absent')
                            <p class="mt-1 flex items-center gap-2 text-lg font-semibold text-slate-900">
                                <span class="h-2.5 w-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                                Aucun départ possible
                            </p>
                            <p class="mt-1 text-sm text-slate-600">Aucun départ possible (absence).</p>
                        @else
                            <p class="mt-1 flex items-center gap-2 text-lg font-semibold text-slate-900">
                                <span class="h-2.5 w-2.5 rounded-full border-2 border-amber-500" aria-hidden="true"></span>
                                Départ à marquer
                            </p>
                            <p class="mt-1 text-sm text-slate-600">Vous n'avez pas encore marqué votre départ.</p>
                        @endif
                        @if ($peutMarquerDepart)
                            <form action="{{ route('stagiaire.presence.depart') }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto {{ $btnPrimaire }}">
                                    <i class="fas fa-sign-out-alt text-xs" aria-hidden="true"></i>Marquer mon départ
                                </button>
                            </form>
                        @endif
                    @elseif ($peutMarquer)
                        <p class="mt-1 flex items-center gap-2 text-lg font-semibold text-slate-500">
                            <span class="h-2.5 w-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                            Pas encore disponible
                        </p>
                        <p class="mt-1 text-sm text-slate-500">Le départ se marque une fois votre arrivée enregistrée.</p>
                    @else
                        <p class="mt-1 flex items-center gap-2 text-lg font-semibold text-slate-500">
                            <span class="h-2.5 w-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                            Aucun départ à marquer
                        </p>
                        <p class="mt-1 text-sm text-slate-500">Jour non travaillé.</p>
                    @endif
                </div>
            </div>

            <!-- Formulaire de présence -->
            @if ($peutMarquer)
                <div id="presenceForm" style="display:{{ $arriveeOuverte ? 'block' : 'none' }}"
                    class="border-t border-brand-100 bg-brand-50 rounded-b-xl p-5 sm:p-6">
                    <h4 class="text-sm font-semibold text-slate-900">Détails de présence</h4>
                    <form action="{{ route('stagiaire.presence.marquer') }}" method="POST" enctype="multipart/form-data"
                        class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="arrivee-motif" class="block text-sm font-medium text-slate-700 mb-1">Motif de
                                retard</label>
                            <input type="text" name="motif" id="arrivee-motif" value="{{ $arriveeOuverte ? old('motif') : '' }}"
                                class="{{ $champ }}" placeholder="Expliquer si vous êtes en retard...">
                            @if ($arriveeOuverte)
                                @error('motif')
                                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                        <div>
                            <label for="arrivee-justificatif"
                                class="block text-sm font-medium text-slate-700 mb-1">Justificatif</label>
                            <input type="file" name="justificatif" id="arrivee-justificatif"
                                class="{{ $fichierTeinte }}">
                            @if ($arriveeOuverte)
                                @error('justificatif')
                                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                        <div class="flex flex-col-reverse sm:flex-row gap-2">
                            <button type="button" onclick="document.getElementById('presenceForm').style.display='none'"
                                class="{{ $btnSecondaire }}">Annuler</button>
                            <button type="submit" class="{{ $btnPrimaire }}">
                                <i class="fas fa-check text-xs" aria-hidden="true"></i>Confirmer mon arrivée
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </section>

        <!-- Horaires et règles -->
        <section class="bg-white border border-brand-100 rounded-xl p-5 sm:p-6">
            <h3 class="text-base font-semibold text-slate-900">Horaires de travail</h3>
            <div class="mt-3 rounded-lg bg-brand-50 border border-brand-100 p-4">
                <p class="text-xs text-slate-600">Plage horaire</p>
                <p class="mt-0.5 text-2xl font-semibold text-slate-900 tabular-nums">{{ $hDebut }} – {{ $hFin }}</p>
                <dl class="mt-4 pt-4 border-t border-brand-100 space-y-3 text-sm">
                    <div class="flex items-start gap-3">
                        <span class="mt-1.5 h-2 w-2 rounded-full bg-emerald-500 flex-shrink-0" aria-hidden="true"></span>
                        <div>
                            <dt class="font-medium text-slate-900">Présent</dt>
                            <dd class="text-slate-600 tabular-nums">Arrivée avant {{ $hDebut }}</dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1.5 h-2 w-2 rounded-full bg-amber-500 flex-shrink-0" aria-hidden="true"></span>
                        <div>
                            <dt class="font-medium text-slate-900">Retard</dt>
                            <dd class="text-slate-600 tabular-nums">Arrivée entre {{ $hDebut }} et {{ $hFin }}
                            </dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1.5 h-2 w-2 rounded-full bg-red-500 flex-shrink-0" aria-hidden="true"></span>
                        <div>
                            <dt class="font-medium text-slate-900">Absent</dt>
                            <dd class="text-slate-600 tabular-nums">Arrivée après {{ $hFin }}</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </section>

        <!-- Demande de permission -->
        <section class="bg-white border border-brand-100 rounded-xl overflow-hidden lg:col-start-3 lg:row-start-2">
            <div class="{{ $enTete }} flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-slate-900">Permissions</h3>
                <button type="button" onclick="togglePermission()" aria-controls="permissionForm" aria-expanded="{{ $permissionOuverte ? 'true' : 'false' }}"
                    class="inline-flex shrink-0 whitespace-nowrap items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors {{ $focus }}">
                    <i class="fas fa-plus text-xs text-slate-400" aria-hidden="true"></i>Nouvelle demande
                </button>
            </div>
            <div class="p-5 sm:p-6">
            <p class="text-sm text-slate-500">
                Les demandes doivent être soumises <strong class="font-semibold text-slate-700">au moins 24h à
                    l'avance</strong>.
            </p>

            @if ($permissionsEnAttente > 0)
                <p class="mt-3 flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-sm text-amber-900">
                    <span class="h-2 w-2 rounded-full bg-amber-500 flex-shrink-0" aria-hidden="true"></span>
                    {{ $permissionsEnAttente }}
                    {{ $permissionsEnAttente > 1 ? 'demandes en attente de validation' : 'demande en attente de validation' }}
                </p>
            @endif

            <div id="permissionForm" style="display:{{ $permissionOuverte ? 'block' : 'none' }}"
                class="mt-4 rounded-lg border border-brand-100 bg-brand-50 p-4">
                <form action="{{ route('stagiaire.presence.demandePermission') }}" method="POST"
                    enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 xl:grid-cols-2 lg:grid-cols-1 gap-4">
                        <div>
                            <label for="perm-debut" class="block text-sm font-medium text-slate-700 mb-1">Date de début
                                *</label>
                            <input type="date" name="date_debut" id="perm-debut" required
                                min="{{ now()->addDays(2)->format('Y-m-d') }}" value="{{ old('date_debut') }}"
                                class="{{ $champ }}">
                            @if ($permissionOuverte)
                                @error('date_debut')
                                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                        <div>
                            <label for="perm-fin" class="block text-sm font-medium text-slate-700 mb-1">Date de fin
                                *</label>
                            <input type="date" name="date_fin" id="perm-fin" required
                                min="{{ now()->addDays(1)->format('Y-m-d') }}" value="{{ old('date_fin') }}"
                                class="{{ $champ }}">
                            @if ($permissionOuverte)
                                @error('date_fin')
                                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                                @enderror
                            @endif
                        </div>
                    </div>
                    <div>
                        <label for="perm-motif" class="block text-sm font-medium text-slate-700 mb-1">Motif *</label>
                        <textarea name="motif" id="perm-motif" rows="2" required class="{{ $champ }} resize-none"
                            placeholder="Raison de votre demande...">{{ $permissionOuverte ? old('motif') : '' }}</textarea>
                        @if ($permissionOuverte)
                            @error('motif')
                                <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label for="perm-justificatif"
                            class="block text-sm font-medium text-slate-700 mb-1">Justificatif</label>
                        <input type="file" name="justificatif" id="perm-justificatif" class="{{ $fichierTeinte }}">
                        @if ($permissionOuverte)
                            @error('justificatif')
                                <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                    <div class="flex flex-col-reverse sm:flex-row gap-2">
                        <button type="button" onclick="togglePermission()"
                            class="{{ $btnSecondaire }}">Annuler</button>
                        <button type="submit" class="{{ $btnPrimaire }}">
                            <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>Envoyer la demande
                        </button>
                    </div>
                </form>
            </div>

            <ul class="mt-4 divide-y divide-slate-100">
                @forelse ($permissions as $perm)
                    @php
                        [$libelle, $cls] = $statutsPermission[$perm->statut] ?? [$perm->statut, 'bg-slate-100 text-slate-700'];
                        $nbJours = (int) $perm->date_debut->diffInDays($perm->date_fin) + 1;
                    @endphp
                    <li class="py-3">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-medium text-slate-900 tabular-nums">
                                {{ $perm->date_debut->format('d/m/Y') }} → {{ $perm->date_fin->format('d/m/Y') }}
                            </p>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $cls }}">
                                {{ $libelle }}
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs text-slate-500 tabular-nums">
                            {{ $nbJours }} {{ $nbJours > 1 ? 'jours' : 'jour' }} · demandée le
                            {{ $perm->demande_le->format('d/m/Y') }}
                        </p>
                        <p class="mt-1 text-sm text-slate-600">{{ $perm->motif }}</p>
                        @if ($perm->commentaire_mentor)
                            <p class="mt-1 text-xs text-slate-500">
                                <span class="font-medium text-slate-600">Réponse du mentor :</span>
                                {{ $perm->commentaire_mentor }}
                            </p>
                        @endif
                        @if ($perm->justificatif)
                            <a href="{{ route('stagiaire.presence.telechargerPermission', $perm) }}" download
                                class="mt-2 {{ $lienFichier }}">
                                <i class="fas fa-download" aria-hidden="true"></i>Télécharger le justificatif
                            </a>
                        @endif
                    </li>
                @empty
                    <li class="py-4 text-sm text-slate-500">Aucune demande de permission pour le moment.</li>
                @endforelse
            </ul>
            </div>
        </section>

        <!-- Historique des présences -->
        <section class="bg-white border border-brand-100 rounded-xl overflow-hidden lg:col-span-2 lg:col-start-1 lg:row-start-2">
            <div class="{{ $enTete }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-slate-900">Historique de présence</h3>
                <input type="search" id="search" placeholder="Rechercher..." aria-label="Rechercher dans l'historique"
                    class="w-full sm:w-52 {{ $champ }}">
            </div>
            <dl class="px-5 sm:px-6 mt-4 flex flex-wrap gap-x-6 gap-y-1 text-sm">
                @foreach ([['Présences', $nbPresent, 'bg-emerald-500'], ['Retards', $nbRetard, 'bg-amber-500'], ['Absences', $nbAbsent, 'bg-red-500']] as [$label, $val, $dot])
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                        <dt class="text-slate-500">{{ $label }}</dt>
                        <dd class="font-semibold text-slate-900 tabular-nums">{{ $val }}</dd>
                    </div>
                @endforeach
            </dl>

            <!-- Tableau (écrans larges) -->
            <div class="hidden xl:block overflow-x-auto mt-4">
                <table class="w-full text-sm">
                    <thead class="bg-brand-50 border-y border-brand-100">
                        <tr class="text-left text-xs font-medium text-slate-600">
                            <th scope="col" class="px-5 py-2.5">Date</th>
                            <th scope="col" class="px-3 py-2.5">Statut</th>
                            <th scope="col" class="px-3 py-2.5">Arrivée</th>
                            <th scope="col" class="px-3 py-2.5">Départ</th>
                            <th scope="col" class="px-3 py-2.5">Motif</th>
                            <th scope="col" class="px-6 py-2.5 text-right">Justificatif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="presTable">
                        @forelse($presences as $p)
                            @php [$libelle, $cls] = $statutsPresence[$p->statut] ?? [ucfirst($p->statut), 'bg-slate-100 text-slate-700']; @endphp
                            <tr class="hover:bg-slate-50 pres-row">
                                <td class="px-5 py-3 font-medium text-slate-900 whitespace-nowrap">
                                    {{ $p->date->translatedFormat('D d/m/Y') }}</td>
                                <td class="px-3 py-3">
                                    <span
                                        class="text-xs font-medium px-2 py-0.5 rounded-md {{ $cls }}">{{ $libelle }}</span>
                                </td>
                                <td class="px-3 py-3 text-slate-700 tabular-nums">
                                    {{ $p->heure_arrivee ? substr($p->heure_arrivee, 0, 5) : '—' }}</td>
                                <td class="px-3 py-3 text-slate-700 tabular-nums">
                                    {{ $p->heure_depart ? substr($p->heure_depart, 0, 5) : '—' }}</td>
                                <td class="px-3 py-3 text-slate-500 max-w-[8rem] 2xl:max-w-[14rem] truncate" title="{{ $p->motif }}">
                                    {{ $p->motif ?: '—' }}</td>
                                <td class="px-6 py-3 text-right">
                                    @if ($p->justificatif)
                                        <a href="{{ route('stagiaire.presence.telechargerJustificatif', $p) }}" download
                                            class="{{ $lienFichier }}" aria-label="Télécharger le justificatif du {{ $p->date->format('d/m/Y') }}">
                                            <i class="fas fa-download" aria-hidden="true"></i><span class="hidden 2xl:inline">Télécharger</span>
                                        </a>
                                    @else
                                        <span class="text-slate-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center">
                                    <p class="text-sm font-medium text-slate-700">Aucune présence enregistrée</p>
                                    <p class="mt-1 text-sm text-slate-500">Votre historique apparaîtra ici dès votre
                                        première arrivée marquée.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Liste (téléphone) -->
            <ul class="xl:hidden mt-4 divide-y divide-slate-100 border-t border-brand-100">
                @forelse($presences as $p)
                    @php [$libelle, $cls] = $statutsPresence[$p->statut] ?? [ucfirst($p->statut), 'bg-slate-100 text-slate-700']; @endphp
                    <li class="px-5 py-3 pres-row">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium text-slate-900">{{ $p->date->translatedFormat('D d/m/Y') }}</p>
                            <span
                                class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0 {{ $cls }}">{{ $libelle }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-600 tabular-nums">
                            Arrivée {{ $p->heure_arrivee ? substr($p->heure_arrivee, 0, 5) : '—' }}
                            · Départ {{ $p->heure_depart ? substr($p->heure_depart, 0, 5) : '—' }}
                        </p>
                        @if ($p->motif)
                            <p class="mt-1 text-sm text-slate-500">{{ $p->motif }}</p>
                        @endif
                        @if ($p->justificatif)
                            <a href="{{ route('stagiaire.presence.telechargerJustificatif', $p) }}" download
                                class="mt-2 {{ $lienFichier }}">
                                <i class="fas fa-download" aria-hidden="true"></i>Télécharger le justificatif
                            </a>
                        @endif
                    </li>
                @empty
                    <li class="px-5 py-8 text-center">
                        <p class="text-sm font-medium text-slate-700">Aucune présence enregistrée</p>
                        <p class="mt-1 text-sm text-slate-500">Votre historique apparaîtra ici dès votre première arrivée
                            marquée.</p>
                    </li>
                @endforelse
            </ul>

            <p id="presNoResult" class="hidden px-5 sm:px-6 py-6 text-center text-sm text-slate-500">Aucun résultat pour
                cette recherche.</p>

            <div class="px-5 sm:px-6 py-4 border-t border-brand-100">{{ $presences->links() }}</div>
        </section>
    </div>
<noscript><style>#presenceForm, #permissionForm { display: block !important; }</style></noscript>
@endsection
@push('scripts')
    <script>
        function marquerPresence() {
            const form = document.getElementById('presenceForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            if (form.style.display === 'block') form.scrollIntoView({
                behavior: 'smooth'
            });
        }

        function togglePermission() {
            const form = document.getElementById('permissionForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            document.querySelector('[aria-controls="permissionForm"]').setAttribute('aria-expanded', form.style.display === 'block' ? 'true' : 'false');
        }
        document.getElementById('search')?.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.pres-row').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
            // Le tableau reste la référence pour compter les résultats visibles
            const lignes = document.querySelectorAll('#presTable .pres-row');
            const visibles = Array.from(lignes).filter(r => r.style.display !== 'none').length;
            document.getElementById('presNoResult')?.classList.toggle('hidden', lignes.length === 0 || visibles > 0);
        });
    </script>
@endpush
