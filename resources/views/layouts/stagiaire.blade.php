<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'Mon Espace') - DOCOS</title>

    <!-- Tailwind avec configuration responsive personnalisée -->
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
                    spacing: {
                        '18': '4.5rem',
                        '88': '22rem',
                        '120': '30rem',
                    }
                }
            }
        }
    </script>

    <!-- Flowbite, FontAwesome, AOS, SweetAlert2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css" rel="stylesheet" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        /* Sidebar – par défaut en mode desktop (visible) */
        .sidebar {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            transition: width .3s ease, transform .3s ease;
            overflow-y: auto;
            z-index: 60;
        }

        /* Version collapsée (bouton toggle) */
        .sidebar.collapsed {
            width: 72px;
        }

        .sidebar.collapsed .sidebar-label,
        .sidebar.collapsed .sidebar-logo-text {
            display: none;
        }

        /* Version mobile : menu latéral masqué par défaut */
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

            /* Overlay pour fermer le menu */
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
            background: rgba(255, 255, 255, .1);
        }

        .sidebar-item.active {
            border-left: 3px solid #f59e0b;
            color: #fbbf24 !important;
        }

        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
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

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(#f59e0b, #d97706);
            border-radius: 3px;
        }

        .progress-bar {
            background: linear-gradient(90deg, #f59e0b, #d97706);
            height: 6px;
            border-radius: 3px;
            transition: width .8s ease;
        }

        /* Désactiver AOS sur petits écrans pour économiser les ressources */
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

<body class="bg-gray-50">

    <!-- Loader -->
    <div class="loader-overlay" id="appLoader">
        <div class="relative mb-4 w-20 h-20 mx-auto">
            <div class="w-20 h-20 rounded-full border-4 border-amber-100 spin border-t-amber-500 absolute inset-0">
            </div>
            <img src="{{ asset('logo.png') }}" alt="DOCOS"
                class="w-14 h-14 object-contain absolute inset-0 m-auto rounded-xl" loading="lazy">
        </div>
        <p class="text-amber-600 font-bold text-xl mt-6">DOCOS</p>
        <p class="text-slate-400 text-sm">Espace Stagiaire</p>
    </div>

    <div class="flex h-screen overflow-hidden">

        <!-- Overlay pour mobile (fermeture du menu) -->
        <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar(false)"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar flex-shrink-0 flex flex-col shadow-2xl">
            <!-- En-tête sidebar -->
            <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
                <img src="{{ asset('logo.png') }}" alt="DOCOS"
                    class="w-10 h-10 rounded-xl object-contain flex-shrink-0" loading="lazy">
                <div class="sidebar-logo-text">
                    <p class="text-white font-bold text-lg">DOCOS</p>
                    <p class="text-amber-400 text-xs">Mon Espace</p>
                </div>
                <!-- Bouton toggle (visible sur tablette/desktop) -->
                <button id="toggleSidebarBtn" class="ml-auto text-white/60 hover:text-white hidden sm:block"
                    aria-label="Réduire/agrandir la sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <!-- Bouton de fermeture (visible sur mobile) -->
                <button id="closeSidebarBtn" class="ml-auto text-white/60 hover:text-white block sm:hidden"
                    aria-label="Fermer le menu" onclick="toggleSidebar(false)">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Info stagiaire -->
            @php $stagiaire = Auth::user()->stagiaire; @endphp
            <div class="px-5 py-4 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center flex-shrink-0 overflow-hidden">
                        @if (Auth::user()->photo)
                            <img src="{{ asset('storage/' . Auth::user()->photo) }}" class="w-full h-full object-cover"
                                loading="lazy">
                        @else
                            <span
                                class="text-white font-bold text-sm">{{ strtoupper(substr(Auth::user()->prenom, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="sidebar-label overflow-hidden">
                        <p class="text-white font-semibold text-sm truncate">{{ Auth::user()->nom_complet }}</p>
                        <p class="text-amber-400 text-xs">{{ $stagiaire?->matricule ?? 'Stagiaire' }}</p>
                    </div>
                </div>
                @if ($stagiaire)
                    <div class="mt-3 sidebar-label">
                        <div class="flex justify-between text-xs text-slate-400 mb-1">
                            <span>Progression du stage</span>
                            @php
                                $total = $stagiaire->date_debut->diffInDays($stagiaire->date_fin);
                                $ecoule = $stagiaire->date_debut->diffInDays(now());
                                $pct = $total > 0 ? min(100, round(($ecoule / $total) * 100)) : 0;
                            @endphp
                            <span class="text-amber-400 font-medium">{{ $pct }}%</span>
                        </div>
                        <div class="bg-white/10 rounded-full h-1.5">
                            <div class="progress-bar" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-4 overflow-y-auto">
                @php
                    $routes = [
                        ['stagiaire.dashboard', 'home', 'Tableau de bord'],
                        ['stagiaire.presence.index', 'user-check', 'Ma Présence'],
                        ['stagiaire.projects.index', 'project-diagram', 'Mon Projet'],
                        ['stagiaire.tasks.index', 'check-square', 'Mes Tâches'],
                        ['stagiaire.reports.index', 'file-upload', 'Mes Rapports'],
                        ['stagiaire.attestations.index', 'certificate', 'Attestations'],
                        ['stagiaire.evenements.index', 'bell', 'Événements'],
                        ['documents.index', 'folder-open', 'Documents'],
                    ];
                @endphp
                @foreach ($routes as [$route, $icon, $label])
                    <a href="{{ route($route) }}"
                        class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs($route) ? 'active' : '' }}">
                        <i class="fas fa-{{ $icon }} w-5 text-center flex-shrink-0"></i>
                        <span class="sidebar-label text-sm font-medium">{{ $label }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- Bas de la sidebar -->
            <div class="p-4 border-t border-white/10">
                <a href="{{ route('profile.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white mb-1">
                    <i class="fas fa-user-cog w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-label text-sm font-medium">Mon Profil</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="sidebar-item flex items-center gap-3 px-4 py-3 text-red-400 hover:text-red-300 w-full text-left">
                        <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i>
                        <span class="sidebar-label text-sm font-medium">Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Contenu principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header
                class="glass border-b border-white/20 px-4 sm:px-6 py-4 flex items-center justify-between sticky top-0 z-40">
                <div class="flex items-center gap-3">
                    <!-- Bouton d'ouverture du menu sur mobile -->
                    <button id="openSidebarBtn" class="block sm:hidden text-slate-700 hover:text-amber-600 text-xl"
                        aria-label="Ouvrir le menu" onclick="toggleSidebar(true)">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h1 class="text-slate-800 font-bold text-lg sm:text-xl">@yield('titre', 'Mon Espace')</h1>
                        <p class="text-slate-400 text-xs">@yield('breadcrumb', 'Stagiaire')</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Espace pour notifications, etc. -->
                </div>
            </header>

            <!-- Main -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
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

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>

    <script>
        // ---- Loader ----
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

        // ---- AOS (désactivé sur mobile) ----
        const isMobile = window.innerWidth < 640;
        AOS.init({
            duration: 600,
            once: true,
            disable: isMobile ? true : false,
        });

        // ---- Toggle sidebar (mobile et desktop) ----
        function toggleSidebar(open) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (window.innerWidth < 768) {
                // Mode mobile
                if (open === undefined) {
                    // Si aucun argument, on inverse
                    const isOpen = sidebar.classList.contains('open');
                    sidebar.classList.toggle('open', !isOpen);
                    overlay.classList.toggle('active', !isOpen);
                } else {
                    sidebar.classList.toggle('open', open);
                    overlay.classList.toggle('active', open);
                }
            } else {
                // Mode tablette/desktop : on toggle la classe collapsed
                sidebar.classList.toggle('collapsed');
            }
        }

        // Bouton d'ouverture sur mobile
        document.getElementById('openSidebarBtn')?.addEventListener('click', () => toggleSidebar(true));
        // Bouton de fermeture dans la sidebar (mobile)
        document.getElementById('closeSidebarBtn')?.addEventListener('click', () => toggleSidebar(false));
        // Bouton toggle sur desktop
        document.getElementById('toggleSidebarBtn')?.addEventListener('click', () => toggleSidebar());

        // Fermeture automatique quand on redimensionne vers desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            }
        });

        // ---- Alertes SweetAlert ----
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

        // ---- Gestion des boutons de suppression (avec confirmation) ----
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
