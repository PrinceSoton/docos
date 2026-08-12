@extends('layouts.admin')
@section('titre', 'Fiche stagiaire')
@section('breadcrumb', 'Stagiaires > Détail')
@section('content')
    <div class="max-w-5xl mx-auto space-y-5">
        <!-- En-tête -->
        <div class="card p-0 overflow-hidden" data-aos="fade-up">
            <div class="h-28 bg-indigo-500{{-- from-indigo-500  via-purple-600{{-- to-pink-500 --}}"></div>
            <div class="px-8 pb-6">
                <div class="flex items-end gap-5 -mt-12 flex-wrap">
                    <div
                        class="w-24 h-24 rounded-2xl border-4 border-white shadow-xl flex items-center justify-center overflow-hidden flex-shrink-0
                    bg-indigo-500">
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
                                class="font-mono text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-lg font-bold">{{ $stagiaire->matricule }}</span>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $stagiaire->statut === 'en_cours' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($stagiaire->statut) }}
                            </span>
                        </div>
                        <p class="text-slate-500 text-sm mt-1">{{ $stagiaire->user->email }} •
                            {{ $stagiaire->user->telephone ?: '—' }}</p>
                    </div>
                    <div class="flex gap-2 flex-wrap mt-4">
                        <a href="{{ route('admin.stagiaires.edit', $stagiaire) }}"
                            class="flex items-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-amber-600 transition">
                            <i class="fas fa-edit"></i>Modifier
                        </a>
                        @if ($stagiaire->cv)
                            <a href="{{ asset('storage/' . $stagiaire->cv) }}" download
                                class="flex items-center gap-2 bg-blue-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-blue-600 transition">
                                <i class="fas fa-download"></i>CV
                            </a>
                        @endif
                        <form action="{{ route('admin.stagiaires.destroy', $stagiaire) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button"
                                class="btn-delete flex items-center gap-2 bg-red-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-red-600 transition">
                                <i class="fas fa-trash"></i>Supprimer
                            </button>
                        </form>
                    </div>
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
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">Mentor</p>
                        <p class="text-slate-700 font-semibold text-sm mt-1">{{ $stagiaire->mentor?->nom_complet ?: '—' }}
                        </p>
                    </div>
                    <div class="bg-indigo-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">Durée de stage</p>
                        <p class="text-indigo-700 font-bold text-sm mt-1">{{ $stagiaire->dureeStageDays() }} jours</p>
                    </div>
                    <div class="bg-indigo-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">Début</p>
                        <p class="text-indigo-700 font-bold text-sm mt-1">{{ $stagiaire->date_debut->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-indigo-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">Fin</p>
                        <p class="text-purple-700 font-bold text-sm mt-1">{{ $stagiaire->date_fin->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-indigo-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">Présences</p>
                        <p class="text-green-700 font-bold text-sm mt-1">
                            {{ $stagiaire->presences->where('statut', 'present')->count() }} jours</p>
                    </div>
                    <div class="bg-indigo-50 rounded-xl p-4">
                        <p class="text-xs text-slate-400 uppercase font-medium">Absences</p>
                        <p class="text-red-700 font-bold text-sm mt-1">
                            {{ $stagiaire->presences->where('statut', 'absent')->count() }} jours</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Projets -->
            <div class="card p-6" {{-- data-aos="fade-right" --}}>
                <h3 class="text-slate-800 font-bold text-lg mb-4"><i
                        class="fas fa-project-diagram text-indigo-500 mr-2"></i>Projets
                    ({{ $stagiaire->projects->count() }})</h3>
                @forelse($stagiaire->projects as $projet)
                    <div class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-folder text-indigo-500 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-slate-700 font-medium text-sm">{{ $projet->titre }}</p>
                            <p class="text-slate-400 text-xs">{{ $projet->progressionPourcent() }}% terminé</p>
                        </div>
                        <span
                            class="text-xs px-2 py-1 rounded-full {{ $projet->statut === 'en_cours' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($projet->statut) }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-4">Aucun projet assigné</p>
                @endforelse
            </div>

            <!-- Rapports -->
            <div class="card p-6" {{-- data-aos="fade-left" --}}>
                <h3 class="text-slate-800 font-bold text-lg mb-4"><i
                        class="fas fa-file-alt text-purple-500 mr-2"></i>Rapports ({{ $stagiaire->reports->count() }})</h3>
                @forelse($stagiaire->reports->take(5) as $rapport)
                    <div class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-alt text-purple-500 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-slate-700 font-medium text-sm">{{ $rapport->titre }}</p>
                            <p class="text-slate-400 text-xs">{{ $rapport->created_at->format('d/m/Y') }}</p>
                        </div>
                        <a href="{{ route('admin.presences.index', ['stagiaire_id' => $stagiaire->id]) }}"
                            class="text-indigo-600 text-xs hover:underline">
                            <span
                                class="px-2 py-1 rounded-full text-xs {{ $rapport->statut === 'valide' ? 'bg-green-100 text-green-700' : ($rapport->statut === 'soumis' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                                {{ ucfirst($rapport->statut) }}
                            </span>
                        </a>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-4">Aucun rapport</p>
                @endforelse
            </div>

            <!-- Attestations -->
            <div class="card p-6" {{-- data-aos="fade-right" --}}>
                <h3 class="text-slate-800 font-bold text-lg mb-4"><i
                        class="fas fa-certificate text-amber-500 mr-2"></i>Attestations
                    ({{ $stagiaire->attestations->count() }})</h3>
                @forelse($stagiaire->attestations as $att)
                    <div class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-certificate text-amber-500 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-slate-700 font-medium text-sm capitalize">{{ $att->type }}</p>
                            <p class="text-slate-400 text-xs">{{ $att->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="text-xs px-2 py-1 rounded-full {{ $att->statut === 'envoye' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ ucfirst(str_replace('_', ' ', $att->statut)) }}
                            </span>
                            @if ($att->fichier)
                                <a href="{{ route('admin.attestations.telecharger', $att) }}"
                                    class="w-6 h-6 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded flex items-center justify-center transition">
                                    <i class="fas fa-download text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-4">Aucune demande</p>
                @endforelse
            </div>

            <!-- Tâches -->
            <div class="card p-6" {{-- data-aos="fade-left" --}}>
                <h3 class="text-slate-800 font-bold text-lg mb-4"><i class="fas fa-tasks text-emerald-500 mr-2"></i>Tâches
                    ({{ $stagiaire->tasks->count() }})</h3>
                @php
                    $total = $stagiaire->tasks->count();
                    $terminees = $stagiaire->tasks->where('statut', 'termine')->count();
                    $pct = $total > 0 ? round(($terminees / $total) * 100) : 0;
                @endphp
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-slate-600 mb-1">
                        <span>Progression globale</span>
                        <span class="font-bold text-indigo-600">{{ $pct }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all"
                            style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-2xl font-black text-slate-700">
                            {{ $stagiaire->tasks->where('statut', 'a_faire')->count() }}</p>
                        <p class="text-xs text-slate-400">À faire</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-3">
                        <p class="text-2xl font-black text-amber-600">
                            {{ $stagiaire->tasks->where('statut', 'en_cours')->count() }}</p>
                        <p class="text-xs text-slate-400">En cours</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3">
                        <p class="text-2xl font-black text-green-600">{{ $terminees }}</p>
                        <p class="text-xs text-slate-400">Terminées</p>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.stagiaires.index') }}"
            class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour à la liste
        </a>
    </div>
@endsection
