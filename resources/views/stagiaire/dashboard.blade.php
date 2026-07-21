@extends('layouts.stagiaire')
@section('titre', 'Mon Tableau de bord')
@section('content')
    <div class="space-y-6">

        <!-- Bienvenue -->
        <div class="bg-gradient-to-r from-slate-800 via-indigo-900 to-slate-900 rounded-3xl p-8 text-white relative overflow-hidden"
            data-aos="fade-down">
            <div class="absolute right-0 top-0 w-80 h-80 bg-amber-500/10 rounded-full -translate-y-1/3 translate-x-1/3">
            </div>
            <div class="absolute left-0 bottom-0 w-48 h-48 bg-indigo-500/10 rounded-full translate-y-1/3 -translate-x-1/3">
            </div>
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <p class="text-amber-300 text-sm font-medium mb-1">{{ now()->translatedFormat('l d F Y') }}</p>
                    <h2 class="text-white font-black text-3xl mb-2">Bienvenue, {{ Auth::user()->prenom }} </h2>
                    <p class="text-slate-300">Matricule : <span
                            class="font-mono font-bold text-amber-400">{{ $stagiaire->matricule }}</span></p>
                    @if ($stagiaire->mentor)
                        <p class="text-slate-400 text-sm mt-1">
                            <i class="fas fa-chalkboard-teacher mr-1 text-emerald-400"></i>
                            Mentor : <span class="text-white font-medium">{{ $stagiaire->mentor->nom_complet }}</span>
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-slate-400 text-xs">Fin de stage dans</p>
                    <p class="text-4xl font-black text-amber-400">{{ $joursRestants }}</p>
                    <p class="text-slate-400 text-xs">jours</p>
                </div>
            </div>

            <!-- Progression stage -->
            @php
                $total = $stagiaire->date_debut->diffInDays($stagiaire->date_fin);
                $ecoule = min($stagiaire->date_debut->diffInDays(now()), $total);
                $pct = $total > 0 ? round(($ecoule / $total) * 100) : 0;
            @endphp
            <div class="relative z-10 mt-5">
                <div class="flex justify-between text-xs text-slate-400 mb-1">
                    <span>{{ $stagiaire->date_debut->format('d/m/Y') }}</span>
                    <span class="text-amber-400 font-bold">{{ $pct }}% du stage effectué</span>
                    <span>{{ $stagiaire->date_fin->format('d/m/Y') }}</span>
                </div>
                <div class="h-2.5 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-2.5 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full transition-all duration-1000"
                        style="width:{{ $pct }}%"></div>
                </div>
            </div>
        </div>

        <!-- Stats principales -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([[$stats['present'], 'Présences', 'from-green-400 to-emerald-500', 'user-check'], [$stats['retard'], 'Retards', 'from-amber-400 to-orange-500', 'clock'], [$stats['absent'], 'Absences', 'from-red-400 to-pink-500', 'user-times'], [$stats['rapports'], 'Mes rapports', 'from-indigo-400 to-purple-500', 'file-alt']] as $i => [$val, $label, $grad, $icon])
                <div class="bg-gradient-to-br {{ $grad }} rounded-2xl p-5 text-white relative overflow-hidden card"
                    data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                    <div class="absolute right-2 top-2 opacity-20"><i class="fas fa-{{ $icon }} text-3xl"></i>
                    </div>
                    <p class="text-white/80 text-xs font-medium">{{ $label }}</p>
                    <p class="text-white font-black text-3xl mt-1">{{ $val }}</p>
                </div>
            @endforeach
        </div>

        <!-- Tâches résumé avec Kanban -->
        <div class="card p-6" data-aos="fade-up">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-slate-800 font-bold text-lg"><i class="fas fa-tasks text-amber-500 mr-2"></i>Mes tâches</h3>
                <a href="{{ route('stagiaire.tasks.index') }}"
                    class="text-amber-600 text-sm hover:underline font-medium">Voir tout →</a>
            </div>
            <div class="grid grid-cols-3 gap-4">
                @foreach ([['a_faire', $stats['a_faire'], 'À faire', 'slate', 'clock'], ['en_cours', $stats['en_cours'], 'En cours', 'amber', 'spinner'], ['terminees', $stats['terminees'], 'Terminées', 'green', 'check-circle']] as [$key, $val, $label, $color, $icon])
                    <div
                        class="bg-{{ $color }}-50 border border-{{ $color }}-100 rounded-2xl p-5 text-center">
                        <div
                            class="w-10 h-10 bg-{{ $color }}-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-{{ $icon }} text-{{ $color }}-600"></i>
                        </div>
                        <p class="text-3xl font-black text-{{ $color }}-600">{{ $val }}</p>
                        <p class="text-slate-500 text-sm mt-1">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
            <!-- Barre progression tâches -->
            @php $totalT = $stats['a_faire']+$stats['en_cours']+$stats['terminees']; @endphp
            @if ($totalT > 0)
                <div class="mt-4">
                    <div class="flex justify-between text-xs text-slate-500 mb-1">
                        <span>Progression des tâches</span>
                        <span class="font-bold text-amber-600">{{ $progression }}%</span>
                    </div>
                    <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-3 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full transition-all duration-1000"
                            style="width:{{ $progression }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Tâches récentes -->
            <div class="card p-6" {{-- data-aos="fade-right" --}}>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-slate-800 font-bold"><i class="fas fa-list text-indigo-500 mr-2"></i>Tâches récentes
                    </h3>
                    <a href="{{ route('stagiaire.tasks.index') }}" class="text-amber-600 text-sm hover:underline">Voir
                        →</a>
                </div>
                @forelse($tachesRecentes as $tache)
                    <div class="flex items-center gap-3 py-2.5 border-b border-slate-50 last:border-0">
                        @php $pc = ['a_faire'=>'bg-slate-100 text-slate-500','en_cours'=>'bg-amber-100 text-amber-600','termine'=>'bg-green-100 text-green-600']; @endphp
                        <span
                            class="w-2.5 h-2.5 rounded-full flex-shrink-0
                    {{ $tache->statut === 'a_faire' ? 'bg-slate-400' : ($tache->statut === 'en_cours' ? 'bg-amber-400' : 'bg-green-400') }}"></span>
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-700 font-medium text-sm truncate">{{ $tache->titre }}</p>
                            <p class="text-slate-400 text-xs">{{ $tache->project->titre ?? '—' }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $pc[$tache->statut] ?? '' }}">
                            {{ ['a_faire' => 'À faire', 'en_cours' => 'En cours', 'termine' => 'Terminé'][$tache->statut] ?? $tache->statut }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-6">Aucune tâche assignée</p>
                @endforelse
            </div>

            <!-- Rapports récents -->
            <div class="card p-6" {{-- data-aos="fade-left" --}}>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-slate-800 font-bold"><i class="fas fa-file-alt text-purple-500 mr-2"></i>Mes rapports
                    </h3>
                    <a href="{{ route('stagiaire.reports.index') }}" class="text-amber-600 text-sm hover:underline">Voir
                        →</a>
                </div>
                @forelse($rapportsRecents as $rapport)
                    <div class="flex items-center gap-3 py-2.5 border-b border-slate-50 last:border-0">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-alt text-purple-500 text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-700 font-medium text-sm truncate">{{ $rapport->titre }}</p>
                            <p class="text-slate-400 text-xs">{{ $rapport->created_at->format('d/m/Y') }}</p>
                        </div>
                        @php $sr = ['soumis'=>'bg-amber-100 text-amber-700','valide'=>'bg-green-100 text-green-700','rejete'=>'bg-red-100 text-red-600','en_revision'=>'bg-blue-100 text-blue-700']; @endphp
                        <span
                            class="text-xs px-2 py-1 rounded-full {{ $sr[$rapport->statut] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($rapport->statut) }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-6">Aucun rapport déposé</p>
                @endforelse
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card p-6" {{--  data-aos="fade-up" --}}>
            <h3 class="text-slate-800 font-bold text-lg mb-4"><i class="fas fa-bolt text-amber-500 mr-2"></i>Raccourci pour
                la créationdes modules
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach ([[route('stagiaire.presence.index'), 'fas fa-user-check', 'bg-green-50 text-green-700 hover:bg-green-100', 'Marquer présence'], [route('stagiaire.reports.create'), 'fas fa-file-upload', 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100', 'Déposer rapport'], [route('stagiaire.attestations.request'), 'fas fa-certificate', 'bg-amber-50 text-amber-700 hover:bg-amber-100', 'Attestation'], [route('stagiaire.tasks.index'), 'fas fa-tasks', 'bg-purple-50 text-purple-700 hover:bg-purple-100', 'Mes tâches']] as [$route, $icon, $cls, $label])
                    <a href="{{ $route }}"
                        class="{{ $cls }} flex flex-col items-center gap-2 py-5 rounded-2xl font-medium text-sm transition-all hover:-translate-y-1 hover:shadow-md">
                        <i class="{{ $icon }} text-2xl"></i>{{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
