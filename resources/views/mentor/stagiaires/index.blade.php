@extends('layouts.mentor')
@section('titre', 'Mes Stagiaires')
@section('breadcrumb', 'Mentor > Mes stagiaires')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Mes Stagiaires</h2>
                <p class="text-slate-500 text-sm">{{ $stagiaires->total() }} stagiaire(s) sous votre encadrement</p>
            </div>
        </div>

        <!-- Recherche -->
        <div class="card p-4" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher par nom, matricule, école..."
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 text-sm transition">
        </div>

        <!-- Grille stagiaires -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="stagGrid">
            @forelse($stagiaires as $stag)
                <div class="card overflow-hidden stag-card hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                    data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                    <!-- Header carte -->
                    <div class="h-16 bg-gradient-to-r from-emerald-500 to-teal-600 relative">
                        <div class="absolute bottom-0 left-5 translate-y-1/2">
                            <div
                                class="w-12 h-12 rounded-xl border-3 border-white bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-lg overflow-hidden">
                                @if ($stag->user->photo)
                                    <img src="{{ asset('storage/' . $stag->user->photo) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <span
                                        class="text-white font-black text-lg">{{ strtoupper(substr($stag->user->prenom, 0, 1)) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="absolute top-2 right-3">
                            <span
                                class="px-2 py-1 rounded-lg text-xs font-semibold {{ $stag->statut === 'en_cours' ? 'bg-white/20 text-white' : 'bg-white/10 text-white/70' }}">
                                {{ $stag->statut === 'en_cours' ? 'En cours' : ucfirst($stag->statut) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-9 px-5 pb-5">
                        <h3 class="text-slate-800 font-bold">{{ $stag->user->nom_complet }}</h3>
                        <p class="text-slate-400 text-xs font-mono">{{ $stag->matricule }}</p>

                        <div class="mt-3 space-y-1.5 text-sm">
                            <div class="flex items-center gap-2 text-slate-600">
                                <i class="fas fa-graduation-cap text-emerald-400 w-4"></i>
                                <span>{{ $stag->ecole ?: '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-600">
                                <i class="fas fa-code-branch text-emerald-400 w-4"></i>
                                <span>{{ $stag->specialite ?: '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-600">
                                <i class="fas fa-calendar-alt text-emerald-400 w-4"></i>
                                <span>{{ $stag->date_debut->format('d/m/Y') }} →
                                    {{ $stag->date_fin->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <!-- Progression tâches -->
                        @php
                            $totalT = $stag->tasks->count();
                            $doneT = $stag->tasks->where('statut', 'termine')->count();
                            $pct = $totalT > 0 ? round(($doneT / $totalT) * 100) : 0;
                        @endphp
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-slate-500 mb-1">
                                <span>Progression</span>
                                <span
                                    class="font-bold {{ $pct >= 75 ? 'text-emerald-600' : ($pct >= 40 ? 'text-amber-600' : 'text-red-500') }}">{{ $pct }}%</span>
                            </div>
                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-700
                            {{ $pct >= 75 ? 'bg-gradient-to-r from-emerald-400 to-green-500' : ($pct >= 40 ? 'bg-gradient-to-r from-amber-400 to-orange-400' : 'bg-gradient-to-r from-red-400 to-pink-400') }}"
                                    style="width:{{ $pct }}%"></div>
                            </div>
                        </div>

                        <!-- Mini stats -->
                        <div class="grid grid-cols-3 gap-2 mt-4">
                            <div class="text-center bg-green-50 rounded-xl py-2">
                                <p class="font-bold text-green-600 text-sm">
                                    {{ $stag->presences->where('statut', 'present')->count() }}</p>
                                <p class="text-xs text-slate-400">Présent</p>
                            </div>
                            <div class="text-center bg-amber-50 rounded-xl py-2">
                                <p class="font-bold text-amber-600 text-sm">{{ $stag->reports->count() }}</p>
                                <p class="text-xs text-slate-400">Rapports</p>
                            </div>
                            <div class="text-center bg-indigo-50 rounded-xl py-2">
                                <p class="font-bold text-indigo-600 text-sm">{{ $totalT }}</p>
                                <p class="text-xs text-slate-400">Tâches</p>
                            </div>
                        </div>

                        <a href="{{ route('mentor.stagiaires.show', $stag) }}"
                            class="mt-4 w-full flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-2.5 rounded-xl font-medium text-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
                            <i class="fas fa-eye"></i>Voir le profil complet
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 card p-16 text-center">
                    <i class="fas fa-user-graduate text-5xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400">Aucun stagiaire assigné</p>
                </div>
            @endforelse
        </div>

        <div>{{ $stagiaires->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.stag-card').forEach(card => {
                card.style.display = card.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
@endpush
