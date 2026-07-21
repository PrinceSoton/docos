@extends('layouts.admin')
@section('titre', 'Archives')
@section('breadcrumb', 'Système > Archives')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Archives</h2>
                <p class="text-slate-500 text-sm">{{ $archives->total() }} archive(s)</p>
            </div>
            <a href="{{ route('admin.archives.create') }}"
                class="flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i>Nouvelle archive
            </a>
        </div>

        <div class="card p-4 flex flex-wrap gap-3" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher par titre, stagiaire..."
                class="flex-1 min-w-48 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm">
            <select id="filterType"
                class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm">
                <option value="">Tous types</option>
                <option value="stagiaire">Stagiaire</option>
                <option value="autre">Autre</option>
            </select>
        </div>

        <div class="card overflow-hidden" data-aos="fade-up">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Titre</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Type</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Stagiaire</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Fichiers</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Créé par</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Date</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="archiveTable">
                        @forelse($archives as $archive)
                            <tr class="hover:bg-slate-50 transition archive-row" data-type="{{ $archive->type }}">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">{{ $archive->titre }}</p>
                                    @if ($archive->description)
                                        <p class="text-slate-400 text-xs mt-0.5 line-clamp-1">{{ $archive->description }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $archive->type === 'stagiaire' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                                        <i
                                            class="fas fa-{{ $archive->type === 'stagiaire' ? 'user-graduate' : 'folder' }} mr-1"></i>
                                        {{ ucfirst($archive->type) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-600 text-sm">
                                    {{ $archive->stagiaire?->user?->nom_complet ?? '—' }}
                                    @if ($archive->stagiaire)
                                        <br><span
                                            class="text-xs text-slate-400 font-mono">{{ $archive->stagiaire->matricule }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                        {{ $archive->fichiers->count() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-600 text-sm">{{ $archive->creePar?->nom_complet ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-slate-400 text-xs">{{ $archive->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.archives.show', $archive) }}"
                                            class="w-8 h-8 bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-700 rounded-lg flex items-center justify-center transition">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.archives.edit', $archive) }}"
                                            class="w-8 h-8 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg flex items-center justify-center transition">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.archives.destroy', $archive) }}" method="POST"
                                            id="del-arch-{{ $archive->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                class="btn-delete w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition"
                                                data-form="del-arch-{{ $archive->id }}">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-16">
                                    <i class="fas fa-archive text-5xl text-slate-200 mb-3"></i>
                                    <p class="text-slate-400">Aucune archive</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100">{{ $archives->links() }}</div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function filterArch() {
            const s = document.getElementById('search').value.toLowerCase();
            const t = document.getElementById('filterType').value;
            document.querySelectorAll('.archive-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(s) && (!t || row.dataset.type === t) ?
                    '' : 'none';
            });
        }
        document.getElementById('search').addEventListener('input', filterArch);
        document.getElementById('filterType').addEventListener('change', filterArch);
    </script>
@endpush
