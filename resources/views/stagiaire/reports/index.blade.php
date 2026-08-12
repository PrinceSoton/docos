@extends('layouts.stagiaire')
@section('titre', 'Mes Rapports')
@section('breadcrumb', 'Mon espace > Rapports')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Mes Rapports</h2>
                <p class="text-slate-500 text-sm">{{ $rapports->total() }} rapport(s) déposé(s)</p>
            </div>
            <a href="{{ route('stagiaire.reports.create') }}"
                class="flex items-center gap-2 {{-- bg-gradient-to-rfrom-indigo-600to-purple-600 --}} bg-gradient-to-r from-green-500 to-emerald-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i>Nouveau rapport
            </a>
        </div>

        <div class="card p-4 flex flex-wrap gap-3" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher par titre, type..."
                class="flex-1 min-w-48 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm">
            <select id="filterStatut"
                class="border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                <option value="">Tous statuts</option>
                <option value="soumis">Soumis</option>
                <option value="valide">Validé</option>
                <option value="rejete">Rejeté</option>
                <option value="en_revision">En révision</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="rapportGrid">
            @forelse($rapports as $rapport)
                <div class="card overflow-hidden rapport-card hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                    data-statut="{{ $rapport->statut }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                    @php
                        $sc = [
                            'soumis' => 'bg-amber-400',
                            'valide' => 'bg-green-400',
                            'rejete' => 'bg-red-400',
                            'en_revision' => 'bg-blue-400',
                        ];
                    @endphp
                    <div class="h-1.5 {{ $sc[$rapport->statut] ?? 'bg-slate-300' }}"></div>
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div>
                                <h3 class="font-bold text-slate-800">{{ $rapport->titre }}</h3>
                                {{-- <pclass="text-slate-400text-xsmt-0.5capitalize">$rapport->type </p> --}}
                            </div>
                            @php $sl = ['soumis'=>'bg-amber-100 text-amber-700','valide'=>'bg-green-100 text-green-700','rejete'=>'bg-red-100 text-red-600','en_revision'=>'bg-blue-100 text-blue-700']; @endphp
                            <span
                                class="px-2 py-1 rounded-lg text-xs font-semibold {{ $sl[$rapport->statut] ?? '' }} flex-shrink-0">
                                {{ ucfirst(str_replace('_', ' ', $rapport->statut)) }}
                            </span>
                        </div>

                        @if ($rapport->description)
                            <p class="text-slate-500 text-sm line-clamp-2 mb-3">{{ $rapport->description }}</p>
                        @endif

                        @if ($rapport->project)
                            <p class="text-xs text-indigo-600 font-medium mb-2">
                                <i class="fas fa-project-diagram mr-1"></i>{{ $rapport->project->titre }}
                            </p>
                        @endif

                        @if ($rapport->note !== null)
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-star text-amber-400"></i>
                                <span
                                    class="font-bold text-{{ $rapport->note >= 14 ? 'green' : ($rapport->note >= 10 ? 'amber' : 'red') }}-600">{{ $rapport->note }}/20</span>
                            </div>
                        @endif

                        <p class="text-slate-400 text-xs mb-4">{{ $rapport->created_at->format('d/m/Y à H:i') }}</p>

                        <div class="flex gap-2">
                            <a href="{{ route('stagiaire.reports.show', $rapport) }}"
                                class="flex-1 text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 py-2 rounded-xl text-sm font-medium transition">
                                <i class="fas fa-eye mr-1"></i>Voir
                            </a>
                            <a href="{{ route('stagiaire.reports.telecharger', $rapport) }}"
                                class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 rounded-xl text-sm font-medium transition">
                                <i class="fas fa-download mr-1"></i>Fichier
                            </a>
                            @if ($rapport->statut === 'soumis')
                                <a href="{{ route('stagiaire.reports.edit', $rapport) }}"
                                    class="w-9 h-9 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center transition">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('stagiaire.reports.destroy', $rapport) }}" method="POST"
                                    id="del-r-{{ $rapport->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                        class="btn-delete w-9 h-9 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl flex items-center justify-center transition"
                                        data-form="del-r-{{ $rapport->id }}">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 card p-16 text-center">
                    <i class="fas fa-file-alt text-5xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400 mb-4">Aucun rapport déposé</p>
                    <a href="{{ route('stagiaire.reports.create') }}"
                        class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-indigo-700 transition">
                        <i class="fas fa-plus"></i>Déposer mon premier rapport
                    </a>
                </div>
            @endforelse
        </div>
        <div>{{ $rapports->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        function filter() {
            const s = document.getElementById('search').value.toLowerCase();
            const st = document.getElementById('filterStatut').value;
            document.querySelectorAll('.rapport-card').forEach(c => {
                c.style.display = c.textContent.toLowerCase().includes(s) && (!st || c.dataset.statut === st) ? '' :
                    'none';
            });
        }
        document.getElementById('search').addEventListener('input', filter);
        document.getElementById('filterStatut').addEventListener('change', filter);
    </script>
@endpush
