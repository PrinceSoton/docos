{{-- @extends('layouts.app')
@section('titre', 'Bienvenue')
@section('content')
    <div class="min-h-screen relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-slate-900"></div>
        <div class="absolute top-20 left-10 w-72 h-72 bg-indigo-500 rounded-full opacity-10 blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-purple-500 rounded-full opacity-10 blur-3xl animate-pulse"
            style="animation-delay:1s"></div>

        <!-- Nav -->
        <nav class="relative z-10 flex items-center justify-between px-8 py-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo.png') }}" alt="DOCOS" class="w-10 h-10 rounded-xl object-contain">
                <span class="text-white font-bold text-2xl">DOCOS</span>
            </div>
            <a href="{{ route('login') }}"
                class="bg-white/10 hover:bg-white/20 border border-white/20 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-300 hover:shadow-lg backdrop-blur-sm">
                <i class="fas fa-sign-in-alt mr-2"></i>Connexion
            </a>
        </nav>

        <!-- Hero -->
        <div class="relative z-10 flex flex-col items-center justify-center min-h-[85vh] text-center px-4"
            data-aos="fade-up">
            <div
                class="inline-flex items-center gap-2 bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 px-4 py-2 rounded-full text-sm font-medium mb-8 backdrop-blur-sm">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                Système de Gestion Professionnelle
            </div>
            <h1 class="text-5xl md:text-7xl font-black text-white mb-6 leading-tight">
                Gérez vos<br>
                <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Stagiaires</span><br>
                avec excellence
            </h1>
            <p class="text-indigo-200 text-xl max-w-2xl mb-10 leading-relaxed">
                DOCOS centralise le suivi des stagiaires, la gestion des présences, des projets et des documents dans une
                interface moderne et intuitive.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('login') }}"
                    class="bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white px-8 py-4 rounded-2xl font-semibold text-lg transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-500/30 hover:-translate-y-1 flex items-center gap-3">
                    <i class="fas fa-rocket"></i>Commencer maintenant
                </a>
            </div>

            <!-- Features grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 max-w-4xl w-full">
                @foreach ([['fas fa-user-graduate', 'Stagiaires', 'Gestion complète'], ['fas fa-tasks', 'Projets & Tâches', 'Suivi en temps réel'], ['fas fa-clipboard-check', 'Présences', 'Marquage automatique'], ['fas fa-certificate', 'Attestations', 'Gestion simplifiée']] as [$icon, $titre, $desc])
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300 hover:-translate-y-1"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="w-10 h-10 bg-indigo-500/20 rounded-xl flex items-center justify-center mb-3 mx-auto">
                            <i class="{{ $icon }} text-indigo-400"></i>
                        </div>
                        <p class="text-white font-semibold text-sm">{{ $titre }}</p>
                        <p class="text-indigo-300 text-xs mt-1">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
--}}

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOCOS - Gestion Professionnelle des Stagiaires</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Flowbite -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            overflow-x: hidden;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

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

        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
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
    </style>
</head>

<body class="bg-gray-50">

    <!-- Loader -->
    <div id="loader" class="loader">
        <div class="text-center">
            <img src="{{ asset('logo.png') }}" alt="DOCOS" class="w-64 mb-8 floating">
            <div class="flex space-x-2 justify-center">
                <div class="w-3 h-3 bg-white rounded-full animate-bounce"></div>
                <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <nav class="fixed w-full top-0 z-50 bg-white/90 backdrop-blur-md shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('logo.png') }}" alt="DOCOS" class="h-12">
                    <span class="text-2xl font-bold gradient-text">DOCOS</span>
                </div>
                <a href="{{ route('login') }}"
                    class="btn-primary text-white px-8 py-3 rounded-full font-semibold flex items-center space-x-2">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Se Connecter</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="min-h-screen flex items-center gradient-bg pt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                        Gestion Professionnelle des <span class="text-yellow-300">Stagiaires</span>
                    </h1>
                    <p class="text-xl text-white/90 mb-8">
                        Solution complète et moderne pour gérer efficacement vos stagiaires, mentors et projets au sein
                        de votre entreprise.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('login') }}"
                            class="bg-white text-purple-600 px-8 py-4 rounded-full font-bold text-lg hover:shadow-2xl hover:scale-105 transition-all">
                            Commencer Maintenant
                        </a>
                        <a href="#features"
                            class="glass-effect text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/20 transition-all">
                            Découvrir Plus
                        </a>
                    </div>
                </div>
                <div data-aos="fade-left" class="floating">
                    <div class="relative">
                        <div class="absolute inset-0 bg-yellow-300 rounded-3xl blur-3xl opacity-30"></div>
                        <img src="{{ asset('logo.png') }}" alt="Hero" class="relative rounded-3xl shadow-2xl">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold gradient-text mb-4">Quelques Fonctionnalités </h2>
                <p class="text-xl text-gray-600">Tout ce dont vous avez besoin pour gérer vos stagiaires</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="hover-lift bg-gradient-to-br from-blue-50 to-blue-100 p-8 rounded-3xl" data-aos="fade-up"
                    data-aos-delay="100">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-users text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-800">Gestion des Stagiaires</h3>
                    <p class="text-gray-600">Suivez l'ensemble de vos stagiaires, leur progression et performances en
                        temps réel.</p>
                </div>

                <div class="hover-lift bg-gradient-to-br from-purple-50 to-purple-100 p-8 rounded-3xl"
                    data-aos="fade-up" data-aos-delay="200">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-tasks text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-800">Gestion des Projets</h3>
                    <p class="text-gray-600">Assignez et suivez les projets avec un système de tâches complet et
                        intuitif.</p>
                </div>

                <div class="hover-lift bg-gradient-to-br from-pink-50 to-pink-100 p-8 rounded-3xl" data-aos="fade-up"
                    data-aos-delay="300">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-pink-500 to-red-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-800">Rapports & Statistiques</h3>
                    <p class="text-gray-600">Générez des rapports détaillés et visualisez les statistiques de
                        performance.</p>
                </div>

                <div class="hover-lift bg-gradient-to-br from-green-50 to-green-100 p-8 rounded-3xl" data-aos="fade-up"
                    data-aos-delay="400">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-calendar-check text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-800">Système de Présence</h3>
                    <p class="text-gray-600">Suivez la présence des stagiaires avec un système fiable et sans faille.
                    </p>
                </div>

                <div class="hover-lift bg-gradient-to-br from-yellow-50 to-yellow-100 p-8 rounded-3xl"
                    data-aos="fade-up" data-aos-delay="500">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-file-alt text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-800">Gestion Documents</h3>
                    <p class="text-gray-600">Stockez, partagez et téléchargez tous vos documents en toute sécurité.</p>
                </div>

                <div class="hover-lift bg-gradient-to-br from-indigo-50 to-indigo-100 p-8 rounded-3xl"
                    data-aos="fade-up" data-aos-delay="600">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-award text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-gray-800">Attestations</h3>
                    <p class="text-gray-600">Gérez les demandes et l'envoi d'attestations et conventions de stage.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-aos="zoom-in">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Prêt à Commencer ?</h2>
            <p class="text-xl text-white/90 mb-8">Rejoignez-nous et gérez vos stagiaires de manière professionnelle</p>
            <a href="{{ route('login') }}"
                class="inline-block bg-white text-purple-600 px-12 py-4 rounded-full font-bold text-xl hover:shadow-2xl hover:scale-105 transition-all">
                Accéder à la Plateforme
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <img src="{{ asset('logo.png') }}" alt="DOCOS" class="h-16 mx-auto mb-4">
            <p class="text-gray-400">© {{ date('Y') }} DOCOS. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

    <script>
        // Loader
        window.addEventListener('load', function() {
            setTimeout(() => {
                document.getElementById('loader').classList.add('hidden');
            }, 2000);
        });

        // AOS Init
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // GSAP Animations
        gsap.registerPlugin(ScrollTrigger);

        gsap.from('.gradient-bg', {
            scrollTrigger: '.gradient-bg',
            opacity: 0,
            duration: 1
        });
    </script>
</body>

</html>
