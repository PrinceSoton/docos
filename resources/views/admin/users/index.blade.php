@extends('layouts.admin')
@section('titre', 'Utilisateurs')
@section('breadcrumb', 'Gestion > Utilisateurs')
@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Utilisateurs</h2>
                <p class="text-slate-500 text-sm">{{ $users->total() }} utilisateur(s) au total</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i>Nouvel utilisateur
            </a>
        </div>

        <div class="card p-4" data-aos="fade-up">
            <input type="text" id="search" placeholder="Rechercher par nom, email, rôle..."
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm">
        </div>

        <div class="card overflow-hidden" data-aos="fade-up">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="usersTable">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                        <tr>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Utilisateur</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Email</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Rôle</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Statut</th>
                            <th class="px-5 py-4 text-left font-semibold text-slate-600">Créé le</th>
                            <th class="px-5 py-4 text-center font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($users as $user)
                            <tr class="table-row-hover transition user-row">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0
                                    {{ $user->role === 'admin' ? 'bg-gradient-to-br from-red-400 to-pink-500' : ($user->role === 'mentor' ? 'bg-gradient-to-br from-emerald-400 to-teal-500' : 'bg-gradient-to-br from-indigo-400 to-purple-500') }}">
                                            @if ($user->photo)
                                                <img src="{{ asset('storage/' . $user->photo) }}"
                                                    class="w-full h-full rounded-xl object-cover">
                                            @else
                                                {{ strtoupper(substr($user->prenom, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $user->nom_complet }}</p>
                                            <p class="text-slate-400 text-xs">{{ $user->telephone ?: '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $user->email }}</td>
                                <td class="px-5 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : ($user->role === 'mentor' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <form action="{{ route('admin.users.toggle', $user) }}" method="POST"
                                        class="no-loader">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-1 rounded-full text-xs font-semibold transition {{ $user->actif ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                            {{ $user->actif ? 'Actif' : 'Inactif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-4 text-slate-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="w-8 h-8 bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-700 rounded-lg flex items-center justify-center transition"
                                            title="Voir">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="w-8 h-8 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg flex items-center justify-center transition"
                                            title="Modifier">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            id="del-user-{{ $user->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                class="btn-delete w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition"
                                                data-form="del-user-{{ $user->id }}" title="Supprimer">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-400">Aucun utilisateur trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.user-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
            });
        });
    </script>
@endpush
