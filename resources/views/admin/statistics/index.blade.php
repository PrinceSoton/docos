@extends('layouts.admin')
@section('titre', 'Statistiques')
@section('breadcrumb', 'Système > Statistiques')
@section('content')
    <div class="space-y-6">
        <div data-aos="fade-down">
            <h2 class="text-slate-800 font-bold text-2xl">Statistiques globales</h2>
            <p class="text-slate-500 text-sm">Vue d'ensemble de l'activité de l'entreprise</p>
        </div>

        <!-- KPIs principaux -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([['bg-gradient-to-br from-indigo-500 to-blue-600', 'fas fa-users', $stats['users'], 'Utilisateurs totaux'], ['bg-gradient-to-br from-emerald-500 to-teal-600', 'fas fa-user-graduate', $stats['stagiaires_actifs'], 'Stagiaires actifs'], ['bg-gradient-to-br from-purple-500 to-pink-600', 'fas fa-project-diagram', $stats['projets_en_cours'], 'Projets en cours'], ['bg-gradient-to-br from-amber-500 to-orange-600', 'fas fa-file-alt', $stats['rapports_valides'], 'Rapports validés']] as $i => [$grad, $icon, $val, $label])
                <div class="{{ $grad }} text-white rounded-2xl p-6 relative overflow-hidden card" data-aos="fade-up"
                    data-aos-delay="{{ $i * 75 }}">
                    <div class="absolute right-3 top-3 opacity-20"><i class="{{ $icon }} text-4xl"></i></div>
                    <p class="text-white/80 text-sm font-medium">{{ $label }}</p>
                    <p class="text-white font-black text-4xl mt-1">{{ $val }}</p>
                </div>
            @endforeach
        </div>

        <!-- Stats secondaires -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ([[$stats['stagiaires'], 'Total stagiaires', 'fas fa-user-graduate', 'indigo'], [$stats['stagiaires_fin'], 'Stages terminés', 'fas fa-graduation-cap', 'green'], [$stats['mentors'], 'Mentors', 'fas fa-chalkboard-teacher', 'emerald'], [$stats['projets'], 'Total projets', 'fas fa-project-diagram', 'purple'], [$stats['taches'], 'Total tâches', 'fas fa-tasks', 'blue'], [$stats['taches_terminees'], 'Tâches terminées', 'fas fa-check-circle', 'green'], [$stats['absences'], 'Total absences', 'fas fa-user-times', 'red'], [$stats['attestations_envoyees'], 'Attestations envoyées', 'fas fa-certificate', 'amber']] as $i => [$val, $label, $icon, $color])
                <div class="card p-5 text-center" data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
                    <div
                        class="w-10 h-10 bg-{{ $color }}-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="{{ $icon }} text-{{ $color }}-600"></i>
                    </div>
                    <p class="text-2xl font-black text-slate-800">{{ $val }}</p>
                    <p class="text-slate-400 text-xs mt-1">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Présences par mois -->
            <div class="card p-6"> {{--  data-aos="fade-right"> --}}
                <h3 class="text-slate-800 font-bold text-lg mb-5">
                    <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Présences sur 6 mois
                </h3>
                <div class="space-y-4">
                    @foreach ($presencesParMois as $mois)
                        <div>
                            <div class="flex justify-between text-xs text-slate-500 mb-1">
                                <span class="font-medium">{{ $mois['mois'] }}</span>
                                <div class="flex gap-3">
                                    <span class="text-green-600"><i
                                            class="fas fa-check mr-0.5"></i>{{ $mois['present'] }}</span>
                                    <span class="text-amber-600"><i
                                            class="fas fa-clock mr-0.5"></i>{{ $mois['retard'] }}</span>
                                    <span class="text-red-600"><i
                                            class="fas fa-times mr-0.5"></i>{{ $mois['absent'] }}</span>
                                </div>
                            </div>
                            @php $total = $mois['present'] + $mois['retard'] + $mois['absent']; @endphp
                            <div class="flex h-3 rounded-full overflow-hidden bg-slate-100">
                                @if ($total > 0)
                                    <div class="bg-green-400 transition-all"
                                        style="width:{{ round(($mois['present'] / $total) * 100) }}%"></div>
                                    <div class="bg-amber-400 transition-all"
                                        style="width:{{ round(($mois['retard'] / $total) * 100) }}%"></div>
                                    <div class="bg-red-400 transition-all"
                                        style="width:{{ round(($mois['absent'] / $total) * 100) }}%"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex gap-4 mt-4 text-xs text-slate-500">
                    <span class="flex items-center gap-1"><span
                            class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>Présent</span>
                    <span class="flex items-center gap-1"><span
                            class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>Retard</span>
                    <span class="flex items-center gap-1"><span
                            class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>Absent</span>
                </div>
            </div>

            <!-- Stagiaires par mentor -->
            <div class="card p-6"> {{-- data-aos="fade-left" --}}
                <h3 class="text-slate-800 font-bold text-lg mb-5">
                    <i class="fas fa-sitemap text-emerald-500 mr-2"></i>Stagiaires par mentor
                </h3>
                <div class="space-y-3">
                    @foreach ($stagiaresParMentor as $mentor)
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center flex-shrink-0">
                                <span
                                    class="text-white font-bold text-xs">{{ strtoupper(substr($mentor->user->prenom, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-slate-700">{{ $mentor->user->nom_complet }}</span>
                                    <span class="font-bold text-emerald-600">{{ $mentor->stagiaires_count }}</span>
                                </div>
                                @php $maxStag = $stagiaresParMentor->max('stagiaires_count') ?: 1; @endphp
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-2 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full transition-all"
                                        style="width:{{ round(($mentor->stagiaires_count / $maxStag) * 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if ($stagiaresParMentor->isEmpty())
                        <p class="text-slate-400 text-sm text-center py-6">Aucun mentor</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Résumé présences -->
        <div class="card p-6"> {{-- data-aos="fade-up"> --}}
            <h3 class="text-slate-800 font-bold text-lg mb-5"><i class="fas fa-calendar-check text-blue-500 mr-2"></i>Résumé
                général des présences</h3>
            <div class="grid grid-cols-3 gap-6 text-center">
                @php $totalP = $stats['presences_total'] ?: 1; @endphp
                <div class="p-5 bg-green-50 rounded-2xl">
                    <p class="text-4xl font-black text-green-600">
                        {{ $stats['presences_total'] - $stats['absences'] - $stats['retards'] }}</p>
                    <p class="text-slate-500 text-sm mt-1">Présences effectives</p>
                    <p class="text-green-500 text-xs font-semibold">
                        {{ round((($stats['presences_total'] - $stats['absences'] - $stats['retards']) / $totalP) * 100) }}%
                    </p>
                </div>
                <div class="p-5 bg-amber-50 rounded-2xl">
                    <p class="text-4xl font-black text-amber-600">{{ $stats['retards'] }}</p>
                    <p class="text-slate-500 text-sm mt-1">Retards enregistrés</p>
                    <p class="text-amber-500 text-xs font-semibold">{{ round(($stats['retards'] / $totalP) * 100) }}%</p>
                </div>
                <div class="p-5 bg-red-50 rounded-2xl">
                    <p class="text-4xl font-black text-red-600">{{ $stats['absences'] }}</p>
                    <p class="text-slate-500 text-sm mt-1">Absences enregistrées</p>
                    <p class="text-red-500 text-xs font-semibold">{{ round(($stats['absences'] / $totalP) * 100) }}%</p>
                </div>
            </div>
        </div>
    </div>
@endsection
