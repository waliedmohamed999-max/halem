<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --bg:#edf4ff;
            --bg2:#f9fbff;
            --panel:rgba(255,255,255,.84);
            --panel-solid:#ffffff;
            --line:rgba(26,74,126,.11);
            --text:#15283f;
            --muted:#6b7f96;
            --accent:#2f6fed;
            --accent-strong:#1f57c8;
            --accent-soft:#e8f1ff;
            --sidebar:#132c45;
            --sidebar-2:#0d1f33;
            --sidebar-line:rgba(255,255,255,.08);
            --sidebar-muted:rgba(226,236,247,.63);
            --sidebar-text:#f6fbff;
            --danger-soft:#fff2ee;
            --shadow:0 22px 60px rgba(27,61,104,.12);
            --radius-xl:30px;
            --radius-lg:22px;
        }

        *{box-sizing:border-box}
        body{
            margin:0;
            min-height:100vh;
            background:
                radial-gradient(circle at top right, rgba(135,180,255,.28), transparent 24rem),
                radial-gradient(circle at bottom left, rgba(198,222,255,.42), transparent 28rem),
                linear-gradient(135deg, var(--bg) 0%, var(--bg2) 60%, #f2f7ff 100%);
            color:var(--text);
            font-family:{{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Plus Jakarta Sans'" }},sans-serif;
        }

        a{text-decoration:none}
        .admin-shell{
            display:grid;
            grid-template-columns:290px minmax(0,1fr);
            gap:1.2rem;
            min-height:100vh;
            padding:1rem;
        }

        .sidebar{position:sticky;top:1rem;align-self:start}
        .sidebar-panel{
            display:flex;
            flex-direction:column;
            gap:1rem;
            padding:1rem;
            border:1px solid var(--sidebar-line);
            border-radius:32px;
            background:linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02)), linear-gradient(180deg, var(--sidebar) 0%, var(--sidebar-2) 100%);
            color:var(--sidebar-text);
            box-shadow:0 28px 60px rgba(7,20,36,.22);
        }

        .sidebar-head{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:.9rem;
            padding-bottom:1rem;
            border-bottom:1px solid var(--sidebar-line);
        }

        .brand-copy small{
            display:block;
            margin-bottom:.35rem;
            color:#9dc2ff;
            letter-spacing:.1em;
            text-transform:uppercase;
            font-weight:700;
            font-size:.7rem;
        }

        .brand-copy strong{display:block;font-size:1.08rem;line-height:1.2}
        .brand-copy span{display:block;margin-top:.28rem;color:var(--sidebar-muted);font-size:.78rem}

        .brand-mark,.nav-icon,.profile-avatar{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }

        .brand-mark{
            width:3.1rem;
            height:3.1rem;
            border-radius:1.1rem;
            background:rgba(255,255,255,.08);
            color:#bfdbff;
        }

        .brand-mark svg,.nav-icon svg{width:1.15rem;height:1.15rem}

        .workspace-card{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:.8rem;
            padding:.95rem 1rem;
            border:1px solid rgba(255,255,255,.1);
            border-radius:20px;
            background:rgba(255,255,255,.06);
        }

        .workspace-card strong{display:block;font-size:.9rem;line-height:1.35}
        .workspace-card span{display:block;margin-top:.2rem;color:var(--sidebar-muted);font-size:.76rem;line-height:1.6}
        .workspace-badge{
            display:inline-flex;
            align-items:center;
            padding:.36rem .7rem;
            border-radius:999px;
            background:rgba(255,255,255,.1);
            color:#d7e8ff;
            font-size:.72rem;
            font-weight:800;
            white-space:nowrap;
        }

        .sidebar-groups{display:flex;flex-direction:column;gap:.8rem}
        .sidebar-group{
            border:1px solid rgba(255,255,255,.07);
            border-radius:20px;
            background:rgba(255,255,255,.04);
            overflow:hidden;
        }

        .sidebar-group.is-finance{
            border-color:rgba(118,177,255,.24);
            background:
                linear-gradient(180deg, rgba(53,100,173,.26), rgba(17,37,60,.34)),
                linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.03));
            box-shadow:0 18px 36px rgba(5,15,29,.18), inset 0 0 0 1px rgba(170,208,255,.05);
        }

        .sidebar-group.is-finance .group-toggle{
            background:linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,0));
        }

        .sidebar-group.is-finance .group-toggle:hover{
            background:linear-gradient(180deg, rgba(135,180,255,.1), rgba(255,255,255,.02));
        }

        .sidebar-group.is-finance .group-bullet{
            background:#9fd0ff;
            box-shadow:0 0 0 7px rgba(101,177,255,.16);
        }

        .sidebar-group.is-finance .group-copy strong{
            color:#f8fbff;
        }

        .sidebar-group.is-finance .group-copy span,
        .sidebar-group.is-finance .group-meta{
            color:rgba(225,237,255,.78);
        }

        .finance-badge{
            display:inline-flex;
            align-items:center;
            padding:.3rem .62rem;
            border:1px solid rgba(159,208,255,.22);
            border-radius:999px;
            background:rgba(207,229,255,.12);
            color:#dff0ff;
            font-size:.66rem;
            font-weight:800;
            letter-spacing:.08em;
            text-transform:uppercase;
        }

        .sidebar-group.is-finance .nav-list{
            gap:.5rem;
        }

        .sidebar-group.is-finance .nav-link-item{
            border-color:rgba(159,208,255,.08);
            background:rgba(255,255,255,.035);
        }

        .sidebar-group.is-finance .nav-link-item:hover{
            border-color:rgba(159,208,255,.18);
            background:rgba(130,182,255,.08);
        }

        .sidebar-group.is-finance .nav-link-item.active{
            border-color:rgba(194,225,255,.32);
            background:linear-gradient(135deg, rgba(103,163,255,.36), rgba(255,255,255,.12));
            box-shadow:0 10px 24px rgba(10,28,50,.18), inset 0 0 0 1px rgba(255,255,255,.06);
        }

        .sidebar-group.is-finance .nav-icon{
            background:rgba(173,214,255,.12);
            color:#d8ecff;
        }

        .group-toggle{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:.9rem;
            width:100%;
            padding:.85rem .95rem;
            border:0;
            background:transparent;
            color:var(--sidebar-text);
            text-align:inherit;
        }

        .group-toggle:hover{background:rgba(255,255,255,.04)}
        .group-main{display:flex;align-items:center;gap:.75rem;min-width:0}
        .group-bullet{
            width:.55rem;
            height:.55rem;
            border-radius:999px;
            background:#7fb0ff;
            box-shadow:0 0 0 6px rgba(127,176,255,.12);
        }

        .group-copy strong{display:block;font-size:.84rem;line-height:1.2}
        .group-copy span{display:block;margin-top:.12rem;color:var(--sidebar-muted);font-size:.72rem}
        .group-meta{display:inline-flex;align-items:center;gap:.55rem;color:var(--sidebar-muted);font-size:.75rem;font-weight:700}
        .group-chevron{transition:transform .2s ease;font-size:.95rem;line-height:1}
        .group-toggle[aria-expanded="true"] .group-chevron{transform:rotate(180deg)}
        .group-body{padding:0 .7rem .75rem}
        .nav-list{display:flex;flex-direction:column;gap:.4rem}

        .nav-link-item{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:.8rem;
            padding:.72rem .78rem;
            border:1px solid transparent;
            border-radius:16px;
            color:#eef5ff;
            transition:.2s ease;
        }

        .nav-link-item:hover{
            border-color:rgba(255,255,255,.08);
            background:rgba(255,255,255,.05);
            color:#fff;
        }

        .nav-link-item.active{
            border-color:rgba(255,255,255,.11);
            background:linear-gradient(135deg, rgba(65,119,217,.38), rgba(255,255,255,.08));
            box-shadow:inset 0 0 0 1px rgba(255,255,255,.03);
            color:#fff;
        }

        .nav-main{display:flex;align-items:center;gap:.75rem;min-width:0}
        .nav-icon{
            width:2.35rem;
            height:2.35rem;
            border-radius:.95rem;
            background:rgba(255,255,255,.07);
            color:#bbd7ff;
        }

        .nav-link-item.active .nav-icon{background:rgba(255,255,255,.13);color:#fff}
        .nav-copy{min-width:0}
        .nav-label{display:block;font-size:.86rem;font-weight:800;line-height:1.2}
        .nav-desc{
            display:block;
            margin-top:.12rem;
            color:var(--sidebar-muted);
            font-size:.71rem;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }

        .nav-link-item.active .nav-desc{color:rgba(255,255,255,.76)}
        .nav-arrow{color:rgba(255,255,255,.42);font-size:1rem;line-height:1}
        .sidebar-footer{display:flex;flex-direction:column;gap:.65rem;padding-top:.25rem}
        .sidebar-footer .btn{border-radius:16px;font-weight:800;padding:.82rem 1rem}

        .main{min-width:0}
        .topbar{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            padding:1rem 1.1rem;
            margin-bottom:1.15rem;
            border:1px solid rgba(255,255,255,.55);
            border-radius:32px;
            background:rgba(255,255,255,.68);
            box-shadow:var(--shadow);
            backdrop-filter:blur(16px);
        }

        .menu-btn{
            display:none;
            width:2.9rem;
            height:2.9rem;
            border:0;
            border-radius:1rem;
            background:#eef4ff;
            color:var(--text);
        }

        .eyebrow{
            display:inline-flex;
            align-items:center;
            padding:.4rem .74rem;
            margin-bottom:.6rem;
            border:1px solid rgba(47,111,237,.13);
            border-radius:999px;
            background:rgba(232,240,255,.95);
            color:var(--accent);
            font-size:.72rem;
            font-weight:800;
            letter-spacing:.08em;
            text-transform:uppercase;
        }

        .page-title{margin:0;font-size:clamp(1.5rem, 1.28rem + .7vw, 2rem);font-weight:800}
        .page-subtitle{margin:.24rem 0 0;color:var(--muted);font-size:.92rem}
        .top-actions{display:flex;align-items:center;gap:.75rem;flex-wrap:wrap}
        .locale{
            display:inline-flex;
            padding:.24rem;
            border:1px solid var(--line);
            border-radius:999px;
            background:rgba(232,240,255,.92);
        }

        .locale a{padding:.46rem .92rem;border-radius:999px;color:var(--muted);font-size:.82rem;font-weight:800}
        .locale a.active{background:var(--accent);color:#fff}

        .profile{
            display:flex;
            align-items:center;
            gap:.75rem;
            padding:.55rem .75rem;
            border:1px solid var(--line);
            border-radius:18px;
            background:rgba(255,255,255,.76);
        }

        .profile-avatar{
            width:2.55rem;
            height:2.55rem;
            border-radius:.95rem;
            background:linear-gradient(135deg, var(--accent), #2d8a74);
            color:#fff;
            font-size:.95rem;
            font-weight:800;
            text-transform:uppercase;
        }

        .profile strong{display:block;font-size:.88rem}
        .profile span{display:block;color:var(--muted);font-size:.76rem}

        .page-frame{
            padding:1.2rem;
            border:1px solid rgba(255,255,255,.55);
            border-radius:34px;
            background:rgba(255,255,255,.28);
            box-shadow:var(--shadow);
        }

        .content-card{
            padding:1.2rem;
            border:1px solid var(--line);
            border-radius:var(--radius-xl);
            background:var(--panel);
            backdrop-filter:blur(12px);
        }

        .content-card .card,.content-card .dash-box,.content-card .kpi-card{
            border:1px solid var(--line) !important;
            border-radius:var(--radius-lg) !important;
            background:var(--panel-solid) !important;
            box-shadow:none !important;
        }

        .content-card .card-body,.content-card .dash-box,.content-card .kpi-card{padding:1.1rem !important}
        .content-card .alert{border:0;border-radius:1rem}
        .content-card .alert-danger{background:var(--danger-soft);color:#aa4627}
        .content-card .table-responsive{
            border:1px solid var(--line);
            border-radius:1.1rem;
            background:var(--panel-solid);
        }

        .content-card .table{margin-bottom:0;--bs-table-bg:transparent}
        .content-card .table thead th{
            padding:1rem .95rem;
            border-bottom-color:var(--line);
            background:#eef4ff !important;
            color:var(--muted);
            font-size:.78rem;
            font-weight:800;
            text-transform:uppercase;
            letter-spacing:.08em;
        }

        .content-card .table tbody td{
            padding:1rem .95rem;
            border-bottom-color:rgba(29,78,137,.08);
            vertical-align:middle;
        }

        .content-card .form-control,.content-card .form-select{
            min-height:2.9rem;
            border-color:var(--line);
            border-radius:1rem;
            background:rgba(255,255,255,.95);
            box-shadow:none;
        }

        .content-card .form-control:focus,.content-card .form-select:focus{
            border-color:rgba(47,111,237,.38);
            box-shadow:0 0 0 .22rem rgba(47,111,237,.12);
        }

        .content-card .btn{border-radius:1rem;font-weight:700}
        .content-card .btn-sm{border-radius:.9rem;padding:.52rem .9rem}
        .content-card .btn-primary,.content-card .btn-success{border-color:var(--accent);background:var(--accent)}
        .content-card .btn-primary:hover,.content-card .btn-success:hover{border-color:var(--accent-strong);background:var(--accent-strong)}
        .content-card .badge{padding:.48rem .7rem;border-radius:999px;font-weight:700}
        .content-card .text-bg-light{background:var(--accent-soft) !important;color:var(--accent) !important}
        .content-card .text-muted,.content-card .small-muted,.content-card small,.content-card .small{color:var(--muted) !important}
        .content-card .pagination{gap:.4rem}
        .content-card .page-link{border:1px solid var(--line);border-radius:.9rem;color:var(--text);background:rgba(255,255,255,.76)}
        .content-card .page-item.active .page-link{border-color:var(--accent);background:var(--accent)}

        .admin-list-page{display:flex;flex-direction:column;gap:1rem}
        .admin-list-header{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:1rem;
            flex-wrap:wrap;
        }

        .admin-list-title{margin:0;font-size:1.2rem;font-weight:800;color:var(--text)}
        .admin-list-subtitle{margin:.28rem 0 0;color:var(--muted);font-size:.88rem}
        .admin-list-actions{display:flex;align-items:center;gap:.65rem;flex-wrap:wrap}
        .admin-list-card{
            border:1px solid var(--line);
            border-radius:24px;
            background:rgba(255,255,255,.74);
            box-shadow:0 16px 40px rgba(25,68,117,.08);
            overflow:hidden;
        }

        .admin-list-card .table{margin-bottom:0}
        .admin-list-card .table thead th{
            background:linear-gradient(180deg, #f4f8ff 0%, #edf4ff 100%) !important;
            color:#5d7591;
            font-size:.77rem;
            font-weight:800;
            letter-spacing:.06em;
        }

        .admin-list-card .table tbody tr:hover{background:rgba(47,111,237,.035)}
        .admin-table-actions{display:flex;align-items:center;gap:.45rem;flex-wrap:wrap}
        .admin-table-actions form{margin:0}
        .admin-stats-grid{
            display:grid;
            grid-template-columns:repeat(4,minmax(0,1fr));
            gap:.9rem;
        }
        .admin-stat-panel{
            padding:1rem 1.05rem;
            border:1px solid var(--line);
            border-radius:24px;
            background:linear-gradient(180deg,#ffffff 0%,#f8fbff 100%);
            box-shadow:0 16px 40px rgba(25,68,117,.08);
        }
        .admin-stat-panel small{
            display:block;
            margin-bottom:.42rem;
            color:var(--muted);
            font-size:.78rem;
            font-weight:800;
        }
        .admin-stat-panel strong{
            display:block;
            font-size:1.5rem;
            line-height:1.05;
            color:var(--text);
        }
        .admin-stat-panel span{
            display:block;
            margin-top:.28rem;
            color:#7b90a8;
            font-size:.76rem;
        }
        .admin-filter-card{
            padding:1rem 1.05rem;
            border:1px solid var(--line);
            border-radius:24px;
            background:rgba(255,255,255,.72);
            box-shadow:0 16px 40px rgba(25,68,117,.08);
        }
        .admin-filter-card .form-control,
        .admin-filter-card .form-select{
            min-height:2.7rem;
        }
        .finance-scope{
            --accent:#1d8f78;
            --accent-strong:#147261;
            --accent-soft:#e8fbf5;
            --line:rgba(20,114,97,.14);
            --shadow:0 24px 60px rgba(13,74,63,.12);
            --panel:rgba(248,255,252,.9);
            --panel-solid:#ffffff;
            --muted:#58706a;
        }
        .finance-scope .topbar{
            border-color:rgba(29,143,120,.18);
            background:linear-gradient(135deg, rgba(232,251,245,.92), rgba(255,255,255,.78)), rgba(255,255,255,.74);
            box-shadow:0 20px 44px rgba(14,82,69,.1);
        }
        .finance-scope .eyebrow{
            border-color:rgba(29,143,120,.16);
            background:rgba(232,251,245,.96);
            color:var(--accent);
        }
        .finance-scope .page-frame{
            border-color:rgba(29,143,120,.16);
            background:radial-gradient(circle at top left, rgba(175,236,221,.22), transparent 18rem), rgba(255,255,255,.3);
        }
        .finance-scope .content-card{
            border-color:rgba(29,143,120,.14);
            background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(247,255,251,.92));
        }
        .finance-scope .admin-list-title,
        .finance-scope .page-title{
            color:#123b35;
        }
        .finance-scope .admin-stat-panel,
        .finance-scope .admin-list-card,
        .finance-scope .admin-filter-card{
            border-color:rgba(29,143,120,.14);
            background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(245,255,251,.92));
            box-shadow:0 16px 34px rgba(14,82,69,.07);
        }
        .finance-scope .content-card .table thead th{
            background:#ebfaf5 !important;
            color:#52726b;
        }
        .finance-scope .content-card .btn-outline-dark,
        .finance-scope .topbar .btn-outline-dark{
            border-color:rgba(29,143,120,.18);
            color:#186a5a;
            background:rgba(232,251,245,.58);
        }
        .finance-scope .content-card .btn-outline-dark:hover,
        .finance-scope .topbar .btn-outline-dark:hover{
            border-color:rgba(20,114,97,.28);
            color:#fff;
            background:var(--accent);
        }
        .finance-scope .content-card .btn-outline-danger,
        .finance-scope .topbar .btn-outline-danger,
        .finance-scope .content-card .btn-outline-primary,
        .finance-scope .topbar .btn-outline-primary{
            border-color:rgba(29,143,120,.18);
            color:#186a5a;
        }
        .finance-scope .content-card .btn-outline-danger:hover,
        .finance-scope .topbar .btn-outline-danger:hover,
        .finance-scope .content-card .btn-outline-primary:hover,
        .finance-scope .topbar .btn-outline-primary:hover{
            border-color:var(--accent);
            background:var(--accent);
            color:#fff;
        }
        .finance-scope .admin-status-pill{
            background:#e8fbf5;
            color:#15715f;
        }
        .finance-scope .content-card .page-item.active .page-link,
        .finance-scope .locale a.active{
            background:var(--accent);
            border-color:var(--accent);
        }
        .admin-status-pill{
            display:inline-flex;
            align-items:center;
            padding:.34rem .62rem;
            border-radius:999px;
            background:var(--accent-soft);
            color:var(--accent);
            font-size:.73rem;
            font-weight:800;
        }

        .admin-status-pill.is-muted{
            background:#f1f5f9;
            color:#64748b;
        }

        .admin-status-pill.is-danger{
            background:#fff0ee;
            color:#d25a46;
        }

        .admin-empty{
            padding:2rem 1rem;
            text-align:center;
            color:var(--muted);
            font-weight:700;
        }

        .mobile-sidebar{display:none}
        .mobile-sidebar .offcanvas-body{padding:1rem;background:transparent}
        .mobile-sidebar .sidebar{display:block;position:static}
        .mobile-sidebar .sidebar-panel{min-height:100%}

        @media (max-width:1199.98px){
            .admin-shell{grid-template-columns:270px minmax(0,1fr)}
            .nav-desc{display:none}
            .admin-stats-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
        }

        @media (max-width:991.98px){
            .admin-shell{grid-template-columns:minmax(0,1fr);padding:1rem}
            .sidebar{display:none}
            .mobile-sidebar{display:block}
            .menu-btn{display:inline-flex;align-items:center;justify-content:center}
        }

        @media (max-width:767.98px){
            .topbar{flex-direction:column;align-items:flex-start}
            .top-actions,.profile{width:100%;justify-content:space-between}
            .page-frame,.content-card{padding:1rem}
            .admin-stats-grid{grid-template-columns:minmax(0,1fr)}
        }
    </style>
</head>
<body>
@php
    use Illuminate\Support\Facades\Route;

    $isAr = app()->getLocale() === 'ar';
    $user = auth()->user();
    $routeName = Route::currentRouteName();
    $initials = collect(explode(' ', trim($user?->name ?? 'Admin')))
        ->filter()
        ->take(2)
        ->map(fn ($value) => mb_substr($value, 0, 1))
        ->implode('');

    $icons = [
        'grid' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 13.5h8V3H3zM13 21h8V10.5h-8zM13 3v4.5h8V3zM3 21h8v-4.5H3z"/></svg>',
        'spark' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l1.9 4.1L18 9l-4.1 1.9L12 15l-1.9-4.1L6 9l4.1-1.9L12 3z"/><path d="M5 17l.9 1.9L8 20l-2.1.9L5 23l-.9-2.1L2 20l2.1-1.1L5 17z"/></svg>',
        'map' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 18l-6 3V6l6-3 6 3 6-3v15l-6 3-6-3z"/><path d="M9 3v15"/><path d="M15 6v15"/></svg>',
        'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.8v4.7l3 1.8"/></svg>',
        'service' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 14a7 7 0 0 1 11.2-5.6l2.8-2.8 1.4 1.4-2.8 2.8A7 7 0 1 1 5 14z"/><path d="M14.5 9.5l-5 5"/></svg>',
        'doctor' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z"/><path d="M4 20a8 8 0 0 1 16 0"/><path d="M19 5v4"/><path d="M17 7h4"/></svg>',
        'patient' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9.5 11.5a3.5 3.5 0 1 0-3.5-3.5 3.5 3.5 0 0 0 3.5 3.5z"/><path d="M3.5 19a6 6 0 0 1 12 0"/><path d="M18.5 10.5V5.5"/><path d="M16 8h5"/></svg>',
        'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>',
        'finance' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v18"/><path d="M17 7.5a4.5 4.5 0 0 0-9 0c0 6.3 9 3.7 9 9a4.5 4.5 0 0 1-9 0"/></svg>',
        'chart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19.5h16"/><path d="M7 16V10"/><path d="M12 16V6"/><path d="M17 16v-3"/></svg>',
        'ledger' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 4.5h14a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-11a2 2 0 0 1 2-2z"/><path d="M8 8.5h8M8 12h8M8 15.5h5"/></svg>',
        'blog' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 4.5h14a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-11a2 2 0 0 1 2-2z"/><path d="M7.5 8.5h9M7.5 12h9M7.5 15.5h5"/></svg>',
        'mail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6.5h16v11H4z"/><path d="M4 7l8 6 8-6"/></svg>',
        'career' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 6.5V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1.5"/><rect x="4" y="6.5" width="16" height="13.5" rx="2"/><path d="M4 12h16"/></svg>',
        'applicants' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 11a3 3 0 1 0-3-3 3 3 0 0 0 3 3z"/><path d="M16 12a3 3 0 1 0-3-3 3 3 0 0 0 3 3z"/><path d="M2.5 19a5.5 5.5 0 0 1 11 0"/><path d="M11 19a5 5 0 0 1 10 0"/></svg>',
        'subs' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16v10H4z"/><path d="M4 7l8 6 8-6"/></svg>',
        'pages' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 4.5h10a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-11a2 2 0 0 1 2-2z"/><path d="M8.5 8.5h7M8.5 12h7M8.5 15.5h4"/></svg>',
        'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11a3.5 3.5 0 1 0-3.5-3.5A3.5 3.5 0 0 0 9 11z"/><path d="M17 10a2.5 2.5 0 1 0-2.5-2.5A2.5 2.5 0 0 0 17 10z"/><path d="M3 19a6 6 0 0 1 12 0"/><path d="M14 18.5a4.5 4.5 0 0 1 7 0"/></svg>',
        'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 8.5A3.5 3.5 0 1 0 15.5 12 3.5 3.5 0 0 0 12 8.5z"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.86l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.86-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-1-1.55 1.7 1.7 0 0 0-1.86.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.55-1H3a2 2 0 1 1 0-4h.09a1.7 1.7 0 0 0 1.55-1 1.7 1.7 0 0 0-.34-1.86l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.55V3a2 2 0 1 1 4 0v.09a1.7 1.7 0 0 0 1 1.55 1.7 1.7 0 0 0 1.86-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.55 1H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.55 1z"/></svg>',
    ];

    $groups = [
        [
            'title' => $isAr ? 'نظرة عامة' : 'Overview',
            'subtitle' => $isAr ? 'المؤشرات والبدء السريع' : 'Reports and quick access',
            'items' => [
                ['label' => $isAr ? 'الرئيسية' : 'Dashboard', 'desc' => $isAr ? 'المؤشرات والتقارير' : 'KPIs and reports', 'route' => 'admin.dashboard', 'icon' => $icons['grid']],
                ['label' => $isAr ? 'أقسام الصفحة الرئيسية' : 'Home Sections', 'desc' => $isAr ? 'إدارة الواجهة والمحتوى' : 'Homepage sections', 'route' => 'admin.home-sections.index', 'can' => ['manage-content', 'manage-home-sections'], 'icon' => $icons['spark']],
                ['label' => $isAr ? 'البنرات والعروض' : 'Banners & Offers', 'desc' => $isAr ? 'العناصر التسويقية' : 'Marketing blocks', 'route' => 'admin.marketing-sections.edit', 'can' => ['manage-content', 'manage-marketing-sections'], 'icon' => $icons['spark']],
            ],
        ],
        [
            'title' => $isAr ? 'العيادة والتشغيل' : 'Clinic Operations',
            'subtitle' => $isAr ? 'الفروع والطاقم والحجوزات' : 'Branches, staff, bookings',
            'items' => [
                ['label' => $isAr ? 'الفروع' : 'Branches', 'desc' => $isAr ? 'مواقع العمل' : 'Clinic locations', 'route' => 'admin.branches.index', 'can' => ['manage-content', 'manage-branches'], 'icon' => $icons['map']],
                ['label' => $isAr ? 'ساعات العمل' : 'Working Hours', 'desc' => $isAr ? 'الجداول الرسمية' : 'Clinic schedule', 'route' => 'admin.working-hours.index', 'can' => ['manage-content', 'manage-working-hours'], 'icon' => $icons['clock']],
                ['label' => $isAr ? 'الخدمات' : 'Services', 'desc' => $isAr ? 'الباقات والأسعار' : 'Packages and pricing', 'route' => 'admin.services.index', 'can' => ['manage-content', 'manage-services'], 'icon' => $icons['service']],
                ['label' => $isAr ? 'الأطباء' : 'Doctors', 'desc' => $isAr ? 'الفريق الطبي' : 'Medical team', 'route' => 'admin.doctors.index', 'can' => ['manage-content', 'manage-doctors'], 'icon' => $icons['doctor']],
                ['label' => $isAr ? 'ملفات المرضى' : 'Patient Files', 'desc' => $isAr ? 'السجلات والزيارات' : 'Medical records', 'route' => 'admin.patients.index', 'can' => 'manage-patient-records', 'icon' => $icons['patient']],
                ['label' => $isAr ? 'الحجوزات' : 'Appointments', 'desc' => $isAr ? 'الجدولة والتحويل' : 'Booking workflow', 'route' => 'admin.appointments.index', 'can' => 'manage-appointments', 'icon' => $icons['calendar']],
            ],
        ],
        [
            'title' => $isAr ? 'المالية والمحاسبة' : 'Finance & Accounting',
            'subtitle' => $isAr ? 'القيود والذمم والتقارير والمخزون' : 'Journals, receivables, reporting, inventory',
            'theme' => 'finance',
            'items' => [
                ['label' => $isAr ? 'المركز المالي' : 'Finance Hub', 'desc' => $isAr ? 'إدارة المالية المركزية' : 'Central finance workspace', 'route' => 'admin.finance.index', 'can' => 'manage-finance', 'icon' => $icons['finance']],
                ['label' => $isAr ? 'المحاسبة العامة' : 'Accounting', 'desc' => $isAr ? 'القيود وميزان المراجعة' : 'Journals and trial balance', 'route' => 'admin.finance.accounting', 'can' => 'manage-finance', 'icon' => $icons['ledger']],
                ['label' => $isAr ? 'الفواتير والذمم' : 'Invoices & AR/AP', 'desc' => $isAr ? 'عملاء وموردون وأعمار الذمم' : 'Customer and supplier balances', 'route' => 'admin.finance.invoices', 'can' => 'manage-finance', 'icon' => $icons['finance']],
                ['label' => $isAr ? 'السندات' : 'Vouchers', 'desc' => $isAr ? 'سندات القبض والصرف' : 'Receipt and payment vouchers', 'route' => 'admin.finance.vouchers', 'can' => 'manage-finance', 'icon' => $icons['ledger']],
                ['label' => $isAr ? 'التقارير والإقفال' : 'Reports & Closing', 'desc' => $isAr ? 'قائمة دخل وميزانية وإقفال' : 'Statements and monthly close', 'route' => 'admin.finance.reports', 'can' => 'manage-finance', 'icon' => $icons['chart']],
                ['label' => $isAr ? 'المخزون والمستودع' : 'Inventory', 'desc' => $isAr ? 'الأدوات والمستلزمات' : 'Warehouse and stock items', 'route' => 'admin.finance.inventory', 'can' => 'manage-finance', 'icon' => $icons['service']],
                ['label' => $isAr ? 'البيانات المرجعية' : 'Master Data', 'desc' => $isAr ? 'مراكز التكلفة والعملاء والموردون' : 'Cost centers and parties', 'route' => 'admin.finance.master-data', 'can' => 'manage-finance', 'icon' => $icons['map']],
            ],
        ],
        [
            'title' => $isAr ? 'المحتوى والعلاقات' : 'Content & Relations',
            'subtitle' => $isAr ? 'المدونة والرسائل والصفحات' : 'Blog, inbox, static content',
            'items' => [
                ['label' => $isAr ? 'المدونة' : 'Blog', 'desc' => $isAr ? 'المقالات والتصنيفات' : 'Posts and categories', 'route' => 'admin.blog-posts.index', 'can' => ['manage-content', 'manage-blog'], 'icon' => $icons['blog']],
                ['label' => $isAr ? 'الرسائل' : 'Messages', 'desc' => $isAr ? 'استفسارات العملاء' : 'Inbox and inquiries', 'route' => 'admin.messages.index', 'can' => 'manage-messages', 'icon' => $icons['mail']],
                ['label' => $isAr ? 'الوظائف' : 'Career Positions', 'desc' => $isAr ? 'الفرص المتاحة' : 'Open positions', 'route' => 'admin.career-positions.index', 'can' => ['manage-content', 'manage-careers'], 'icon' => $icons['career']],
                ['label' => $isAr ? 'طلبات التوظيف' : 'Applications', 'desc' => $isAr ? 'مرشحو الوظائف' : 'Candidate pipeline', 'route' => 'admin.career-applications.index', 'can' => ['manage-content', 'manage-career-applications'], 'icon' => $icons['applicants']],
                ['label' => $isAr ? 'المشتركون' : 'Subscribers', 'desc' => $isAr ? 'قائمة النشرة البريدية' : 'Audience list', 'route' => 'admin.subscribers.index', 'can' => 'manage-subscribers', 'icon' => $icons['subs']],
                ['label' => $isAr ? 'الصفحات' : 'Pages', 'desc' => $isAr ? 'الصفحات الثابتة' : 'Static pages', 'route' => 'admin.pages.index', 'can' => ['manage-content', 'manage-pages'], 'icon' => $icons['pages']],
            ],
        ],
        [
            'title' => $isAr ? 'الإدارة' : 'Administration',
            'subtitle' => $isAr ? 'الصلاحيات والإعدادات' : 'Users and settings',
            'items' => [
                ['label' => $isAr ? 'المستخدمون' : 'Users', 'desc' => $isAr ? 'الأدوار والصلاحيات' : 'Roles and access', 'route' => 'admin.users.index', 'can' => 'manage-users', 'icon' => $icons['users']],
                ['label' => $isAr ? 'الإعدادات' : 'Settings', 'desc' => $isAr ? 'ضبط النظام العام' : 'Global configuration', 'route' => 'admin.settings.index', 'can' => 'manage-settings', 'icon' => $icons['settings']],
            ],
        ],
    ];

    $visibleGroups = collect($groups)->map(function ($group) use ($user) {
        $items = collect($group['items'])->filter(function ($item) use ($user) {
            if (!isset($item['can'])) {
                return true;
            }

            return is_array($item['can'])
                ? $user?->canAny($item['can'])
                : $user?->can($item['can']);
        })->values();

        return array_merge($group, ['items' => $items]);
    })->filter(fn ($group) => $group['items']->isNotEmpty())->values();

    $pageTitle = $title ?? ($isAr ? 'لوحة الإدارة' : 'Admin Workspace');
    $pageSubtitle = $isAr ? 'واجهة موحدة لإدارة العيادة والمحتوى.' : 'A unified workspace for clinic operations and content.';
    $activeGroupIndex = 0;
    $isFinanceSection = $routeName && str_starts_with($routeName, 'admin.finance');

    foreach ($visibleGroups as $groupIndex => $group) {
        foreach ($group['items'] as $item) {
            if ($routeName && str_starts_with($routeName, $item['route'])) {
                $pageTitle = $item['label'];
                $pageSubtitle = $item['desc'];
                $activeGroupIndex = $groupIndex;
                break 2;
            }
        }
    }
@endphp

<div class="offcanvas offcanvas-{{ $isAr ? 'end' : 'start' }} mobile-sidebar" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-body">
        @include('layouts.partials.admin-sidebar', [
            'groups' => $visibleGroups,
            'routeName' => $routeName,
            'isAr' => $isAr,
            'icons' => $icons,
            'sidebarIdPrefix' => 'mobile',
            'activeGroupIndex' => $activeGroupIndex,
        ])
    </div>
</div>

<div class="admin-shell">
    @include('layouts.partials.admin-sidebar', [
        'groups' => $visibleGroups,
        'routeName' => $routeName,
        'isAr' => $isAr,
        'icons' => $icons,
        'sidebarIdPrefix' => 'desktop',
        'activeGroupIndex' => $activeGroupIndex,
    ])

    <main class="main {{ $isFinanceSection ? 'finance-scope' : '' }}">
        <div class="topbar">
            <div class="d-flex align-items-start gap-3">
                <button class="menu-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-label="Toggle sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>
                <div>
                    <span class="eyebrow">{{ $isAr ? 'واجهة الإدارة' : 'Admin Workspace' }}</span>
                    <h1 class="page-title">{{ $pageTitle }}</h1>
                    <p class="page-subtitle">{{ $pageSubtitle }}</p>
                </div>
            </div>

            <div class="top-actions">
                <div class="locale">
                    <a href="{{ url('/ar') }}" class="{{ $isAr ? 'active' : '' }}">AR</a>
                    <a href="{{ url('/en') }}" class="{{ !$isAr ? 'active' : '' }}">EN</a>
                </div>

                <div class="profile">
                    <span class="profile-avatar">{{ $initials ?: 'A' }}</span>
                    <div>
                        <strong>{{ $user?->name ?? ($isAr ? 'مشرف النظام' : 'Admin User') }}</strong>
                        <span>{{ $user?->email ?? 'admin@drhalim.local' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-frame">
            @if(session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: @json($isAr ? 'تم بنجاح' : 'Success'),
                        text: @json(session('success')),
                        timer: 1800,
                        showConfirmButton: false
                    });
                </script>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
            @endif

            <div class="content-card">
                @yield('content')
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
</body>
</html>
