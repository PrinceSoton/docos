<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'DOCOS') - Gestion des Stagiaires</title>

    <!-- Tailwind CSS + Flowbite -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
    <!-- AOS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet"/>
    <!-- SweetAlert2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css" rel="stylesheet"/>

    <script>
        tailwind.config = {
            theme: {
                screens: { xs: '375px', sm: '640px', md: '768px', lg: '1024px', xl: '1280px', '2xl': '1536px' },
                extend: {
                    // Palette DOCOS (identique aux espaces Admin, Mentor et Stagiaire)
                    colors: {
                        brand:  { 50:'#eff5fa', 100:'#dce8f2', 200:'#b9d1e4', 600:'#1d5c8d', 700:'#174a73', 800:'#103860', 900:'#0b2540' },
                        accent: { 50:'#e8f6ef', 100:'#cdebdc', 500:'#22a06b', 600:'#1a8a5c', 700:'#146e4a' },
                        // Anciennes clés conservées pour les vues qui les utiliseraient encore
                        primary: { 50:'#eff6ff',100:'#dbeafe',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' }
                    },
                    fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
                    backdropBlur: { xs:'2px' }
                }
            }
        }
    </script>

    {{-- Briques communes du parcours d'authentification (champs, boutons, messages) --}}
    <style type="text/tailwindcss">
        @layer components {
            .auth-label { @apply block text-sm font-medium text-slate-800 mb-1.5; }
            .auth-input { @apply w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-[15px] text-slate-900 placeholder:text-slate-400 transition-colors focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/25; }
            .auth-input-invalide { @apply border-red-600 focus:border-red-600 focus:ring-red-600/25; }
            .auth-input-lecture { @apply bg-brand-50 text-slate-700 cursor-default; }
            .auth-icone { @apply pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400; }
            .auth-oeil { @apply absolute right-1.5 top-1/2 -translate-y-1/2 inline-flex h-9 w-9 items-center justify-center rounded-md text-slate-500 transition-colors hover:bg-brand-50 hover:text-brand-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600; }
            .auth-btn { @apply inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand-800 px-4 sm:px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-700 disabled:cursor-wait focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2; }
            .auth-lien { @apply rounded text-sm font-medium text-brand-700 underline-offset-4 hover:text-brand-900 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2; }
            .auth-erreur { @apply rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800; }
            .auth-info { @apply rounded-lg border border-accent-100 bg-accent-50 p-3 text-sm text-accent-700; }
            .auth-alerte { @apply rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900; }
        }
    </style>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        * { font-family:'Inter',sans-serif; }
        ::selection { background:#b9d1e4; color:#0b2540; }

        /* Logotype : le fichier source est une image large avec marges, on en recadre la partie utile */
        .logotype { position:relative; overflow:hidden; display:block; }
        .logotype img { position:absolute; max-width:none; mix-blend-mode:multiply; filter:brightness(1.05) contrast(1.08); }

        /* Flèche circulaire du logo, en filigrane dans le panneau de marque */
        .filigrane { position:absolute; pointer-events:none; color:rgba(185,209,228,.14); }

        /* Loader : masqué à la fin du chargement par une transition CSS (sans dépendre d'une bibliothèque) */
        .loader-overlay { position:fixed; inset:0; background:#eff5fa; z-index:9999; display:flex; align-items:center; justify-content:center; flex-direction:column; transition:opacity .3s ease; }
        .loader-overlay.is-done { opacity:0; pointer-events:none; }
        .loader-anneau { width:34px; height:34px; border-radius:9999px; border:3px solid #dce8f2; border-top-color:#103860; animation:loaderTour 1s linear infinite; }
        @keyframes loaderTour { to { transform:rotate(360deg); } }

        /* Notifications SweetAlert aux couleurs DOCOS */
        .swal2-popup.swal2-toast { border:1px solid #dce8f2; border-radius:12px; box-shadow:0 12px 32px -12px rgba(11,37,64,.35); }
        .swal2-popup.swal2-toast .swal2-title { color:#0b2540; font-size:14px; font-weight:600; }
        .swal2-popup.swal2-toast .swal2-html-container { color:#475569; font-size:14px; }
        .swal2-popup.swal2-toast .swal2-timer-progress-bar { background:#b9d1e4; }

        @media (prefers-reduced-motion: reduce) {
            .loader-overlay { transition:none; }
            .loader-anneau { animation:none; }
            *, *::before, *::after { scroll-behavior:auto !important; }
        }
    </style>
    @stack('styles')
    {{-- Sans JavaScript, le loader ne masque jamais la page --}}
    <noscript><style>#appLoader { display:none !important; } [data-aos] { opacity:1 !important; transform:none !important; }</style></noscript>
</head>
<body class="bg-brand-50 text-slate-900 antialiased min-h-screen">

<!-- Loader -->
<div class="loader-overlay" id="appLoader" role="status" aria-label="Chargement">
    <span class="logotype w-[198px] h-[76px] mb-5" aria-hidden="true">
        <img src="{{ asset('logo.png') }}" alt="" class="w-[273px]" style="left:-38px;top:-36px">
    </span>
    <div class="loader-anneau" aria-hidden="true"></div>
</div>

@php
    // Fil d'étapes optionnel fourni par la page : [[libellé, 'fait'|'actif'|'a_venir'], ...]
    $etapes = $etapes ?? [];
@endphp

<div class="min-h-screen lg:grid lg:grid-cols-[minmax(380px,5fr)_7fr]">
    <!-- Panneau de marque -->
    <aside class="relative overflow-hidden bg-brand-900 text-white lg:min-h-screen" aria-label="DOCOS">
        <svg class="filigrane w-[620px] h-[620px] -right-64 -bottom-64 hidden lg:block" viewBox="0 0 480 480" fill="none" aria-hidden="true">
            <path d="M308.4 52.1 A200 200 0 1 1 171.6 52.1" stroke="currentColor" stroke-width="16" stroke-linecap="round"/>
        </svg>
        <svg class="filigrane w-[220px] h-[220px] -right-16 -top-20 lg:hidden" viewBox="0 0 480 480" fill="none" aria-hidden="true">
            <path d="M308.4 52.1 A200 200 0 1 1 171.6 52.1" stroke="currentColor" stroke-width="22" stroke-linecap="round"/>
        </svg>

        <div class="relative flex h-full flex-col px-5 py-5 sm:px-8 lg:px-12 lg:py-12" data-entree>
            <a href="{{ route('welcome') }}"
                class="inline-flex w-fit rounded-xl bg-white px-3 py-1.5 lg:px-4 lg:py-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900"
                aria-label="DOCOS, retour à l'accueil">
                <span class="logotype w-[132px] h-[51px] lg:w-[172px] lg:h-[66px]" aria-hidden="true">
                    <img src="{{ asset('logo.png') }}" alt="" class="w-[182px] left-[-25px] top-[-24px] lg:w-[237px] lg:left-[-33px] lg:top-[-31px]">
                </span>
            </a>

            <div class="mt-6 lg:mt-auto lg:mb-auto lg:py-12 max-w-md">
                <p class="text-xs font-semibold uppercase tracking-[.14em] text-accent-100">@yield('panneau_surtitre', 'Gestion numérique des documents des stagiaires')</p>
                <p class="mt-3 text-2xl lg:text-[2.1rem] font-bold leading-tight tracking-tight">@yield('panneau_titre', 'Gestion Professionnelle des Stagiaires')</p>
                @hasSection('panneau_texte')
                    <p class="mt-3 hidden sm:block text-[15px] leading-relaxed text-brand-100">@yield('panneau_texte')</p>
                @endif

                @if (count($etapes))
                    @php
                        $rangActif = collect($etapes)->search(fn ($e) => $e[1] === 'actif');
                    @endphp
                    @if ($rangActif !== false)
                        <p class="mt-3 sm:hidden text-sm text-brand-100">
                            Étape {{ $rangActif + 1 }} sur {{ count($etapes) }} :
                            <span class="font-semibold text-white">{{ $etapes[$rangActif][0] }}</span>
                        </p>
                    @endif
                    <ol class="mt-6 lg:mt-10 hidden sm:grid gap-3" aria-label="Étapes">
                        @foreach ($etapes as $i => [$libelle, $etat])
                            <li class="flex items-center gap-3 text-sm" @if ($etat === 'actif') aria-current="step" @endif>
                                <span @class([
                                    'flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-xs font-semibold',
                                    'bg-accent-500 text-white' => $etat === 'fait',
                                    'bg-white text-brand-900 ring-4 ring-white/15' => $etat === 'actif',
                                    'border border-white/30 text-brand-100' => $etat === 'a_venir',
                                ])>
                                    @if ($etat === 'fait')
                                        <i class="fas fa-check text-[11px]" aria-hidden="true"></i><span class="sr-only">Étape terminée :</span>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </span>
                                <span @class(['font-semibold text-white' => $etat === 'actif', 'text-brand-100' => $etat !== 'actif'])>{{ $libelle }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            <p class="hidden lg:block text-xs text-brand-200">© {{ date('Y') }} DOCOS — Système de Gestion des Stagiaires</p>
        </div>
    </aside>

    <!-- Zone de travail -->
    <main class="flex items-start lg:items-center justify-center px-4 py-8 sm:px-8 sm:py-12 lg:py-16">
        <div class="w-full max-w-[440px]" data-entree>
            @yield('content')
            <p class="lg:hidden mt-8 text-center text-xs text-slate-600">© {{ date('Y') }} DOCOS — Système de Gestion des Stagiaires</p>
        </div>
    </main>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.1/vanilla-tilt.min.js"></script>

<script>
    const mouvementReduit = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Loader : masqué à la fin du chargement, avec un filet de sécurité si une ressource externe tarde
    (() => {
        const loader = document.getElementById('appLoader');
        let fait = false;
        const masquer = () => {
            if (fait || !loader) return;
            fait = true;
            loader.classList.add('is-done');
            setTimeout(() => { loader.style.display = 'none'; }, mouvementReduit ? 0 : 320);
        };
        if (mouvementReduit) document.addEventListener('DOMContentLoaded', masquer);
        window.addEventListener('load', () => setTimeout(masquer, mouvementReduit ? 0 : 200));
        setTimeout(masquer, 3500);
    })();

    // Entrée synchronisée du panneau et du formulaire (désactivée si l'utilisateur limite les animations)
    if (window.gsap && !mouvementReduit) {
        const blocs = document.querySelectorAll('[data-entree] > *');
        gsap.from(blocs, { y: 18, opacity: 0, duration: .6, stagger: .07, ease: 'power2.out', delay: .25 });
        // État final garanti même si l'animation est interrompue
        setTimeout(() => { gsap.killTweensOf(blocs); gsap.set(blocs, { clearProps: 'transform,opacity' }); }, 2200);
    }

    // AOS
    if (window.AOS) AOS.init({ duration:700, easing:'ease-out-cubic', once:true, offset:50, disable: mouvementReduit });

    // Alerts (texte encodé en JSON : apostrophes et caractères spéciaux affichés tels quels)
    if (window.Swal) {
        @if(session('succes'))
            Swal.fire({ icon:'success', title:'Succès', text:@json(session('succes')), timer:4000, timerProgressBar:true, showConfirmButton:false, toast:true, position:'top-end', iconColor:'#22a06b' });
        @endif
        @if(session('erreur'))
            Swal.fire({ icon:'error', title:'Erreur', text:@json(session('erreur')), timer:6000, timerProgressBar:true, showConfirmButton:false, toast:true, position:'top-end', iconColor:'#b91c1c' });
        @endif
    }

    // Confirm delete
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form') || document.getElementById(this.dataset.form);
            Swal.fire({ title:'Confirmer la suppression', text:'Cette action est irréversible.', icon:'warning', showCancelButton:true, confirmButtonColor:'#b91c1c', cancelButtonColor:'#103860', confirmButtonText:'Supprimer', cancelButtonText:'Annuler' })
            .then(r => { if(r.isConfirmed) form.submit(); });
        });
    });

    // Loading on form submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('[type=submit]');
            if(btn) { btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin mr-2"></i>Traitement...'; }
        });
    });
</script>
@stack('scripts')
</body>
</html>
