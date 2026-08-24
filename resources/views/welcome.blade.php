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
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Google Font (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Styles personnalisés -->
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            overflow-x: hidden;
        }

        /* Gradient de fond */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* Dégradé de texte */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Effet de verre */
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Animation de flottement */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        /* Loader */
        .loader {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        /* Amélioration du survol des cartes */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Bouton primaire */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        /* Ajustements pour l'accessibilité tactile */
        @media (max-width: 640px) {

            .btn-primary,
            .glass-effect {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
                font-size: 1rem;
            }
        }

        /* Désactiver les animations AOS sur petits écrans pour économiser des ressources */
        @media (max-width: 640px) {
            [data-aos] {
                opacity: 1 !important;
                transform: none !important;
                pointer-events: auto !important;
            }
        }
    </style>
</head>

<body>

    <!-- ============================================
    LOADER
    ============================================ -->
    <div id="loader" class="loader" role="status" aria-label="Chargement">
        <div class="text-center">
            <img src="{{ asset('logo.png') }}" alt="DOCOS"
                class="relative rounded-3xl shadow-2xl w-32 xs:w-48 md:w-64 mb-6 floating" loading="lazy">
            <div class="flex space-x-2 justify-center" aria-hidden="true">
                <div class="w-3 h-3 bg-white rounded-full animate-bounce"></div>
                <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>

    <!-- ============================================
    HEADER / NAVIGATION
    ============================================ -->
    <header class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-md shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20">
                <!-- Logo + Nom -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('logo.png') }}" alt="DOCOS" class="h-10 md:h-12 w-auto rounded-xl shadow-md"
                        loading="lazy">
                    <span class="text-xl md:text-2xl font-bold gradient-text">DOCOS</span>
                </div>

                <!-- Bouton de connexion -->
                <a href="{{ route('login') }}"
                    class="btn-primary text-white px-5 py-2.5 md:px-8 md:py-3 rounded-full font-semibold flex items-center space-x-2 text-sm md:text-base transition-all hover:shadow-xl">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="hidden xs:inline">Se Connecter</span>
                    <span class="xs:hidden">Connexion</span>
                </a>
            </div>
        </div>
    </header>

    <!-- ============================================
    SECTION HERO
    ============================================ -->
    <section class="min-h-screen flex items-center gradient-bg pt-16 md:pt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
            <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">

                <!-- Texte -->
                <div data-aos="fade-right" data-aos-duration="800" data-aos-once="true">
                    <h1 class="text-3xl xs:text-4xl sm:text-5xl md:text-6xl font-bold text-white leading-tight">
                        Gestion Professionnelle des
                        <span class="text-yellow-300">Stagiaires</span>
                    </h1>
                    <p class="text-base xs:text-lg md:text-xl text-white/90 mt-4 md:mt-6 max-w-2xl">
                        Solution complète et moderne pour gérer efficacement vos stagiaires, mentors et projets au sein
                        de votre entreprise.
                    </p>
                    <div class="flex flex-wrap gap-3 sm:gap-4 mt-6 md:mt-8">
                        <a href="{{ route('login') }}"
                            class="bg-white text-purple-600 px-6 sm:px-8 py-3 md:py-4 rounded-full font-bold text-sm sm:text-base md:text-lg hover:shadow-2xl hover:scale-105 transition-all inline-flex items-center">
                            Débuter l'expérience
                            <i class="fas fa-arrow-right ml-2 hidden sm:inline"></i>
                        </a>
                        <a href="#features"
                            class="glass-effect text-white px-6 sm:px-8 py-3 md:py-4 rounded-full font-bold text-sm sm:text-base md:text-lg hover:bg-white/20 transition-all inline-flex items-center">
                            Découvrir
                            <i class="fas fa-chevron-down ml-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Image (flottante) -->
                <div data-aos="fade-left" data-aos-duration="800" data-aos-once="true"
                    class="flex justify-center lg:justify-end">
                    <div class="relative w-48 xs:w-56 sm:w-72 md:w-80 lg:w-96 xl:w-104 2xl:w-120">
                        <div class="absolute inset-0 bg-yellow-300/30 rounded-3xl blur-3xl -z-10"></div>
                        <img src="{{ asset('logo.png') }}" alt="DOCOS - Illustration"
                            class="relative rounded-3xl shadow-2xl w-full h-auto floating" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
    SECTION FONCTIONNALITÉS
    ============================================ -->
    <section id="features" class="py-16 sm:py-20 md:py-24 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 md:mb-16" data-aos="fade-up" data-aos-once="true">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold gradient-text">Fonctionnalités clés</h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 mt-3 max-w-2xl mx-auto">
                    Tout ce dont vous avez besoin pour gérer vos stagiaires efficacement.
                </p>
            </div>

            <!-- Grille responsive : 1 colonne sur mobile, 2 sur tablette, 3 sur desktop -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">

                <!-- Carte 1 -->
                <div class="hover-lift bg-gradient-to-br from-blue-50 to-blue-100/60 p-6 md:p-8 rounded-3xl"
                    data-aos="fade-up" data-aos-delay="100" data-aos-once="true">
                    <div
                        class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-users text-2xl md:text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Gestion des Stagiaires</h3>
                    <p class="text-sm md:text-base text-gray-600">Suivez l'ensemble de vos stagiaires, leur progression
                        et performances en temps réel.</p>
                </div>

                <!-- Carte 2 -->
                <div class="hover-lift bg-gradient-to-br from-purple-50 to-purple-100/60 p-6 md:p-8 rounded-3xl"
                    data-aos="fade-up" data-aos-delay="150" data-aos-once="true">
                    <div
                        class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-tasks text-2xl md:text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Projets & Tâches</h3>
                    <p class="text-sm md:text-base text-gray-600">Assignez et suivez les projets avec un système de
                        tâches complet et intuitif.</p>
                </div>

                <!-- Carte 3 -->
                <div class="hover-lift bg-gradient-to-br from-pink-50 to-pink-100/60 p-6 md:p-8 rounded-3xl"
                    data-aos="fade-up" data-aos-delay="200" data-aos-once="true">
                    <div
                        class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-pink-500 to-red-600 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-chart-line text-2xl md:text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Rapports & Stats</h3>
                    <p class="text-sm md:text-base text-gray-600">Générez des rapports détaillés et visualisez les
                        statistiques de performance.</p>
                </div>

                <!-- Carte 4 -->
                <div class="hover-lift bg-gradient-to-br from-green-50 to-green-100/60 p-6 md:p-8 rounded-3xl"
                    data-aos="fade-up" data-aos-delay="250" data-aos-once="true">
                    <div
                        class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-calendar-check text-2xl md:text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Présence</h3>
                    <p class="text-sm md:text-base text-gray-600">Suivez la présence des stagiaires avec un système
                        fiable et sans faille.</p>
                </div>

                <!-- Carte 5 -->
                <div class="hover-lift bg-gradient-to-br from-yellow-50 to-yellow-100/60 p-6 md:p-8 rounded-3xl"
                    data-aos="fade-up" data-aos-delay="300" data-aos-once="true">
                    <div
                        class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-file-alt text-2xl md:text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Documents</h3>
                    <p class="text-sm md:text-base text-gray-600">Stockez, partagez et téléchargez tous vos documents
                        en toute sécurité.</p>
                </div>

                <!-- Carte 6 -->
                <div class="hover-lift bg-gradient-to-br from-indigo-50 to-indigo-100/60 p-6 md:p-8 rounded-3xl"
                    data-aos="fade-up" data-aos-delay="350" data-aos-once="true">
                    <div
                        class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-award text-2xl md:text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Attestations</h3>
                    <p class="text-sm md:text-base text-gray-600">Gérez les demandes et l'envoi d'attestations et
                        conventions de stage.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- ============================================
    SECTION APPEL À L'ACTION (CTA)
    ============================================ -->
    <section class="py-16 sm:py-20 md:py-24 gradient-bg relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div
                class="absolute top-0 left-0 w-72 h-72 bg-white rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2">
            </div>
            <div
                class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl translate-x-1/3 translate-y-1/3">
            </div>
        </div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10" data-aos="zoom-in"
            data-aos-once="true">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4 md:mb-6">Prêt à Commencer ?</h2>
            <p class="text-lg sm:text-xl md:text-2xl text-white/90 mb-6 md:mb-8">
                Rejoignez-nous et gérez vos stagiaires de manière professionnelle.
            </p>
            <a href="{{ route('login') }}"
                class="inline-block bg-white text-purple-600 px-8 sm:px-12 py-3.5 md:py-4 rounded-full font-bold text-base sm:text-lg md:text-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
                Accéder à la Plateforme
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </section>

    <!-- ============================================
    FOOTER
    ============================================ -->
    <footer class="bg-gray-900 text-white py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <img src="{{ asset('logo.png') }}" alt="DOCOS"
                class="relative rounded-3xl shadow-2xl h-12 md:h-16 w-auto mx-auto mb-4" loading="lazy">
            <p class="text-xs sm:text-sm text-gray-400">
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
        // ---- Loader ----
        window.addEventListener('load', function() {
            setTimeout(() => {
                document.getElementById('loader').classList.add('hidden');
            }, 1500);
        });

        // ---- AOS ----
        // Désactiver les animations sur les petits écrans pour économiser les ressources
        const isMobile = window.innerWidth < 640;
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
            disable: isMobile ? true : false, // désactive AOS sur mobile (<640px)
        });

        // ---- GSAP (optionnel, pour des animations supplémentaires) ----
        gsap.registerPlugin(ScrollTrigger);

        // Exemple : animation au scroll des cartes (déjà gérées par AOS, mais on peut ajouter un effet)
        // Ici on ne fait rien de plus pour éviter le conflit avec AOS.
    </script>
</body>

</html>
