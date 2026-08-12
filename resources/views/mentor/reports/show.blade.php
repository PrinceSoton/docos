@extends('layouts.mentor')
@section('titre', 'Rapport')
@section('breadcrumb', 'Rapports > Détail')
@section('content')
    <div class="max-w-4xl mx-auto space-y-5">
        <!-- En-tête rapport -->
        <div class="card p-6" data-aos="fade-up">
            <div class="flex items-start justify-between flex-wrap gap-4 mb-5">
                <div>
                    <h2 class="text-slate-800 font-black text-2xl">{{ $report->titre }}</h2>
                    <div class="flex items-center gap-3 mt-2 flex-wrap">
                        <span
                            class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-lg capitalize">{{ $report->type_affiche }}</span>
                        @php $sc = ['soumis'=>'bg-amber-100 text-amber-700','valide'=>'bg-green-100 text-green-700','rejete'=>'bg-red-100 text-red-600','en_revision'=>'bg-blue-100 text-blue-700']; @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sc[$report->statut] ?? '' }}">
                            {{ ucfirst(str_replace('_', ' ', $report->statut)) }}
                        </span>
                        @if ($report->note !== null)
                            <span
                                class="font-bold text-{{ $report->note >= 14 ? 'green' : ($report->note >= 10 ? 'amber' : 'red') }}-600 text-sm">
                                Note : {{ $report->note }}/20
                            </span>
                        @endif
                    </div>
                    <p class="text-slate-400 text-sm mt-1">
                        Déposé le {{ $report->created_at->format('d/m/Y à H:i') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('mentor.reports.telecharger', $report) }}"
                        class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-blue-700 transition hover:shadow-md">
                        <i class="fas fa-download"></i>Télécharger
                    </a>
                    @if ($report->statut === 'soumis' || $report->statut === 'en_revision')
                        <a href="{{ route('mentor.reports.evaluate', $report) }}"
                            class="flex items-center gap-2 bg-emerald-500 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-emerald-600 transition hover:shadow-md">
                            <i class="fas fa-star"></i>Évaluer
                        </a>
                    @endif
                </div>
            </div>

            <!-- Info stagiaire + projet -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-emerald-50 rounded-2xl">
                    <p class="text-xs text-emerald-400 uppercase font-medium mb-2">Stagiaire</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-emerald-200 flex items-center justify-center font-bold text-emerald-700 flex-shrink-0">
                            {{ strtoupper(substr($report->stagiaire->user->prenom ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm">{{ $report->stagiaire->user->nom_complet ?? '—' }}
                            </p>
                            <p class="text-slate-500 text-xs">{{ $report->stagiaire->matricule }}</p>
                        </div>
                    </div>
                </div>
                @if ($report->project)
                    <div class="p-4 bg-blue-50 rounded-2xl">
                        <p class="text-xs text-blue-400 uppercase font-medium mb-2">Projet lié</p>
                        <p class="font-bold text-slate-800 text-sm">{{ $report->project->titre }}</p>
                        <p class="text-slate-400 text-xs">{{ $report->project->progressionPourcent() }}% terminé</p>
                    </div>
                @endif
            </div>

            @if ($report->description)
                <div class="mt-4 p-4 bg-slate-50 rounded-xl">
                    <p class="text-xs text-slate-400 uppercase font-medium mb-1">Description</p>
                    <p class="text-slate-700 text-sm">{{ $report->description }}</p>
                </div>
            @endif

            @if ($report->commentaire_mentor)
                <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <p class="text-xs text-amber-500 uppercase font-medium mb-1"><i class="fas fa-comment mr-1"></i>Mon
                        commentaire</p>
                    <p class="text-slate-700 text-sm">{{ $report->commentaire_mentor }}</p>
                </div>
            @endif
        </div>

        <!-- Commentaires -->
        <div class="card p-6" data-aos="fade-up">
            <h3 class="text-slate-800 font-bold text-lg mb-4">
                <i class="fas fa-comments text-emerald-500 mr-2"></i>Commentaires ({{ $report->comments->count() }})
            </h3>

            <!-- Ajouter commentaire -->
            <form action="{{ route('mentor.reports.commenter', $report) }}" method="POST" class="mb-5">
                @csrf
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 flex items-center justify-center flex-shrink-0">
                        <span
                            class="text-white font-bold text-sm">{{ strtoupper(substr(Auth::user()->prenom, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1">
                        <textarea name="contenu" rows="2" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 transition text-sm resize-none"
                            placeholder="Ajouter un commentaire..."></textarea>
                        <button type="submit"
                            class="mt-2 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition hover:shadow-md">
                            <i class="fas fa-paper-plane mr-1"></i>Commenter
                        </button>
                    </div>
                </div>
            </form>

            <!-- Liste commentaires -->
            <div class="space-y-3">
                @forelse($report->comments as $comment)
                    <div class="flex gap-3 {{ $comment->user_id === Auth::id() ? '' : 'flex-row-reverse' }}">
                        <div
                            class="w-8 h-8 rounded-xl {{ $comment->user_id === Auth::id() ? 'bg-emerald-400' : 'bg-indigo-400' }} flex items-center justify-center flex-shrink-0">
                            <span
                                class="text-white font-bold text-xs">{{ strtoupper(substr($comment->user->prenom ?? '', 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 {{ $comment->user_id === Auth::id() ? '' : 'text-right' }}">
                            <div
                                class="inline-block max-w-xs {{ $comment->user_id === Auth::id() ? 'bg-emerald-50' : 'bg-slate-50' }} rounded-2xl px-4 py-3">
                                <p class="text-slate-500 text-xs font-medium mb-1">{{ $comment->user->nom_complet ?? '—' }}
                                </p>
                                <p class="text-slate-700 text-sm">{{ $comment->contenu }}</p>
                                <p class="text-slate-400 text-xs mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-6">Aucun commentaire</p>
                @endforelse
            </div>
        </div>

        <a href="{{ route('mentor.reports.index') }}"
            class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-800 font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Retour aux rapports
        </a>
    </div>
@endsection
