@extends('layouts.admin')
@section('titre', 'Calendrier')
@section('breadcrumb', 'Système > Calendrier')
@section('content')
    @php
        $focus = 'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2';
        $champ = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25';
        $champErreur = 'w-full rounded-lg border border-red-400 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-red-600 focus:ring-2 focus:ring-red-600/20';
        $btnPrimaire = 'inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors ' . $focus;
        $btnPetitSecondaire = 'inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors ' . $focus;
        $btnIconeDanger = 'inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg border border-red-200 bg-white text-red-700 hover:bg-red-50 transition-colors ' . $focus;

        $tc = [
            'ferie' => ['Jour férié', 'bg-red-50 text-red-800'],
            'sejour' => ['Jour de séjour', 'bg-sky-50 text-sky-800'],
            'autre' => ['Autre', 'bg-slate-100 text-slate-700'],
        ];
    @endphp

    <div class="max-w-6xl mx-auto space-y-5">

        <!-- Configuration des jours de travail -->
        <section class="bg-white border border-brand-100 rounded-xl overflow-hidden">
            <div class="px-5 sm:px-6 py-3.5 bg-brand-50 border-b border-brand-100">
                <h2 class="text-base font-semibold text-slate-900">Configuration des jours de travail</h2>
                <p class="text-sm text-slate-600">Jours ouvrés et horaires appliqués au marquage de présence</p>
            </div>
            <form action="{{ route('admin.calendars.updateConfig') }}" method="POST" class="p-5 sm:p-6 space-y-5">
                @csrf @method('PUT')
                <fieldset>
                    <legend class="block text-sm font-medium text-slate-700 mb-2">Jours travaillés</legend>
                    <div class="grid grid-cols-4 xs:grid-cols-7 gap-2">
                        @foreach ([['lundi', 'Lun'], ['mardi', 'Mar'], ['mercredi', 'Mer'], ['jeudi', 'Jeu'], ['vendredi', 'Ven'], ['samedi', 'Sam'], ['dimanche', 'Dim']] as [$key, $label])
                            <label
                                class="flex flex-col items-center gap-2 p-2 sm:p-3 rounded-lg border cursor-pointer transition-colors hover:border-brand-600 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-brand-600 has-[:focus-visible]:ring-offset-2 {{ $config && $config->{$key} ? 'border-brand-600 bg-brand-50' : 'border-slate-300 bg-white' }}"
                                id="label-{{ $key }}">
                                <input type="checkbox" name="{{ $key }}" value="1"
                                    {{ $config && $config->{$key} ? 'checked' : '' }} class="sr-only"
                                    onchange="toggleDay(this,'{{ $key }}')">
                                <span
                                    class="font-medium text-xs sm:text-sm {{ $config && $config->{$key} ? 'text-brand-800' : 'text-slate-500' }}"><abbr
                                        title="{{ ucfirst($key) }}" class="no-underline">{{ $label }}</abbr></span>
                                <div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full {{ $config && $config->{$key} ? 'bg-accent-500' : 'bg-slate-200' }}"
                                    id="dot-{{ $key }}" aria-hidden="true"></div>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Pastille verte : jour travaillé. Cliquez sur un jour pour l'activer ou le désactiver.</p>
                </fieldset>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="heure_debut" class="block text-sm font-medium text-slate-700 mb-1">
                            Heure de début <span class="text-red-700" aria-hidden="true">*</span>
                        </label>
                        <input type="time" id="heure_debut" name="heure_debut" value="{{ $config?->heure_debut ?? '09:00' }}" required
                            class="{{ $errors->has('heure_debut') ? $champErreur : $champ }} tabular-nums"
                            @error('heure_debut') aria-invalid="true" @enderror>
                        @error('heure_debut')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="heure_fin" class="block text-sm font-medium text-slate-700 mb-1">
                            Heure de fin <span class="text-red-700" aria-hidden="true">*</span>
                        </label>
                        <input type="time" id="heure_fin" name="heure_fin" value="{{ $config?->heure_fin ?? '18:15' }}" required
                            class="{{ $errors->has('heure_fin') ? $champErreur : $champ }} tabular-nums"
                            @error('heure_fin') aria-invalid="true" @enderror>
                        @error('heure_fin')
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="{{ $btnPrimaire }} w-full sm:w-auto">
                        <i class="fas fa-save text-xs" aria-hidden="true"></i>Enregistrer la configuration
                    </button>
                </div>
            </form>
        </section>

        <!-- Barre d'outils : jours spéciaux -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-base font-semibold text-slate-900">Jours fériés et jours de séjour</h2>
                <p class="text-sm text-slate-600">Ces jours bloquent le marquage de présence</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="search" id="search" placeholder="Libellé, type, date..."
                    aria-label="Rechercher un jour" class="w-full sm:w-72 {{ $champ }}">
                <a href="{{ route('admin.calendars.create') }}" class="{{ $btnPrimaire }} whitespace-nowrap">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>Ajouter un jour
                </a>
            </div>
        </div>

        @if ($jours->count())
            <section class="bg-white border border-brand-100 rounded-xl overflow-hidden" aria-label="Jours spéciaux">

                <!-- Tableau (écrans larges) -->
                <div class="hidden xl:block">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col class="w-44">
                            <col>
                            <col class="w-36">
                            <col class="w-[32%]">
                            <col class="w-36">
                        </colgroup>
                        <thead class="bg-brand-50 border-b border-brand-100">
                            <tr class="text-left text-xs font-medium text-slate-600">
                                <th scope="col" class="px-5 py-3">Date</th>
                                <th scope="col" class="px-3 py-3">Libellé</th>
                                <th scope="col" class="px-3 py-3">Type</th>
                                <th scope="col" class="px-3 py-3">Description</th>
                                <th scope="col" class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="calTable">
                            @foreach ($jours as $jour)
                                @php [$libType, $clsType] = $tc[$jour->type] ?? [ucfirst($jour->type), 'bg-slate-100 text-slate-700']; @endphp
                                <tr class="hover:bg-brand-50/60 transition-colors cal-row">
                                    <td class="px-5 py-3.5 font-medium text-slate-900 tabular-nums whitespace-nowrap">
                                        {{ $jour->date->translatedFormat('D d M Y') }}</td>
                                    <td class="px-3 py-3.5 min-w-0">
                                        <p class="font-medium text-slate-900 truncate" title="{{ $jour->libelle }}">{{ $jour->libelle }}</p>
                                    </td>
                                    <td class="px-3 py-3.5">
                                        <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-md whitespace-nowrap {{ $clsType }}">{{ $libType }}</span><span hidden>{{ $jour->type }}</span>
                                    </td>
                                    <td class="px-3 py-3.5 min-w-0">
                                        <p class="text-slate-500 truncate" title="{{ $jour->description }}">{{ $jour->description ?: '—' }}</p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.calendars.edit', $jour) }}"
                                                class="{{ $btnPetitSecondaire }} whitespace-nowrap">Modifier</a>
                                            <form action="{{ route('admin.calendars.destroy', $jour) }}" method="POST"
                                                id="del-cal-{{ $jour->id }}">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                                    data-form="del-cal-{{ $jour->id }}"
                                                    aria-label="Supprimer {{ $jour->libelle }}" title="Supprimer">
                                                    <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Liste empilée (téléphone, tablette, petit écran) -->
                <ul class="xl:hidden divide-y divide-slate-100">
                    @foreach ($jours as $jour)
                        @php [$libType, $clsType] = $tc[$jour->type] ?? [ucfirst($jour->type), 'bg-slate-100 text-slate-700']; @endphp
                        <li class="cal-row p-4 sm:p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-900 tabular-nums">{{ $jour->date->translatedFormat('D d M Y') }}</p>
                                    <p class="mt-0.5 text-sm text-slate-700 break-words">{{ $jour->libelle }}</p>
                                </div>
                                <span class="flex-shrink-0 text-xs font-medium px-2 py-0.5 rounded-md {{ $clsType }}">{{ $libType }}</span><span hidden>{{ $jour->type }}</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-500 break-words">{{ $jour->description ?: '—' }}</p>
                            <div class="mt-3 flex items-center gap-1.5">
                                <a href="{{ route('admin.calendars.edit', $jour) }}"
                                    class="{{ $btnPetitSecondaire }} whitespace-nowrap">Modifier</a>
                                <form action="{{ route('admin.calendars.destroy', $jour) }}" method="POST"
                                    id="del-cal-{{ $jour->id }}-carte">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-delete {{ $btnIconeDanger }}"
                                        data-form="del-cal-{{ $jour->id }}-carte"
                                        aria-label="Supprimer {{ $jour->libelle }}" title="Supprimer">
                                        <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <p id="calAucun" data-sans-revelation class="hidden border-t border-slate-100 px-6 py-8 text-center text-sm text-slate-600">
                    Aucun résultat pour cette recherche.
                </p>
            </section>
        @else
            <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50 px-6 py-10 text-center">
                <p class="text-sm font-medium text-slate-900">Aucun jour spécial configuré</p>
                <p class="mt-1 text-sm text-slate-600 max-w-md mx-auto">
                    Ajoutez les jours fériés ou de séjour pour bloquer le marquage de présence ces jours-là.
                </p>
                <a href="{{ route('admin.calendars.create') }}"
                    class="mt-3 inline-block text-sm font-medium text-brand-700 hover:underline underline-offset-4 rounded {{ $focus }}">Ajouter
                    un jour</a>
            </div>
        @endif

        <div>{{ $jours->links() }}</div>
    </div>
@endsection
@push('scripts')
    <script>
        function toggleDay(cb, key) {
            const label = document.getElementById('label-' + key);
            const dot = document.getElementById('dot-' + key);
            const span = label.querySelector('span');
            if (cb.checked) {
                label.classList.add('border-brand-600', 'bg-brand-50');
                label.classList.remove('border-slate-300', 'bg-white');
                span.classList.add('text-brand-800');
                span.classList.remove('text-slate-500');
                dot.classList.add('bg-accent-500');
                dot.classList.remove('bg-slate-200');
            } else {
                label.classList.remove('border-brand-600', 'bg-brand-50');
                label.classList.add('border-slate-300', 'bg-white');
                span.classList.remove('text-brand-800');
                span.classList.add('text-slate-500');
                dot.classList.remove('bg-accent-500');
                dot.classList.add('bg-slate-200');
            }
        }
        document.getElementById('search').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            let visibles = 0;
            document.querySelectorAll('.cal-row').forEach(row => {
                const visible = row.textContent.toLowerCase().includes(val);
                row.style.display = visible ? '' : 'none';
                if (visible) visibles++;
            });
            const aucun = document.getElementById('calAucun');
            if (aucun) aucun.classList.toggle('hidden', visibles > 0);
        });
    </script>
@endpush
