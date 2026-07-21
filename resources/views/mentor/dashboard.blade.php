@extends('layouts.mentor')
@section('titre', 'Tableau de bord Mentor')
@section('content')
    <div class="space-y-6">

        <!-- Bienvenue -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-3xl p-8 text-white relative overflow-hidden"
            data-aos="fade-down">
            <div class="absolute right-0 top-0 w-72 h-72 bg-white/5 rounded-full -translate-y-1/3 translate-x-1/3"></div>
            <div class="absolute left-0 bottom-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/3 -translate-x-1/3"></div>
            <div class="relative z-10">
                <p class="text-emerald-200 text-sm font-medium mb-1">{{ now()->translatedFormat('l d F Y') }}</p>
                <h2 class="text-white font-black text-3xl mb-2">Bonjour, {{ Auth::user()->prenom }} 👋</h2>
                <p class="text-emerald-200">Vous encadrez <span class="text-white font-bold">{{ $stats['stagiaires'] }}</span>
                    stagiaire(s) actuellement</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach ([[$stats['stagiaires'], 'Stagiaires', 'fas fa-user-graduate', 'from-emerald-400 to-teal-500'], [$stats['projets'], 'Projets', 'fas fa-project-diagram', 'from-blue-400 to-indigo-500'], [$stats['rapports_soumis'], 'Rapports à évaluer', 'fas fa-file-alt', 'from-amber-400 to-orange-500'], [$stats['permissions_en_attente'], 'Permissions en attente', 'fas fa-calendar-check', 'from-purple-400 to-pink-500'], [$stats['attestations_a_valider'], 'Attestations à valider', 'fas fa-certificate', 'from-rose-400 to-red-500']] as $i => [$val, $label, $icon, $grad])
                <div class="bg-gradient-to-br {{ $grad }} text-white rounded-2xl p-5 relative overflow-hidden card"
                    data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                    <div class="absolute right-2 top-2 opacity-20"><i class="{{ $icon }} text-3xl"></i></div>
                    <p class="text-white/80 text-xs font-medium leading-tight">{{ $label }}</p>
                    <p class="text-white font-black text-3xl mt-1">{{ $val }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Avancement stagiaires -->
            <div class="card p-6" data-aos="fade-right">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-slate-800 font-bold text-lg">
                        <i class="fas fa-chart-line text-emerald-500 mr-2"></i>Avancement de mes stagiaires
                    </h3>
                    <a href="{{ route('mentor.stagiaires.index') }}" class="text-emerald-600 text-sm hover:underline">Voir
                        tous →</a>
                </div>
                @forelse($avancementStagiaires as $stag)
                    <div class="mb-5 last:mb-0">
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center flex-shrink-0">
                                @if ($stag->user->photo)
                                    <img src="{{ asset('storage/' . $stag->user->photo) }}"
                                        class="w-full h-full rounded-xl object-cover">
                                @else
                                    <span
                                        class="text-white font-bold text-sm">{{ strtoupper(substr($stag->user->prenom, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <p class="text-slate-800 font-semibold text-sm">{{ $stag->user->nom_complet }}</p>
                                    <span class="text-emerald-600 font-bold text-sm">{{ $stag->progression }}%</span>
                                </div>
                                <p class="text-slate-400 text-xs">{{ $stag->matricule }}</p>
                            </div>
                        </div>
                        <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden ml-12">
                            <div class="h-2.5 rounded-full transition-all duration-1000
                        {{ $stag->progression >= 75 ? 'bg-gradient-to-r from-emerald-400 to-green-500' : ($stag->progression >= 40 ? 'bg-gradient-to-r from-amber-400 to-orange-400' : 'bg-gradient-to-r from-red-400 to-pink-400') }}"
                                style="width:{{ $stag->progression }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-user-graduate text-4xl text-slate-200 mb-3"></i>
                        <p class="text-slate-400 text-sm">Aucun stagiaire assigné</p>
                    </div>
                @endforelse
            </div>

            <div class="space-y-5">
                <!-- Rapports à évaluer -->
                <div class="card p-6" data-aos="fade-left">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-slate-800 font-bold"><i class="fas fa-file-alt text-amber-500 mr-2"></i>Rapports à
                            évaluer</h3>
                        <a href="{{ route('mentor.reports.index') }}" class="text-emerald-600 text-sm hover:underline">Voir
                            tous →</a>
                    </div>
                    @forelse($rapportsRecents as $rapport)
                        <div class="flex items-center gap-3 py-2.5 border-b border-slate-50 last:border-0">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-alt text-amber-600 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-slate-700 font-medium text-sm truncate">{{ $rapport->titre }}</p>
                                <p class="text-slate-400 text-xs">{{ $rapport->stagiaire->user->nom_complet ?? '—' }}</p>
                            </div>
                            <a href="{{ route('mentor.reports.show', $rapport) }}"
                                class="text-xs bg-amber-100 hover:bg-amber-200 text-amber-700 px-3 py-1 rounded-lg font-medium transition">
                                Évaluer
                            </a>
                        </div>
                    @empty
                        <p class="text-slate-400 text-sm text-center py-4">Aucun rapport en attente</p>
                    @endforelse
                </div>

                <!-- Permissions en attente -->
                <div class="card p-6" data-aos="fade-left" data-aos-delay="100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-slate-800 font-bold"><i
                                class="fas fa-calendar-check text-purple-500 mr-2"></i>Permissions à valider</h3>
                        <a href="{{ route('mentor.presences.index') }}"
                            class="text-emerald-600 text-sm hover:underline">Voir →</a>
                    </div>
                    @forelse($permissionsEnAttente as $perm)
                        <div class="p-3 bg-purple-50 rounded-xl mb-2 last:mb-0">
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <p class="text-slate-800 font-semibold text-sm">
                                        {{ $perm->stagiaire->user->nom_complet ?? '—' }}</p>
                                    <p class="text-slate-500 text-xs">{{ $perm->date_debut->format('d/m/Y') }} →
                                        {{ $perm->date_fin->format('d/m/Y') }}</p>
                                    <p class="text-slate-400 text-xs mt-0.5 truncate">{{ $perm->motif }}</p>
                                </div>
                                <div class="flex gap-1.5 flex-shrink-0">
                                    <form action="{{ route('mentor.presences.validerPermission', $perm) }}" method="POST"
                                        class="no-loader">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="valide">
                                        <button type="submit"
                                            class="w-8 h-8 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg flex items-center justify-center transition"
                                            title="Valider">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('mentor.presences.validerPermission', $perm) }}" method="POST"
                                        class="no-loader">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="refuse">
                                        <button type="submit"
                                            class="w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition"
                                            title="Refuser">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-sm text-center py-4">Aucune permission en attente</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card p-6"> {{-- data-aos="fade-up" --}}
            <h3 class="text-slate-800 font-bold text-lg mb-4"><i class="fas fa-bolt text-amber-500 mr-2"></i>Raccourci pour
                la création des modules
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach ([[route('mentor.projects.create'), 'fas fa-plus-circle', 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100', 'Créer un projet'], [route('mentor.tasks.create'), 'fas fa-tasks', 'bg-blue-50 text-blue-700 hover:bg-blue-100', 'Créer une tâche'], [route('mentor.reports.index'), 'fas fa-file-alt', 'bg-amber-50 text-amber-700 hover:bg-amber-100', 'Voir les rapports'], [route('mentor.stagiaires.index'), 'fas fa-user-graduate', 'bg-purple-50 text-purple-700 hover:bg-purple-100', 'Mes stagiaires']] as [$route, $icon, $cls, $label])
                    <a href="{{ $route }}"
                        class="{{ $cls }} flex flex-col items-center gap-2 py-5 rounded-2xl font-medium text-sm transition-all hover:-translate-y-1 hover:shadow-md">
                        <i class="{{ $icon }} text-2xl"></i>{{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
