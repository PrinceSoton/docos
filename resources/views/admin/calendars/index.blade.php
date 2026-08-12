@extends('layouts.admin')
@section('titre', 'Calendrier')
@section('breadcrumb', 'Système > Calendrier')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Calendrier & Jours de séjours </h2>
                <p class="text-slate-500 text-sm">Gérez les jours fériés et la configuration des jours de travail</p>
            </div>
            <a href="{{ route('admin.calendars.create') }}"
                class="flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i>Ajouter un jour
            </a>
        </div>

        <!-- Config jours de travail -->
        <div class="card p-6" data-aos="fade-up">
            <h3 class="text-slate-800 font-bold text-lg mb-4">
                <i class="fas fa-cog text-indigo-500 mr-2"></i>Configuration des jours de travail
            </h3>
            <form action="{{ route('admin.calendars.updateConfig') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-7 gap-2">
                    @foreach ([['lundi', 'Lun'], ['mardi', 'Mar'], ['mercredi', 'Mer'], ['jeudi', 'Jeu'], ['vendredi', 'Ven'], ['samedi', 'Sam'], ['dimanche', 'Dim']] as [$key, $label])
                        <label
                            class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition
                    {{ $config && $config->{$key} ? 'border-indigo-400 bg-indigo-50' : 'border-slate-200' }}"
                            id="label-{{ $key }}">
                            <input type="checkbox" name="{{ $key }}" value="1"
                                {{ $config && $config->{$key} ? 'checked' : '' }} class="sr-only"
                                onchange="toggleDay(this,'{{ $key }}')">
                            <span
                                class="font-semibold text-sm {{ $config && $config->{$key} ? 'text-indigo-700' : 'text-slate-400' }}">{{ $label }}</span>
                            <div class="w-4 h-4 rounded-full {{ $config && $config->{$key} ? 'bg-indigo-500' : 'bg-slate-200' }}"
                                id="dot-{{ $key }}"></div>
                        </label>
                    @endforeach
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1"><i
                                class="fas fa-sun text-amber-400 mr-1"></i>Heure de début *</label>
                        <input type="time" name="heure_debut" value="{{ $config?->heure_debut ?? '09:00' }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1"><i
                                class="fas fa-moon text-indigo-400 mr-1"></i>Heure de fin *</label>
                        <input type="time" name="heure_fin" value="{{ $config?->heure_fin ?? '18:15' }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 transition text-sm">
                    </div>
                </div>
                <button type="submit"
                    class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2.5 rounded-xl font-medium hover:shadow-lg transition-all hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i>Enregistrer la configuration
                </button>
            </form>
        </div>

        <!-- Recherche -->
        <div class="card p-4" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher un jour (libellé, type, date)..."
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm">
        </div>

        <!-- Tableau jours spéciaux -->
        <div class="card overflow-hidden" {{-- data-aos="fade-up" --}}>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Date</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Libellé</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Type</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Description</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="calTable">
                        @forelse($jours as $jour)
                            <tr class="hover:bg-slate-50 transition cal-row">
                                <td class="px-5 py-4 font-bold text-slate-800 font-mono">
                                    {{ $jour->date->translatedFormat('D d M Y') }}
                                </td>
                                <td class="px-5 py-4 font-medium text-slate-700">{{ $jour->libelle }}</td>
                                <td class="px-5 py-4">
                                    @php $tc = ['ferie'=>'bg-red-100 text-red-700','sejour'=>'bg-blue-100 text-blue-700','autre'=>'bg-slate-100 text-slate-600']; @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold {{ $tc[$jour->type] ?? '' }}">
                                        {{ ucfirst($jour->type) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-400 text-xs max-w-xs truncate">
                                    {{ $jour->description ?: '—' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.calendars.edit', $jour) }}"
                                            class="w-8 h-8 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg flex items-center justify-center transition">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.calendars.destroy', $jour) }}" method="POST"
                                            id="del-cal-{{ $jour->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                class="btn-delete w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition"
                                                data-form="del-cal-{{ $jour->id }}">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400">
                                    <i class="fas fa-calendar text-4xl mb-3 text-slate-200"></i>
                                    <p>Aucun jour spécial configuré</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100">{{ $jours->links() }}</div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function toggleDay(cb, key) {
            const label = document.getElementById('label-' + key);
            const dot = document.getElementById('dot-' + key);
            const span = label.querySelector('span');
            if (cb.checked) {
                label.classList.add('border-indigo-400', 'bg-indigo-50');
                label.classList.remove('border-slate-200');
                span.classList.add('text-indigo-700');
                span.classList.remove('text-slate-400');
                dot.classList.add('bg-indigo-500');
                dot.classList.remove('bg-slate-200');
            } else {
                label.classList.remove('border-indigo-400', 'bg-indigo-50');
                label.classList.add('border-slate-200');
                span.classList.remove('text-indigo-700');
                span.classList.add('text-slate-400');
                dot.classList.remove('bg-indigo-500');
                dot.classList.add('bg-slate-200');
            }
        }
        document.getElementById('search').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.cal-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
@endpush
