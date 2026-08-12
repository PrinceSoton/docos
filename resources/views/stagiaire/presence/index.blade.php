@extends('layouts.stagiaire')
@section('titre', 'Ma Présence')
@section('breadcrumb', 'Mon espace > Présence')
@section('content')
    <div class="space-y-6">
        <div data-aos="fade-down">
            <h2 class="text-slate-800 font-bold text-2xl">Ma Présence</h2>
            <p class="text-slate-500 text-sm">Marquez votre présence et gérez vos permissions</p>
        </div>

        <!-- Juste après le titre -->
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5" data-aos="fade-up">
            <p class="text-white-700 text-sm">
                <i class="fas fa-clock mr-2"></i>
                Horaires de travail configurés : <strong>{{ \Carbon\Carbon::parse($heureDebut)->format('H:i') }}</strong> –
                <strong>{{ \Carbon\Carbon::parse($heureFin)->format('H:i') }}</strong>
            </p>
            <p class="text-white-600 text-xs mt-1">
                <span class="inline-block mr-3"><span class="text-blue-600">●</span> Présent avant
                    {{ \Carbon\Carbon::parse($heureDebut)->format('H:i') }}</span>
                <span class="inline-block mr-3"><span class="text-amber-600">●</span> Retard entre
                    {{ \Carbon\Carbon::parse($heureDebut)->format('H:i') }} et
                    {{ \Carbon\Carbon::parse($heureFin)->format('H:i') }}</span>
                <span class="inline-block"><span class="text-red-600">●</span> Absent après
                    {{ \Carbon\Carbon::parse($heureFin)->format('H:i') }}</span>
            </p>
        </div>

        <!-- Marquage présence du jour -->
        <div class="card p-6 border-l-4 {{ $peutMarquer ? 'border-green-400' : 'border-slate-300' }}" data-aos="fade-up">
            <div class="flex items-center gap-4 flex-wrap">
                <div
                    class="w-14 h-14 rounded-2xl {{ $peutMarquer ? 'bg-green-100' : 'bg-slate-100' }} flex items-center justify-center flex-shrink-0">
                    <i
                        class="fas fa-{{ $peutMarquer ? 'user-check' : 'check-double' }} text-{{ $peutMarquer ? 'green' : 'slate' }}-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-slate-800 text-lg">Aujourd'hui — {{ now()->translatedFormat('l d F Y') }}
                    </h3>
                    @if ($presenceAujourdhui)
                        <p class="text-green-600 font-semibold text-sm mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            Présence marquée : <span class="capitalize">{{ $presenceAujourdhui->statut }}</span>
                            @if ($presenceAujourdhui->heure_arrivee)
                                à {{ substr($presenceAujourdhui->heure_arrivee, 0, 5) }}
                            @endif
                        </p>
                    @elseif($peutMarquer)
                        <p class="text-slate-500 text-sm mt-1">Vous n'avez pas encore marqué votre présence aujourd'hui</p>
                    @else
                        <p class="text-slate-400 text-sm mt-1">Pas de service aujourd'hui (jour non travaillé)</p>
                    @endif
                </div>
                @if ($peutMarquer)
                    <button onclick="marquerPresence()"
                        class="flex items-center gap-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-user-check"></i>Marquer ma présence
                    </button>
                @endif
            </div>
        </div>

        <!-- Formulaire de présence (si possible marquer) -->
        @if ($peutMarquer)
            <div class="card p-6" data-aos="fade-up" id="presenceForm" style="display:none">
                <h3 class="font-bold text-slate-800 mb-4"><i class="fas fa-clipboard text-indigo-500 mr-2"></i>Détails de
                    présence</h3>
                <form action="{{ route('stagiaire.presence.marquer') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Motif de retard <span class="text-slate-400 font-normal">(si arrivée après 09h00)</span>
                        </label>
                        <input type="text" name="motif"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm"
                            placeholder="Expliquer si vous êtes en retard...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Justificatif <span class="text-slate-400 font-normal">(optionnel, tous formats)</span>
                        </label>
                        <input type="file" name="justificatif"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-amber-50 file:text-amber-700 file:font-medium hover:file:bg-amber-100 transition">
                    </div>
                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition-all hover:-translate-y-0.5">
                            <i class="fas fa-check mr-2"></i>Confirmer ma présence
                        </button>
                        <button type="button" onclick="document.getElementById('presenceForm').style.display='none'"
                            class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Demande de permission -->
        <div class="card p-6" data-aos="fade-up">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                <h3 class="text-slate-800 font-bold text-lg"><i
                        class="fas fa-calendar-plus text-purple-500 mr-2"></i>Demande de permission</h3>
                <button onclick="togglePermission()"
                    class="flex items-center gap-2 bg-purple-100 hover:bg-purple-200 text-purple-700 px-4 py-2 rounded-xl text-sm font-medium transition">
                    <i class="fas fa-plus"></i>Nouvelle demande
                </button>
            </div>
            <p class="text-slate-400 text-sm mb-4">
                <i class="fas fa-info-circle text-indigo-400 mr-1"></i>
                Les demandes doivent être soumises <strong>au moins 24h à l'avance</strong> et seront validées par votre
                mentor.
            </p>

            <div id="permissionForm" style="display:none"
                class="p-5 bg-purple-50 rounded-2xl border border-purple-100 mb-4">
                <form action="{{ route('stagiaire.presence.demandePermission') }}" method="POST"
                    enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Date de début *</label>
                            <input type="date" name="date_debut" required min="{{ now()->addDays(1)->format('Y-m-d') }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 transition text-sm bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Date de fin *</label>
                            <input type="date" name="date_fin" required min="{{ now()->addDays(1)->format('Y-m-d') }}"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 transition text-sm bg-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Motif *</label>
                        <textarea name="motif" rows="2" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-purple-400 transition text-sm resize-none bg-white"
                            placeholder="Raison de votre demande..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Justificatif <span
                                class="text-slate-400 font-normal">(optionnel)</span></label>
                        <input type="file" name="justificatif"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-purple-50 file:text-purple-700 file:font-medium hover:file:bg-purple-100">
                    </div>
                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-purple-500 to-indigo-600 text-white py-2.5 rounded-xl font-semibold hover:shadow-lg transition-all">
                            <i class="fas fa-paper-plane mr-2"></i>Envoyer la demande
                        </button>
                        <button type="button" onclick="togglePermission()"
                            class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-xl font-medium hover:bg-slate-50 transition">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>

            <!-- Historique permissions -->
            @if ($permissions->count() > 0)
                <div class="space-y-2">
                    @foreach ($permissions as $perm)
                        <div
                            class="flex items-center gap-4 p-4 rounded-xl {{ $perm->statut === 'valide' ? 'bg-green-50 border border-green-100' : ($perm->statut === 'refuse' ? 'bg-red-50 border border-red-100' : 'bg-amber-50 border border-amber-100') }}">
                            <div class="flex-1">
                                <p class="text-slate-800 font-medium text-sm">
                                    {{ $perm->date_debut->format('d/m/Y') }} → {{ $perm->date_fin->format('d/m/Y') }}
                                    @php $j = $perm->date_debut->diffInDays($perm->date_fin)+1; @endphp
                                    <span class="text-slate-400">({{ $j }} jour(s))</span>
                                </p>
                                <p class="text-slate-500 text-xs mt-0.5">{{ $perm->motif }}</p>
                                @if ($perm->commentaire_mentor)
                                    <p class="text-slate-400 text-xs mt-0.5 italic">
                                        <i class="fas fa-comment mr-1"></i>{{ $perm->commentaire_mentor }}
                                    </p>
                                @endif
                            </div>
                            @php $pb = ['en_attente'=>'bg-amber-100 text-amber-700','valide'=>'bg-green-100 text-green-700','refuse'=>'bg-red-100 text-red-600']; @endphp
                            <div class="text-right">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $pb[$perm->statut] ?? '' }}">
                                    {{ ['en_attente' => 'En attente', 'valide' => 'Validée', 'refuse' => 'Refusée'][$perm->statut] ?? $perm->statut }}
                                </span>
                                <p class="text-slate-400 text-xs mt-1">{{ $perm->demande_le->format('d/m/Y') }}</p>
                            </div>
                            @if ($perm->justificatif)
                                <a href="{{ route('stagiaire.presence.telechargerJustificatif', $perm->id) }}" download
                                    class="w-8 h-8 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-download text-xs"></i>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Historique présences -->
        <div class="card overflow-hidden" {{-- data-aos="fade-up" --}}>
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
                <h3 class="font-bold text-slate-800 text-lg"><i class="fas fa-history text-amber-500 mr-2"></i>Mon
                    historique de présence</h3>
                <input type="text" id="search" placeholder="Rechercher..."
                    class="border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-amber-400 transition w-44">
            </div>

            <!-- Mini stats -->
            @php
                $totalPres = $presences->total();
                $nbPresent = \App\Models\Presence::where('stagiaire_id', Auth::user()->stagiaire->id)
                    ->where('statut', 'present')
                    ->count();
                $nbRetard = \App\Models\Presence::where('stagiaire_id', Auth::user()->stagiaire->id)
                    ->where('statut', 'retard')
                    ->count();
                $nbAbsent = \App\Models\Presence::where('stagiaire_id', Auth::user()->stagiaire->id)
                    ->where('statut', 'absent')
                    ->count();
            @endphp
            <div class="grid grid-cols-3 gap-0 border-b border-slate-100">
                <div class="p-4 text-center border-r border-slate-100">
                    <p class="text-2xl font-black text-green-600">{{ $nbPresent }}</p>
                    <p class="text-xs text-slate-400">Présent</p>
                </div>
                <div class="p-4 text-center border-r border-slate-100">
                    <p class="text-2xl font-black text-amber-600">{{ $nbRetard }}</p>
                    <p class="text-xs text-slate-400">Retard</p>
                </div>
                <div class="p-4 text-center">
                    <p class="text-2xl font-black text-red-600">{{ $nbAbsent }}</p>
                    <p class="text-xs text-slate-400">Absent</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Date</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Statut</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Heure</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Motif</th>
                            <th class="px-5 py-3 text-center font-semibold text-slate-600">Justificatif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="presTable">
                        @forelse($presences as $p)
                            <tr class="hover:bg-slate-50 transition pres-row">
                                <td class="px-5 py-3 font-medium text-slate-800">
                                    {{ $p->date->translatedFormat('D d/m/Y') }}</td>
                                <td class="px-5 py-3">
                                    @php $sc = ['present'=>'bg-green-100 text-green-700','retard'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-600']; @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$p->statut] ?? '' }}">
                                        {{ ucfirst($p->statut) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-mono text-slate-600">
                                    {{ $p->heure_arrivee ? substr($p->heure_arrivee, 0, 5) : '—' }}</td>
                                <td class="px-5 py-3 text-slate-500 text-xs max-w-xs truncate">{{ $p->motif ?: '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    @if ($p->justificatif)
                                        <a href="{{ route('stagiaire.presence.telechargerJustificatif', $p) }}" download
                                            class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-1 rounded-lg text-xs font-medium transition">
                                            <i class="fas fa-download"></i>Fichier
                                        </a>
                                    @else<span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400">Aucune présence enregistrée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100">{{ $presences->links() }}</div>
        </div>
    </div>
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
        }
        document.getElementById('search')?.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.pres-row').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
@endpush
