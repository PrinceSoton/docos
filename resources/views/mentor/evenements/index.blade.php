@extends('layouts.mentor')
@section('titre', 'Événements')
@section('breadcrumb', 'Mentor > Événements')
@section('content')
    <div class="space-y-5">
        <div data-aos="fade-down">
            <h2 class="text-slate-800 font-bold text-2xl">Événements & Informations</h2>
            <p class="text-slate-500 text-sm">Publications partagées par l'administration</p>
        </div>

        <div class="card p-4 flex flex-wrap gap-3" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher un événement..."
                class="flex-1 min-w-48 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-400 text-sm">
            <select id="filterType"
                class="border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-400">
                <option value="">Tous types</option>
                <option value="information">Information</option>
                <option value="evenement">Événement</option>
                <option value="note">Note</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="evtGrid">
            @forelse($evenements as $evt)
                <div class="card overflow-hidden evt-card hover:shadow-xl transition-all hover:-translate-y-1 duration-300"
                    data-type="{{ $evt->type }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                    @if ($evt->image)
                        <div class="h-36 overflow-hidden">
                            <img src="{{ asset('storage/' . $evt->image) }}"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                        </div>
                    @endif
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                            @php $tc = ['information'=>'bg-blue-100 text-blue-700','evenement'=>'bg-purple-100 text-purple-700','note'=>'bg-amber-100 text-amber-700']; @endphp
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $tc[$evt->type] ?? '' }}">
                                {{ ucfirst($evt->type) }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $evt->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="font-bold text-slate-800 mb-2">{{ $evt->titre }}</h3>
                        @if ($evt->contenu)
                            <p class="text-slate-500 text-sm line-clamp-2">{{ Str::limit(strip_tags($evt->contenu), 80) }}
                            </p>
                        @endif
                        @if ($evt->date_evenement)
                            <p class="text-xs text-indigo-600 font-medium mt-2">
                                <i class="fas fa-calendar mr-1"></i>{{ $evt->date_evenement->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                        <div class="flex items-center gap-2 mt-4">
                            <a href="{{ route('mentor.evenements.show', $evt) }}"
                                class="flex-1 text-center bg-emerald-50 hover:bg-emerald-100 text-emerald-700 py-2 rounded-xl text-sm font-medium transition">
                                <i class="fas fa-eye mr-1"></i>Voir
                            </a>
                            @if ($evt->fichier)
                                <a href="{{ asset('storage/' . $evt->fichier) }}" download
                                    class="w-9 h-9 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl flex items-center justify-center transition">
                                    <i class="fas fa-download text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 card p-16 text-center">
                    <i class="fas fa-bell-slash text-5xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400">Aucun événement pour le moment</p>
                </div>
            @endforelse
        </div>
        <div>{{ $evenements->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        function filterEvt() {
            const s = document.getElementById('search').value.toLowerCase();
            const t = document.getElementById('filterType').value;
            document.querySelectorAll('.evt-card').forEach(card => {
                card.style.display = card.textContent.toLowerCase().includes(s) && (!t || card.dataset.type === t) ?
                    '' : 'none';
            });
        }
        document.getElementById('search').addEventListener('input', filterEvt);
        document.getElementById('filterType').addEventListener('change', filterEvt);
    </script>
@endpush
