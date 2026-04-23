<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dr Halim Dental' }}</title>
    @php
        $faviconPath = \App\Models\Setting::getValue('favicon');
        $faviconUrl = null;
        if ($faviconPath) {
            if (\Illuminate\Support\Str::startsWith($faviconPath, ['http://', 'https://'])) {
                $faviconUrl = $faviconPath;
            } elseif (\Illuminate\Support\Str::startsWith($faviconPath, ['/storage/', 'storage/'])) {
                $faviconUrl = asset(ltrim($faviconPath, '/'));
            } else {
                $faviconUrl = asset('storage/' . ltrim($faviconPath, '/'));
            }
        }
    @endphp
    @if($faviconUrl)
        <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --ink:#17324d;
            --soft:#667d90;
            --line:#d8e8e4;
            --brand:#0f8b8d;
            --brand-deep:#0d5f73;
            --brand-soft:#dff7f2;
            --accent:#7ed6c6;
            --bg:#eff9f6;
            --surface:#ffffff;
        }
        body {
            background: var(--bg);
            color: var(--ink);
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
        }
        .container {
            width: min(100%, 1320px);
        }
        .alert {
            border-radius: 1rem;
            border: 1px solid #d7e4f2;
            box-shadow: 0 10px 22px rgba(18, 54, 95, .05);
        }
        .nav-wrap {
            background: linear-gradient(140deg, #dbf4ef 0%, #cceee7 100%);
            border: 1px solid #c9e6de;
            border-radius: 1.6rem;
            margin-top: 1rem;
            box-shadow: 0 10px 24px rgba(16, 82, 92, .08);
            backdrop-filter: blur(3px);
        }
        .nav-shell {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .nav-center {
            flex: 1;
            display: flex;
            justify-content: center;
            min-width: 0;
        }
        .nav-main-links .nav-link {
            color: #1b5562;
            font-weight: 600;
            border-radius: .6rem;
            padding: .38rem .68rem;
            transition: .2s ease;
            white-space: nowrap;
        }
        .nav-main-links .nav-link:hover {
            color: #0e4958;
            background: rgba(255, 255, 255, .62);
            transform: translateY(-1px);
        }
        .nav-main-links .nav-link.active {
            color: #0e4958;
            background: rgba(255, 255, 255, .82);
            box-shadow: inset 0 0 0 1px rgba(15, 102, 111, .12);
        }
        .nav-actions .btn {
            border-radius: .58rem;
            font-weight: 600;
            padding: .34rem .72rem;
        }
        .nav-actions .btn-primary {
            box-shadow: 0 8px 20px rgba(15, 139, 141, .22);
        }
        .navbar-toggler {
            border: 1px solid #b1cae0;
            background: rgba(255, 255, 255, .72);
            border-radius: .75rem;
            padding: .45rem .58rem;
        }
        .navbar-toggler:focus {
            box-shadow: 0 0 0 .2rem rgba(47, 154, 231, .25);
        }
        .navbar-toggler-icon {
            width: 1.2rem;
            height: 1.2rem;
            background-image: none;
            position: relative;
        }
        .navbar-toggler-icon::before {
            content: "\F479";
            font-family: bootstrap-icons !important;
            font-style: normal;
            font-weight: 700 !important;
            color: #1c5b61;
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            line-height: 1;
            font-size: 1.15rem;
        }
        .hero { background: linear-gradient(120deg, var(--brand-deep), #157c81); color:#fff; border-radius:1.2rem; padding:2rem; }
        .card-soft { border:1px solid var(--line); border-radius:1rem; background:#fff; }
        .btn-primary {
            background: linear-gradient(135deg, var(--brand), #11a6a8);
            border-color: transparent;
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background: linear-gradient(135deg, #0b7a7c, #15999b);
            border-color: transparent;
        }
        .btn-outline-primary {
            color: var(--brand-deep);
            border-color: #9ed8cd;
        }
        .btn-outline-primary:hover,
        .btn-outline-primary:focus {
            background: var(--brand-soft);
            color: var(--brand-deep);
            border-color: #8fcec1;
        }
        .front-page {
            display: grid;
            gap: 1rem;
        }
        .page-shell {
            position: relative;
            border: 1px solid #d5e8e2;
            border-radius: 1.6rem;
            background:
                radial-gradient(420px 180px at 100% 0, rgba(67, 175, 167, .11), transparent 62%),
                linear-gradient(180deg, rgba(255,255,255,.98) 0%, rgba(246,252,250,.94) 100%);
            padding: 1.15rem;
            box-shadow: 0 18px 38px rgba(16, 82, 92, .08);
            overflow: hidden;
        }
        .page-shell::before {
            content: "";
            position: absolute;
            inset: 0 auto auto 0;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(126, 214, 198, .16), transparent 70%);
            pointer-events: none;
        }
        .page-shell > * {
            position: relative;
            z-index: 1;
        }
        .page-hero-modern {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.3rem;
            border: 1px solid #d5e7e2;
            border-radius: 1.35rem;
            background:
                radial-gradient(circle at top right, rgba(72, 184, 175, .14), transparent 30%),
                linear-gradient(135deg, #f7fcfb 0%, #ffffff 100%);
            box-shadow: 0 16px 32px rgba(16, 82, 92, .06);
        }
        .page-kicker {
            display: inline-flex;
            align-items: center;
            gap: .42rem;
            padding: .34rem .76rem;
            border-radius: 999px;
            background: #ebfaf7;
            border: 1px solid #d1ebe5;
            color: #17726e;
            font-size: .78rem;
            font-weight: 800;
        }
        .page-title {
            margin: .65rem 0 .35rem;
            color: #17465a;
            font-size: clamp(1.8rem, 1.45rem + .8vw, 2.45rem);
            line-height: 1.2;
            font-weight: 900;
        }
        .page-copy {
            margin: 0;
            color: #617c86;
            line-height: 1.9;
            max-width: 66ch;
        }
        .page-actions {
            display: flex;
            align-items: center;
            gap: .6rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .page-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .75rem;
        }
        .page-stat {
            padding: .85rem .8rem;
            border: 1px solid #d6e8e3;
            border-radius: 1rem;
            background: rgba(255,255,255,.9);
            box-shadow: 0 12px 24px rgba(16, 82, 92, .05);
            text-align: center;
        }
        .page-stat strong {
            display: block;
            color: #15636e;
            font-size: 1.35rem;
            line-height: 1;
            font-weight: 900;
        }
        .page-stat span {
            display: block;
            margin-top: .3rem;
            color: #607b86;
            font-size: .8rem;
            font-weight: 700;
        }
        .surface-card {
            position: relative;
            border: 1px solid #d7e8e3;
            border-radius: 1.25rem;
            background: linear-gradient(180deg, #ffffff 0%, #f6fcfa 100%);
            box-shadow: 0 14px 28px rgba(16, 82, 92, .07);
            overflow: hidden;
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }
        .surface-card:hover {
            transform: translateY(-4px);
            border-color: #bfe1d8;
            box-shadow: 0 20px 36px rgba(16, 82, 92, .11);
        }
        .surface-card-soft {
            border: 1px solid #d8e8e3;
            border-radius: 1.1rem;
            background: rgba(255,255,255,.92);
            box-shadow: 0 10px 24px rgba(16, 82, 92, .05);
        }
        .card-media-overlay {
            position: relative;
            overflow: hidden;
        }
        .card-media-overlay::after {
            content: "";
            position: absolute;
            inset: auto 0 0;
            height: 88px;
            background: linear-gradient(180deg, rgba(9, 28, 31, 0), rgba(9, 28, 31, .34));
            pointer-events: none;
        }
        .card-media-overlay img {
            transition: transform .35s ease;
        }
        .surface-card:hover .card-media-overlay img {
            transform: scale(1.04);
        }
        .surface-section-title {
            color: #184f63;
            font-size: 1.1rem;
            font-weight: 800;
        }
        .working-hours-list {
            display: grid;
            gap: .45rem;
        }
        .work-hour-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .55rem .72rem;
            border: 1px solid #d9e8e4;
            border-radius: .8rem;
            background: #fbfefd;
            font-size: .88rem;
        }
        .submit-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .8rem;
            margin-top: 1rem;
            padding-top: .85rem;
            border-top: 1px dashed #d8e5f2;
        }
        .info-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .8rem;
        }
        .split-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.85fr) minmax(290px, .95fr);
            gap: 1rem;
            align-items: start;
        }
        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .26rem .58rem;
            border-radius: 999px;
            background: #eff6fd;
            border: 1px solid #d3e8e2;
            color: #2d6768;
            font-size: .76rem;
            font-weight: 700;
        }
        .action-link {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            color: #0f8b8d;
            font-weight: 700;
            text-decoration: none;
        }
        .action-link:hover {
            color: #0d6f76;
        }
        .footer-main { background: radial-gradient(1000px 350px at 20% -20%, #37a39d 0%, transparent 60%), #114c5f; color:#fff; border-radius:1rem 1rem 0 0; }
        .footer-main a { color:#fff; text-decoration:none; opacity:.9; }
        .footer-main a:hover { opacity:1; }
        .floating-btn { position: fixed; left: 12px; z-index: 999; width: 46px; height: 46px; border-radius: 50%; display:flex; align-items:center; justify-content:center; color:#fff; box-shadow:0 4px 14px rgba(0,0,0,.2); border:0; }
        .float-chat { bottom: 134px; background:#0f8b8d; }
        .float-wa { bottom: 78px; background:#28b18d; }
        .float-call { bottom: 22px; background:#156e8a; }
        .chat-widget {
            position: fixed;
            left: 72px;
            bottom: 22px;
            z-index: 1000;
            width: min(420px, calc(100vw - 96px));
            height: min(680px, calc(100vh - 44px));
            display: flex;
            flex-direction: column;
            border: 1px solid #cfe0ec;
            border-radius: 1.4rem;
            overflow: hidden;
            background: #f7fcfb;
            box-shadow: 0 22px 56px rgba(15, 72, 83, .22);
            opacity: 0;
            pointer-events: none;
            transform: translateY(14px) scale(.98);
            transition: .22s ease;
        }
        .chat-widget.is-open {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }
        .chat-widget-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: 1rem 1rem .95rem;
            background:
                radial-gradient(180px 80px at 0% 0%, rgba(16, 139, 141, .12), transparent 65%),
                radial-gradient(180px 80px at 100% 0%, rgba(64, 190, 160, .12), transparent 65%),
                linear-gradient(160deg, #f2fbf8, #ffffff);
            border-bottom: 1px solid #d8e8e3;
        }
        .chat-widget-head-main {
            display: flex;
            align-items: center;
            gap: .8rem;
            min-width: 0;
        }
        .chat-widget-avatar {
            width: 44px;
            height: 44px;
            border-radius: 1rem;
            display: grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(135deg, #0f8b8d, #29b4a0);
            box-shadow: 0 14px 26px rgba(16, 90, 97, .18);
            font-size: 1.05rem;
            flex-shrink: 0;
        }
        .chat-widget-head-copy {
            min-width: 0;
        }
        .chat-widget-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 800;
            color: #184f63;
        }
        .chat-widget-subtitle {
            margin: .15rem 0 0;
            color: #678096;
            font-size: .84rem;
        }
        .chat-widget-close {
            width: 40px;
            height: 40px;
            border-radius: .9rem;
            border: 1px solid #d5e2ed;
            background: #fff;
            color: #224562;
        }
        .chat-widget-status {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .34rem .72rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 800;
        }
        .chat-widget-status.bot { background:#e7f5ee; color:#17704f; }
        .chat-widget-status.human { background:#eaf2ff; color:#2258a8; }
        .chat-widget-status.closed { background:#f3f4f6; color:#556070; }
        .chat-widget-status::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: currentColor;
            opacity: .75;
        }
        .chat-widget-body {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            padding: .9rem;
            background:
                linear-gradient(180deg, rgba(255,255,255,.55), rgba(255,255,255,.22)),
                linear-gradient(180deg, #f4f9ff, #edf5ff);
            overflow: auto;
        }
        .chat-widget-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-bottom: .75rem;
            padding: .72rem .85rem;
            border: 1px solid #d7e7f5;
            border-radius: 1rem;
            background: rgba(255,255,255,.82);
            box-shadow: 0 12px 24px rgba(19, 58, 106, .05);
        }
        .chat-widget-banner-copy {
            min-width: 0;
        }
        .chat-widget-banner-title {
            margin: 0;
            color: #123a6a;
            font-size: .88rem;
            font-weight: 800;
        }
        .chat-widget-banner-text {
            margin: .18rem 0 0;
            color: #698095;
            font-size: .77rem;
            line-height: 1.5;
        }
        .chat-widget-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .34rem .62rem;
            border-radius: 999px;
            background: #eff8f3;
            color: #17704f;
            font-size: .76rem;
            font-weight: 800;
            white-space: nowrap;
        }
        .chat-widget-thread {
            flex: 0 0 auto;
            min-height: 180px;
            max-height: 260px;
            overflow: auto;
            display: flex;
            flex-direction: column;
            gap: .8rem;
            padding: .35rem;
            border: 1px solid #dbe7f3;
            border-radius: 1.15rem;
            background:
                radial-gradient(circle at top right, rgba(29, 125, 250, .06), transparent 28%),
                linear-gradient(180deg, #ffffff, #f6fbff);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
        }
        .chat-widget-thread::-webkit-scrollbar { width: 8px; }
        .chat-widget-thread::-webkit-scrollbar-thumb { background: #b8cde0; border-radius: 999px; }
        .chat-widget-message-row {
            display: flex;
            align-items: flex-end;
            gap: .55rem;
        }
        .chat-widget-message-row.customer {
            justify-content: flex-end;
        }
        .chat-widget-message-meta {
            display: flex;
            align-items: center;
            gap: .35rem;
            margin-bottom: .26rem;
            font-size: .72rem;
            font-weight: 700;
            opacity: .82;
        }
        .chat-widget-message-meta .dot {
            width: 4px;
            height: 4px;
            border-radius: 999px;
            background: currentColor;
            opacity: .55;
        }
        .chat-widget-message-avatar {
            width: 34px;
            height: 34px;
            border-radius: .85rem;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            color: #fff;
            box-shadow: 0 10px 22px rgba(12,46,84,.08);
        }
        .chat-widget-message-avatar.customer { background: linear-gradient(135deg, #1d7dfa, #4f8dff); }
        .chat-widget-message-avatar.ai { background: linear-gradient(135deg, #0f766e, #22c55e); }
        .chat-widget-message-avatar.admin { background: linear-gradient(135deg, #123a6a, #2f9ae7); }
        .chat-widget-message-avatar.system { background: linear-gradient(135deg, #d2a325, #f0c860); color: #7b5a00; }
        .chat-widget-message-stack {
            max-width: calc(100% - 42px);
        }
        .chat-widget-bubble {
            max-width: 100%;
            padding: .78rem .92rem;
            border-radius: 1.1rem;
            line-height: 1.6;
            box-shadow: 0 12px 26px rgba(12,46,84,.05);
        }
        .chat-widget-bubble small {
            display: block;
            margin-top: .45rem;
            opacity: .68;
            font-size: .69rem;
        }
        .chat-widget-customer {
            align-self: flex-end;
            background: linear-gradient(135deg, #1d7dfa, #2e89ff);
            color:#fff;
            border-bottom-right-radius:.35rem;
        }
        .chat-widget-admin, .chat-widget-ai, .chat-widget-system { align-self:flex-start; border-bottom-left-radius:.35rem; }
        .chat-widget-admin { background:#fff; color:#17324d; border:1px solid #dce7f2; }
        .chat-widget-ai { background:linear-gradient(180deg, #effbf4, #e7f8ef); color:#0e5c3e; border:1px solid #cde8d9; }
        .chat-widget-system { background:linear-gradient(180deg, #fff8df, #fff2c9); color:#7b5a00; border:1px solid #f1e2ab; }
        .chat-widget-attachment {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            margin-top: .6rem;
            padding: .34rem .62rem;
            border-radius: .7rem;
            text-decoration: none;
            font-size: .8rem;
            background: rgba(255,255,255,.18);
            color: inherit;
        }
        .chat-widget-attachment.light { background:#f2f7fb; color:#18425a; }
        .chat-widget-form {
            margin-top: .85rem;
            background: rgba(255,255,255,.92);
            border: 1px solid #dce7f2;
            border-radius: 1.15rem;
            padding: .9rem;
            display: flex;
            flex-direction: column;
            box-shadow: 0 12px 24px rgba(19, 58, 106, .05);
        }
        .chat-widget-grid {
            display: grid;
            gap: .75rem;
        }
        .chat-widget-input-group {
            display: grid;
            gap: .35rem;
        }
        .chat-widget-input-group .form-label {
            margin-bottom: 0;
            color: #294966;
            font-size: .82rem;
            font-weight: 700;
        }
        .chat-widget .form-control {
            border-radius: .9rem;
            border-color: #d4e2ef;
            background: #fbfdff;
            box-shadow: none;
        }
        .chat-widget .form-control:focus {
            border-color: #72aaf4;
            box-shadow: 0 0 0 .2rem rgba(47,154,231,.14);
            background: #fff;
        }
        .chat-widget-attachment-row {
            display: flex;
            align-items: center;
            gap: .55rem;
            flex-wrap: wrap;
            padding: .6rem .7rem;
            border: 1px dashed #c7d9ea;
            border-radius: .95rem;
            background: #f8fbff;
        }
        .chat-widget-upload-btn {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .52rem .78rem;
            border-radius: .85rem;
            background: #eef5fd;
            color: #1f5b95;
            font-weight: 700;
            cursor: pointer;
        }
        .chat-widget-upload-btn input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }
        .chat-widget-file-name {
            color: #6d8398;
            font-size: .78rem;
        }
        .chat-widget-actions {
            position: sticky;
            bottom: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
            padding-top: .35rem;
            background: linear-gradient(180deg, rgba(255,255,255,0), #fff 24%);
        }
        .chat-widget-feedback {
            display: none;
            margin-bottom: .65rem;
        }
        .chat-widget-feedback.is-visible {
            display: block;
        }
        .chat-widget-note {
            color: #698095;
            font-size: .77rem;
        }
        .chat-widget-send {
            min-width: 128px;
            border-radius: .9rem;
            padding-block: .7rem;
            font-weight: 800;
            box-shadow: 0 12px 24px rgba(29,125,250,.2);
        }
        .chat-widget textarea.form-control {
            min-height: 88px;
            resize: vertical;
        }
        .brand-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            text-decoration: none;
            color: inherit;
            max-width: 100%;
            min-height: 86px;
            padding-inline: .35rem;
            border-radius: 1rem;
            transition: .2s ease;
        }
        .brand-wrap:hover { background: rgba(255,255,255,.35); }
        .brand-logo {
            width: clamp(240px, 28vw, 360px);
            height: clamp(92px, 10vw, 132px);
            object-fit: contain;
            background: transparent;
            border: 0;
            padding: 0;
            border-radius: 0;
            flex-shrink: 0;
        }
        .brand-title {
            font-size: clamp(1rem, 2vw, 1.28rem);
            font-weight: 800;
            color: #123c65;
            line-height: 1.3;
        }
        .lang-btn {
            min-width: 42px;
            padding-inline: .45rem !important;
        }
        .btn-book-now {
            min-width: 108px;
        }
        @media (max-width: 991.98px) {
            .container.py-2.pb-4 {
                padding-inline: .9rem;
                padding-bottom: 5.4rem !important;
            }
            .nav-wrap {
                margin-top: .55rem;
                border-radius: 1.25rem;
            }
            .nav-shell { gap: .75rem; }
            .nav-center {
                width: 100%;
                justify-content: flex-start;
            }
            .brand-logo {
                width: min(64vw, 280px);
                height: 96px;
            }
            .brand-wrap {
                min-height: 70px;
                justify-content: flex-start;
                padding-inline: 0;
            }
            .nav-actions {
                padding-top: .65rem;
                border-top: 1px dashed #b8d1e4;
                margin-top: .6rem;
                width: 100%;
            }
            .nav-main-links {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: .35rem;
                padding-block: .4rem .25rem;
                width: 100%;
            }
            .nav-main-links .nav-link {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 42px;
                padding: .58rem .7rem;
                text-align: center;
            }
            #navMain {
                background: rgba(255, 255, 255, .55);
                border: 1px solid #c5dcee;
                border-radius: .95rem;
                margin-top: .7rem;
                padding: .48rem .6rem .7rem;
                box-shadow: 0 16px 32px rgba(18, 54, 95, .08);
            }
            .nav-actions {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: .45rem;
            }
            .nav-actions .btn {
                flex: 1 1 auto;
                text-align: center;
            }
            .footer-main {
                border-radius: 1.2rem 1.2rem 0 0;
                padding-block: 2.8rem !important;
            }
            .page-hero-modern {
                flex-direction: column;
            }
            .split-layout {
                grid-template-columns: 1fr;
            }
            .info-grid-2 {
                grid-template-columns: 1fr;
            }
            .page-actions {
                justify-content: flex-start;
            }
            .submit-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .chat-widget {
                left: 12px;
                right: 12px;
                width: auto;
                height: min(76vh, 620px);
                bottom: 78px;
            }
        }
        @media (max-width: 767.98px) {
            .container.py-2.pb-4 {
                padding-inline: .75rem;
                padding-top: .55rem !important;
                padding-bottom: 5.75rem !important;
            }
            .nav-wrap {
                border-radius: 1.15rem;
            }
            .nav-shell {
                align-items: center;
            }
            .brand-logo {
                width: min(62vw, 230px);
                height: 72px;
            }
            .brand-title {
                font-size: 1rem;
            }
            .navbar-toggler {
                padding: .42rem .52rem;
            }
            .nav-main-links {
                grid-template-columns: 1fr;
            }
            .nav-actions {
                grid-template-columns: 1fr;
            }
            .nav-actions .btn {
                min-height: 42px;
            }
            .py-4 {
                padding-top: 1rem !important;
            }
            .floating-btn {
                width: 48px;
                height: 48px;
                left: 8px;
                box-shadow: 0 10px 24px rgba(0,0,0,.18);
            }
            .float-call { bottom: 16px; }
            .float-wa { bottom: 70px; }
            .float-chat { bottom: 124px; }
            .chat-widget {
                left: 8px;
                right: 8px;
                bottom: 8px;
                width: auto;
                height: min(82vh, 700px);
                border-radius: 1.2rem;
            }
            .chat-widget-head {
                padding: .85rem .85rem .8rem;
                align-items: flex-start;
            }
            .chat-widget-head-main {
                gap: .65rem;
            }
            .chat-widget-avatar {
                width: 40px;
                height: 40px;
                border-radius: .9rem;
            }
            .chat-widget-head .d-flex {
                flex-shrink: 0;
            }
            .chat-widget-title {
                font-size: .98rem;
            }
            .chat-widget-subtitle {
                font-size: .78rem;
                line-height: 1.5;
            }
            .chat-widget-status {
                font-size: .74rem;
                padding: .3rem .62rem;
            }
            .chat-widget-close {
                width: 38px;
                height: 38px;
            }
            .chat-widget-body {
                padding: .75rem;
            }
            .chat-widget-banner {
                padding: .62rem .7rem;
                margin-bottom: .65rem;
            }
            .chat-widget-thread {
                min-height: 170px;
                max-height: 220px;
                gap: .7rem;
                padding: .3rem;
            }
            .chat-widget-message-avatar {
                width: 30px;
                height: 30px;
                border-radius: .75rem;
                font-size: .86rem;
            }
            .chat-widget-message-stack {
                max-width: calc(100% - 36px);
            }
            .chat-widget-bubble {
                max-width: 90%;
                padding: .72rem .82rem;
                font-size: .93rem;
            }
            .chat-widget-form {
                margin-top: .75rem;
                padding: .75rem;
                border-radius: .9rem;
            }
            .chat-widget-grid {
                gap: .55rem;
            }
            .chat-widget .form-label {
                margin-bottom: .25rem;
                font-size: .88rem;
            }
            .chat-widget .form-control {
                font-size: .95rem;
                min-height: 44px;
            }
            .chat-widget textarea.form-control {
                min-height: 88px;
            }
            .chat-widget-note {
                width: 100%;
                font-size: .74rem;
                line-height: 1.5;
            }
            .chat-widget-actions {
                gap: .55rem;
                padding-top: .2rem;
            }
            .chat-widget-form .btn {
                width: 100%;
            }
            .footer-main {
                text-align: center;
                border-radius: 1rem 1rem 0 0;
                padding-block: 2.4rem !important;
            }
            .page-shell {
                padding: .95rem;
                border-radius: 1.25rem;
            }
            .page-hero-modern {
                padding: 1rem;
                border-radius: 1.1rem;
            }
            .page-title {
                font-size: clamp(1.55rem, 1.25rem + 1vw, 2rem);
            }
            .page-stats {
                grid-template-columns: 1fr;
            }
            .footer-main .row {
                row-gap: 1.5rem !important;
            }
            .footer-main h4 {
                margin-bottom: .8rem !important;
            }
        }
        @media (max-width: 575.98px) {
            .container.py-2.pb-4 {
                padding-inline: .6rem;
            }
            .nav-wrap {
                padding-inline: .8rem !important;
            }
            #navMain {
                padding: .42rem .48rem .6rem;
            }
            .brand-wrap {
                max-width: calc(100% - 56px);
            }
            .brand-logo {
                width: min(58vw, 200px);
                height: 64px;
            }
            .btn-book-now {
                min-width: 0;
            }
            .chat-widget {
                border-radius: 1rem;
            }
        }
    </style>
</head>
<body>
@php
    $siteName = \App\Models\Setting::getValue('site_name', 'مركز د. حليم لطب الأسنان');
    $siteCity = \App\Models\Setting::getValue('site_city', 'مصر - القاهرة');
    $sitePhone = \App\Models\Setting::getValue('site_phone', '01028234921');
    $waUrl = \App\Models\Setting::getValue('whatsapp_url', 'https://wa.me/201028234921');
    $brandMode = \App\Models\Setting::getValue('brand_mode', 'logo');
    $logoPath = \App\Models\Setting::getValue('logo');
    $logoUrl = null;
    if ($logoPath) {
        if (\Illuminate\Support\Str::startsWith($logoPath, ['http://', 'https://'])) {
            $logoUrl = $logoPath;
        } elseif (\Illuminate\Support\Str::startsWith($logoPath, ['/storage/', 'storage/'])) {
            $logoUrl = asset(ltrim($logoPath, '/'));
        } else {
            $logoUrl = asset('storage/' . ltrim($logoPath, '/'));
        }
    }
    if (! $logoUrl) {
        $brandMode = 'text';
    } else {
        // Always prioritize logo in navbar when available.
        $brandMode = in_array($brandMode, ['logo', 'both'], true) ? $brandMode : 'logo';
    }
    $quickServices = rescue(
        fn () => \App\Models\Service::query()->where('is_active', true)->orderBy('sort_order')->take(7)->get(),
        collect(),
        report: false
    );
    $frontChatConversation = rescue(
        fn () => \App\Models\ChatConversation::query()
            ->with(['messages' => fn ($query) => $query->latest('id')->limit(40)])
            ->find(session('front_chat_conversation_id')),
        null,
        report: false
    );
    $frontChatMessages = $frontChatConversation
        ? $frontChatConversation->messages->sortBy('id')->values()
        : collect();
@endphp

<button class="floating-btn float-chat" type="button" id="chat-widget-toggle" aria-label="{{ app()->getLocale() === 'ar' ? 'فتح المحادثة المباشرة' : 'Open direct chat' }}" title="{{ app()->getLocale() === 'ar' ? 'المحادثة المباشرة' : 'Direct chat' }}">
    <i class="bi bi-chat-dots"></i>
</button>
@if($waUrl)
    <a class="floating-btn float-wa" target="_blank" href="{{ $waUrl }}"><i class="bi bi-whatsapp"></i></a>
@endif
<a class="floating-btn float-call" href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}"><i class="bi bi-telephone"></i></a>

<div class="container py-2 pb-4">
    <nav class="navbar navbar-expand-lg nav-wrap px-3 px-lg-4">
        <div class="container-fluid nav-shell">
            <a class="navbar-brand fw-bold brand-wrap" href="{{ route('front.home', app()->getLocale()) }}">
                @if(in_array($brandMode, ['logo', 'both'], true) && $logoUrl)
                    <img src="{{ $logoUrl }}" class="brand-logo" alt="{{ $siteName }}">
                @endif
                @if(in_array($brandMode, ['text', 'both'], true))
                    <span class="brand-title">{{ $siteName }}</span>
                @endif
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse nav-center" id="navMain">
                <ul class="navbar-nav nav-main-links mx-auto gap-lg-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('front.home', app()->getLocale()) }}">الرئيسية</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('front.pages.show', [app()->getLocale(), 'about']) }}">معلومات عنا</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('front.services.index', app()->getLocale()) }}">خدماتنا</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('front.doctors.index', app()->getLocale()) }}">فريقنا الطبي</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('front.blog.index', app()->getLocale()) }}">المدونة</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('front.careers.index', app()->getLocale()) }}">الوظائف</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('front.contact.index', app()->getLocale()) }}">اتصل بنا</a></li>
                </ul>
                <div class="d-flex gap-2 nav-actions">
                    <a class="btn btn-sm btn-outline-secondary lang-btn" href="{{ url('/en') }}">EN</a>
                    <a class="btn btn-sm btn-outline-secondary lang-btn" href="{{ url('/ar') }}">AR</a>
                    <a class="btn btn-sm btn-primary btn-book-now" href="{{ route('front.appointments.create', app()->getLocale()) }}">احجز الآن</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </div>
</div>

@include('layouts.partials.front-chat-widget', ['conversation' => $frontChatConversation, 'messages' => $frontChatMessages])

<footer class="footer-main py-5 mt-2">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
                <h4 class="fw-bold mb-3">{{ $siteName }}</h4>
                <p class="mb-2">يقدم لك مركزنا رعاية متكاملة للأسنان باستخدام أحدث التقنيات.</p>
                <p class="mb-0"><i class="bi bi-geo-alt"></i> {{ $siteCity }}</p>
                <p class="mb-0"><i class="bi bi-telephone"></i> {{ $sitePhone }}</p>
            </div>
            <div class="col-lg-4">
                <h4 class="fw-bold mb-3">خدماتنا</h4>
                <div class="d-flex flex-column gap-2">
                    @foreach($quickServices as $service)
                        <a href="{{ route('front.services.show', [app()->getLocale(), $service->slug]) }}">{{ $service->title }}</a>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-4">
                <h4 class="fw-bold mb-3">روابط تهمك</h4>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('front.home', app()->getLocale()) }}">الرئيسية</a>
                    <a href="{{ route('front.pages.show', [app()->getLocale(), 'about']) }}">معلومات عنا</a>
                    <a href="{{ route('front.doctors.index', app()->getLocale()) }}">فريقنا الطبي</a>
                    <a href="{{ route('front.blog.index', app()->getLocale()) }}">المدونة</a>
                    <a href="{{ route('front.careers.index', app()->getLocale()) }}">الوظائف</a>
                    <a href="{{ route('front.contact.index', app()->getLocale()) }}">اتصل بنا</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        const links = document.querySelectorAll('.nav-main-links .nav-link');
        const currentPath = window.location.pathname.replace(/\/+$/, '');
        links.forEach((link) => {
            const linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/+$/, '');
            if (currentPath === linkPath || (linkPath !== '' && currentPath.startsWith(linkPath + '/'))) {
                link.classList.add('active');
            }
        });
    })();
</script>
</body>
</html>
