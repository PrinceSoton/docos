<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre','Mon Espace') - DOCOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css" rel="stylesheet"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family:'Inter',sans-serif; }
        .sidebar { background:linear-gradient(180deg,#0f172a 0%,#1e293b 50%,#0f172a 100%); min-height:100vh; transition:width .3s ease; }
        .sidebar-item { transition:all .25s ease; border-radius:12px; margin:2px 8px; }
        .sidebar-item:hover,.sidebar-item.active { background:rgba(255,255,255,.1); }
        .sidebar-item.active { border-left:3px solid #f59e0b; color:#fbbf24 !important; }
        .glass { background:rgba(255,255,255,0.9); backdrop-filter:blur(16px); }
        .card { background:white; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,.06); transition:all .3s ease; }
        .card:hover { transform:translateY(-2px); box-shadow:0 8px 30px rgba(0,0,0,.1); }
        .loader-overlay { position:fixed;inset:0;background:rgba(255,255,255,.96);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column; }
        @keyframes spin { to { transform:rotate(360deg); } }
        .spin { animation:spin 1s linear infinite; }
        @keyframes fadeSlide { from{opacity:0;transform:translateY(15px)} to{opacity:1;transform:translateY(0)} }
        .fade-slide { animation:fadeSlide .4s ease forwards; }
        #sidebar { width:260px; } #sidebar.collapsed { width:72px; }
        #sidebar.collapsed .sidebar-label,#sidebar.collapsed .sidebar-logo-text { display:none; }
        ::-webkit-scrollbar { width:5px; } ::-webkit-scrollbar-thumb { background:linear-gradient(#f59e0b,#d97706); border-radius:3px; }
        .progress-bar { background:linear-gradient(90deg,#f59e0b,#d97706); height:6px; border-radius:3px; transition:width .8s ease; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50">

<div class="loader-overlay" id="appLoader">
    <div class="relative mb-4 w-20 h-20 mx-auto">
        <div class="w-20 h-20 rounded-full border-4 border-amber-100 spin border-t-amber-500 absolute inset-0"></div>
        <img src="{{ asset('logo.png') }}" alt="DOCOS" class="w-14 h-14 object-contain absolute inset-0 m-auto rounded-xl">
    </div>
    <p class="text-amber-600 font-bold text-xl mt-6">DOCOS</p>
    <p class="text-slate-400 text-sm">Espace Stagiaire</p>
</div>

<div class="flex h-screen overflow-hidden">
    <aside id="sidebar" class="sidebar flex-shrink-0 flex flex-col shadow-2xl z-50">
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
            <img src="{{ asset('logo.png') }}" alt="DOCOS" class="w-10 h-10 rounded-xl object-contain flex-shrink-0">
            <div class="sidebar-logo-text">
                <p class="text-white font-bold text-lg">DOCOS</p>
                <p class="text-amber-400 text-xs">Mon Espace</p>
            </div>
            <button id="toggleSidebar" class="ml-auto text-white/60 hover:text-white">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Stagiaire info -->
        @php $stagiaire = Auth::user()->stagiaire; @endphp
        <div class="px-5 py-4 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center flex-shrink-0">
                    @if(Auth::user()->photo)
                        <img src="{{ asset('storage/'.Auth::user()->photo) }}" class="w-full h-full rounded-full object-cover">
                    @else
                        <span class="text-white font-bold text-sm">{{ strtoupper(substr(Auth::user()->prenom,0,1)) }}</span>
                    @endif
                </div>
                <div class="sidebar-label overflow-hidden">
                    <p class="text-white font-semibold text-sm truncate">{{ Auth::user()->nom_complet }}</p>
                    <p class="text-amber-400 text-xs">{{ $stagiaire?->matricule ?? 'Stagiaire' }}</p>
                </div>
            </div>
            @if($stagiaire)
            <div class="mt-3 sidebar-label">
                <div class="flex justify-between text-xs text-slate-400 mb-1">
                    <span>Progression du stage</span>
                    @php
                        $total = $stagiaire->date_debut->diffInDays($stagiaire->date_fin);
                        $ecoule = $stagiaire->date_debut->diffInDays(now());
                        $pct = $total > 0 ? min(100, round(($ecoule/$total)*100)) : 0;
                    @endphp
                    <span class="text-amber-400 font-medium">{{ $pct }}%</span>
                </div>
                <div class="bg-white/10 rounded-full h-1.5">
                    <div class="progress-bar" style="width:{{ $pct }}%"></div>
                </div>
            </div>
            @endif
        </div>

        <nav class="flex-1 py-4 overflow-y-auto">
            <a href="{{ route('stagiaire.dashboard') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('stagiaire.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home w-5 text-center flex-shrink-0"></i><span class="sidebar-label text-sm font-medium">Tableau de bord</span>
            </a>
            <a href="{{ route('stagiaire.presence.index') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('stagiaire.presence.*') ? 'active' : '' }}">
                <i class="fas fa-user-check w-5 text-center flex-shrink-0"></i><span class="sidebar-label text-sm font-medium">Ma Présence</span>
            </a>
            <a href="{{ route('stagiaire.projects.index') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('stagiaire.projects.*') ? 'active' : '' }}">
                <i class="fas fa-project-diagram w-5 text-center flex-shrink-0"></i><span class="sidebar-label text-sm font-medium">Mon Projet</span>
            </a>
            <a href="{{ route('stagiaire.tasks.index') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('stagiaire.tasks.*') ? 'active' : '' }}">
                <i class="fas fa-check-square w-5 text-center flex-shrink-0"></i><span class="sidebar-label text-sm font-medium">Mes Tâches</span>
            </a>
            <a href="{{ route('stagiaire.reports.index') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('stagiaire.reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-upload w-5 text-center flex-shrink-0"></i><span class="sidebar-label text-sm font-medium">Mes Rapports</span>
            </a>
            <a href="{{ route('stagiaire.attestations.index') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('stagiaire.attestations.*') ? 'active' : '' }}">
                <i class="fas fa-certificate w-5 text-center flex-shrink-0"></i><span class="sidebar-label text-sm font-medium">Attestations</span>
            </a>
            <a href="{{ route('stagiaire.evenements.index') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white {{ request()->routeIs('stagiaire.evenements.*') ? 'active' : '' }}">
                <i class="fas fa-bell w-5 text-center flex-shrink-0"></i><span class="sidebar-label text-sm font-medium">Événements</span>
            </a>
            <a href="{{ route('documents.index') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white">
                <i class="fas fa-folder-open w-5 text-center flex-shrink-0"></i><span class="sidebar-label text-sm font-medium">Documents</span>
            </a>
        </nav>

        <div class="p-4 border-t border-white/10">
            <a href="{{ route('profile.index') }}" class="sidebar-item flex items-center gap-3 px-4 py-3 text-white/80 hover:text-white mb-1">
                <i class="fas fa-user-cog w-5 text-center flex-shrink-0"></i><span class="sidebar-label text-sm font-medium">Mon Profil</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-item flex items-center gap-3 px-4 py-3 text-red-400 hover:text-red-300 w-full text-left">
                    <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i><span class="sidebar-label text-sm font-medium">Déconnexion</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="glass border-b border-white/20 px-6 py-4 flex items-center justify-between sticky top-0 z-40">
            <div>
                <h1 class="text-slate-800 font-bold text-lg">@yield('titre','Mon Espace')</h1>
                <p class="text-slate-400 text-xs">@yield('breadcrumb','Stagiaire')</p>
            </div>
            <div class="flex items-center gap-3">
                {{--<span class="hidden sm:block text-slate-500 text-sm">{{ now()->translatedFormat('l d F Y') }}</span> --}}
            </div>
        </header>
        <main class="flex-1 overflow-y-auto p-6">
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <ul class="list-disc list-inside text-red-600 text-sm">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
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
    window.addEventListener('load', () => { gsap.to('#appLoader', { opacity:0, duration:.6, delay:.5, onComplete:() => { document.getElementById('appLoader').style.display='none'; } }); });
    AOS.init({ duration:600, once:true });
    document.getElementById('toggleSidebar')?.addEventListener('click', () => { document.getElementById('sidebar').classList.toggle('collapsed'); });
    @if(session('succes')) Swal.fire({ icon:'success', title:'Succès', text:@json(session('succes')), toast:true, position:'top-end', timer:3000, showConfirmButton:false }); @endif
    @if(session('erreur')) Swal.fire({ icon:'error', title:'Erreur', text:@json(session('erreur')), toast:true, position:'top-end', timer:4000, showConfirmButton:false }); @endif
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const form = btn.closest('form');
            Swal.fire({ title:'Supprimer ?', icon:'warning', showCancelButton:true, confirmButtonColor:'#ef4444', cancelButtonText:'Annuler', confirmButtonText:'Supprimer' }).then(r => { if(r.isConfirmed && form) form.submit(); });
        });
    });
</script>
@stack('scripts')
</body>
</html>