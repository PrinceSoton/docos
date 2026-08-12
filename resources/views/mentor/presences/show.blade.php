@extends('layouts.mentor')
@section('titre', 'Présences - ' . $stagiaire->user->nom_complet)
@section('breadcrumb', 'Présences > Fiche complète')
@section('content')
    <div class="max-w-4xl mx-auto space-y-5">
        <!-- En-tête -->
        <div class="card p-6 flex items-center gap-5 flex-wrap" data-aos="fade-down">
            <div
                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center overflow-hidden flex-shrink-0 shadow">
                @if ($stagiaire->user->photo)
                    <img src="{{ asset('storage/' . $stagiaire->user->photo) }}" class="w-full h-full object-cover">
                @else
                    <span
                        class="text-white font-black text-2xl">{{ strtoupper(substr($stagiaire->user->prenom, 0, 1)) }}</span>
                @endif
            </div>
            <div class="flex-1">
                <h2 class="text-slate-800 font-black text-2xl">{{ $stagiaire->user->nom_complet }}</h2>
                <p class="text-slate-500 text-sm">{{ $stagiaire->matricule }} • {{ $stagiaire->ecole ?: '—' }}</p>
            </div>
            <a href="{{ route('mentor.stagiaires.show', $stagiaire) }}"
                class="bg-emerald-100 hover:bg-emerald-200 text-emerald-700 px-4 py-2 rounded-xl text-sm font-medium transition">
                <i class="fas fa-user mr-1"></i>Voir profil
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" data-aos="fade-up">
            @foreach ([[$stats['present'], 'Présent', 'from-green-400 to-emerald-500', 'user-check'], [$stats['retard'], 'Retard', 'from-amber-400 to-orange-500', 'clock'], [$stats['absent'], 'Absent', 'from-red-400 to-pink-500', 'user-times'], [$permissions->count(), 'Permissions', 'from-purple-400 to-indigo-500', 'calendar']] as [$val, $label, $grad, $icon])
                <div class="bg-gradient-to-br {{ $grad }} rounded-2xl p-5 text-white relative overflow-hidden">
                    <div class="absolute right-2 top-2 opacity-20"><i class="fas fa-{{ $icon }} text-3xl"></i></div>
                    <p class="text-white/80 text-xs font-medium">{{ $label }}</p>
                    <p class="text-white font-black text-3xl mt-1">{{ $val }}</p>
                </div>
            @endforeach
        </div>

        <!-- Permissions -->
        @if ($permissions->count() > 0)
            <div class="card p-6" data-aos="fade-up">
                <h3 class="text-slate-800 font-bold text-lg mb-4"><i
                        class="fas fa-calendar-check text-purple-500 mr-2"></i>Permissions</h3>
                <div class="space-y-2">
                    @foreach ($permissions as $perm)
                        <div
                            class="flex items-center gap-4 p-4 rounded-xl {{ $perm->statut === 'en_attente' ? 'bg-amber-50 border border-amber-100' : ($perm->statut === 'valide' ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100') }}">
                            <div class="flex-1">
                                <p class="text-slate-800 font-medium text-sm">{{ $perm->date_debut->format('d/m/Y') }} →
                                    {{ $perm->date_fin->format('d/m/Y') }}</p>
                                <p class="text-slate-500 text-xs mt-0.5">{{ $perm->motif }}</p>
                                @if ($perm->commentaire_mentor)
                                    <p class="text-slate-400 text-xs mt-0.5 italic">Commentaire :
                                        {{ $perm->commentaire_mentor }}</p>
                                @endif
                            </div>
                            @php $pb = ['en_attente'=>'bg-amber-100 text-amber-700','valide'=>'bg-green-100 text-green-700','refuse'=>'bg-red-100 text-red-600']; @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $pb[$perm->statut] ?? '' }}">
                                {{ ucfirst($perm->statut) }}
                            </span>
                            @if ($perm->statut === 'en_attente')
                                <div class="flex gap-2">
                                    <form action="{{ route('mentor.presences.validerPermission', $perm) }}" method="POST"
                                        class="no-loader">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="valide">
                                        <button type="submit"
                                            class="w-8 h-8 bg-green-500 text-white rounded-lg flex items-center justify-center hover:bg-green-600 transition">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('mentor.presences.validerPermission', $perm) }}" method="POST"
                                        class="no-loader">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="refuse">
                                        <button type="submit"
                                            class="w-8 h-8 bg-red-500 text-white rounded-lg flex items-center justify-center hover:bg-red-600 transition">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                            @if ($perm->justificatif)
                                <a href="{{ asset('storage/' . $perm->justificatif) }}" download
                                    class="w-8 h-8 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-download text-xs"></i>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tableau présences -->
        <div class="card overflow-hidden" {{-- data-aos="fade-up" --}}>
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Historique présences ({{ $presences->total() }})</h3>
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
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($presences as $p)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-3 font-medium text-slate-800">
                                    {{ $p->date->translatedFormat('D d M Y') }}</td>
                                <td class="px-5 py-3">
                                    @php $sc = ['present'=>'bg-green-100 text-green-700','retard'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-600']; @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$p->statut] ?? '' }}">{{ ucfirst($p->statut) }}</span>
                                </td>
                                <td class="px-5 py-3 font-mono text-slate-600 text-sm">
                                    {{ $p->heure_arrivee ? substr($p->heure_arrivee, 0, 5) : '—' }}</td>
                                <td class="px-5 py-3 text-slate-500 text-xs">{{ $p->motif ?: '—' }}</td>
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

        <a href="{{ route('mentor.presences.index') }}"
            class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour
        </a>
    </div>
@endsection
