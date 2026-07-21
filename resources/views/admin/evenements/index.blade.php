@extends('layouts.admin')
@section('titre', 'Événements & Informations')
@section('breadcrumb', 'Système > Événements')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Événements & Communications</h2>
                <p class="text-slate-500 text-sm">{{ $evenements->total() }} publication(s)</p>
            </div>
            <a href="{{ route('admin.evenements.create') }}"
                class="flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i>Nouvelle publication
            </a>
        </div>

        <div class="card p-4 flex flex-wrap gap-3" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher par titre, type..."
                class="flex-1 min-w-48 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm">
            <select id="filterType"
                class="border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                <option value="">Tous types</option>
                <option value="information">Information</option>
                <option value="evenement">Événement</option>
                <option value="note">Note</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="evtGrid" data-aos="fade-up">
            @forelse($evenements as $evt)
                <div class="card overflow-hidden evt-card hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                    data-type="{{ $evt->type }}">
                    @if ($evt->image)
                        <div class="h-40 overflow-hidden">
                            <img src="{{ asset('storage/' . $evt->image) }}" alt="{{ $evt->titre }}"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                        </div>
                    @endif
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            @php
                                $tc = [
                                    'information' => 'bg-blue-100 text-blue-700',
                                    'evenement' => 'bg-purple-100 text-purple-700',
                                    'note' => 'bg-amber-100 text-amber-700',
                                ];
                                $ti = [
                                    'information' => 'info-circle',
                                    'evenement' => 'calendar-star',
                                    'note' => 'sticky-note',
                                ];
                            @endphp
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $tc[$evt->type] ?? 'bg-slate-100 text-slate-600' }}">
                                <i class="fas fa-{{ $ti[$evt->type] ?? 'circle' }} mr-1"></i>{{ ucfirst($evt->type) }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $evt->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="font-bold text-slate-800 mb-2">{{ $evt->titre }}</h3>
                        @if ($evt->contenu)
                            <p class="text-slate-500 text-sm line-clamp-2">{{ Str::limit(strip_tags($evt->contenu), 100) }}
                            </p>
                        @endif
                        @if ($evt->date_evenement)
                            <p class="text-xs text-indigo-600 font-medium mt-2">
                                <i class="fas fa-calendar mr-1"></i>{{ $evt->date_evenement->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                        <div class="flex items-center gap-2 mt-4 pt-3 border-t border-slate-100">
                            <span
                                class="text-xs {{ $evt->partage_tous ? 'text-green-600' : 'text-amber-600' }} font-medium">
                                <i class="fas fa-{{ $evt->partage_tous ? 'globe' : 'users' }} mr-1"></i>
                                {{ $evt->partage_tous ? 'Tous' : $evt->utilisateurssCibles->count() . ' destinataire(s)' }}
                            </span>
                            <div class="flex gap-1.5 ml-auto">
                                <a href="{{ route('admin.evenements.show', $evt) }}"
                                    class="w-7 h-7 bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-700 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.evenements.edit', $evt) }}"
                                    class="w-7 h-7 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg flex items-center justify-center transition">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.evenements.destroy', $evt) }}" method="POST"
                                    id="del-evt-{{ $evt->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                        class="btn-delete w-7 h-7 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition"
                                        data-form="del-evt-{{ $evt->id }}">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-16">
                    <i class="fas fa-bullhorn text-5xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400">Aucune publication</p>
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
