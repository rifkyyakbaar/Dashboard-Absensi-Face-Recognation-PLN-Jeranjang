<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - PLN IP</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/Logo-PLN.png') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh; 
        }

        .wrapper {
            display: flex;
            width: 100%;
            flex-grow: 1; 
            flex-direction: column;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        /* -------------------------------------------------------------
           CSS EFEK BLUR (OVERLAY)
        ------------------------------------------------------------- */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(12, 74, 82, 0.4); 
            backdrop-filter: blur(4px); 
            -webkit-backdrop-filter: blur(4px);
            z-index: 990; 
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease-in-out;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* -------------------------------------------------------------
           CSS TOPBAR (Header) - DENGAN EFEK KACA TRANSAPARAN
        ------------------------------------------------------------- */
        .topbar {
            /* Warna #125d72 dengan transparansi 85% */
            background-color: rgba(12, 74, 82, 0.9); 
            /* Efek kaca buram ala Glassmorphism */
            backdrop-filter: blur(10px); 
            -webkit-backdrop-filter: blur(10px); 
            
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease-in-out;
        }

        .topbar-hidden {
            transform: translateY(-100%);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            position: relative; 
        }

        .menu-wrapper {
            position: relative;
            padding-bottom: 10px; 
            margin-bottom: -10px;
        }

        .menu-toggle {
            background: rgba(255, 255, 255, 0.1); 
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 42px;
            height: 42px;
            border-radius: 8px;
            margin-right: 20px;
            color: white; 
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-toggle:hover, .menu-wrapper:hover .menu-toggle {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .page-title {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: white; 
        }
        
        .page-subtitle {
            margin: 0;
            font-size: 12px;
            color: #a0c4c9; 
        }

        .topbar-right {
            display: flex;
            align-items: center;
        }

        /* -------------------------------------------------------------
           CSS POP-UP MENU (Dropdown Hover)
        ------------------------------------------------------------- */
        .popup-menu {
            position: absolute;
            top: 52px; 
            left: 0;
            width: 280px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border: 1px solid #cbd5e1;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1050;
            overflow: hidden;
        }

        .menu-wrapper:hover .popup-menu, .popup-menu.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .popup-header {
            background-color: #125d72;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1); 
        }

        .popup-title {
            font-weight: bold;
            font-size: 16px;
            margin: 0;
            line-height: 1.2;
        }

        .popup-subtitle {
            font-size: 10px;
            color: #a0c4c9;
            margin: 0;
        }

        .popup-items {
            list-style: none;
            padding: 10px 0;
            margin: 0;
        }

        .popup-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: #475569;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .popup-item i {
            margin-right: 15px;
            font-size: 18px;
            width: 20px;
            text-align: center;
            color: #14a2ba;
        }

        .popup-item:hover {
            background-color: #f8fafc;
            color: #125d72;
        }

        .popup-item.active {
            background-color: #f1f5f9;
            color: #0c4a52;
            border-left: 4px solid #efe62f;
            padding-left: 16px;
        }

        .popup-footer {
            padding: 12px 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }

        /* -------------------------------------------------------------
           LAIN-LAIN
        ------------------------------------------------------------- */
        .content-area {
            padding: 30px;
            flex-grow: 1; 
        }

        .main-footer {
            background-color: #0c4a52e6;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="wrapper">
        
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main-content">
            
            <div class="topbar" id="smartTopbar">
                <div class="topbar-left">
                    
                    <div class="menu-wrapper" id="menuWrapper">
                        <button class="menu-toggle" id="openMenuBtn"><i class="fa-solid fa-bars"></i></button>
                        
                        <div class="popup-menu" id="popupMenu">
                            <div class="popup-header">
                                <img src="{{ asset('images/Logo-PLN.png') }}" alt="Logo PLN" style="height: 35px; width: auto; margin-right: 15px; border-radius: 5px;">
                                <div>
                                    <h1 class="popup-title">PLN INDONESIA POWER</h1>
                                    <p class="popup-subtitle">UBP JERANJANG</p>
                                </div>
                            </div>
                            
                            <ul class="popup-items">
                                <li>
                                    <a href="{{ url('/dashboard') }}" class="popup-item @yield('nav_dashboard')">
                                        <i class="fa-solid fa-chart-pie"></i> Realtime Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/laporan') }}" class="popup-item @yield('nav_laporan')">
                                        <i class="fa-solid fa-file-export"></i> Laporan & Statistik
                                    </a>
                                </li>
                            </ul>

                            <div class="popup-footer">
                                <p style="color: #64748b; font-size: 11px; font-weight: bold; margin-bottom: 2px;">Face Recognition System</p>
                                <p style="color: #94a3b8; font-size: 10px; margin-bottom: 0;">Version 1.0 - Auto Sync</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="page-title">@yield('title')</h2>
                        <p class="page-subtitle">Face Recognition System</p>
                    </div>
                </div>
                
                <div class="topbar-right">
                    <img src="{{ asset('images/danantara_indonesia.png') }}" alt="Logo Danantara Indonesia" style="height: 30px; width: auto; object-fit: contain; border-radius: 4px;">
                    <div style="width: 1px; height: 25px; background-color: rgba(255,255,255,0.3); margin: 0 15px;"></div>
                    <img src="{{ asset('images/PLN-IP.png') }}" alt="Logo PLN Indonesia Power" style="height: 55px; width: auto; object-fit: contain;">
                </div>
            </div>

            <div class="content-area">
                @yield('content')
            </div>

            <footer class="main-footer">
                <p class="mb-0">© 2026 PT PLN Indonesia Power UBP Jeranjang. All rights reserved.</p>
            </footer>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            const menuWrapper = document.getElementById('menuWrapper');
            const popupMenu = document.getElementById('popupMenu');
            const overlay = document.getElementById('sidebarOverlay');

            menuWrapper.addEventListener('mouseenter', function() {
                overlay.classList.add('active');
            });

            menuWrapper.addEventListener('mouseleave', function() {
                overlay.classList.remove('active');
                popupMenu.classList.remove('active'); 
            });

            const openMenuBtn = document.getElementById('openMenuBtn');
            openMenuBtn.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    e.stopPropagation();
                    const isActive = popupMenu.classList.contains('active');
                    if(isActive) {
                        popupMenu.classList.remove('active');
                        overlay.classList.remove('active');
                    } else {
                        popupMenu.classList.add('active');
                        overlay.classList.add('active');
                    }
                }
            });

            overlay.addEventListener('click', function() {
                popupMenu.classList.remove('active');
                overlay.classList.remove('active');
            });

            const topbar = document.getElementById("smartTopbar");
            let lastScrollTop = 0;

            window.addEventListener("scroll", function() {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                if (scrollTop > lastScrollTop && scrollTop > 50) {
                    topbar.classList.add('topbar-hidden');
                    popupMenu.classList.remove('active'); 
                    overlay.classList.remove('active');
                } else {
                    topbar.classList.remove('topbar-hidden');
                }
                lastScrollTop = scrollTop;
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#106770',
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif

        });
    </script>
</body>
</html>