<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'Mentor') - DOCOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            background: linear-gradient(180deg, #064e3b 0%, #065f46 50%, #047857 100%);
            min-height: 100vh;
            transition: width .3s ease;
        }

        .sidebar-item {
            transition: all .25s ease;
            border-radius: 12px;
            margin: 2px 8px;
        }

        .sidebar-item:hover,
        .sidebar-item.active {
            background: rgba(255, 255, 255, .15);
        }

        .sidebar-item.active {
            border-left: 3px solid #6ee7b7;
        }

        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
            transition: all .3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, .1);
        }

        .loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, .96);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(15px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .fade-slide {
            animation: fadeSlide .4s ease forwards;
        }

        #sidebar.collapsed .sidebar-label,
        .sidebar.collapsed .sidebar-logo-text {
            display: none;
        }

        #sidebar {
            width: 260px;
        }

        #sidebar.collapsed {
            width: 72px;
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(#047857, #065f46);
            border-radius: 3px;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50">

    <div class="loader-overlay" id="appLoader">
        <div class="relative mb-4">
            <div
                class="w-16 h-16 rounded-full border-4 border-emerald-100 spin border-t-emerald-600 absolute inset-0 m-auto">
            </div>
            <img src="{{ asset('logo.png') }}" alt="DOCOS"
                class="w-12 h-12 object-contain mx-auto rounded-xl relative z-10 mt-2">
        </div>
        <p class="text-emerald-700 font-bold text-xl mt-4">DOCOS</p>
        <p class="text-slate-400 text-sm">Espace Mentor</p>
    </div>

    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="sidebar flex-shrink-0 flex flex-col shadow-2xl z-50">
            <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
                <img src="{{ asset('logo.png') }}" alt="DOCOS"
                    class="w-10 h-10 rounded-xl object-contain flex-shrink-0">
                <div class="sidebar-logo-text">
                    <p class="text-white font-bold text-lg">DOCOS</p>
                    <p class="text-emerald-300 text-xs">Mentor</p>
                </div>
                <button id="toggleSidebar" class="ml-auto text-white/60 hover:text-white">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="flex items-center gap-3 px-5 py-4 border-b border-white/10">
                <div
                    class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-teal-400 flex items-center justify-center flex-shrink-0">
                    @if (Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                            class="w-full h-full rounded-full object-cover">
                    @else
                        <span
                            class="text-white font-bold text-sm">{{ strtoupper(substr(Auth::user()->prenom, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="sidebar-label">
                    <p class="text-white font-semibold text-sm truncate">{{ Auth::user()->nom_complet }}</p>
                    <p class="text-emerald-300 text-xs">Tuteur/Mentor</p>
                </div>
            </div>
            <nav class="flex-1 py-4 overflow-y-auto">
                <a href="{{ route('mentor.dashboard') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('mentor.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center flex-shrink-0"></i><span
                        class="sidebar-label text-sm font-medium">Tableau de bord</span>
                </a>
                <a href="{{ route('mentor.stagiaires.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('mentor.stagiaires.*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate w-5 text-center flex-shrink-0"></i><span
                        class="sidebar-label text-sm font-medium">Mes Stagiaires</span>
                </a>
                <a href="{{ route('mentor.projects.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('mentor.projects.*') ? 'active' : '' }}">
                    <i class="fas fa-project-diagram w-5 text-center flex-shrink-0"></i><span
                        class="sidebar-label text-sm font-medium">Projets</span>
                </a>
                <a href="{{ route('mentor.tasks.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('mentor.tasks.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks w-5 text-center flex-shrink-0"></i><span
                        class="sidebar-label text-sm font-medium">Tâches</span>
                </a>
                <a href="{{ route('mentor.reports.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('mentor.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt w-5 text-center flex-shrink-0"></i><span
                        class="sidebar-label text-sm font-medium">Rapports</span>
                </a>
                <a href="{{ route('mentor.presences.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('mentor.presences.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list w-5 text-center flex-shrink-0"></i><span
                        class="sidebar-label text-sm font-medium">Présences</span>
                </a>
                <a href="{{ route('mentor.attestations.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('mentor.attestations.*') ? 'active' : '' }}">
                    <i class="fas fa-certificate w-5 text-center flex-shrink-0"></i><span
                        class="sidebar-label text-sm font-medium">Attestations</span>
                </a>
                <a href="{{ route('mentor.evenements.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('mentor.evenements.*') ? 'active' : '' }}">
                    <i class="fas fa-bell w-5 text-center flex-shrink-0"></i><span
                        class="sidebar-label text-sm font-medium">Événements</span>
                </a>
                <a href="{{ route('documents.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white">
                    <i class="fas fa-folder-open w-5 text-center flex-shrink-0"></i><span
                        class="sidebar-label text-sm font-medium">Documents</span>
                </a>
            </nav>
            <div class="p-4 border-t border-white/10">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="sidebar-item flex items-center gap-3 px-4 py-3 text-red-300 hover:text-red-200 w-full text-left">
                        <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i>
                        <span class="sidebar-label text-sm font-medium">Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header
                class="glass border-b border-white/20 px-6 py-4 flex items-center justify-between sticky top-0 z-40">
                <div>
                    <h1 class="text-slate-800 font-bold text-lg">@yield('titre', 'Tableau de bord')</h1>
                    <p class="text-slate-400 text-xs">@yield('breadcrumb', 'Espace Mentor')</p>
                </div>
                <div class="flex items-center gap-4">
                    {{-- <span
                        class="hidden sm:block text-slate-500 text-sm">{{ now()->translatedFormat('l d F Y') }}</span> --}}
                    <a href="{{ route('profile.index') }}"
                        class="flex items-center gap-2 hover:bg-emerald-50 px-3 py-2 rounded-xl transition">
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center">
                            <span
                                class="text-white text-xs font-bold">{{ strtoupper(substr(Auth::user()->prenom, 0, 1)) }}</span>
                        </div>
                        <span
                            class="text-slate-700 font-medium text-sm hidden md:block">{{ Auth::user()->prenom }}</span>
                    </a>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto p-6">
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <ul class="list-disc list-inside text-red-600 text-sm">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="fade-slide">@yield('content')</div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
    <script>
        window.addEventListener('load', () => {
            gsap.to('#appLoader', {
                opacity: 0,
                duration: .6,
                delay: .5,
                onComplete: () => {
                    document.getElementById('appLoader').style.display = 'none';
                }
            });
        });
        AOS.init({
            duration: 600,
            once: true
        });
        document.getElementById('toggleSidebar')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });
        @if (session('succes'))
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: @json(session('succes')),
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
        @if (session('erreur'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: @json(session('erreur')),
                toast: true,
                position: 'top-end',
                timer: 4000,
                showConfirmButton: false
            });
        @endif
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const form = btn.closest('form');
                Swal.fire({
                    title: 'Supprimer ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonText: 'Annuler',
                    confirmButtonText: 'Supprimer'
                }).then(r => {
                    if (r.isConfirmed && form) form.submit();
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
