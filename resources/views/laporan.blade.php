@extends('layouts.app')

@section('title', 'LAPORAN DAN STATISTIK')

@section('nav_laporan', 'active')

@section('content')

    <style>
        /* Custom Scrollbar Tipis */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .form-label-custom {
            font-size: 11px; 
            color: #475569; 
            font-weight: 800; 
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        /* Zebra Striping Tabel */
        .table-striped-custom tbody tr:nth-of-type(odd) td { background-color: #ffffff !important; }
        .table-striped-custom tbody tr:nth-of-type(even) td { background-color: #f8fafc !important; }
        .table-striped-custom tbody tr:hover td { background-color: #f1f5f9 !important; }

        /* CSS ANIMASI BACKGROUND HEADER */
        .bg-layer-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; }
        .bg-layer-top { position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; }
        .scrolling-bg-top { position: absolute; top: 0; left: -100%; height: 100%; display: flex; width: 200%; animation: scrollBackgroundRight 40s linear infinite; }
        .scrolling-bg-top img { width: calc(100% / 12); height: 100%; object-fit: cover; flex-shrink: 0; }
        
        @keyframes scrollBackgroundRight { 0% { transform: translateX(0); } 100% { transform: translateX(50%); } }

        /* Overlay Khas PLN IP */
        .hero-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(12, 74, 82, 0.85) 0%, rgba(20, 162, 186, 0.75) 100%); z-index: 1;
        }

        /* ========================================================
           TAMBAHAN CSS UNTUK MODAL PROFILE
           ======================================================== */
        .clickable-name {
            color: #125d72;
            cursor: pointer;
            font-weight: bold;
            transition: 0.2s;
            border-bottom: 1px dashed transparent;
        }
        .clickable-name:hover {
            color: #14a2ba;
            border-bottom: 1px dashed #14a2ba;
        }
        .modal-content { border-radius: 15px; border: none; overflow: hidden; }
        .modal-header-custom { background-color: #125d72; color: white; padding: 20px; }
        .profile-img-placeholder {
            width: 80px; height: 80px; border-radius: 50%;
            background-color: #e2e8f0; display: flex; align-items: center;
            justify-content: center; border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .stat-box {
            background: #f8fafc; border-radius: 8px; padding: 10px;
            border: 1px solid #e2e8f0; text-align: center;
            transition: 0.3s; height: 100%; display: flex; flex-direction: column; justify-content: center;
        }
        .stat-box:hover {
            background: #ffffff; border-color: #14a2ba; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .truncate-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; display: inline-block; }

        /* ========================================================
           GITHUB STYLE HEATMAP CSS (REVISI FIT-WIDTH TANPA SCROLL)
           ======================================================== */
        .heatmap-wrapper { 
            display: flex; align-items: flex-start; width: 100%; 
            margin-top: 5px; padding-bottom: 5px; overflow: hidden;
        }
        .heatmap-labels { 
            display: grid; grid-template-rows: repeat(7, 11px); gap: 3px; 
            font-size: 9px; color: #94a3b8; margin-right: 8px; text-align: right;
            margin-top: 16px; /* Turun menyesuaikan tinggi label bulan di sebelahnya */
            font-weight: 600; line-height: 11px; width: 20px; flex-shrink: 0;
        }
        .heatmap-content {
            display: flex; flex-direction: column;
        }
        .heatmap-content.full-width {
            width: 100%; flex-grow: 1; /* Expand full width */
        }
        .heatmap-header {
            position: relative; height: 16px; width: 100%;
        }
        .heatmap-month-label {
            position: absolute; font-size: 10px; color: #64748b; font-weight: bold; top: 0;
        }
        .heatmap-container { 
            display: grid; grid-template-rows: repeat(7, 11px); 
            grid-auto-columns: 11px; grid-auto-flow: column; gap: 3px; 
        }
        .heatmap-content.full-width .heatmap-container {
            width: 100%; justify-content: space-between; /* Fit otomatis 1 tahun full */
        }
        .heatmap-box { 
            width: 11px; height: 11px; border-radius: 2px; cursor: pointer; 
            transition: transform 0.1s, box-shadow 0.1s;
        }
        .heatmap-box.hidden { background-color: transparent; pointer-events: none; }
        .heatmap-box:hover:not(.hidden) { transform: scale(1.4); z-index: 10; box-shadow: 0 2px 5px rgba(0,0,0,0.3); }
        
        /* Warna Status Heatmap Persis GitHub/Referensi */
        .bg-hadir { background-color: #0c5a66; }  
        .bg-telat { background-color: #fde047; }  
        .bg-alpha { background-color: #e13b48; }  
        .bg-libur { background-color: #ffb4b4; }  
        .bg-weekend { background-color: #e2e8f0; } 
        .bg-empty-future { background-color: #f8fafc; border: 1px dashed #cbd5e1; } 
        
        .heatmap-legend { 
            display: flex; gap: 15px; font-size: 10px; margin-top: 15px; 
            align-items: center; flex-wrap: wrap; color: #475569; font-weight: bold;
            justify-content: center; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;
        }
        .legend-box { width: 10px; height: 10px; border-radius: 2px; display: inline-block; margin-right: 6px; vertical-align: middle; }
    </style>

    <div class="mb-4" style="position: relative; width: 100%; min-height: 100px; border-radius: 12px; overflow: hidden; display: flex; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); padding: 20px 30px;">
        <div class="bg-layer-container">
            <div class="bg-layer-top">
                <div class="scrolling-bg-top">
                    <img src="{{ asset('images/gambar1.png') }}" alt="bg1"><img src="{{ asset('images/gambar2.jpg') }}">
                    <img src="{{ asset('images/gambar3.jpg') }}" alt="bg3"><img src="{{ asset('images/gambar4.jpg') }}">
                    <img src="{{ asset('images/gambar5.jpg') }}" alt="bg5"><img src="{{ asset('images/gambar6.jpg') }}">
                    <img src="{{ asset('images/gambar1.png') }}" alt="bg1"><img src="{{ asset('images/gambar2.jpg') }}">
                    <img src="{{ asset('images/gambar3.jpg') }}" alt="bg3"><img src="{{ asset('images/gambar4.jpg') }}">
                    <img src="{{ asset('images/gambar5.jpg') }}" alt="bg5"><img src="{{ asset('images/gambar6.jpg') }}">
                </div>
            </div>
        </div>
        <div class="hero-overlay"></div>
        <div class="w-100 d-flex justify-content-between align-items-center flex-wrap gap-3" style="position: relative; z-index: 2; text-align: left;">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-clipboard-list text-warning" style="font-size: 30px; margin-right: 20px;"></i>
                <div>
                    <h5 class="fw-bold mb-1" style="color: white; text-transform: uppercase; letter-spacing: 1px;">LAPORAN ABSENSI (FIRST IN LAST OUT)</h5>
                    <p class="mb-0" style="font-size: 12px; color: #e0ecee;">Menghitung Jam Masuk Pertama dan Jam Keluar Terakhir Karyawan.</p>
                </div>
            </div>
            <div class="px-3 py-1" style="background-color: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 20px; font-size: 12px; font-weight: bold; color: white;">
                <span style="color: #4ade80;">●</span> Logic Active
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5 align-items-stretch">
        
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border: 1px solid #cbd5e1 !important; z-index: 10;">
                <div class="card-header bg-white border-bottom p-4">
                    <h6 class="fw-bold mb-0" style="color: #0c4a52;"><i class="fa-solid fa-filter text-info me-2"></i> Filter Data</h6>
                </div>
                
                <div class="card-body p-4">
                    <form action="#" method="GET" id="formFilterData">
                        <div class="mb-3">
                            <label class="form-label-custom">Pilih Bulan</label>
                            <select name="bulan" id="filterBulan" class="form-select form-select-sm py-2" style="border-radius: 6px; border-color: #cbd5e1; color: #475569;">
                                <option value="semua">Sepanjang Tahun (YTD)</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04" selected>April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Pilih Tahun</label>
                            <select name="tahun" id="filterTahun" class="form-select form-select-sm py-2" style="border-radius: 6px; border-color: #cbd5e1; color: #475569;">
                                <option value="2026" selected>2026</option>
                                <option value="2025">2025</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Status Absensi</label>
                            <select name="status" id="filterStatus" class="form-select form-select-sm py-2" style="border-radius: 6px; border-color: #cbd5e1; color: #475569;">
                                <option value="semua" selected>Semua Status</option>
                                <option value="hadir">Hadir</option>
                                <option value="terlambat">Terlambat</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label-custom">Filter Karyawan</label>
                            <select name="karyawan" id="karyawanSelect" class="form-select form-select-sm py-2" style="border-radius: 6px; border-color: #cbd5e1; color: #475569;">
                                <option value="semua" selected>Semua Karyawan</option>
                                <option value="lainnya">Lainnya (Ketik Nama/ID)</option>
                            </select>
                            <input type="text" name="karyawan_custom" id="karyawanCustom" class="form-control form-control-sm py-2 mt-2 shadow-sm" placeholder="Ketik nama/ID..." style="display: none; border-radius: 6px; border-color: #14a2ba;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label-custom">Filter Departemen</label>
                            <select name="departemen" id="filterDepartemen" class="form-select form-select-sm py-2" style="border-radius: 6px; border-color: #cbd5e1; color: #475569;">
                                <option value="semua" selected>Semua Departemen</option>
                                <option value="PT CHANDRA WIJAYA UTAMA">PT CHANDRA WIJAYA UTAMA</option>
                                <option value="PT GADA ANEKA SOLUSINDO">PT GADA ANEKA SOLUSINDO</option>
                                <option value="PLN IPS UBP JERANJANG">PLN IPS UBP JERANJANG</option>
                                <option value="PT KOPJAS">PT KOPJAS</option>
                                <option value="DEPARTEMEN OPERASI">DEPARTEMEN OPERASI</option>
                                <option value="DEPARTEMEN PEMELIHARAAN">DEPARTEMEN PEMELIHARAAN</option>
                            </select>
                        </div>
                        <button type="button" id="btnExportLaporan" class="btn text-white w-100 py-2 shadow-sm" style="background-color: #125d72; font-weight: bold; border-radius: 8px;">
                            <i class="fa-solid fa-download me-2"></i> Export Data
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card border-0 shadow-sm h-100 d-flex flex-column" style="border-radius: 12px; overflow: hidden; border: 1px solid #cbd5e1 !important;">
                
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-black mb-1" id="tableTitle" style="color: #1e293b;">Data Absensi April 2026</h5>
                        <p class="text-muted mb-0" style="font-size: 12px;">Menerapkan logika <b>First-In Last-Out</b>. Klik nama untuk melihat statistik individu.</p>
                    </div>
                    <span id="totalDataBadge" class="d-inline-flex align-items-center justify-content-center fw-bold" style="background-color: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 20px; padding: 6px 16px; color: #475569; font-size: 12px;">
                        Total: Memuat...
                    </span>
                </div>
                
                <div class="card-body p-0 d-flex flex-column" style="flex-grow: 1; overflow: hidden;">
                    <div class="table-responsive custom-scrollbar flex-grow-1" style="overflow-y: auto; height: 0;">
                        <table class="table align-middle mb-0 table-striped-custom" style="font-size: 13px; border-collapse: collapse;" id="dataTableLaporan">
                            <thead style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 5; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                <tr>
                                    <th class="ps-4 py-3" style="color: #6c757d; font-size: 11px; font-weight: bold; letter-spacing: 0.5px;">TANGGAL</th>
                                    <th class="py-3" style="color: #6c757d; font-size: 11px; font-weight: bold; letter-spacing: 0.5px;">PERSONNEL ID</th>
                                    <th class="py-3" style="color: #6c757d; font-size: 11px; font-weight: bold; letter-spacing: 0.5px;">NAMA KARYAWAN</th>
                                    <th class="py-3" style="color: #6c757d; font-size: 11px; font-weight: bold; letter-spacing: 0.5px;">DEPARTEMEN</th>
                                    <th class="py-3" style="color: #6c757d; font-size: 11px; font-weight: bold; letter-spacing: 0.5px;">JAM MASUK (AWAL)</th>
                                    <th class="py-3" style="color: #6c757d; font-size: 11px; font-weight: bold; letter-spacing: 0.5px;">JAM KELUAR (AKHIR)</th>
                                    <th class="py-3 pe-4" style="color: #6c757d; font-size: 11px; font-weight: bold; letter-spacing: 0.5px;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center border-top mt-auto">
                    <span id="dataCountText" class="text-muted" style="font-size: 13px;">Menyiapkan data...</span>
                    <div class="d-flex align-items-center gap-3">
                        <select id="rowLimitSelect" class="form-select form-select-sm" style="width: auto; cursor: pointer;">
                            <option value="10">10 rows per page</option>
                            <option value="25">25 rows per page</option>
                            <option value="50" selected>50 rows per page</option>
                            <option value="100">100 rows per page</option>
                        </select>
                        <div class="btn-group">
                            <button id="btnPrevPage" class="btn btn-sm btn-outline-secondary px-3" disabled style="border-radius: 6px 0 0 6px;">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <button id="btnNextPage" class="btn btn-sm btn-outline-secondary px-3" disabled style="border-radius: 0 6px 6px 0;">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg">
                
                <div class="modal-header-custom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="profile-img-placeholder me-3">
                            <i class="fa-solid fa-user-tie fa-2x text-secondary"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold" id="mName">Nama Lengkap</h4>
                            <p class="mb-0 text-white-50" id="mID">ID: -</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-4">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-chart-pie text-info fs-5"></i>
                            <h6 class="fw-bold mb-0 text-dark">STATISTIK INDIVIDU</h6>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="text-muted" style="font-size: 12px; font-weight: bold;">Filter:</span>
                            <select id="modalBulan" class="form-select form-select-sm fw-bold shadow-sm" style="width: auto; border-color: #cbd5e1; color: #0c4a52; cursor:pointer;">
                                <option value="semua">Sepanjang Tahun (YTD)</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04" selected>April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                            <select id="modalTahun" class="form-select form-select-sm fw-bold shadow-sm" style="width: auto; border-color: #cbd5e1; color: #0c4a52; cursor:pointer;">
                                <option value="2025">2025</option>
                                <option value="2026" selected>2026</option>
                            </select>
                            <button id="btnModalExport" class="btn btn-sm text-white fw-bold shadow-sm ms-2" style="background-color: #14a2ba; border-radius: 6px;">
                                <i class="fa-solid fa-file-excel me-1"></i> Unduh Data
                            </button>
                        </div>
                    </div>

                    <div class="row g-4 align-items-center">
                        <div class="col-md-5 border-end text-center">
                            <div style="height: 200px; margin-bottom: 10px;"><canvas id="mChart"></canvas></div>
                            <span class="badge bg-light text-dark border" id="sKerja">Hari Kerja Efektif: 0 Hari</span>
                        </div>
                        <div class="col-md-7">
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="stat-box p-2">
                                        <small class="d-block text-muted fw-bold" style="font-size:10px;">Hadir (Tepat)</small>
                                        <b class="fs-5 text-dark" id="sHadir">0</b>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box p-2">
                                        <small class="d-block text-muted fw-bold" style="font-size:10px;">Terlambat</small>
                                        <b class="fs-5 text-warning" id="sTelat">0</b>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box p-2">
                                        <small class="d-block text-muted fw-bold" style="font-size:10px;">Tidak Hadir</small>
                                        <b class="fs-5 text-danger" id="sAlpha">0</b>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box p-2">
                                        <small class="d-block text-muted fw-bold" style="font-size:10px;">On-Time Rate</small>
                                        <b class="fs-5 text-success" id="sPersen">0%</b>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box p-2">
                                        <small class="d-block text-muted fw-bold" style="font-size:10px;">Libur Nas/Wknd</small>
                                        <b class="fs-5 text-secondary" id="sLibur">0</b>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box p-2">
                                        <small class="d-block text-muted fw-bold" style="font-size:10px;">Departemen</small>
                                        <span class="d-block mt-1 fw-bold truncate-text" style="font-size: 9px; color: #125d72;" id="mDept" title="-">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 pt-4 border-top">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-muted" style="font-size:11px;">
                                    <i class="fa-regular fa-calendar-days me-1"></i> KALENDER KONTRIBUSI (HEATMAP)
                                </h6>
                            </div>
                            
                            <div class="heatmap-wrapper custom-scrollbar">
                                <div class="heatmap-labels">
                                    <div style="visibility: hidden;">Min</div>
                                    <div>Sen</div>
                                    <div style="visibility: hidden;">Sel</div>
                                    <div>Rab</div>
                                    <div style="visibility: hidden;">Kam</div>
                                    <div>Jum</div>
                                    <div style="visibility: hidden;">Sab</div>
                                </div>
                                <div id="heatmapContentWrapper" class="heatmap-content">
                                    <div id="heatmapMonthLabels" class="heatmap-header">
                                        </div>
                                    <div id="heatmapContainer" class="heatmap-container">
                                        </div>
                                </div>
                            </div>
                            
                            <div class="heatmap-legend">
                                <div><span class="legend-box bg-hadir"></span> Hadir Tepat Waktu</div>
                                <div><span class="legend-box bg-telat"></span> Terlambat</div>
                                <div><span class="legend-box bg-alpha"></span> Tidak Hadir</div>
                                <div><span class="legend-box bg-libur"></span> Libur Nasional</div>
                                <div><span class="legend-box bg-weekend"></span> Akhir Pekan</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // =========================================================================
            // 1. SISTEM INIT & GENERATOR DATA LOGIS
            // =========================================================================
            const tableBody = document.getElementById('tableBody');
            const dataCountText = document.getElementById('dataCountText');
            const totalDataBadge = document.getElementById('totalDataBadge');
            const btnPrevPage = document.getElementById('btnPrevPage');
            const btnNextPage = document.getElementById('btnNextPage');
            const rowLimitSelect = document.getElementById('rowLimitSelect');
            
            // Daftar Pegawai Fiktif
            const employeesList = [
                { id: "199001", name: "Budi Santoso", dept: "PT CHANDRA WIJAYA UTAMA" },
                { id: "199002", name: "Siti Aminah", dept: "PT KOPJAS" },
                { id: "199003", name: "Agus Pratama", dept: "DEPARTEMEN OPERASI" },
                { id: "199004", name: "Rina Wijaya", dept: "PLN IPS UBP JERANJANG" },
                { id: "199005", name: "Andi Saputra", dept: "PT GADA ANEKA SOLUSINDO" },
                { id: "199006", name: "Dewi Lestari", dept: "DEPARTEMEN PEMELIHARAAN" },
                { id: "199007", name: "Eko Nugroho", dept: "PT CHANDRA WIJAYA UTAMA" },
                { id: "199008", name: "Rudi Hartono", dept: "DEPARTEMEN OPERASI" },
            ];
            
            let allData = [];
            let filteredData = [];
            let liburNasional = [];
            let currentPage = 1;
            let rowsPerPage = 50;

            const namaHari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const listBulanIndo = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const listBulanSingkat = ["", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];

            // Fetch API Hari Libur Nasional
            async function initSystem() {
                try {
                    const response = await fetch(`https://api-harilibur.vercel.app/api?year=2026`);
                    if (!response.ok) throw new Error("API Error");
                    const data = await response.json();
                    
                    const holidays = data.filter(hari => hari.is_national_holiday);
                    liburNasional = holidays.map(hari => hari.holiday_date || hari.tanggal);
                } catch (error) {
                    liburNasional = ["2026-01-01", "2026-02-17", "2026-03-20", "2026-04-10", "2026-04-11", "2026-05-01"];
                }
                
                generateLogicalData();
                applyFilters();
            }

            function generateLogicalData() {
                allData = [];
                for (let m = 1; m <= 4; m++) {
                    let daysInMonth = new Date(2026, m, 0).getDate();
                    for (let d = 1; d <= daysInMonth; d++) {
                        let dateString = `2026-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                        let dateObj = new Date(2026, m - 1, d);
                        
                        let isWeekend = (dateObj.getDay() === 0 || dateObj.getDay() === 6);
                        let isHoliday = liburNasional.includes(dateString);

                        if (isWeekend || isHoliday) continue; 

                        employeesList.forEach(emp => {
                            if (Math.random() < 0.90) { 
                                let isLate = Math.random() > 0.85; 
                                let hourIn = isLate ? '08' : '07';
                                let minIn = isLate ? String(Math.floor(Math.random() * 45) + 1).padStart(2, '0') : String(Math.floor(Math.random() * 45) + 10).padStart(2, '0');
                                let hourOut = Math.random() > 0.4 ? '16' : '17';
                                let minOut = String(Math.floor(Math.random() * 59)).padStart(2, '0');

                                allData.push({
                                    date: dateString, id: emp.id, name: emp.name, dept: emp.dept,
                                    inTime: `${hourIn}:${minIn}:00`, outTime: `${hourOut}:${minOut}:00`,
                                    status: isLate ? 'TERLAMBAT' : 'HADIR'
                                });
                            }
                        });
                    }
                }
                allData.sort((a,b) => b.date.localeCompare(a.date));
            }

            // =========================================================================
            // 2. RENDER TABEL & PAGINATION
            // =========================================================================
            function renderTable() {
                const totalRecords = filteredData.length;
                const totalPages = Math.ceil(totalRecords / rowsPerPage);
                
                if (currentPage > totalPages) currentPage = totalPages || 1;

                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const paginated = filteredData.slice(start, end);

                let html = '';
                if (paginated.length === 0) {
                    html = `<tr><td colspan="7" class="text-center py-5 text-muted"><i class="fa-solid fa-folder-open mb-3" style="font-size: 40px; color: #cbd5e1;"></i><br><b style="font-size: 15px;">Data tidak ditemukan</b></td></tr>`;
                } else {
                    paginated.forEach(row => {
                        let badgeBg = row.status === 'TERLAMBAT' ? '#fffbeb' : '#dcfce7';
                        let badgeText = row.status === 'TERLAMBAT' ? '#d97706' : '#125d72';
                        let inColor = row.status === 'TERLAMBAT' ? '#d97706' : '#6c757d';
                        let inWeight = row.status === 'TERLAMBAT' ? 'bold' : 'normal';

                        html += `
                            <tr class="data-row">
                                <td class="ps-4 val-date" style="color: #0c4a52; font-weight: 500;">${row.date}</td>
                                <td class="text-muted val-id">${row.id}</td>
                                <td><span class="clickable-name val-name" onclick="showProfile('${row.name}', '${row.id}', '${row.dept}')">${row.name}</span></td>
                                <td class="text-muted val-dept" style="font-size: 12px;">${row.dept}</td>
                                <td class="val-in" style="color: ${inColor}; font-weight: ${inWeight};">${row.inTime}</td>
                                <td class="text-primary fw-bold val-out">${row.outTime}</td>
                                <td class="pe-4 val-status"><span class="badge" style="background-color: ${badgeBg}; color: ${badgeText}; padding:6px 12px; border-radius:4px; font-weight:600;">${row.status}</span></td>
                            </tr>
                        `;
                    });
                }
                
                tableBody.innerHTML = html;
                totalDataBadge.innerText = `Total: ${totalRecords} Data`;

                if (totalRecords > 0) {
                    dataCountText.innerHTML = `Menampilkan <strong class="text-dark">${start + 1}-${Math.min(end, totalRecords)}</strong> dari <strong class="text-dark">${totalRecords}</strong> data`;
                } else {
                    dataCountText.innerHTML = `Menampilkan <strong class="text-dark">0-0</strong> dari <strong class="text-dark">0</strong> data`;
                }

                btnPrevPage.disabled = currentPage === 1;
                btnNextPage.disabled = currentPage === totalPages || totalPages === 0;
            }

            function applyFilters() {
                const bulan = document.getElementById('filterBulan').value;
                const tahun = document.getElementById('filterTahun').value;
                const status = document.getElementById('filterStatus').value;
                const dept = document.getElementById('filterDepartemen').value;
                const search = document.getElementById('karyawanCustom').value.toLowerCase();
                const searchMode = document.getElementById('karyawanSelect').value;

                const bulanText = document.getElementById('filterBulan').options[document.getElementById('filterBulan').selectedIndex].text;
                document.getElementById('tableTitle').innerText = bulan === 'semua' ? `Data Absensi Tahun ${tahun}` : `Data Absensi ${bulanText} ${tahun}`;

                filteredData = allData.filter(d => {
                    const matchBulan = (bulan === 'semua') || (bulan === d.date.substring(5, 7));
                    const matchTahun = (tahun === d.date.substring(0, 4));
                    const matchStatus = (status === 'semua') || (d.status.toLowerCase() === status);
                    const matchDept = (dept === 'semua') || (d.dept === dept);
                    let matchSearch = true;
                    if (searchMode === 'lainnya' && search !== '') matchSearch = d.name.toLowerCase().includes(search) || d.id.includes(search);

                    return matchBulan && matchTahun && matchStatus && matchDept && matchSearch;
                });

                currentPage = 1;
                renderTable();
            }

            document.getElementById('filterBulan').addEventListener('change', applyFilters);
            document.getElementById('filterTahun').addEventListener('change', applyFilters);
            document.getElementById('filterStatus').addEventListener('change', applyFilters);
            document.getElementById('filterDepartemen').addEventListener('change', applyFilters);
            document.getElementById('karyawanCustom').addEventListener('input', applyFilters);
            
            document.getElementById('karyawanSelect').addEventListener('change', function() {
                if (this.value === 'lainnya') {
                    document.getElementById('karyawanCustom').style.display = 'block'; document.getElementById('karyawanCustom').focus();
                } else {
                    document.getElementById('karyawanCustom').style.display = 'none'; document.getElementById('karyawanCustom').value = ''; applyFilters(); 
                }
            });

            document.getElementById('rowLimitSelect').addEventListener('change', function() { rowsPerPage = parseInt(this.value); currentPage = 1; renderTable(); });
            document.getElementById('btnPrevPage').addEventListener('click', function() { if (currentPage > 1) { currentPage--; renderTable(); } });
            document.getElementById('btnNextPage').addEventListener('click', function() { currentPage++; renderTable(); });

            document.getElementById('btnExportLaporan').addEventListener('click', function(e) {
                e.preventDefault();
                if(filteredData.length === 0) return Swal.fire({ icon: 'error', title: 'Data Kosong' });

                let exportData = filteredData.map(d => ({
                    "Tanggal": d.date, "Personnel ID": d.id, "Nama Karyawan": d.name, "Departemen": d.dept,
                    "Jam Masuk (Pertama)": d.inTime, "Jam Keluar (Terakhir)": d.outTime, "Status": d.status
                }));
                const ws = XLSX.utils.json_to_sheet(exportData);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Laporan_Absensi");
                XLSX.writeFile(wb, 'Laporan_Kehadiran_FILO.xlsx');
            });

            // =========================================================================
            // 3. LOGIKA MODAL PROFILE & SMART CALENDAR ENGINE (SINKRON DATA ASLI)
            // =========================================================================
            let profileChart = null;
            let currentEmployee = { name: '', id: '', dept: '', stats: null, filterName: '' };

            function calculateEmployeeStats(monthStr, year, empId) {
                const CURRENT_SIMULATION_MONTH = 4; 
                
                let filterStart = monthStr === 'semua' ? 1 : parseInt(monthStr, 10);
                let filterEnd = monthStr === 'semua' ? CURRENT_SIMULATION_MONTH : parseInt(monthStr, 10); 

                let empData = allData.filter(d => d.id === String(empId));

                let monthlyBreakdown = [];
                let dailyLog = []; 
                let grandTotal = { totalDays: 0, workingDays: 0, weekendDays: 0, liburNasionalDays: 0, hadir: 0, telat: 0, alpha: 0 };

                // LOGIKA GENERATE KALENDER GITHUB (SESUAI FILTER BULAN)
                let renderStartMonth = filterStart;
                let renderEndMonth = monthStr === 'semua' ? 12 : filterEnd;

                for (let m = renderStartMonth; m <= renderEndMonth; m++) {
                    let m_totalDays = new Date(year, m, 0).getDate();
                    let m_weekendDays = 0, m_liburNasionalDays = 0, m_workingDays = 0;
                    let m_hadir = 0, m_telat = 0, m_alpha = 0; 

                    for (let d = 1; d <= m_totalDays; d++) {
                        let currentDate = new Date(year, m - 1, d);
                        let dayOfWeek = currentDate.getDay(); 
                        let dateString = `${year}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;

                        let isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                        let isHoliday = liburNasional.includes(dateString);
                        
                        let dayStatus = ''; 
                        let actualInTime = '-';
                        let actualOutTime = '-';

                        if (m > CURRENT_SIMULATION_MONTH) {
                            dayStatus = 'empty-future';
                        } else {
                            if (isWeekend) {
                                m_weekendDays++; dayStatus = 'weekend';
                            } else if (isHoliday) {
                                m_liburNasionalDays++; dayStatus = 'libur';
                            } else {
                                m_workingDays++;
                                let record = empData.find(r => r.date === dateString);
                                if (record) {
                                    actualInTime = record.inTime;
                                    actualOutTime = record.outTime;
                                    if (record.status === 'TERLAMBAT') { m_telat++; dayStatus = 'telat'; }
                                    else { m_hadir++; dayStatus = 'hadir'; }
                                } else {
                                    m_alpha++; dayStatus = 'alpha'; 
                                }
                            }
                        }

                        dailyLog.push({ date: dateString, dayName: namaHari[dayOfWeek], dayOfWeek: dayOfWeek, status: dayStatus, month: m, inTime: actualInTime, outTime: actualOutTime, isHidden: false });
                    }

                    if (m >= filterStart && m <= filterEnd) {
                        let m_persen = m_workingDays > 0 ? Math.round((m_hadir / m_workingDays) * 100) : 0;
                        monthlyBreakdown.push({
                            monthName: listBulanIndo[m], year: year, totalDays: m_totalDays, workingDays: m_workingDays,
                            weekendDays: m_weekendDays, liburNasionalDays: m_liburNasionalDays,
                            hadir: m_hadir, telat: m_telat, alpha: m_alpha, persen: m_persen
                        });

                        grandTotal.totalDays += m_totalDays; grandTotal.workingDays += m_workingDays; grandTotal.weekendDays += m_weekendDays;
                        grandTotal.liburNasionalDays += m_liburNasionalDays; grandTotal.hadir += m_hadir; grandTotal.telat += m_telat; grandTotal.alpha += m_alpha;
                    }
                }

                grandTotal.totalLiburOff = grandTotal.weekendDays + grandTotal.liburNasionalDays;
                grandTotal.persen = grandTotal.workingDays > 0 ? Math.round((grandTotal.hadir / grandTotal.workingDays) * 100) : 0;

                return { totals: grandTotal, breakdown: monthlyBreakdown, dailyLog: dailyLog };
            }

            function renderHeatmap(dailyLog, isFullYear) {
                const container = document.getElementById('heatmapContainer');
                const contentWrapper = document.getElementById('heatmapContentWrapper');
                const monthLabelsContainer = document.getElementById('heatmapMonthLabels');
                
                container.innerHTML = '';
                monthLabelsContainer.innerHTML = '';
                
                if(dailyLog.length === 0) return;

                if(isFullYear) {
                    contentWrapper.classList.add('full-width');
                } else {
                    contentWrapper.classList.remove('full-width');
                }

                const firstDayIndex = dailyLog[0].dayOfWeek; 
                for(let i=0; i<firstDayIndex; i++) {
                    let emptyBox = document.createElement('div');
                    emptyBox.className = 'heatmap-box hidden';
                    container.appendChild(emptyBox);
                }

                let currentMonthTracker = -1;
                let monthLabelsHtml = '';
                const totalColsRendered = Math.ceil((dailyLog.length + firstDayIndex) / 7);

                dailyLog.forEach((day, index) => {
                    let colIndex = Math.floor((index + firstDayIndex) / 7);
                    
                    if (day.month !== currentMonthTracker) {
                        if (currentMonthTracker !== -1 || parseInt(day.date.substring(8,10)) <= 7) { 
                            let leftPercentage = (colIndex / totalColsRendered) * 100;
                            monthLabelsHtml += `<span class="heatmap-month-label" style="left:${leftPercentage}%;">${listBulanSingkat[day.month]}</span>`;
                        }
                        currentMonthTracker = day.month;
                    }

                    let bgClass = '', textStatus = '';
                    switch(day.status) {
                        case 'hadir': bgClass = 'bg-hadir'; textStatus = 'Hadir Tepat Waktu'; break;
                        case 'telat': bgClass = 'bg-telat'; textStatus = 'Terlambat'; break;
                        case 'alpha': bgClass = 'bg-alpha'; textStatus = 'Tidak Hadir (Alpha)'; break;
                        case 'libur': bgClass = 'bg-libur'; textStatus = 'Libur Nasional'; break;
                        case 'weekend': bgClass = 'bg-weekend'; textStatus = 'Akhir Pekan'; break;
                        case 'empty-future': bgClass = 'bg-empty-future'; textStatus = 'Belum Ada Data'; break;
                    }

                    let box = document.createElement('div');
                    box.className = `heatmap-box ${bgClass}`;
                    box.title = `${day.dayName}, ${day.date} \nStatus: ${textStatus}`;
                    container.appendChild(box);
                });

                monthLabelsContainer.innerHTML = monthLabelsHtml;
            }

            function updateModalUI() {
                const monthVal = document.getElementById('modalBulan').value;
                const yearVal = parseInt(document.getElementById('modalTahun').value);
                const monthText = document.getElementById('modalBulan').options[document.getElementById('modalBulan').selectedIndex].text;
                
                const statsObj = calculateEmployeeStats(monthVal, yearVal, currentEmployee.id);
                
                currentEmployee.stats = statsObj; 
                currentEmployee.filterName = monthVal === 'semua' ? `Tahun ${yearVal} (YTD)` : `${monthText} ${yearVal}`;

                const t = statsObj.totals;
                document.getElementById('sKerja').innerText = `Hari Kerja Efektif: ${t.workingDays} Hari`;
                document.getElementById('sHadir').innerText = t.hadir;
                document.getElementById('sTelat').innerText = t.telat;
                document.getElementById('sAlpha').innerText = t.alpha; 
                document.getElementById('sLibur').innerText = t.totalLiburOff; 
                document.getElementById('sPersen').innerText = t.persen + "%";
                
                const elDept = document.getElementById('mDept');
                elDept.innerText = currentEmployee.dept;
                elDept.title = currentEmployee.dept;

                const ctx = document.getElementById('mChart').getContext('2d');
                if (profileChart) profileChart.destroy();
                
                profileChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir Tepat', 'Terlambat', 'Tidak Hadir'],
                        datasets: [{ data: [t.hadir, t.telat, t.alpha], backgroundColor: ['#0c5a66', '#fde047', '#e13b48'], borderWidth: 0 }]
                    },
                    options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } }, cutout: '70%', animation: { duration: 800, easing: 'easeOutQuart' } }
                });

                renderHeatmap(statsObj.dailyLog, monthVal === 'semua');
            }

            document.getElementById('modalBulan').addEventListener('change', updateModalUI);
            document.getElementById('modalTahun').addEventListener('change', async function(e) {
                document.getElementById('sLibur').innerText = "Loading..."; 
                await fetchLiburNasionalAPI(e.target.value);
                updateModalUI();
            });

            window.showProfile = function(name, id, dept) {
                currentEmployee.name = name; currentEmployee.id = id; currentEmployee.dept = dept;
                document.getElementById('mName').innerText = name; document.getElementById('mID').innerText = "Personnel ID: " + id;
                
                document.getElementById('modalBulan').value = document.getElementById('filterBulan').value;
                document.getElementById('modalTahun').value = document.getElementById('filterTahun').value;

                updateModalUI();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('profileModal')).show();
            }

            // =========================================================================
            // 4. EXPORT EXCEL KHUSUS PROFIL INDIVIDU (LOG HARIAN JIKA FILTER BULAN)
            // =========================================================================
            // =========================================================================
            // 4. EXPORT EXCEL KHUSUS PROFIL INDIVIDU (LOG HARIAN JIKA FILTER BULAN)
            // =========================================================================
            document.getElementById('btnModalExport').addEventListener('click', function() {
                let exportData = [];
                const monthVal = document.getElementById('modalBulan').value;
                
                // [BARU] Mengambil nama bulan dari dropdown filter
                const monthText = document.getElementById('modalBulan').options[document.getElementById('modalBulan').selectedIndex].text;
                // [BARU] Membuat logika teks dinamis
                let pesanFilter = monthVal === 'semua' ? 'sepanjang tahun' : `bulan ${monthText}`;

                if (monthVal === 'semua') {
                    currentEmployee.stats.breakdown.forEach(m => {
                        exportData.push({
                            "Nama Karyawan": currentEmployee.name, "Personnel ID": currentEmployee.id, "Departemen": currentEmployee.dept, "Periode Laporan": `${m.monthName} ${m.year}`,
                            "Total Hari": m.totalDays, "Libur Sbt/Mgg": m.weekendDays, "Libur Nasional": m.liburNasionalDays, "Hari Kerja Efektif": m.workingDays,
                            "Hadir": m.hadir, "Terlambat": m.telat, "Alpha": m.alpha, "On-Time Rate": m.persen + "%"
                        });
                    });
                    exportData.push({});
                    const t = currentEmployee.stats.totals;
                    exportData.push({
                        "Nama Karyawan": "TOTAL KESELURUHAN (YTD)", "Personnel ID": "-", "Departemen": "-", "Periode Laporan": currentEmployee.filterName,
                        "Total Hari": t.totalDays, "Libur Sbt/Mgg": t.weekendDays, "Libur Nasional": t.liburNasionalDays, "Hari Kerja Efektif": t.workingDays,
                        "Hadir": t.hadir, "Terlambat": t.telat, "Alpha": t.alpha, "On-Time Rate": t.persen + "%"
                    });
                } else {
                    let targetMonth = parseInt(monthVal, 10);
                    let daysInMonthLog = currentEmployee.stats.dailyLog.filter(d => d.month === targetMonth && d.status !== 'empty-future');

                    if(daysInMonthLog.length === 0) return Swal.fire({ icon: 'warning', title: 'Belum Ada Data', text: 'Bulan ini belum dilalui.' });

                    daysInMonthLog.forEach(day => {
                        let textStatus = '';
                        switch(day.status) {
                            case 'hadir': textStatus = 'Hadir'; break;
                            case 'telat': textStatus = 'Terlambat'; break;
                            case 'alpha': textStatus = 'Tidak Hadir (Alpha)'; break;
                            case 'libur': textStatus = 'Libur Nasional'; break;
                            case 'weekend': textStatus = 'Akhir Pekan'; break;
                        }

                        exportData.push({
                            "Nama Karyawan": currentEmployee.name, "Personnel ID": currentEmployee.id, "Departemen": currentEmployee.dept,
                            "Tanggal": day.date, "Hari": day.dayName, "Jam Masuk (Awal)": day.inTime || '-', "Jam Keluar (Akhir)": day.outTime || '-', "Status Kehadiran": textStatus
                        });
                    });

                    exportData.push({});
                    const m = currentEmployee.stats.breakdown[0]; 
                    if(m) {
                        exportData.push({
                            "Nama Karyawan": "TOTAL REKAPITULASI BULAN INI", "Personnel ID": "", "Departemen": "", "Tanggal": "",
                            "Hari": `Kerja Efektif: ${m.workingDays} Hari`, "Jam Masuk (Awal)": `Hadir: ${m.hadir}`, "Jam Keluar (Akhir)": `Terlambat: ${m.telat}`, "Status Kehadiran": `Alpha: ${m.alpha}`
                        });
                    }
                }

                const worksheet = XLSX.utils.json_to_sheet(exportData);
                worksheet['!cols'] = [ { wpx: 160 }, { wpx: 110 }, { wpx: 160 }, { wpx: 100 }, { wpx: 80 }, { wpx: 120 }, { wpx: 120 }, { wpx: 130 } ];
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Log_Individu");
                
                let safeFileName = currentEmployee.name.replace(/\s+/g, '_');
                let periodeNama = monthVal === 'semua' ? 'YTD' : monthVal;
                XLSX.writeFile(workbook, `Log_Absen_${safeFileName}_${periodeNama}.xlsx`);
                
                Swal.fire({ icon: 'success', title: 'Export Berhasil!', text: `Data log ${pesanFilter} diunduh.`, timer: 3000, showConfirmButton: false });
            });

            initSystem();
        });
    </script>
@endsection