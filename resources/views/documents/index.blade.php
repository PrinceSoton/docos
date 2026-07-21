@extends('layouts.' . Auth::user()->role)
@section('titre', 'Documents')
@section('breadcrumb', 'Mes documents & partages')
@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between flex-wrap gap-4" data-aos="fade-down">
            <div>
                <h2 class="text-slate-800 font-bold text-2xl">Documents</h2>
                <p class="text-slate-500 text-sm">Gérez et partagez vos fichiers</p>
            </div>
            <a href="{{ route('documents.create') }}"
                class="flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                <i class="fas fa-plus"></i>Nouveau document
            </a>
        </div>

        <!-- Recherche -->
        <div class="card p-4" data-aos="fade-up">
            <input type="text" id="searchDocs" placeholder="Rechercher un document..."
                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-indigo-400 text-sm">
        </div>

        <!-- Mes documents -->
        <div data-aos="fade-up">
            <h3 class="text-slate-700 font-bold text-lg mb-4"><i class="fas fa-folder text-amber-500 mr-2"></i>Mes documents
                ({{ $mesDocs->count() }})</h3>
            @if ($mesDocs->isEmpty())
                <div class="card p-12 text-center">
                    <i class="fas fa-folder-open text-5xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400">Aucun document pour l'instant.</p>
                    <a href="{{ route('documents.create') }}"
                        class="mt-4 inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                        <i class="fas fa-plus"></i>Ajouter un document
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="mesDocs">
                    @foreach ($mesDocs as $doc)
                        <div class="card p-5 doc-item" data-nom="{{ strtolower($doc->titre) }}" data-aos="fade-up"
                            data-aos-delay="{{ $loop->index * 50 }}">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                        {{ in_array($doc->type_fichier, ['pdf']) ? 'bg-red-100 text-red-600' : (in_array($doc->type_fichier, ['jpg', 'jpeg', 'png', 'gif']) ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-600') }}">
                                    <i
                                        class="fas fa-{{ in_array($doc->type_fichier, ['pdf']) ? 'file-pdf' : (in_array($doc->type_fichier, ['jpg', 'jpeg', 'png', 'gif']) ? 'file-image' : 'file') }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-slate-800 font-semibold text-sm truncate">{{ $doc->titre }}</p>
                                    <p class="text-slate-400 text-xs mt-0.5">
                                        {{ $doc->type_fichier ? strtoupper($doc->type_fichier) : 'Fichier' }} •
                                        {{ $doc->taille ? round($doc->taille / 1024, 1) . 'Ko' : '' }}</p>
                                    <p class="text-slate-400 text-xs">{{ $doc->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @if ($doc->description)
                                <p class="text-slate-500 text-xs mt-3 line-clamp-2">{{ $doc->description }}</p>
                            @endif
                            <div class="flex items-center gap-2 mt-4">
                                @if ($doc->partage_tous)
                                    <span
                                        class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium"><i
                                            class="fas fa-globe mr-1"></i>Public</span>
                                @else
                                    <span
                                        class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium"><i
                                            class="fas fa-lock mr-1"></i>Limité</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                                <a href="{{ route('documents.telecharger', $doc) }}"
                                    class="flex-1 text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 py-2 rounded-lg text-xs font-medium transition">
                                    <i class="fas fa-download mr-1"></i>Télécharger
                                </a>
                                <a href="{{ route('documents.show', $doc) }}"
                                    class="flex-1 text-center bg-slate-50 hover:bg-slate-100 text-slate-700 py-2 rounded-lg text-xs font-medium transition">
                                    <i class="fas fa-eye mr-1"></i>Voir
                                </a>
                                @if ($doc->user_id === Auth::id())
                                    <a href="{{ route('documents.edit', $doc) }}"
                                        class="w-8 h-8 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center transition">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('documents.destroy', $doc) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                            class="btn-delete w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg flex items-center justify-center transition">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Documents partagés -->
        @if ($docsPartages->count() > 0)
            <div ><!--data-aos="fade-up"-->
                <h3 class="text-slate-700 font-bold text-lg mb-4"><i
                        class="fas fa-share-alt text-indigo-500 mr-2"></i>Partagés avec moi ({{ $docsPartages->count() }})
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($docsPartages as $doc)
                        <div class="card p-5 border-l-4 border-indigo-400">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-file text-indigo-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-slate-800 font-semibold text-sm truncate">{{ $doc->titre }}</p>
                                    <p class="text-slate-400 text-xs">Par {{ $doc->user->nom_complet }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <a href="{{ route('documents.telecharger', $doc) }}"
                                    class="flex-1 text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 py-2 rounded-lg text-xs font-medium transition">
                                    <i class="fas fa-download mr-1"></i>Télécharger
                                </a>
                                <a href="{{ route('documents.show', $doc) }}"
                                    class="flex-1 text-center bg-slate-50 hover:bg-slate-100 text-slate-700 py-2 rounded-lg text-xs font-medium transition">
                                    <i class="fas fa-eye mr-1"></i>Voir
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('searchDocs')?.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.doc-item').forEach(item => {
                item.style.display = item.dataset.nom?.includes(val) ? '' : 'none';
            });
        });
    </script>
@endpush
