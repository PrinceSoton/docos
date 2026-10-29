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
                    },
                    // Palette DOCOS tirée du logo : bleu marine (structure, actions principales)
                    // et vert (accent, progression). Les couleurs de statut restent sémantiques.
                    colors: {
                        brand: {
                            50: '#eff5fa',
                            100: '#dce8f2',
                            200: '#b9d1e4',
                            600: '#1d5c8d',
                            700: '#174a73',
                            800: '#103860',
                            900: '#0b2540',
                        },
                        accent: {
                            50: '#e8f6ef',
                            100: '#cdebdc',
                            500: '#22a06b',
                            600: '#1a8a5c',
                            700: '#146e4a',
                        },
                    }
                }
            }
        }
    </script>

    <!-- Flowbite, FontAwesome, SweetAlert2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css" rel="stylesheet" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        /* Sidebar – claire, fixe et pleine hauteur dès 768px (md) */
        .sidebar {
            background: #fff;
            border-right: 1px solid #dce8f2;
            width: 264px;
            height: 100vh;
            z-index: 60;
        }

        /* Mobile (< 768px) : menu latéral masqué par défaut, ouvert via le bouton hamburger */
        @media (max-width: 767px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 280px;
                max-width: 85vw;
                transform: translateX(-100%);
                visibility: hidden;
                transition: transform .3s cubic-bezier(.22, 1, .36, 1), visibility 0s linear .3s;
                box-shadow: 0 0 24px rgba(15, 23, 42, .18);
            }

            .sidebar.open {
                transform: translateX(0);
                visibility: visible;
                transition-delay: 0s;
            }

            /* Overlay pour fermer le menu */
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(11, 37, 64, .45);
                z-index: 50;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 2px 12px;
            padding: 10px 12px;
            border-radius: 8px;
            color: #475569;
            font-size: 14px;
            font-weight: 500;
            transition: background-color .2s ease, color .2s ease;
        }

        .sidebar-item i {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            color: #94a3b8;
            transition: color .2s ease;
        }

        .sidebar-item:hover {
            background: #eff5fa;
            color: #0b2540;
        }

        .sidebar-item:hover i {
            color: #475569;
        }

        .sidebar-item.active {
            background: #103860;
            color: #fff;
        }

        .sidebar-item.active i {
            color: #fff;
        }

        .sidebar-item:focus-visible,
        .sidebar-btn:focus-visible {
            outline: 2px solid #1d5c8d;
            outline-offset: 2px;
        }

        .sidebar-item.sidebar-danger:hover,
        .sidebar-item.sidebar-danger:hover i {
            background: #fef2f2;
            color: #b91c1c;
        }

        .skip-link {
            position: absolute;
            left: 12px;
            top: -48px;
            z-index: 10000;
            padding: 8px 14px;
            border-radius: 8px;
            background: #103860;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
        }

        .skip-link:focus {
            top: 12px;
            outline: 2px solid #fff;
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {

            .sidebar,
            .sidebar-item,
            .sidebar-item i {
                transition: none;
            }
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

        .loader-logo {
            width: 180px;
            height: 100px;
            object-fit: contain;
            display: block;
        }

        .loader-spinner {
            width: 28px;
            height: 28px;
            margin: 12px 0;
            border: 3px solid #dce8f2;
            border-top-color: #103860;
            border-radius: 50%;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        /* Loader : disparaît à la fin du chargement (transition CSS, sans dépendre d'une bibliothèque) */
        .loader-overlay {
            transition: opacity .25s ease;
        }

        .loader-overlay.is-done {
            opacity: 0;
            pointer-events: none;
        }

        /* Révélation à l'entrée : l'état initial n'est posé que par le script, le contenu reste visible sans JS */
        .reveal {
            opacity: 0;
            transform: translateY(8px);
        }

        .reveal.is-revealed {
            opacity: 1;
            transform: none;
            transition: opacity .45s cubic-bezier(.22, 1, .36, 1), transform .45s cubic-bezier(.22, 1, .36, 1);
            transition-delay: var(--reveal-delay, 0ms);
        }

        /* État final garanti même si le navigateur suspend les animations (onglet en arrière-plan, etc.) */
        .reveal.is-revealed.reveal-fini {
            opacity: 1;
            transform: none;
            transition: none;
        }

        /* Notifications (toasts) */
        .toast {
            transition: opacity .25s ease, transform .25s cubic-bezier(.22, 1, .36, 1);
        }

        .toast.is-entering,
        .toast.is-leaving {
            opacity: 0;
            transform: translateY(8px);
        }

        @media (prefers-reduced-motion: reduce) {

            .loader-overlay,
            .toast,
            .reveal.is-revealed {
                transition: none;
            }

            .reveal {
                opacity: 1;
                transform: none;
            }
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::selection {
            background: #b9d1e4;
            color: #0b2540;
        }

        input,
        textarea,
        select {
            caret-color: #103860;
        }
    </style>
    @stack('styles')
    {{-- Sans JavaScript, le loader ne doit jamais masquer la page --}}
    <noscript>
        <style>
            #appLoader {
                display: none !important;
            }
        </style>
    </noscript>
</head>

<body class="bg-brand-50 text-slate-900">

    <a href="#contenu" class="skip-link">Aller au contenu</a>

    <!-- Loader -->
    <div class="loader-overlay" id="appLoader">
        <img src="{{ asset('logo.png') }}" alt="DOCOS" class="loader-logo" loading="eager">
        <div class="loader-spinner spin" aria-hidden="true"></div>
        <p class="text-slate-600 text-sm">Espace Stagiaire</p>
    </div>

    <div class="flex h-screen overflow-hidden">

        <!-- Overlay pour mobile (fermeture du menu) -->
        <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar(false)"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar flex-shrink-0 flex flex-col" aria-label="Navigation principale">
            <!-- Logo -->
            <div class="flex items-center justify-between gap-2 px-5 py-3 flex-shrink-0 border-b border-slate-200">
                <!-- Le logo source est une image large avec marges : on en recadre le logotype (symbole + DOCOS) -->
                <div class="relative w-[188px] h-[72px] overflow-hidden flex-shrink-0" role="img"
                    aria-label="DOCOS, espace stagiaire">
                    <img src="{{ asset('logo.png') }}" alt="" aria-hidden="true"
                        class="absolute max-w-none w-[259px] mix-blend-multiply"
                        style="left:-36px;top:-34px;filter:brightness(1.05) contrast(1.08)" loading="lazy">
                </div>
                <!-- Fermeture du menu (mobile uniquement) -->
                <button id="closeSidebarBtn" type="button"
                    class="sidebar-btn md:hidden inline-flex items-center justify-center h-10 w-10 -mr-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100"
                    aria-label="Fermer le menu">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>

            <!-- Profil du stagiaire -->
            @php
                $stagiaire = Auth::user()->stagiaire;
                if ($stagiaire) {
                    // Progression calculée en jours calendaires : 0 % avant le début, 100 % au dernier jour et après
                    $sbDebut = $stagiaire->date_debut->copy()->startOfDay();
                    $sbFin = $stagiaire->date_fin->copy()->startOfDay();
                    $sbAujourdhui = now()->startOfDay();
                    $sbTotal = (int) $sbDebut->diffInDays($sbFin, false);
                    if ($sbAujourdhui->lt($sbDebut)) {
                        $sbPct = 0;
                    } elseif ($sbAujourdhui->gte($sbFin)) {
                        $sbPct = 100;
                    } else {
                        $sbPct = (int) round(((int) $sbDebut->diffInDays($sbAujourdhui, false) / $sbTotal) * 100);
                    }
                }
            @endphp
            <div class="mx-3 mt-3 mb-1 p-3 rounded-xl bg-brand-50 border border-brand-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-brand-800 flex items-center justify-center flex-shrink-0 overflow-hidden">
                        @if (Auth::user()->photo)
                            <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt=""
                                class="w-full h-full object-cover" loading="lazy">
                        @else
                            <span
                                class="text-white font-semibold text-sm">{{ strtoupper(substr(Auth::user()->prenom, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-slate-900 font-semibold text-sm truncate">{{ Auth::user()->nom_complet }}</p>
                        <p class="text-slate-600 text-xs truncate tabular-nums">
                            {{ $stagiaire?->matricule ?? 'Stagiaire' }}</p>
                    </div>
                </div>
                @if ($stagiaire)
                    <div class="mt-3">
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-slate-600">Avancement du stage</span>
                            <span class="text-slate-900 font-medium tabular-nums">{{ $sbPct }} %</span>
                        </div>
                        <div class="bg-white rounded-full h-1.5 overflow-hidden" role="progressbar"
                            aria-label="Avancement du stage" aria-valuemin="0" aria-valuemax="100"
                            aria-valuenow="{{ $sbPct }}">
                            <div class="h-1.5 rounded-full bg-accent-500" style="width:{{ $sbPct }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-3 overflow-y-auto" aria-label="Menu stagiaire">
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
                    @php $actif = request()->routeIs(str_ends_with($route, '.index') ? substr($route, 0, -5) . '*' : $route); @endphp
                    <a href="{{ route($route) }}" class="sidebar-item {{ $actif ? 'active' : '' }}"
                        @if ($actif) aria-current="page" @endif>
                        <i class="fas fa-{{ $icon }}" aria-hidden="true"></i>
                        <span>{{ $label }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- Pied de menu -->
            <div class="py-3 border-t border-brand-100 flex-shrink-0">
                <a href="{{ route('profile.index') }}" class="sidebar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                    @if (request()->routeIs('profile.*')) aria-current="page" @endif>
                    <i class="fas fa-user-cog" aria-hidden="true"></i>
                    <span>Mon Profil</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="sidebar-item sidebar-danger w-[calc(100%-24px)] text-left">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Contenu principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header
                class="bg-white border-b border-brand-100 px-4 sm:px-6 py-3 flex items-center justify-between sticky top-0 z-40">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Bouton d'ouverture du menu sur mobile -->
                    <button id="openSidebarBtn" type="button"
                        class="md:hidden inline-flex items-center justify-center h-10 w-10 -ml-2 rounded-lg text-slate-700 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600"
                        aria-label="Ouvrir le menu" aria-controls="sidebar" aria-expanded="false">
                        <i class="fas fa-bars text-lg" aria-hidden="true"></i>
                    </button>
                    <div class="min-w-0" data-reveal-titre>
                        <h1 class="text-slate-900 font-semibold text-lg leading-tight truncate">@yield('titre', 'Mon Espace')</h1>
                        <p class="text-slate-500 text-xs truncate">@yield('breadcrumb', 'Stagiaire')</p>
                    </div>
                </div>
            </header>

            <!-- Main -->
            <main id="contenu" tabindex="-1"
                class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 focus:outline-none">
                @if ($errors->any())
                    {{-- Résumé des erreurs de validation : reste affiché (pas de toast), le détail est aussi près des champs --}}
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl max-w-6xl mx-auto" role="alert"
                        aria-labelledby="resume-erreurs-titre">
                        <p id="resume-erreurs-titre" class="text-red-800 text-sm font-medium mb-1">Certaines informations
                            sont à corriger :</p>
                        <ul class="list-disc list-inside text-red-700 text-sm">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div id="page-contenu">@yield('content')</div>
            </main>
        </div>
    </div>

    <!-- Notifications : messages flash existants (succes / erreur), non bloquants -->
    <div id="toasts" aria-live="polite"
        class="fixed z-[70] inset-x-3 bottom-3 sm:inset-x-auto sm:bottom-auto sm:top-[76px] sm:right-6 sm:w-[380px] flex flex-col gap-2 pointer-events-none">
        @foreach ([['succes', 'Succès', 'status', 6000, 'fa-check', 'bg-emerald-50 text-emerald-700'], ['erreur', 'Action impossible', 'alert', 10000, 'fa-exclamation', 'bg-red-50 text-red-700']] as [$cle, $titreToast, $roleToast, $dureeToast, $iconeToast, $pastilleToast])
            @if (session($cle))
                <div class="toast pointer-events-auto flex items-start gap-3 rounded-xl border border-brand-100 bg-white p-4 shadow-[0_12px_32px_-12px_rgba(11,37,64,.35)]"
                    role="{{ $roleToast }}" data-duree="{{ $dureeToast }}">
                    <span class="mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full {{ $pastilleToast }}"
                        aria-hidden="true"><i class="fas {{ $iconeToast }} text-xs"></i></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-900">{{ $titreToast }}</p>
                        <p class="mt-0.5 text-sm text-slate-600 break-words">{{ session($cle) }}</p>
                    </div>
                    <button type="button"
                        class="toast-fermer -mr-1 -mt-1 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-brand-50 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600"
                        aria-label="Fermer la notification">
                        <i class="fas fa-times text-sm" aria-hidden="true"></i>
                    </button>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>

    <script>
        const mouvementReduit = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // ---- Révélation à l'entrée et au défilement ----
        // Les blocs de premier niveau de la page (en-têtes, sections, grilles) apparaissent une seule fois,
        // par groupes, avec un léger décalage. Rien n'est masqué si le script ne s'exécute pas.
        const Revelation = (() => {
            const racine = document.getElementById('page-contenu');
            if (mouvementReduit || !racine || !('IntersectionObserver' in window)) return { demarrer() {} };

            const unites = [];
            const titre = document.querySelector('[data-reveal-titre]');
            if (titre) unites.push(titre);
            const estConteneur = el => el.children.length > 1
                && !el.matches('section, article, form, ul, ol, table, dl, fieldset, a, button, li, nav, header')
                && /(^|\s)(grid|space-y-\d+|max-w-\S+)(\s|$)/.test(el.className);
            (function parcourir(el, profondeur) {
                for (const enfant of el.children) {
                    if (enfant.matches('script, style, template, [data-sans-revelation]')) continue;
                    if (profondeur < 4 && estConteneur(enfant)) parcourir(enfant, profondeur + 1);
                    else unites.push(enfant);
                }
            })(racine, 0);
            unites.forEach(el => el.classList.add('reveal'));

            let demarre = false;
            return {
                demarrer() {
                    if (demarre) return;
                    demarre = true;
                    const observateur = new IntersectionObserver(entrees => {
                        const visibles = entrees.filter(e => e.isIntersecting);
                        visibles.forEach((e, i) => {
                            const delai = Math.min(i, 6) * 70;
                            e.target.style.setProperty('--reveal-delay', delai + 'ms');
                            e.target.classList.add('is-revealed');
                            observateur.unobserve(e.target);
                            setTimeout(() => e.target.classList.add('reveal-fini'), delai + 700);
                        });
                    }, { root: null, rootMargin: '0px', threshold: 0 });
                    unites.forEach(el => observateur.observe(el));
                    // Filet de sécurité : un bloc non observable (ex. impression) ne reste jamais invisible
                    window.addEventListener('beforeprint', () => unites.forEach(el => el.classList.add('is-revealed')));
                }
            };
        })();

        // ---- Notifications (toasts) ----
        const Toasts = (() => {
            const zone = document.getElementById('toasts');
            const toasts = zone ? [...zone.querySelectorAll('.toast')] : [];
            const fermer = toast => {
                if (!toast.isConnected || toast.classList.contains('is-leaving')) return;
                toast.classList.add('is-leaving');
                setTimeout(() => toast.remove(), mouvementReduit ? 0 : 260);
            };
            toasts.forEach(toast => {
                toast.querySelector('.toast-fermer')?.addEventListener('click', () => fermer(toast));
                toast.addEventListener('keydown', e => { if (e.key === 'Escape') fermer(toast); });
            });
            return {
                demarrer() {
                    toasts.forEach(toast => {
                        // Réinsertion pour que les lecteurs d'écran annoncent le message
                        toast.classList.add('is-entering');
                        zone.appendChild(toast);
                        void toast.offsetHeight; // applique l'état initial avant la transition (sans dépendre de requestAnimationFrame)
                        toast.classList.remove('is-entering');
                        let restant = parseInt(toast.dataset.duree, 10) || 6000;
                        let debut = Date.now();
                        let minuterie = setTimeout(() => fermer(toast), restant);
                        const pause = () => { clearTimeout(minuterie); restant -= Date.now() - debut; };
                        const reprise = () => { debut = Date.now(); clearTimeout(minuterie); minuterie = setTimeout(() => fermer(toast), Math.max(restant, 1500)); };
                        toast.addEventListener('mouseenter', pause);
                        toast.addEventListener('mouseleave', reprise);
                        toast.addEventListener('focusin', pause);
                        toast.addEventListener('focusout', reprise);
                    });
                }
            };
        })();

        // ---- Loader : masqué à la fin du chargement, avec un filet de sécurité si une ressource tarde ----
        (() => {
            const loader = document.getElementById('appLoader');
            let termine = false;
            const terminer = () => {
                if (termine) return;
                termine = true;
                if (loader) {
                    loader.classList.add('is-done');
                    setTimeout(() => { loader.style.display = 'none'; }, mouvementReduit ? 0 : 260);
                }
                Revelation.demarrer();
                Toasts.demarrer();
            };
            if (document.readyState === 'complete') terminer();
            else window.addEventListener('load', terminer);
            setTimeout(terminer, 4000);
        })();

        // ---- Menu latéral : ouvert/fermé uniquement sur mobile (< 768px), fixe au-delà ----
        function toggleSidebar(open) {
            if (window.innerWidth >= 768) return;
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const opener = document.getElementById('openSidebarBtn');
            const isOpen = open === undefined ? !sidebar.classList.contains('open') : open;
            sidebar.classList.toggle('open', isOpen);
            overlay.classList.toggle('active', isOpen);
            opener?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (isOpen) {
                document.getElementById('closeSidebarBtn')?.focus();
            } else {
                opener?.focus();
            }
        }

        document.getElementById('openSidebarBtn')?.addEventListener('click', () => toggleSidebar(true));
        document.getElementById('closeSidebarBtn')?.addEventListener('click', () => toggleSidebar(false));
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && document.getElementById('sidebar').classList.contains('open')) {
                toggleSidebar(false);
            }
        });

        // Réinitialise le menu mobile quand on repasse en mode ordinateur
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('sidebarOverlay').classList.remove('active');
                document.getElementById('openSidebarBtn')?.setAttribute('aria-expanded', 'false');
            }
        });

        // ---- Gestion des boutons de suppression (avec confirmation) ----
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const form = btn.closest('form');
                Swal.fire({
                    title: 'Supprimer ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#b91c1c',
                    cancelButtonColor: '#103860',
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
