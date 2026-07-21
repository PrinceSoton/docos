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
                extend: {
                    colors: {
                        primary: { 50:'#eff6ff',100:'#dbeafe',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' },
                        accent:  { 400:'#818cf8',500:'#6366f1',600:'#4f46e5' }
                    },
                    fontFamily: { sans: ['Inter','system-ui','sans-serif'] },
                    backdropBlur: { xs:'2px' }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        * { font-family:'Inter',sans-serif; }
        .glass { background:rgba(255,255,255,0.85); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.3); }
        .gradient-bg { background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); }
        .card-hover { transition:all .3s cubic-bezier(.4,0,.2,1); }
        .card-hover:hover { transform:translateY(-4px); box-shadow:0 20px 40px rgba(0,0,0,.12); }
        .btn-primary { background:linear-gradient(135deg,#3b82f6,#6366f1); transition:all .3s ease; }
        .btn-primary:hover { background:linear-gradient(135deg,#2563eb,#4f46e5); transform:translateY(-2px); box-shadow:0 8px 25px rgba(99,102,241,.4); }
        .sidebar-link { transition:all .25s ease; border-radius:.75rem; }
        .sidebar-link:hover,.sidebar-link.active { background:linear-gradient(135deg,rgba(99,102,241,.15),rgba(59,130,246,.15)); color:#4f46e5; }
        ::-webkit-scrollbar { width:6px; } ::-webkit-scrollbar-track { background:#f1f5f9; } ::-webkit-scrollbar-thumb { background:linear-gradient(#6366f1,#3b82f6); border-radius:3px; }
        .loader-overlay { position:fixed;inset:0;background:rgba(255,255,255,.95);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column; }
        .pulse-ring { animation:pulseRing 1.5s ease infinite; }
        @keyframes pulseRing { 0%{transform:scale(1);opacity:1} 100%{transform:scale(1.8);opacity:0} }
        .fade-in { animation:fadeIn .5s ease forwards; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    </style>
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">

<!-- Loader -->
<div class="loader-overlay" id="appLoader">
    <div class="relative flex items-center justify-center mb-6">
        <div class="pulse-ring absolute w-24 h-24 rounded-full border-4 border-indigo-300"></div>
        <img src="{{ asset('logo.png') }}" alt="DOCOS" class="w-20 h-20 object-contain relative z-10 rounded-2xl shadow-xl">
    </div>
    <p class="text-indigo-600 font-semibold text-lg tracking-wide">DOCOS</p>
    <p class="text-slate-400 text-sm mt-1">Chargement...</p>
</div>

@yield('content')

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.1/vanilla-tilt.min.js"></script>

<script>
    // Loader
    window.addEventListener('load', () => {
        gsap.to('#appLoader', { opacity:0, duration:.8, delay:.8, onComplete:() => { document.getElementById('appLoader').style.display='none'; } });
    });

    // AOS
    AOS.init({ duration:700, easing:'ease-out-cubic', once:true, offset:50 });

    // Alerts
    @if(session('succes'))
        Swal.fire({ icon:'success', title:'Succès', text:'{{ session('succes') }}', timer:3000, showConfirmButton:false, toast:true, position:'top-end' });
    @endif
    @if(session('erreur'))
        Swal.fire({ icon:'error', title:'Erreur', text:'{{ session('erreur') }}', timer:4000, showConfirmButton:false, toast:true, position:'top-end' });
    @endif

    // Confirm delete
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form') || document.getElementById(this.dataset.form);
            Swal.fire({ title:'Confirmer la suppression', text:'Cette action est irréversible.', icon:'warning', showCancelButton:true, confirmButtonColor:'#ef4444', cancelButtonColor:'#6b7280', confirmButtonText:'Supprimer', cancelButtonText:'Annuler' })
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