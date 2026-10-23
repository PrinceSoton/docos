<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCOS - Gestion Professionnelle des Stagiaires</title>

    <!-- Tailwind CSS (avec CDN) -->
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
                    '3xl': '1920px',
                },
                extend: {
                    spacing: {
                        '18': '4.5rem',
                        '88': '22rem',
                        '120': '30rem',
                        '144': '36rem',
                    },
                    // Palette DOCOS (identique aux espaces Admin, Mentor et Stagiaire)
                    colors: {
                        brand: { 50: '#eff5fa', 100: '#dce8f2', 200: '#b9d1e4', 600: '#1d5c8d', 700: '#174a73', 800: '#103860', 900: '#0b2540' },
                        accent: { 50: '#e8f6ef', 100: '#cdebdc', 500: '#22a06b', 600: '#1a8a5c', 700: '#146e4a' },
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Google Font (Inter, comme les espaces de l'application) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            overflow-x: hidden;
        }

        ::selection {
            background: #b9d1e4;
            color: #0b2540;
        }

        /* Logotype : le fichier source est une image large avec marges, on en recadre la partie utile */
        .logotype {
            position: relative;
            overflow: hidden;
            display: block;
            flex-shrink: 0;
        }

        .logotype img {
            position: absolute;
            max-width: none;
            mix-blend-mode: multiply;
            filter: brightness(1.05) contrast(1.08);
        }

        /* ---------- Signature : le cycle du dossier de stage ---------- */
        .cycle-arc {
            stroke-dasharray: 1120;
            stroke-dashoffset: 0;
        }

        .cycle-pointe {
            opacity: 1;
        }

        .js .cycle-arc {
            stroke-dashoffset: 1120;
            animation: traceArc 1.5s cubic-bezier(.65, 0, .35, 1) .45s forwards;
        }

        .js .cycle-pointe {
            opacity: 0;
            animation: apparait .35s ease 1.85s forwards;
        }

        @keyframes traceArc {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes apparait {
            to {
                opacity: 1;
            }
        }

        .fiche {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 54%;
            aspect-ratio: 4 / 5;
            border-radius: 14px;
            transform: translate(-50%, -50%) rotate(var(--r, 0deg)) translate(var(--x, 0), var(--y, 0));
        }

        /* Entrée orchestrée du héros (une seule fois, contenu visible sans JavaScript) */
        .js .entree {
            opacity: 0;
            transform: translateY(14px);
            animation: entree .7s cubic-bezier(.22, 1, .36, 1) forwards;
            animation-delay: var(--d, 0ms);
        }

        .js .fiche.entree {
            transform: translate(-50%, -46%) rotate(var(--r, 0deg)) translate(var(--x, 0), var(--y, 0));
            animation-name: ficheEntree;
        }

        @keyframes entree {
            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes ficheEntree {
            to {
                opacity: 1;
                transform: translate(-50%, -50%) rotate(var(--r, 0deg)) translate(var(--x, 0), var(--y, 0));
            }
        }

        /* L'entrée attend la disparition du loader, puis se joue une seule fois */
        .js:not(.pret) .entree,
        .js:not(.pret) .cycle-arc,
        .js:not(.pret) .cycle-pointe {
            animation-play-state: paused;
        }

        /* État final garanti (animations suspendues par le navigateur, onglet en arrière-plan…) */
        .entree-finie .entree {
            animation: none !important;
            opacity: 1 !important;
            transform: none !important;
        }

        .entree-finie .fiche.entree {
            transform: translate(-50%, -50%) rotate(var(--r, 0deg)) translate(var(--x, 0), var(--y, 0)) !important;
        }

        .entree-finie .cycle-arc {
            animation: none !important;
            stroke-dashoffset: 0 !important;
        }

        .entree-finie .cycle-pointe {
            animation: none !important;
            opacity: 1 !important;
        }

        /* Fiches à onglet (section fonctionnalités) */
        .onglet::before {
            content: "";
            position: absolute;
            left: -1px;
            top: -12px;
            width: 92px;
            height: 13px;
            background: inherit;
            border: 1px solid #dce8f2;
            border-bottom: 0;
            border-radius: 10px 10px 0 0;
        }

        .onglet:hover::before {
            border-color: #b9d1e4;
        }

        /* Loader */
        .loader {
            position: fixed;
            inset: 0;
            background: #0b2540;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s, visibility 0.5s;
        }

        .loader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        /* Désactiver les animations AOS sur petits écrans pour économiser des ressources */
        @media (max-width: 640px) {
            [data-aos] {
                opacity: 1 !important;
                transform: none !important;
                pointer-events: auto !important;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            .js .cycle-arc {
                animation: none;
                stroke-dashoffset: 0;
            }

            .js .cycle-pointe,
            .js .entree {
                animation: none;
                opacity: 1;
                transform: none;
            }

            .js .fiche.entree {
                transform: translate(-50%, -50%) rotate(var(--r, 0deg)) translate(var(--x, 0), var(--y, 0));
            }

            .loader,
            .loader * {
                transition: none !important;
                animation: none !important;
            }

            [data-aos] {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }
    </style>

    {{-- Les animations d'entrée ne masquent du contenu que si JavaScript est actif --}}
    <script>
        document.documentElement.classList.add('js');
    </script>
    <noscript>
        <style>
            #loader {
                display: none !important;
            }

            [data-aos] {
                opacity: 1 !important;
                transform: none !important;
            }
        </style>
    </noscript>
</head>

<body class="bg-brand-50 text-slate-900 antialiased">

    <!-- ============================================
    LOADER
    ============================================ -->
    <div id="loader" class="loader" role="status" aria-label="Chargement">
        <div class="text-center">
            <span class="inline-block rounded-2xl bg-white px-4 py-2 mb-6" aria-hidden="true">
                <span class="logotype w-[198px] h-[76px]">
                    <img src="{{ asset('logo.png') }}" alt="" class="w-[273px] left-[-38px] top-[-36px]" loading="eager">
                </span>
            </span>
            <div class="flex space-x-2 justify-center" aria-hidden="true">
                <div class="w-2.5 h-2.5 bg-white rounded-full animate-bounce"></div>
                <div class="w-2.5 h-2.5 bg-white rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2.5 h-2.5 bg-accent-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>

    <!-- ============================================
    HEADER / NAVIGATION
    ============================================ -->
    <header class="fixed inset-x-0 top-0 z-50 bg-brand-900/95 backdrop-blur border-b border-white/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4 h-16 md:h-[72px]">
                <a href="#haut"
                    class="inline-flex rounded-lg bg-white px-2.5 py-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900"
                    aria-label="DOCOS, retour en haut de page">
                    <span class="logotype w-[120px] h-[46px] md:w-[140px] md:h-[54px]" aria-hidden="true">
                        <img src="{{ asset('logo.png') }}" alt=""
                            class="w-[165px] left-[-23px] top-[-22px] md:w-[193px] md:left-[-27px] md:top-[-25px]">
                    </span>
                </a>

                <nav class="flex items-center gap-2 sm:gap-6" aria-label="Navigation principale">
                    <a href="#features"
                        class="hidden sm:inline rounded text-sm font-medium text-brand-100 hover:text-white transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900">Fonctionnalités</a>
                    <!-- Bouton de connexion -->
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-brand-900 whitespace-nowrap hover:bg-brand-50 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900">
                        <i class="fas fa-sign-in-alt text-xs text-accent-600" aria-hidden="true"></i>
                        <span class="hidden xs:inline">Se Connecter</span>
                        <span class="xs:hidden">Connexion</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main id="haut">
        <!-- ============================================
        SECTION HERO
        ============================================ -->
        <section class="relative overflow-hidden bg-brand-900 text-white pt-28 md:pt-32 pb-28 md:pb-36">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-[1.08fr_1fr] gap-12 lg:gap-10 items-center">

                    <!-- Texte -->
                    <div>
                        <p class="entree inline-flex items-center gap-2 text-xs sm:text-sm font-semibold uppercase tracking-[.14em] text-accent-100"
                            style="--d:80ms">
                            <span class="h-px w-8 bg-accent-500" aria-hidden="true"></span>
                            Gestion numérique des documents des stagiaires
                        </p>
                        <h1 class="entree mt-6 text-[2.5rem] leading-[1.04] xs:text-5xl sm:text-6xl xl:text-[4.4rem] font-extrabold tracking-[-0.035em] text-balance"
                            style="--d:180ms">
                            Gestion Professionnelle des
                            <span class="relative whitespace-nowrap text-accent-100">Stagiaires<svg
                                    class="absolute left-0 -bottom-2 w-full h-3 text-accent-500" viewBox="0 0 200 12"
                                    preserveAspectRatio="none" fill="none" aria-hidden="true">
                                    <path d="M2 9 C 60 2, 140 2, 198 7" stroke="currentColor" stroke-width="4"
                                        stroke-linecap="round" />
                                </svg></span>
                        </h1>
                        <p class="entree mt-7 text-base sm:text-lg leading-relaxed text-brand-100 max-w-xl" style="--d:280ms">
                            Solution complète et moderne pour gérer efficacement vos stagiaires, mentors et projets au
                            sein de votre entreprise.
                        </p>
                        <div class="entree flex flex-col xs:flex-row flex-wrap gap-3 mt-9" style="--d:380ms">
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-accent-500 px-6 py-3.5 text-base font-semibold text-brand-900 whitespace-nowrap hover:bg-accent-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900">
                                Débuter l'expérience
                                <i class="fas fa-arrow-right text-sm" aria-hidden="true"></i>
                            </a>
                            <a href="#features"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/25 px-6 py-3.5 text-base font-semibold text-white whitespace-nowrap hover:bg-white/10 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900">
                                Découvrir
                                <i class="fas fa-chevron-down text-sm" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Illustration : le dossier de stage dans la flèche circulaire du logo -->
                    <div class="relative mx-auto w-full max-w-[300px] xs:max-w-[340px] sm:max-w-[420px] lg:max-w-[470px]"
                        role="img"
                        aria-label="Illustration : les documents du stagiaire (présence, rapports, attestations) réunis dans le cycle DOCOS">
                        <div class="relative aspect-square">
                            <svg class="absolute inset-0 h-full w-full" viewBox="0 0 480 480" fill="none" aria-hidden="true">
                                <circle cx="240" cy="240" r="200" stroke="rgba(185,209,228,.12)" stroke-width="1" />
                                <path class="cycle-arc" d="M308.4 52.1 A200 200 0 1 1 171.6 52.1" stroke="#22a06b"
                                    stroke-width="14" stroke-linecap="round" />
                                <path class="cycle-pointe" d="M190.5 45.4 L170.4 66.7 L163.2 41.6 Z" fill="#22a06b" />
                            </svg>

                            <!-- Fiches empilées (arrière-plan) -->
                            <div class="fiche entree bg-brand-700 border border-white/10" style="--r:-11deg;--x:-6%;--y:2%;--d:520ms" aria-hidden="true"></div>
                            <div class="fiche entree bg-brand-200" style="--r:7deg;--x:7%;--y:-1%;--d:600ms" aria-hidden="true"></div>

                            <!-- Fiche principale -->
                            <div class="fiche entree bg-white shadow-[0_28px_60px_-24px_rgba(0,0,0,.55)] p-[7%] flex flex-col"
                                style="--d:700ms" aria-hidden="true">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-md bg-brand-900 text-white text-xs">
                                        <i class="fas fa-folder-open"></i>
                                    </span>
                                    <span class="h-2 w-[42%] rounded-full bg-brand-100"></span>
                                </div>
                                <div class="mt-[9%] grid gap-[6%] flex-1 content-start">
                                    @foreach ([['calendar-check', 'Présence'], ['file-alt', 'Rapports'], ['award', 'Attestations']] as [$icone, $libelle])
                                        <div class="flex items-center gap-2.5 rounded-lg border border-brand-100 bg-brand-50 px-2.5 py-2">
                                            <i class="fas fa-{{ $icone }} w-4 text-center text-[13px] text-brand-800"></i>
                                            <span class="text-[12px] sm:text-[13px] font-semibold text-brand-900">{{ $libelle }}</span>
                                            <span class="ml-auto flex h-4 w-4 items-center justify-center rounded-full bg-accent-500 text-white">
                                                <i class="fas fa-check text-[8px]"></i>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-auto grid gap-1.5">
                                    <span class="h-1.5 w-full rounded-full bg-brand-100"></span>
                                    <span class="h-1.5 w-[70%] rounded-full bg-brand-100"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transition courbe vers la section suivante -->
            <svg class="absolute inset-x-0 bottom-[-1px] w-full h-12 sm:h-20 text-brand-50" viewBox="0 0 1440 80"
                preserveAspectRatio="none" fill="currentColor" aria-hidden="true">
                <path d="M0 80 L0 40 C 360 0, 1080 0, 1440 40 L1440 80 Z" />
            </svg>
        </section>

        <!-- ============================================
        SECTION FONCTIONNALITÉS
        ============================================ -->
        <section id="features" class="scroll-mt-20 py-16 sm:py-20 md:py-24">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)] gap-10 lg:gap-14">
                    <div class="lg:sticky lg:top-28 lg:self-start" data-aos="fade-up" data-aos-once="true">
                        <span class="block h-1 w-12 rounded-full bg-accent-500" aria-hidden="true"></span>
                        <h2 class="mt-5 text-3xl sm:text-4xl font-extrabold tracking-[-0.025em] text-brand-900">
                            Fonctionnalités clés
                        </h2>
                        <p class="mt-3 text-base sm:text-lg leading-relaxed text-slate-600">
                            Tout ce dont vous avez besoin pour gérer vos stagiaires efficacement.
                        </p>
                    </div>

                    <!-- Fiches : 1 colonne sur mobile, 2 à partir de la tablette -->
                    @php
                        $fonctionnalites = [
                            ['users', 'Gestion des Stagiaires', "Suivez l'ensemble de vos stagiaires, leur progression et performances en temps réel."],
                            ['tasks', 'Projets & Tâches', 'Assignez et suivez les projets avec un système de tâches complet et intuitif.'],
                            ['chart-line', 'Rapports & Stats', 'Générez des rapports détaillés et visualisez les statistiques de performance.'],
                            ['calendar-check', 'Présence', 'Suivez la présence des stagiaires avec un système fiable et sans faille.'],
                            ['file-alt', 'Documents', 'Stockez, partagez et téléchargez tous vos documents en toute sécurité.'],
                            ['award', 'Attestations', "Gérez les demandes et l'envoi d'attestations et conventions de stage."],
                        ];
                    @endphp
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-9 pt-3">
                        @foreach ($fonctionnalites as $i => [$icone, $titre, $texte])
                            <li class="onglet relative rounded-xl rounded-tl-none border border-brand-100 bg-white p-6 transition-colors hover:border-brand-200"
                                data-aos="fade-up" data-aos-delay="{{ 60 * ($i % 2) }}" data-aos-once="true">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-900 text-white"
                                        aria-hidden="true">
                                        <i class="fas fa-{{ $icone }} text-sm"></i>
                                    </span>
                                    <h3 class="text-lg font-semibold tracking-tight text-brand-900">{{ $titre }}</h3>
                                </div>
                                <p class="mt-3 text-[15px] leading-relaxed text-slate-600">{{ $texte }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>

        <!-- ============================================
        SECTION APPEL À L'ACTION (CTA)
        ============================================ -->
        <section class="pb-20 sm:pb-24">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative" data-aos="fade-up" data-aos-once="true">
                    <!-- Feuilles empilées sous la feuille principale -->
                    <div class="absolute inset-0 rounded-2xl bg-brand-200 rotate-[-2.2deg] translate-y-2" aria-hidden="true"></div>
                    <div class="absolute inset-0 rounded-2xl bg-brand-100 rotate-[1.4deg]" aria-hidden="true"></div>
                    <div
                        class="relative rounded-2xl border border-brand-100 bg-white px-6 py-10 sm:px-12 sm:py-12 text-center">
                        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-[-0.025em] text-brand-900">Prêt à Commencer ?</h2>
                        <p class="mt-3 text-base sm:text-lg text-slate-600">
                            Rejoignez-nous et gérez vos stagiaires de manière professionnelle.
                        </p>
                        <a href="{{ route('login') }}"
                            class="mt-8 inline-flex items-center justify-center gap-2 rounded-lg bg-brand-800 px-7 py-3.5 text-base font-semibold text-white whitespace-nowrap hover:bg-brand-700 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
                            Accéder à la Plateforme
                            <i class="fas fa-arrow-right text-sm text-accent-100" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ============================================
    FOOTER
    ============================================ -->
    <footer class="bg-brand-900 text-brand-100">
        <div
            class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col md:flex-row items-center md:items-center justify-between gap-6">
            <span class="inline-flex rounded-lg bg-white px-2.5 py-1" role="img" aria-label="DOCOS">
                <span class="logotype w-[120px] h-[46px]" aria-hidden="true">
                    <img src="{{ asset('logo.png') }}" alt="" class="w-[165px] left-[-23px] top-[-22px]">
                </span>
            </span>
            <nav class="flex items-center gap-6 text-sm" aria-label="Pied de page">
                <a href="#features"
                    class="rounded hover:text-white transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900">Fonctionnalités</a>
                <a href="{{ route('login') }}"
                    class="rounded hover:text-white transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900">Se Connecter</a>
            </nav>
            <p class="text-xs sm:text-sm text-brand-200">
                &copy; {{ date('Y') }} DOCOS. Tous droits réservés.
            </p>
        </div>
    </footer>

    <!-- ============================================
    SCRIPTS
    ============================================ -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    <script>
        const mouvementReduit = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // ---- Loader ----
        let loaderMasque = false;
        const masquerLoader = () => {
            if (loaderMasque) return;
            loaderMasque = true;
            document.getElementById('loader').classList.add('hidden');
            // Lance l'entrée du héros, puis fige l'état final quelle que soit la suite
            document.documentElement.classList.add('pret');
            setTimeout(() => document.documentElement.classList.add('entree-finie'), 2600);
        };
        window.addEventListener('load', function() {
            setTimeout(masquerLoader, mouvementReduit ? 0 : 1500);
        });
        // Animations limitées : pas d'attente du chargement complet des ressources externes
        if (mouvementReduit) document.addEventListener('DOMContentLoaded', masquerLoader);
        // Filet de sécurité : le loader ne bloque jamais la page si une ressource tarde
        setTimeout(masquerLoader, 6000);

        // ---- AOS ----
        // Désactiver les animations sur les petits écrans (et si l'utilisateur limite les animations)
        const isMobile = window.innerWidth < 640;
        if (window.AOS) {
            AOS.init({
                duration: 700,
                once: true,
                offset: 80,
                disable: isMobile || mouvementReduit,
            });
        }

        // ---- GSAP (optionnel, pour des animations supplémentaires) ----
        if (window.gsap && window.ScrollTrigger) gsap.registerPlugin(ScrollTrigger);
    </script>
</body>

</html>
