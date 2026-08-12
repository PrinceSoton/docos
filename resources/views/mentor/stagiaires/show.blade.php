@extends('layouts.mentor')
@section('titre', 'Profil Stagiaire')
@section('breadcrumb', 'Stagiaires > Profil')
@section('content')
    <div class="max-w-5xl mx-auto space-y-5" data-aos="fade-up">
        <!-- En-tête -->
        <div class="card p-0 overflow-hidden" data-aos="fade-up">
            <div class="h-28 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
            <div class="px-8 pb-6">
                <div class="flex items-end gap-5 -mt-12 flex-wrap">
                    <div
                        class="w-24 h-24 rounded-2xl border-4 border-white shadow-xl flex items-center justify-center overflow-hidden flex-shrink-0
                    bg-gradient-to-br from-emerald-400 to-teal-500">
                        @if ($stagiaire->user->photo)
                            <img src="{{ asset('storage/' . $stagiaire->user->photo) }}" class="w-full h-full object-cover">
                        @else
                            <span
                                class="text-white font-black text-3xl">{{ strtoupper(substr($stagiaire->user->prenom, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 mt-14">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h2 class="text-slate-800 font-black text-2xl">{{ $stagiaire->user->nom_complet }}</h2>
                            <span
                                class="font-mono text-xs bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg font-bold">{{ $stagiaire->matricule }}</span>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $stagiaire->statut === 'en_cours' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($stagiaire->statut) }}
                            </span>
                        </div>
                        <p class="text-slate-500 text-sm mt-1">{{ $stagiaire->user->email }} •
                            {{ $stagiaire->user->telephone ?: '—' }}</p>
                    </div>
                    @if ($stagiaire->cv)
                        <a href="{{ asset('storage/' . $stagiaire->cv) }}" download
                            class="mt-4 flex items-center gap-2 bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-blue-600 transition">
                            <i class="fas fa-download"></i>CV
                        </a>
                    @endif
                </div>

                <!-- Infos stage -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">École</p>
                        <p class="text-slate-700 font-semibold text-sm mt-1">{{ $stagiaire->ecole ?: '—' }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">Spécialité</p>
                        <p class="text-slate-700 font-semibold text-sm mt-1">{{ $stagiaire->specialite ?: '—' }}</p>
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-4">
                        <p class="text-xs text-emerald-400 uppercase font-medium">Début</p>
                        <p class="text-emerald-700 font-bold text-sm mt-1">{{ $stagiaire->date_debut->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="bg-teal-50 rounded-xl p-4">
                        <p class="text-xs text-teal-400 uppercase font-medium">Fin ({{ $stagiaire->dureeStageDays() }}j)
                        </p>
                        <p class="text-teal-700 font-bold text-sm mt-1">{{ $stagiaire->date_fin->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" data-aos="fade-up">
            @foreach ([[$stats['present'], 'Présences', 'green', 'user-check'], [$stats['retard'], 'Retards', 'amber', 'clock'], [$stats['absent'], 'Absences', 'red', 'user-times'], [$stats['rapports'], 'Rapports', 'indigo', 'file-alt']] as [$val, $label, $c, $icon])
                <div class="card p-5 text-center">
                    <div
                        class="w-10 h-10 bg-{{ $c }}-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-{{ $icon }} text-{{ $c }}-600"></i>
                    </div>
                    <p class="text-2xl font-black text-slate-800">{{ $val }}</p>
                    <p class="text-slate-400 text-xs mt-1">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <!-- Progression globale tâches -->
        <div class="card p-6" {{-- data-aos="fade-up" --}}>
            <div class="flex justify-between mb-2">
                <h3 class="text-slate-800 font-bold text-lg"><i class="fas fa-tasks text-emerald-500 mr-2"></i>Progression
                    des tâches</h3>
                <span class="text-emerald-600 font-black text-xl">{{ $progression }}%</span>
            </div>
            <div class="h-4 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-4 rounded-full bg-gradient-to-r from-emerald-400 to-teal-500 transition-all duration-1000"
                    style="width:{{ $progression }}%"></div>
            </div>
            <div class="grid grid-cols-3 gap-3 mt-4">
                @foreach ([['a_faire', 'À faire', 'slate'], ['en_cours', 'En cours', 'amber'], ['termine', 'Terminé', 'green']] as [$s, $l, $c])
                    <div class="bg-{{ $c }}-50 rounded-xl p-3 text-center">
                        <p class="text-2xl font-black text-{{ $c }}-600">
                            {{ $stagiaire->tasks->where('statut', $s)->count() }}</p>
                        <p class="text-xs text-slate-500">{{ $l }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Projets -->
            <div class="card p-6" {{-- data-aos="fade-right" --}}>
                <h3 class="text-slate-800 font-bold text-lg mb-4"><i
                        class="fas fa-project-diagram text-blue-500 mr-2"></i>Projets ({{ $stagiaire->projects->count() }})
                </h3>
                @forelse($stagiaire->projects as $p)
                    <div class="flex items-center gap-3 py-2.5 border-b border-slate-50 last:border-0">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-folder text-blue-500 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-slate-700 font-medium text-sm">{{ $p->titre }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <div class="flex-1 h-1.5 bg-slate-100 rounded-full">
                                    <div class="h-1.5 bg-blue-400 rounded-full"
                                        style="width:{{ $p->progressionPourcent() }}%"></div>
                                </div>
                                <span class="text-xs text-blue-600 font-semibold">{{ $p->progressionPourcent() }}%</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-6">Aucun projet</p>
                @endforelse
            </div>

            <!-- Rapports récents -->
            <div class="card p-6" {{-- data-aos="fade-left" --}}>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-slate-800 font-bold text-lg"><i
                            class="fas fa-file-alt text-purple-500 mr-2"></i>Rapports ({{ $stats['rapports'] }})</h3>
                    <a href="{{ route('mentor.reports.index') }}" class="text-emerald-600 text-sm hover:underline">Voir
                        tous →</a>
                </div>
                @forelse($stagiaire->reports->take(5) as $rapport)
                    <div class="flex items-center gap-3 py-2.5 border-b border-slate-50 last:border-0">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-alt text-purple-500 text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-700 font-medium text-sm truncate">{{ $rapport->titre }}</p>
                            <p class="text-slate-400 text-xs">{{ $rapport->created_at->format('d/m/Y') }}</p>
                        </div>
                        @php $sc = ['soumis'=>'bg-amber-100 text-amber-700','valide'=>'bg-green-100 text-green-700','rejete'=>'bg-red-100 text-red-600']; @endphp
                        <span
                            class="text-xs px-2 py-1 rounded-full {{ $sc[$rapport->statut] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($rapport->statut) }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-6">Aucun rapport</p>
                @endforelse
            </div>
        </div>

        <a href="{{ route('mentor.stagiaires.index') }}"
            class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour à mes stagiaires
        </a>
    </div>
@endsection
