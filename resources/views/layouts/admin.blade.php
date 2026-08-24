<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'Admin') - DOCOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                screens: {
                    'xs': '375px',
                    'sm': '640px',
                    'md': '768px',
                    'lg': '1024px',
                    'xl': '1280px',
                    '2xl': '1536px',
                },
                extend: {
                    colors: {
                        primary: {
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        },
                        accent: {
                            500: '#6366f1',
                            600: '#4f46e5'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css" rel="stylesheet" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .sidebar {
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 50%, #1e40af 100%);
            min-height: 100vh;
            transition: width .3s ease, transform .3s ease;
            overflow-y: auto;
            z-index: 60;
        }

        .sidebar.collapsed {
            width: 72px;
        }

        .sidebar.collapsed .sidebar-label {
            display: none;
        }

        .sidebar.collapsed .sidebar-logo-text {
            display: none;
        }

        /* Version mobile : menu latéral masqué */
        @media (max-width: 767px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 280px;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform .3s ease;
                box-shadow: 4px 0 30px rgba(0, 0, 0, 0.5);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 50;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }

        .sidebar-item {
            transition: all .25s ease;
            border-radius: 12px;
            margin: 2px 8px;
        }

        .sidebar-item:hover,
        .sidebar-item.active {
            background: rgba(255, 255, 255, .15);
            padding-left: 1.25rem;
        }

        .sidebar-item.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, .4), rgba(59, 130, 246, .4));
            border-left: 3px solid #818cf8;
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

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border: none;
            transition: all .3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, .4);
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

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(#6366f1, #3b82f6);
            border-radius: 3px;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-slide {
            animation: fadeSlide .4s ease forwards;
        }

        /* Désactiver AOS sur petits écrans */
        @media (max-width: 640px) {
            [data-aos] {
                opacity: 1 !important;
                transform: none !important;
                pointer-events: auto !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50 overflow-x-hidden">

    <!-- Loader -->
    <div class="loader-overlay" id="appLoader">
        <div class="relative mb-4">
            <div class="w-20 h-20 rounded-full border-4 border-indigo-100 spin border-t-indigo-600 absolute inset-0">
            </div>
            <img src="{{ asset('logo.png') }}" alt="DOCOS"
                class="relative rounded-3xl shadow-2xl w-16 h-16 object-contain mx-auto" loading="lazy">
        </div>
        <p class="text-indigo-700 font-bold text-xl mt-4">DOCOS</p>
        <p class="text-slate-400 text-sm">Administration</p>
    </div>

    <div class="flex h-screen overflow-hidden">
        <!-- Overlay mobile -->
        <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar(false)"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar flex-shrink-0 flex flex-col shadow-2xl">
            <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
                <img src="{{ asset('logo.png') }}" alt="DOCOS"
                    class="w-10 h-10 rounded-xl object-contain flex-shrink-0" loading="lazy">
                <div class="sidebar-logo-text">
                    <p class="text-white font-bold text-lg leading-none">DOCOS</p>
                </div>
                <button id="toggleSidebarBtn" class="ml-auto text-white/60 hover:text-white hidden sm:block"
                    aria-label="Réduire/agrandir la sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <button id="closeSidebarBtn" class="ml-auto text-white/60 hover:text-white block sm:hidden"
                    aria-label="Fermer le menu" onclick="toggleSidebar(false)">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-4 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Tableau de bord</span>
                </a>
                <div class="px-5 py-2 mt-2">
                    <p class="text-indigo-300/60 text-xs uppercase tracking-widest sidebar-label">Gestion</p>
                </div>
                <a href="{{ route('admin.users.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Utilisateurs</span>
                </a>
                <a href="{{ route('admin.mentors.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('admin.mentors.*') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Mentors</span>
                </a>
                <a href="{{ route('admin.stagiaires.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('admin.stagiaires.*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Stagiaires</span>
                </a>
                <div class="px-5 py-2 mt-2">
                    <p class="text-indigo-300/60 text-xs uppercase tracking-widest sidebar-label">Suivi</p>
                </div>
                <a href="{{ route('admin.presences.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('admin.presences.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-check w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Présences</span>
                </a>
                <a href="{{ route('admin.attestations.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('admin.attestations.*') ? 'active' : '' }}">
                    <i class="fas fa-certificate w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Attestations</span>
                </a>
                <a href="{{ route('admin.archives.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('admin.archives.*') ? 'active' : '' }}">
                    <i class="fas fa-archive w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Archives</span>
                </a>
                <div class="px-5 py-2 mt-2">
                    <p class="text-indigo-300/60 text-xs uppercase tracking-widest sidebar-label">Système</p>
                </div>
                <a href="{{ route('admin.evenements.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('admin.evenements.*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Événements</span>
                </a>
                <a href="{{ route('admin.calendars.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('admin.calendars.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Calendrier</span>
                </a>
                <a href="{{ route('documents.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white">
                    <i class="fas fa-folder-open w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Documents</span>
                </a>
                <a href="{{ route('admin.statistics.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('admin.statistics.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Statistiques</span>
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

        <!-- Main -->
        <div class="flex-1 flex flex-col overflow-hidden" id="mainContent">
            <header
                class="glass border-b border-white/20 px-4 sm:px-6 py-4 flex items-center justify-between sticky top-0 z-40">
                <div class="flex items-center gap-3">
                    <button id="openSidebarBtn" class="lg:hidden text-slate-600 hover:text-indigo-600 transition"
                        aria-label="Ouvrir le menu">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-slate-800 font-bold text-lg sm:text-xl">@yield('titre', 'Tableau de bord')</h1>
                        <p class="text-slate-400 text-xs">@yield('breadcrumb', 'Administration')</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 sm:gap-4">
                    <a href="{{ route('profile.index') }}"
                        class="flex items-center gap-2 hover:bg-indigo-50 px-3 py-2 rounded-xl transition">
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-blue-500 flex items-center justify-center overflow-hidden">
                            @if (Auth::user()->photo)
                                <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                                    class="w-full h-full rounded-full object-cover" loading="lazy">
                            @else
                                <span
                                    class="text-white text-xs font-bold">{{ strtoupper(substr(Auth::user()->prenom, 0, 1)) }}</span>
                            @endif
                        </div>
                        <span class="text-slate-700 font-medium text-sm hidden md:block">
                            <p>{{ Auth::user()->nom_complet }}</p>
                            <p class="text-indigo-300 text-xs">Administrateur</p>
                        </span>
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl" data-aos="fade-down">
                        <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
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

    <!-- Scripts -->
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

        const isMobile = window.innerWidth < 640;
        AOS.init({
            duration: 600,
            once: true,
            offset: 30,
            disable: isMobile
        });

        // Toggle sidebar
        function toggleSidebar(open) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (window.innerWidth < 768) {
                if (open === undefined) {
                    const isOpen = sidebar.classList.contains('open');
                    sidebar.classList.toggle('open', !isOpen);
                    overlay.classList.toggle('active', !isOpen);
                } else {
                    sidebar.classList.toggle('open', open);
                    overlay.classList.toggle('active', open);
                }
            } else {
                sidebar.classList.toggle('collapsed');
            }
        }

        document.getElementById('openSidebarBtn')?.addEventListener('click', () => toggleSidebar(true));
        document.getElementById('closeSidebarBtn')?.addEventListener('click', () => toggleSidebar(false));
        document.getElementById('toggleSidebarBtn')?.addEventListener('click', () => toggleSidebar());

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            }
        });

        // Alertes
        @if (session('succes'))
            Swal.fire({
                icon: 'success',
                title: 'Succès !',
                text: @json(session('succes')),
                toast: true,
                position: 'top-end',
                timer: 3500,
                showConfirmButton: false,
                background: '#f0fdf4',
                color: '#166534'
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

        // Boutons de suppression
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const formId = btn.dataset.form;
                const form = formId ? document.getElementById(formId) : btn.closest('form');
                Swal.fire({
                        title: 'Confirmer',
                        text: 'Supprimer cet élément ?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Oui, supprimer',
                        cancelButtonText: 'Annuler'
                    })
                    .then(r => {
                        if (r.isConfirmed && form) form.submit();
                    });
            });
        });

        // Désactivation des boutons lors de la soumission
        document.querySelectorAll('form:not(.no-loader)').forEach(f => {
            f.addEventListener('submit', function() {
                const btn = this.querySelector('[type=submit]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Patientez...';
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
