@extends('layouts.app')

@section('title', 'REALTIME DASHBOARD')

@section('nav_dashboard', 'active')

@section('content')

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8f9fa; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .card {
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
        }
        
        .border-l-teal { border-left: 7px solid #106770 !important; }
        .border-l-green { border-left: 7px solid #28a745 !important; }
        .border-l-red { border-left: 7px solid #dc3545 !important; }
        .border-l-purple { border-left: 7px solid #8a2be2 !important; }

        .table-striped-custom tbody tr:nth-of-type(odd) td { background-color: #ffffff !important; }
        .table-striped-custom tbody tr:nth-of-type(even) td { background-color: #f8fafc !important; }
        .table-striped-custom tbody tr:hover td { background-color: #f1f5f9 !important; }

        .topbar, .navbar, header {
            transition: transform 0.4s ease-in-out, filter 0.3s ease-in-out !important;
            z-index: 1030 !important; 
        }
        .topbar.hidden-nav, .navbar.hidden-nav, header.hidden-nav {
            transform: translateY(-100%) !important;
        }

        .sidebar, .main-sidebar, #sidebar, .offcanvas, .app-sidebar, .sidebar-wrapper {
            z-index: 99999 !important; 
            padding-top: 0 !important; 
        }

        .hero-banner {
            position: relative;
            width: 100%;
            height: 250px; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 2;
            border-radius: 16px;
            overflow: hidden;
            
            clip-path: polygon(
                0 0, calc(100% - 260px) 0, calc(100% - 255.15px) 0.6px, calc(100% - 250.6px) 2.5px, calc(100% - 246.4px) 5.6px, calc(100% - 242.5px) 10px,
                calc(100% - 227.5px) 30px, calc(100% - 223.6px) 34.4px, calc(100% - 219.4px) 37.5px, calc(100% - 214.8px) 39.4px, calc(100% - 210px) 40px,
                calc(100% - 10px) 40px, calc(100% - 5.6px) 40.6px, calc(100% - 2.5px) 42.5px, calc(100% - 0.6px) 45.6px, 100% 50px, 100% 100%, 260px 100%,
                255.15px calc(100% - 0.6px), 250.6px calc(100% - 2.5px), 246.4px calc(100% - 5.6px), 242.5px calc(100% - 10px), 227.5px calc(100% - 30px),
                223.6px calc(100% - 34.4px), 219.4px calc(100% - 37.5px), 214.8px calc(100% - 39.4px), 210px calc(100% - 40px), 10px calc(100% - 40px),
                5.6px calc(100% - 40.6px), 2.5px calc(100% - 42.5px), 0.6px calc(100% - 45.6px), 0 calc(100% - 50px)
            );
        }

        @media (max-width: 768px) {
            .hero-banner {
                height: 200px;
                clip-path: polygon(
                    0 0, calc(100% - 158px) 0, calc(100% - 153px) 1px, calc(100% - 148px) 4px, calc(100% - 134px) 25px, calc(100% - 131px) 28px, 
                    calc(100% - 125px) 30px, calc(100% - 8px) 30px, calc(100% - 3px) 31px, 100% 36px, 100% 100%, 158px 100%, 153px calc(100% - 1px), 
                    148px calc(100% - 4px), 134px calc(100% - 25px), 131px calc(100% - 28px), 125px calc(100% - 30px), 8px calc(100% - 30px), 
                    3px calc(100% - 31px), 0 calc(100% - 36px)
                );
            }
        }

        .bg-layer-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; }
        .bg-layer-top { position: absolute; top: 0; left: 0; width: 100%; height: 25%; overflow: hidden; }
        .scrolling-bg-top { position: absolute; top: 0; left: -100%; height: 100%; display: flex; width: 200%; animation: scrollBackgroundRight 40s linear infinite; }
        .bg-layer-bottom { position: absolute; bottom: 0; left: 0; width: 100%; height: 75%; overflow: hidden; }
        .scrolling-bg-bottom { position: absolute; top: 0; left: 0; height: 100%; display: flex; width: 200%; animation: scrollBackgroundLeft 40s linear infinite; }
        
        .scrolling-bg-top img, .scrolling-bg-bottom img { width: calc(100% / 12); height: 100%; object-fit: cover; flex-shrink: 0; }

        @keyframes scrollBackgroundLeft { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        @keyframes scrollBackgroundRight { 0% { transform: translateX(0); } 100% { transform: translateX(50%); } }

        @keyframes flashGreen {
            0% { background-color: #dcfce7; }
            100% { background-color: transparent; }
        }
        .new-log-row td {
            animation: flashGreen 2.5s ease-out forwards;
        }

        .hero-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(12, 74, 82, 0.9) 0%, rgba(16, 103, 112, 0.8) 100%); z-index: 1;
        }

        .hero-content {
            position: relative; z-index: 2; color: white; text-align: center; width: 100%; padding: 0 20px;
        }
    </style>

    <div class="mb-4 mt-2 position-relative">
        <select id="zoneSelect" class="form-select form-select-sm fw-bold shadow-sm position-absolute" style="top: 0px; right: 5px; width: auto; z-index: 3; background-color: #ffffff; color: #106770; border: 0.5px solid #cbd5e1; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <option value="kuning" selected>Monitoring Zona Kuning</option>
            <option value="merah">Monitoring Zona Merah</option>
            <option value="semua">Monitoring Semua Zona</option>
        </select>

        <div class="hero-banner" style="filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1));">
            <div class="bg-layer-container">
                <div class="bg-layer-top">
                    <div class="scrolling-bg-top">
                        <img src="{{ asset('images/gambar1.png') }}" alt="bg1"><img src="{{ asset('images/gambar2.jpg') }}" alt="bg2">
                        <img src="{{ asset('images/gambar3.jpg') }}" alt="bg3"><img src="{{ asset('images/gambar4.jpg') }}" alt="bg4">
                        <img src="{{ asset('images/gambar5.jpg') }}" alt="bg5"><img src="{{ asset('images/gambar6.jpg') }}" alt="bg6">
                        <img src="{{ asset('images/gambar1.png') }}" alt="bg1"><img src="{{ asset('images/gambar2.jpg') }}" alt="bg2">
                        <img src="{{ asset('images/gambar3.jpg') }}" alt="bg3"><img src="{{ asset('images/gambar4.jpg') }}" alt="bg4">
                        <img src="{{ asset('images/gambar5.jpg') }}" alt="bg5"><img src="{{ asset('images/gambar6.jpg') }}" alt="bg6">
                    </div>
                </div>
                <div class="bg-layer-bottom">
                    <div class="scrolling-bg-bottom">
                        <img src="{{ asset('images/gambar1.png') }}" alt="bg1"><img src="{{ asset('images/gambar2.jpg') }}" alt="bg2">
                        <img src="{{ asset('images/gambar3.jpg') }}" alt="bg3"><img src="{{ asset('images/gambar4.jpg') }}" alt="bg4">
                        <img src="{{ asset('images/gambar5.jpg') }}" alt="bg5"><img src="{{ asset('images/gambar6.jpg') }}" alt="bg6">
                        <img src="{{ asset('images/gambar1.png') }}" alt="bg1"><img src="{{ asset('images/gambar2.jpg') }}" alt="bg2">
                        <img src="{{ asset('images/gambar3.jpg') }}" alt="bg3"><img src="{{ asset('images/gambar4.jpg') }}" alt="bg4">
                        <img src="{{ asset('images/gambar5.jpg') }}" alt="bg5"><img src="{{ asset('images/gambar6.jpg') }}" alt="bg6">
                    </div>
                </div>
            </div>
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <h2 id="heroTitle" style="font-weight: 900; letter-spacing: 1px; margin-bottom: 15px; font-size: 38px; text-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: opacity 0.2s ease-in-out;">
                    MONITORING ZONA KUNING
                </h2>
                <div class="d-inline-flex justify-content-center align-items-center" style="background-color: rgba(0,0,0,0.25); backdrop-filter: blur(4px); padding: 8px 24px; border-radius: 50px; border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <span id="centerDateTime" style="font-size: 15px; font-weight: bold; letter-spacing: 0.5px;">Memuat waktu...</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4 align-items-stretch">
        <div class="col-12">
            <div class="row g-3 h-100">
                <div class="col-md-6">
                    <div class="card border-l-teal h-100 py-2" style="border-radius: 12px;">
                        <div class="card-body p-4 d-flex flex-column justify-content-center">
                            <p class="text-muted fw-bold mb-2" style="font-size: 11px; letter-spacing: 0.5px;">TOTAL ORANG MASUK</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <h2 id="statMasuk" class="fw-black mb-0 display-6" style="color: #222; font-weight: 900;">0</h2>
                                <i class="fa-solid fa-user-group" style="color: #a0c4c9; font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-l-green h-100 py-2" style="border-radius: 12px;">
                        <div class="card-body p-4 d-flex flex-column justify-content-center">
                            <p class="text-muted fw-bold mb-2" style="font-size: 11px; letter-spacing: 0.5px;">SEDANG DI DALAM</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <h2 id="statDiDalam" class="fw-black mb-0 display-6" style="color: #222; font-weight: 900;">0</h2>
                                <i class="fa-solid fa-arrow-right-to-bracket" style="color: #a0c4c9; font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-l-red h-100 py-2" style="border-radius: 12px;">
                        <div class="card-body p-4 d-flex flex-column justify-content-center">
                            <p class="text-muted fw-bold mb-2" style="font-size: 11px; letter-spacing: 0.5px;">KELUAR</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <h2 id="statKeluar" class="fw-black mb-0 display-6" style="color: #222; font-weight: 900;">0</h2>
                                <i class="fa-solid fa-arrow-right-from-bracket" style="color: #a0c4c9; font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-l-purple h-100 py-2" style="border-radius: 12px;">
                        <div class="card-body p-4 d-flex flex-column justify-content-center">
                            <p class="text-muted fw-bold mb-2" style="font-size: 11px; letter-spacing: 0.5px;">TAMU BARU HARI INI</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <h2 id="statTamu" class="fw-black mb-0 display-6" style="color: #222; font-weight: 900;">0</h2>
                                <i class="fa-solid fa-user-plus" style="color: #a0c4c9; font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white border-0 pt-4 pb-2 ps-4" style="border-bottom: 1px solid #e9ecef !important;">
            <h6 class="fw-bold mb-0" style="color: #125d72;">
                <i class="fa-regular fa-building text-info me-2" style="color: #125d72 !important;"></i> Rekap per Departemen (Semua Departemen)
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive custom-scrollbar" id="rekapContainer" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-borderless table-hover align-middle mb-0 table-striped-custom" style="font-size: 14px;">
                    <thead style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 5; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <tr>
                            <th class="ps-4 py-3" style="color: #6c757d; font-size: 12px; font-weight: bold; letter-spacing: 0.5px;">DEPARTEMEN / VENDOR</th>
                            <th class="text-center py-3" style="color: #106770; font-size: 12px; font-weight: bold; letter-spacing: 0.5px;">MASUK</th>
                            <th class="text-center py-3" style="color: #28a745; font-size: 12px; font-weight: bold; letter-spacing: 0.5px;">DI DALAM</th>
                            <th class="text-center py-3" style="color: #dc3545; font-size: 12px; font-weight: bold; letter-spacing: 0.5px;">KELUAR</th>
                        </tr>
                    </thead>
                    <tbody id="rekapDeptBody">
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center flex-wrap" style="border-bottom: 1px solid #e9ecef !important;">
                    <h6 class="fw-bold mb-0" style="color: #0c4a52;">
                        <i class="fa-solid fa-chart-line text-info me-2" style="color: #106770 !important;"></i> Tren Kehadiran Harian
                    </h6>
                    <select id="filterWaktu" class="form-select form-select-sm fw-bold mt-2 mt-md-0" style="width: auto; color: #0c4a52; background-color: #e0ecee; border: none; border-radius: 6px; cursor: pointer;">
                        <option value="masuk">Berdasarkan Jam Masuk</option>
                        <option value="keluar">Berdasarkan Jam Keluar</option>
                    </select>
                </div>
                <div class="card-body px-4 pb-4 pt-4">
                    <div class="row g-4">
                        <div class="col-lg-6" style="border-right: 1px dashed #e2e8f0;">
                            <p class="text-center fw-bold mb-3" style="font-size: 12px; color: #475569; text-transform: uppercase;">Distribusi Volume (Bar Chart)</p>
                            <div style="height: 280px; width: 100%;">
                                <canvas id="trendBarChart"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <p class="text-center fw-bold mb-3" style="font-size: 12px; color: #475569; text-transform: uppercase;">Pola Aliran (Area Chart)</p>
                            <div style="height: 280px; width: 100%;">
                                <canvas id="trendLineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4" style="position: relative; width: 100%; height: 80px; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <div class="bg-layer-container">
            <div class="bg-layer-bottom" style="height: 100%; top: 0;">
                <div class="scrolling-bg-bottom"> 
                    <img src="{{ asset('images/gambar1.png') }}" alt="bg1"><img src="{{ asset('images/gambar2.jpg') }}" alt="bg2">
                    <img src="{{ asset('images/gambar3.jpg') }}" alt="bg3"><img src="{{ asset('images/gambar4.jpg') }}" alt="bg4">
                    <img src="{{ asset('images/gambar5.jpg') }}" alt="bg5"><img src="{{ asset('images/gambar6.jpg') }}" alt="bg6">
                    <img src="{{ asset('images/gambar1.png') }}" alt="bg1"><img src="{{ asset('images/gambar2.jpg') }}" alt="bg2">
                    <img src="{{ asset('images/gambar3.jpg') }}" alt="bg3"><img src="{{ asset('images/gambar4.jpg') }}" alt="bg4">
                    <img src="{{ asset('images/gambar5.jpg') }}" alt="bg5"><img src="{{ asset('images/gambar6.jpg') }}" alt="bg6">
                </div>
            </div>
        </div>
        <div class="hero-overlay" style="background: linear-gradient(135deg, rgba(16, 103, 112, 0.8) 0%, rgba(12, 74, 82, 0.7) 100%); z-index: 1;"></div>
        <div class="hero-content" style="display: flex; align-items: center; padding: 0 20px; z-index: 2;">
            <div class="d-none d-md-block" style="width: 60px; height: 2px; background-color: #efe62f; margin-right: 20px;"></div>
            <h4 style="font-weight: 800; letter-spacing: 3px; margin-bottom: 0; font-size: 16px; text-shadow: 0 2px 4px rgba(0,0,0,0.3); text-transform: uppercase; color: #ffffff;">
                Monitoring Live Log & Traffic Gerbang
            </h4>
            <div class="d-none d-md-block" style="width: 60px; height: 2px; background-color: #efe62f; margin-left: 20px;"></div>
        </div>
    </div>

    <div class="row g-4 mb-4 align-items-stretch">
        
        <div class="col-lg-8">
            <div class="card h-100" style="border-radius: 12px; border: 1px solid #cbd5e1; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                <div class="card-header bg-white border-0 pt-4 pb-3 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0" style="color: #475569; font-size: 14px;">
                        <span style="color: #28a745; font-size: 10px; margin-right: 8px;" class="spinner-grow spinner-grow-sm" role="status"></span> Data Realtime Log Gerbang
                    </h6>
                    <span class="badge bg-danger" style="animation: flashGreen 2s infinite;">LIVE UPDATE</span>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive custom-scrollbar" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead style="position: sticky; top: 0; background-color: #ffffff; z-index: 5; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                <tr>
                                    <th class="ps-4 py-3" style="color: #6c757d; font-size: 11px; font-weight: bold; letter-spacing: 0.5px; background-color: #ffffff; border-top: 1px solid #e9ecef; border-bottom: 1px solid #e9ecef;">NAMA/ID</th>
                                    <th class="py-3" style="color: #6c757d; font-size: 11px; font-weight: bold; letter-spacing: 0.5px; background-color: #ffffff; border-top: 1px solid #e9ecef; border-bottom: 1px solid #e9ecef;">WAKTU</th>
                                    <th class="py-3" style="color: #6c757d; font-size: 11px; font-weight: bold; letter-spacing: 0.5px; background-color: #ffffff; border-top: 1px solid #e9ecef; border-bottom: 1px solid #e9ecef;">AKTIVITAS</th>
                                    <th class="ps-3 py-3" style="color: #6c757d; font-size: 11px; font-weight: bold; letter-spacing: 0.5px; background-color: #ffffff; border-top: 1px solid #e9ecef; border-bottom: 1px solid #e9ecef;">KETERANGAN</th>
                                </tr>
                            </thead>
                            <tbody id="liveLogBody">
                                </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center border-top mt-auto">
                    <span id="logDataCountText" class="text-muted" style="font-size: 13px;">Menampilkan data...</span>
                    <div class="d-flex align-items-center gap-3">
                        <select id="logRowLimitSelect" class="form-select form-select-sm" style="width: auto; cursor: pointer;">
                            <option value="10">10 rows per page</option>
                            <option value="25">25 rows per page</option>
                            <option value="50" selected>50 rows per page</option>
                            <option value="100">100 rows per page</option>
                        </select>
                        <div class="btn-group">
                            <button id="btnLogPrevPage" class="btn btn-sm btn-outline-secondary px-3" disabled style="border-radius: 6px 0 0 6px;"><i class="fa-solid fa-chevron-left"></i></button>
                            <button id="btnLogNextPage" class="btn btn-sm btn-outline-secondary px-3" disabled style="border-radius: 0 6px 6px 0;"><i class="fa-solid fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100" style="border-radius: 12px; background-color: #ffffff;">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="fw-bold mb-1" style="color: #1e293b; font-size: 16px;">Traffic Gerbang</h6>
                        <p class="text-muted" style="font-size: 11px;">Distribusi akses per perangkat</p>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                    <select id="filterGateZone" class="form-select form-select-sm d-none" style="width: 130px; border-radius: 6px; font-size: 11px; font-weight: bold; cursor: pointer; border-color: #cbd5e1; background-color: #f1f5f9;">
                        <option value="semua" selected>Gabungan Zona</option>
                        <option value="kuning">Zona Kuning</option>
                        <option value="merah">Zona Merah</option>
                    </select>
                    <select id="filterDirectionGate" class="form-select form-select-sm" style="width: 130px; border-radius: 6px; font-size: 11px; font-weight: bold; cursor: pointer; border-color: #cbd5e1;">
                        <option value="masuk" selected>Arah Masuk</option>
                        <option value="keluar">Arah Keluar</option>
                    </select>
                </div>
                </div>
                <div class="card-body px-4 pb-4 d-flex flex-column justify-content-center align-items-center">
                    <div style="height: 300px; width: 100%;">
                        <canvas id="gateChart"></canvas>
                    </div>
                    <div id="gateLegend" class="row w-100 g-2 mt-3 pt-3" style="border-top: 1px dashed #cbd5e1; margin: 0;">
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="mb-4" style="position: relative; width: 100%; height: 80px; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <div class="bg-layer-container">
            <div class="bg-layer-top" style="height: 100%;">
                <div class="scrolling-bg-top">
                    <img src="{{ asset('images/gambar1.png') }}" alt="bg1"><img src="{{ asset('images/gambar2.jpg') }}" alt="bg2">
                    <img src="{{ asset('images/gambar3.jpg') }}" alt="bg3"><img src="{{ asset('images/gambar4.jpg') }}" alt="bg4">
                    <img src="{{ asset('images/gambar5.jpg') }}" alt="bg5"><img src="{{ asset('images/gambar6.jpg') }}" alt="bg6">
                    <img src="{{ asset('images/gambar1.png') }}" alt="bg1"><img src="{{ asset('images/gambar2.jpg') }}" alt="bg2">
                    <img src="{{ asset('images/gambar3.jpg') }}" alt="bg3"><img src="{{ asset('images/gambar4.jpg') }}" alt="bg4">
                    <img src="{{ asset('images/gambar5.jpg') }}" alt="bg5"><img src="{{ asset('images/gambar6.jpg') }}" alt="bg6">
                </div>
            </div>
        </div>
        <div class="hero-overlay" style="background: linear-gradient(135deg, rgba(12, 74, 82, 0.9) 0%, rgba(16, 103, 112, 0.8) 100%); z-index: 1;"></div>
        <div class="hero-content" style="display: flex; align-items: center; padding: 0 20px; z-index: 2;">
            <div class="d-none d-md-block" style="width: 60px; height: 2px; background-color: #efe62f; margin-right: 20px;"></div>
            <h4 style="font-weight: 800; letter-spacing: 3px; margin-bottom: 0; font-size: 16px; text-shadow: 0 2px 4px rgba(0,0,0,0.3); text-transform: uppercase; color: #ffffff;">
                Statistik Tingkat Kehadiran Departemen
            </h4>
            <div class="d-none d-md-block" style="width: 60px; height: 2px; background-color: #efe62f; margin-left: 20px;"></div>
        </div>
    </div>

    <div class="card mb-5" style="border-radius: 12px; background-color: #ffffff;">
        <div class="card-header bg-white border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center flex-wrap" style="border-bottom: 1px solid #e9ecef !important;">
            <div>
                <h6 class="fw-bold mb-1" style="color: #1e293b; font-size: 16px;">Tingkat Kehadiran per Departemen</h6>
                <p class="text-muted mb-0" style="font-size: 11px;">Rincian persentase dan jumlah personel (Semua Departemen)</p>
            </div>
            <select id="filterKehadiranDept" class="form-select form-select-sm" style="width: auto; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer; border-color: #cbd5e1;">
                <option value="hadir" selected>Hadir (Tepat Waktu)</option>
                <option value="telat">Terlambat</option>
                <option value="absen">Tidak Masuk (Kosong)</option>
            </select>
        </div>
        <div class="card-body p-4">
            <div id="tingkatListContainer" class="custom-scrollbar pe-3" style="max-height: 400px; overflow-y: auto;">
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            let lastScrollTop = 0;
            const topNavbar = document.querySelector('.topbar, .navbar, header'); 

            if (topNavbar) {
                window.addEventListener('scroll', function() {
                    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    if (scrollTop > lastScrollTop && scrollTop > 60) topNavbar.classList.add('hidden-nav');
                    else topNavbar.classList.remove('hidden-nav');
                    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; 
                }, false);
            }

            function updateCenterClock() {
                const now = new Date();
                const timeString = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ':' + String(now.getSeconds()).padStart(2, '0');
                const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"];
                const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                const dateString = days[now.getDay()] + ', ' + String(now.getDate()).padStart(2, '0') + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
                const dateTimeEl = document.getElementById('centerDateTime');
                if(dateTimeEl) dateTimeEl.innerText = dateString + '   ' + timeString + ' WITA';
            }
            setInterval(updateCenterClock, 1000);
            updateCenterClock();

            const listNamaDept = [
                "PT CHANDRA WIJAYA UTAMA", "PT GADA ANEKA SOLUSINDO", "PLN IPS UBP JERANJANG", "PT KOPJAS", "PT HALEYORA POWER", 
                "PT COGINDO DAYABERSAMA", "PT BARATA INDONESIA", "PT TARUNA", "DEPARTEMEN K3 & LINGKUNGAN", "DEPARTEMEN OPERASI", 
                "DEPARTEMEN PEMELIHARAAN", "DEPARTEMEN LOGISTIK", "DEPARTEMEN KEUANGAN", "DEPARTEMEN PENGADAAN", "RENDAL HAR", 
                "RENDAL OPS", "ENJINIRING", "ADMINISTRASI", "UMUM & FASILITAS", "SIPIL", 
                "INSTRUMENTASI", "LISTRIK", "MESIN", "PENGUJIAN", "GUDANG UTAMA", 
                "LAB KIMIA", "SECURITY", "CLEANING SERVICE", "TAMU / VISITOR", "VENDOR LOKAL"
            ];

            const departemenSemua = listNamaDept.map((nama, index) => {
                let totalAnggota = Math.floor(Math.random() * 150) + 50; 
                if(index === 0) totalAnggota = 550;
                if(index === 1) totalAnggota = 420;
                if(index === 2) totalAnggota = 350;

                let hadir_val = Math.floor(totalAnggota * (Math.random() * 0.5 + 0.4)); 
                let telat_val = Math.floor(totalAnggota * (Math.random() * 0.2));       
                
                if(index === 0) { hadir_val = 480; telat_val = 50; }
                if(index === 1) { hadir_val = 360; telat_val = 40; }
                if(index === 2) { hadir_val = 290; telat_val = 40; }

                let absen_val = totalAnggota - hadir_val - telat_val; 

                let hadir = Math.round((hadir_val / totalAnggota) * 100);
                let telat = Math.round((telat_val / totalAnggota) * 100);
                let absen = 100 - hadir - telat;

                let masuk = hadir_val + telat_val;
                let didalam = Math.floor(masuk * 0.7);
                let keluar = masuk - didalam;

                return { 
                    nama, totalAnggota,
                    masuk, didalam, keluar, 
                    hadir, telat, absen, 
                    hadir_val, telat_val, absen_val 
                };
            });

            const departemenKuning = departemenSemua.map(d => ({...d, masuk: Math.max(0, Math.floor(d.masuk * 0.6)), didalam: Math.max(0, Math.floor(d.didalam * 0.6)) }));
            const departemenMerah = departemenSemua.map(d => ({...d, masuk: Math.max(0, Math.floor(d.masuk * 0.2)), didalam: Math.max(0, Math.floor(d.didalam * 0.2)) }));

            const zoneData = {
                kuning: { 
                    title: 'MONITORING ZONA KUNING', 
                    masuk: 1850, didalam: 1600, keluar: 250, tamu: 45, 
                    gateLabels: ['Tripod', 'Motor', 'Mobil Kecil', 'Mobil Besar'],
                    gateData: { masuk: [800, 600, 300, 150], keluar: [100, 80, 50, 20] }, 
                    departemen: departemenKuning 
                },
                merah: { 
                    title: 'MONITORING ZONA MERAH', 
                    masuk: 1100, didalam: 950, keluar: 150, tamu: 12, 
                    gateLabels: ['Tripod', 'Sepeda/Motor', 'Mobil Kecil/Besar', 'VIP'],
                    gateData: { masuk: [500, 400, 150, 50], keluar: [80, 40, 20, 10] }, 
                    departemen: departemenMerah 
                }, 
                semua: { 
                    title: 'MONITORING SEMUA ZONA', 
                    masuk: 2950, didalam: 2550, keluar: 400, tamu: 57, 
                    gateLabels: ['Semua Tripod', 'Semua Roda 2', 'Semua Roda 4', 'Akses VIP'],
                    gateData: { masuk: [1300, 1000, 450, 200], keluar: [180, 120, 70, 30] }, 
                    departemen: departemenSemua 
                } 
            };

            const liveLogData = [];
            const firstNames = ["Budi", "Siti", "Agus", "Rina", "Andi", "Dewi", "Eko", "Sri", "Rudi", "Ayu", "Joko", "Fitri", "Dedi", "Indah"];
            const lastNames = ["Santoso", "Aminah", "Pratama", "Wijaya", "Saputra", "Lestari", "Nugroho", "Wahyu", "Hartono", "Kusuma", "Setiawan"];

            for (let i = 0; i < 120; i++) {
                let isMasuk = Math.random() > 0.4;
                let jam = String(Math.floor(Math.random() * 8) + 6).padStart(2, '0');
                let menit = String(Math.floor(Math.random() * 60)).padStart(2, '0');
                let detik = String(Math.floor(Math.random() * 60)).padStart(2, '0');
                let isTelat = isMasuk && parseInt(jam) >= 8;

                liveLogData.push({
                    nama: firstNames[Math.floor(Math.random() * firstNames.length)] + " " + lastNames[Math.floor(Math.random() * lastNames.length)],
                    waktu: `${jam}:${menit}:${detik}`,
                    aktivitas: isMasuk ? 'MASUK' : 'KELUAR',
                    status: isMasuk ? (isTelat ? 'TERLAMBAT' : 'HADIR') : 'KELUAR',
                });
            }
            liveLogData.sort((a, b) => b.waktu.localeCompare(a.waktu));

            const gateColors = ['#106770', '#14a2ba', '#efe62f', '#dc3545'];

            const ctxGate = document.getElementById('gateChart');
            let gateChart = ctxGate ? new Chart(ctxGate.getContext('2d'), {
                type: 'polarArea',
                data: { labels: [], datasets: [{ data: [0, 0, 0, 0], backgroundColor: gateColors.map(c => c+'CC'), borderWidth: 2, borderColor: '#ffffff' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(context) { return ' ' + context.label + ': ' + context.raw + ' Orang'; } } } }, scales: { r: { ticks: { display: false } } } }
            }) : null;

            const zoneSelect = document.getElementById('zoneSelect');
            const filterKehadiranDept = document.getElementById('filterKehadiranDept');
            const filterDirectionGate = document.getElementById('filterDirectionGate');
            const filterGateZone = document.getElementById('filterGateZone'); // Dropdown baru
            const heroTitle = document.getElementById('heroTitle');
            const rekapBody = document.getElementById('rekapDeptBody');
            const tingkatListContainer = document.getElementById('tingkatListContainer');

            function animateValue(obj, start, end, duration) {
                if(!obj) return;
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    obj.innerHTML = Math.floor(progress * (end - start) + start);
                    if (progress < 1) window.requestAnimationFrame(step);
                };
                window.requestAnimationFrame(step);
            }

            function updateDashboard(isSimulating = false) {
                const selectedZone = zoneSelect.value;
                const data = zoneData[selectedZone];
                const selectedFilter = filterKehadiranDept.value; 
                const selectedGateDirection = filterDirectionGate ? filterDirectionGate.value : 'masuk';

                // LOGIKA UNTUK MENAMPILKAN/MENYEMBUNYIKAN DROPDOWN SUB-ZONA
                let gateZoneKey = selectedZone;
                if (selectedZone === 'semua') {
                    if(filterGateZone) filterGateZone.classList.remove('d-none');
                    gateZoneKey = filterGateZone ? filterGateZone.value : 'semua';
                } else {
                    if(filterGateZone) {
                        filterGateZone.classList.add('d-none');
                        filterGateZone.value = 'semua'; // Reset otomatis saat disembunyikan
                    }
                }
                const dataGate = zoneData[gateZoneKey];

                if(!isSimulating && heroTitle) { 
                    heroTitle.style.opacity = 0; 
                    setTimeout(() => { heroTitle.innerText = data.title; heroTitle.style.opacity = 1; }, 200); 
                }

                if (isSimulating) {
                    document.getElementById('statMasuk').innerHTML = data.masuk;
                    document.getElementById('statDiDalam').innerHTML = data.didalam;
                    document.getElementById('statKeluar').innerHTML = data.keluar;
                    document.getElementById('statTamu').innerHTML = data.tamu;
                } else {
                    animateValue(document.getElementById('statMasuk'), parseInt(document.getElementById('statMasuk')?.innerText) || 0, data.masuk, 800);
                    animateValue(document.getElementById('statDiDalam'), parseInt(document.getElementById('statDiDalam')?.innerText) || 0, data.didalam, 800);
                    animateValue(document.getElementById('statKeluar'), parseInt(document.getElementById('statKeluar')?.innerText) || 0, data.keluar, 800);
                    animateValue(document.getElementById('statTamu'), parseInt(document.getElementById('statTamu')?.innerText) || 0, data.tamu, 800);
                }

                if(gateChart) {
                    // Pakai data dari dataGate (bukan data utama) agar chart gerbang bisa terisolasi
                    const currentGateData = dataGate.gateData[selectedGateDirection];
                    const currentGateLabels = dataGate.gateLabels; 
                    
                    gateChart.data.labels = currentGateLabels; 
                    gateChart.data.datasets[0].data = currentGateData;
                    gateChart.update();

                    const gateLegend = document.getElementById('gateLegend');
                    if(gateLegend) {
                        let gHtml = '';
                        currentGateLabels.forEach((label, i) => {
                            gHtml += `
                                <div class="col-6">
                                    <div class="d-flex align-items-center justify-content-between pe-3 mb-2">
                                        <div class="d-flex align-items-center">
                                            <span style="width:10px; height:10px; background-color:${gateColors[i]}; border-radius:50%; margin-right:8px;"></span>
                                            <span style="font-size:11px; font-weight:600; color:#475569;">${label}</span>
                                        </div>
                                        <span style="font-size:13px; font-weight:900; color:#1e293b;">${currentGateData[i]}</span>
                                    </div>
                                </div>
                            `;
                        });
                        gateLegend.innerHTML = gHtml;
                    }
                }

                if (rekapBody) {
                    const sortedDepts = [...data.departemen].sort((a, b) => b.masuk - a.masuk);
                    rekapBody.innerHTML = ''; 
                    sortedDepts.forEach(dept => {
                        rekapBody.innerHTML += `
                            <tr>
                                <td class="ps-4 fw-bold" style="color: #1e293b;">${dept.nama}</td>
                                <td class="text-center fw-bold" style="color: #106770; font-size: 15px;">${dept.masuk}</td>
                                <td class="text-center fw-bold" style="color: #28a745; font-size: 15px;">${dept.didalam}</td>
                                <td class="text-center fw-bold" style="color: #dc3545; font-size: 15px;">${dept.keluar}</td>
                            </tr>
                        `;
                    });
                }

                if (tingkatListContainer) {
                    const fixedDepts = [...data.departemen].sort((a, b) => b.totalAnggota - a.totalAnggota);
                    
                    let barColor = '#106770';
                    if (selectedFilter === 'hadir') barColor = '#106770'; 
                    else if (selectedFilter === 'telat') barColor = '#efe62f'; 
                    else if (selectedFilter === 'absen') barColor = '#dc3545'; 

                    tingkatListContainer.innerHTML = ''; 

                    fixedDepts.forEach(dept => {
                        let exactCount = dept[selectedFilter + '_val']; 
                        let persen = dept[selectedFilter];

                        tingkatListContainer.innerHTML += `
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <div class="d-flex align-items-center">
                                        <span style="display:inline-block; width:8px; height:8px; background-color:${barColor}; border-radius:50%; margin-right:8px;"></span>
                                        <span class="fw-bold" style="font-size: 12px; color: #475569;">${dept.nama}</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold" style="font-size: 13px; color: #1e293b;">${exactCount} <span style="font-size:10px; font-weight:normal; color:#64748b;">org</span></span>
                                        <span class="fw-bold ms-2" style="font-size: 12px; color: ${barColor};">${persen}%</span>
                                    </div>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 10px; background-color: #f1f5f9;">
                                    <div class="progress-bar" role="progressbar" style="width: ${persen}%; background-color: ${barColor}; border-radius: 10px; transition: width 0.5s ease-in-out;"></div>
                                </div>
                            </div>
                        `;
                    });
                }
            }

            if(zoneSelect) zoneSelect.addEventListener('change', () => updateDashboard(false));
            if(filterKehadiranDept) filterKehadiranDept.addEventListener('change', () => updateDashboard(false));
            if(filterDirectionGate) filterDirectionGate.addEventListener('change', () => updateDashboard(false));
            if(filterGateZone) filterGateZone.addEventListener('change', () => updateDashboard(false)); // Listener baru
            
            updateDashboard(false);

            const labelsWaktu = ['06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
            
            const dataMasukTepat = [400, 1800, 200, 50, 0, 0, 0, 0, 0, 0, 0, 0];
            const dataMasukTelat = [0, 0, 400, 120, 30, 0, 0, 0, 0, 0, 0, 0];
            const dataKeluarTepat = [0, 0, 0, 0, 0, 0, 50, 30, 20, 100, 2400, 150];
            const dataKeluarTelat = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 200, 50];

            const ctxBar = document.getElementById('trendBarChart');
            const ctxLine = document.getElementById('trendLineChart');
            let trendBarChart, trendLineChart;

            const yAxisConfig = { 
                beginAtZero: true, 
                suggestedMax: 3000, 
                grid: { borderDash: [5, 5] } 
            };

            if(ctxBar && ctxLine) {
                trendBarChart = new Chart(ctxBar.getContext('2d'), {
                    type: 'bar',
                    data: { labels: labelsWaktu, datasets: [{ label: 'Tepat Waktu', data: [...dataMasukTepat], backgroundColor: '#106770', borderRadius: 4 }, { label: 'Terlambat', data: [...dataMasukTelat], backgroundColor: '#efe62f', borderRadius: 4 }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } }, scales: { x: { grid: { display: false } }, y: yAxisConfig }, animation: { duration: 500 } }
                });

                trendLineChart = new Chart(ctxLine.getContext('2d'), {
                    type: 'line',
                    data: { labels: labelsWaktu, datasets: [{ label: 'Tepat Waktu', data: [...dataMasukTepat], backgroundColor: 'rgba(16, 103, 112, 0.2)', borderColor: '#106770', borderWidth: 2, fill: true, tension: 0.4 }, { label: 'Terlambat', data: [...dataMasukTelat], backgroundColor: 'rgba(239, 230, 47, 0.2)', borderColor: '#efe62f', borderWidth: 2, fill: true, tension: 0.4 }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } }, scales: { x: { grid: { display: false } }, y: yAxisConfig }, animation: { duration: 500 } }
                });

                document.getElementById('filterWaktu')?.addEventListener('change', function(e) {
                    const isMasuk = e.target.value === 'masuk';
                    trendBarChart.data.datasets[0].data = isMasuk ? dataMasukTepat : dataKeluarTepat;
                    trendBarChart.data.datasets[1].data = isMasuk ? dataMasukTelat : dataKeluarTelat;
                    trendBarChart.update();

                    trendLineChart.data.datasets[0].data = isMasuk ? dataMasukTepat : dataKeluarTepat;
                    trendLineChart.data.datasets[1].data = isMasuk ? dataMasukTelat : dataKeluarTelat;
                    trendLineChart.update();
                });
            }

            const liveLogBody = document.getElementById('liveLogBody');
            const logDataCountText = document.getElementById('logDataCountText');
            const logRowLimitSelect = document.getElementById('logRowLimitSelect');
            const btnLogPrevPage = document.getElementById('btnLogPrevPage');
            const btnLogNextPage = document.getElementById('btnLogNextPage');

            let currentLogPage = 1;
            let logsPerPage = logRowLimitSelect ? parseInt(logRowLimitSelect.value) : 50; 

            function renderLiveLogs() {
                if(liveLogData.length === 0) return;
                const totalRecords = liveLogData.length;
                const totalPages = Math.ceil(totalRecords / logsPerPage);
                if (currentLogPage > totalPages) currentLogPage = totalPages || 1;

                const startIndex = (currentLogPage - 1) * logsPerPage;
                const endIndex = startIndex + logsPerPage;
                const pagedData = liveLogData.slice(startIndex, endIndex);

                liveLogBody.innerHTML = '';
                pagedData.forEach((log, index) => {
                    let icon = log.aktivitas === 'MASUK' ? '<i class="fa-solid fa-arrow-right" style="transform: rotate(-45deg);"></i>' : '<i class="fa-solid fa-arrow-right" style="transform: rotate(135deg);"></i>';
                    let bgColor = log.aktivitas === 'MASUK' ? '#14a2ba' : '#ff0000';
                    let textColor = log.aktivitas === 'MASUK' ? '#125d72' : '#dc3545';
                    let badgeBg = log.status === 'HADIR' ? '#dcfce7' : (log.status === 'TERLAMBAT' ? '#fffbeb' : '#e2e8f0');
                    let badgeText = log.status === 'HADIR' ? '#125d72' : (log.status === 'TERLAMBAT' ? '#d97706' : '#ff0000');
                    
                    let trClass = (index === 0 && currentLogPage === 1) ? 'log-row new-log-row' : 'log-row';

                    liveLogBody.innerHTML += `
                        <tr class="${trClass}">
                            <td class="ps-4 fw-bold" style="color: #1e293b;">${log.nama}</td>
                            <td class="text-muted">${log.waktu}</td>
                            <td>
                                <div class="fw-bold d-flex align-items-center">
                                    <div class="d-inline-flex align-items-center justify-content-center" style="background-color: ${bgColor}; color: white; width: 22px; height: 22px; border-radius: 4px; margin-right: 10px; font-size: 10px;">${icon}</div>
                                    <span style="color: ${textColor}; font-size: 12px;">${log.aktivitas}</span>
                                </div>
                            </td>
                            <td class="ps-3"><span class="badge" style="background-color: ${badgeBg}; color: ${badgeText}; padding: 6px 15px; border-radius: 4px; font-weight: 600; letter-spacing: 0.5px; font-size: 11px;">${log.status}</span></td>
                        </tr>
                    `;
                });

                if (totalRecords > 0) {
                    let startDisplay = startIndex + 1;
                    let endDisplay = Math.min(endIndex, totalRecords);
                    if(logDataCountText) logDataCountText.innerHTML = `Menampilkan <strong class="text-dark">${startDisplay}-${endDisplay}</strong> dari <strong class="text-dark">${totalRecords}</strong> data`;
                }

                if (btnLogPrevPage) btnLogPrevPage.disabled = currentLogPage === 1;
                if (btnLogNextPage) btnLogNextPage.disabled = currentLogPage === totalPages || totalPages === 0;
            }

            if (logRowLimitSelect) logRowLimitSelect.addEventListener('change', function() { logsPerPage = parseInt(this.value); currentLogPage = 1; renderLiveLogs(); });
            if (btnLogPrevPage) btnLogPrevPage.addEventListener('click', function() { if (currentLogPage > 1) { currentLogPage--; renderLiveLogs(); } });
            if (btnLogNextPage) btnLogNextPage.addEventListener('click', function() { const totalPages = Math.ceil(liveLogData.length / logsPerPage); if (currentLogPage < totalPages) { currentLogPage++; renderLiveLogs(); } });

            renderLiveLogs();

            setInterval(() => {
                const isMasuk = Math.random() > 0.3; 
                const count = Math.floor(Math.random() * 4) + 1; 
                
                const selectedZone = zoneSelect.value;
                const data = zoneData[selectedZone];
                
                if (isMasuk) {
                    data.masuk += count;
                    data.didalam += count;
                } else {
                    if(data.didalam >= count) {
                        data.didalam -= count;
                        data.keluar += count;
                    }
                }

                // Logika agar saat Semua Zona terpilih, simulasi tetap mengisi data Kuning/Merah
                const randomGate = Math.floor(Math.random() * 4);
                if (isMasuk) {
                    data.gateData.masuk[randomGate] += count;
                    if(selectedZone === 'semua') {
                        const subZone = Math.random() > 0.5 ? 'kuning' : 'merah';
                        zoneData[subZone].gateData.masuk[randomGate] += count;
                    }
                } else {
                    data.gateData.keluar[randomGate] += count;
                    if(selectedZone === 'semua') {
                        const subZone = Math.random() > 0.5 ? 'kuning' : 'merah';
                        zoneData[subZone].gateData.keluar[randomGate] += count;
                    }
                }

                const randomDeptIndex = Math.floor(Math.random() * 15);
                const dept = data.departemen[randomDeptIndex];
                if (isMasuk) {
                    dept.masuk += count;
                    dept.didalam += count;
                    dept.hadir_val += count; 
                    dept.hadir = Math.round((dept.hadir_val / dept.totalAnggota) * 100);
                } else {
                    if (dept.didalam >= count) {
                        dept.didalam -= count;
                        dept.keluar += count;
                    }
                }

                const tableContainer = document.querySelector('#rekapDeptBody')?.closest('.custom-scrollbar');
                const listContainer = document.getElementById('tingkatListContainer');
                const tableScroll = tableContainer ? tableContainer.scrollTop : 0;
                const listScroll = listContainer ? listContainer.scrollTop : 0;

                updateDashboard(true);
                
                if(tableContainer) tableContainer.scrollTop = tableScroll;
                if(listContainer) listContainer.scrollTop = listScroll;

                const now = new Date();
                const currentHourIndex = now.getHours() - 6; 
                const isTrendMasuk = document.getElementById('filterWaktu').value === 'masuk';
                const statusBaru = isMasuk ? (now.getHours() >= 8 ? 'TERLAMBAT' : 'HADIR') : 'KELUAR';
                
                if (currentHourIndex >= 0 && currentHourIndex <= 11) {
                    if (isMasuk) {
                        if (statusBaru === 'HADIR') dataMasukTepat[currentHourIndex] += count;
                        if (statusBaru === 'TERLAMBAT') dataMasukTelat[currentHourIndex] += count;
                    } else {
                        dataKeluarTepat[currentHourIndex] += count; 
                    }

                    if (trendBarChart && trendLineChart) {
                        trendBarChart.data.datasets[0].data = isTrendMasuk ? dataMasukTepat : dataKeluarTepat;
                        trendBarChart.data.datasets[1].data = isTrendMasuk ? dataMasukTelat : dataKeluarTelat;
                        trendBarChart.update();
                        
                        trendLineChart.data.datasets[0].data = isTrendMasuk ? dataMasukTepat : dataKeluarTepat;
                        trendLineChart.data.datasets[1].data = isTrendMasuk ? dataMasukTelat : dataKeluarTelat;
                        trendLineChart.update();
                    }
                }

                for(let c=0; c<count; c++) {
                    const jamStr = String(now.getHours()).padStart(2, '0');
                    const menitStr = String(now.getMinutes()).padStart(2, '0');
                    const detikStr = String(now.getSeconds()).padStart(2, '0');
                    
                    const namaBaru = firstNames[Math.floor(Math.random() * firstNames.length)] + " " + lastNames[Math.floor(Math.random() * lastNames.length)];
                    
                    liveLogData.unshift({
                        nama: namaBaru,
                        waktu: `${jamStr}:${menitStr}:${detikStr}`,
                        aktivitas: isMasuk ? 'MASUK' : 'KELUAR',
                        status: statusBaru,
                    });
                }
                
                if (currentLogPage === 1) renderLiveLogs();

            }, 3000); 

        });
    </script>
@endsection