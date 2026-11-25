<!DOCTYPE html>
<html lang="{{ old('language', 'bn') === 'en' ? 'en' : 'bn' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Install · One Blood Bank</title>
    <style>
        :root {
            --red: #a51c1c;
            --red-dark: #8a1717;
            --ink: #1f1b16;
            --muted: #7b7266;
            --line: #e4ddd0;
            --soft: #f7f4ee;
            --green: #15803d;
            --amber: #8a5a12;
        }

        * { box-sizing: border-box; }

        html, body { min-height: 100%; }

        body {
            margin: 0;
            font-family: "Hind Siliguri", "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--ink);
            background: #f7f4ee;
        }

        button, input, select, textarea { font: inherit; }

        .shell {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            padding: 34px 0 46px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 22px;
        }

        .brand { display: flex; align-items: center; gap: 13px; }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            color: #fff;
            font-weight: 500;
            font-size: 16px;
            letter-spacing: -.02em;
            background: var(--red);
        }

        .brand h1 { margin: 0; font-size: 19px; letter-spacing: -.02em; font-weight: 500; }
        .brand p { margin: 2px 0 0; font-size: 12.5px; color: var(--muted); }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: #fdfbf7;
            color: var(--muted);
            font-size: 11.5px;
            font-weight: 500;
            white-space: nowrap;
        }

        .pill i { width: 6px; height: 6px; border-radius: 50%; background: var(--green); }

        .layout { display: grid; grid-template-columns: 300px minmax(0, 1fr); gap: 20px; align-items: start; }

        .sidebar {
            position: sticky;
            top: 18px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fdfbf7;
            overflow: hidden;
        }

        .sidebar-head { padding: 20px 20px 12px; }
        .sidebar-head span { font-size: 11px; font-weight: 500; color: var(--muted); }
        .sidebar-head h2 { margin: 6px 0 0; font-size: 17px; letter-spacing: -.01em; font-weight: 500; }

        .steps { list-style: none; margin: 0; padding: 4px 12px 12px; }

        .step-link {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 10px;
            border: 0;
            border-radius: 8px;
            color: var(--muted);
            background: transparent;
            text-align: left;
            cursor: pointer;
            transition: background .18s ease, color .18s ease;
        }

        .step-link:hover { background: #f7f4ee; color: var(--ink); }
        .step-link.active { background: #f4e4e0; color: var(--red-dark); }
        .step-link.done { color: var(--green); }

        .step-num {
            flex: 0 0 auto;
            width: 28px;
            height: 28px;
            display: grid;
            place-items: center;
            border-radius: 7px;
            border: 1px solid var(--line);
            background: #fdfbf7;
            font-size: 11.5px;
            font-weight: 500;
        }

        .step-link.active .step-num { border-color: var(--red); background: var(--red); color: #fff; }
        .step-link.done .step-num { border-color: #bbf7d0; background: #edf5ee; }

        .step-copy strong { display: block; font-size: 12.5px; line-height: 1.25; font-weight: 500; }
        .step-copy small { display: block; margin-top: 2px; font-size: 10.8px; color: var(--muted); }

        .checks { margin: 0 12px 14px; padding: 13px; border-radius: 10px; background: #f7f4ee; border: 1px solid var(--line); }
        .checks h3 { margin: 0 0 9px; font-size: 10.5px; color: var(--muted); font-weight: 500; }
        .check-row { display: flex; align-items: flex-start; gap: 8px; padding: 4px 0; font-size: 11.2px; color: var(--muted); }
        .check-dot { flex: 0 0 auto; width: 7px; height: 7px; margin-top: 4px; border-radius: 50%; background: var(--green); }
        .check-dot.bad { background: var(--red); }
        .check-row b { font-weight: 500; color: var(--ink); }
        .check-row span { margin-left: auto; color: var(--muted); font-size: 10px; white-space: nowrap; }

        .help { margin: 0 20px 20px; padding-top: 14px; border-top: 1px dashed var(--line); font-size: 11px; line-height: 1.6; color: var(--muted); }

        .panel {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #fdfbf7;
            overflow: hidden;
        }

        .panel-head { padding: 26px 30px 20px; border-bottom: 1px solid var(--line); }
        .eyebrow { display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 500; color: var(--muted); }
        .eyebrow span { width: 24px; height: 1px; background: var(--red); }
        .panel-head h2 { margin: 8px 0 0; font-size: clamp(22px, 3vw, 30px); letter-spacing: -.02em; line-height: 1.15; font-weight: 500; }
        .panel-head p { margin: 7px 0 0; max-width: 620px; color: var(--muted); font-size: 13px; line-height: 1.6; }

        .progress { display: flex; gap: 5px; margin-top: 17px; }
        .progress span { height: 4px; flex: 1; border-radius: 999px; background: #efe9df; overflow: hidden; }
        .progress span::after { content: ""; display: block; width: 0; height: 100%; border-radius: inherit; background: var(--red); transition: width .25s ease; }
        .progress span.on::after { width: 100%; }

        .panel-body { padding: 26px 30px 30px; }

        .alert {
            margin: 0 0 20px;
            padding: 13px 15px;
            border: 1px solid var(--line);
            border-left: 2px solid var(--red);
            border-radius: 8px;
            background: #fbf1ee;
            color: var(--red-dark);
            font-size: 12.5px;
            line-height: 1.6;
        }
        .alert ul { margin: 6px 0 0; padding-left: 18px; }
        .alert.soft { border-left-color: var(--amber); background: #f8efd9; color: #6b4e16; }

        .step { display: none; animation: rise .22s ease; }
        .step.on { display: block; }
        @keyframes rise { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: none; } }

        .section-title { display: flex; align-items: center; gap: 10px; margin: 0 0 15px; font-size: 14px; letter-spacing: -.01em; font-weight: 500; }
        .section-title span { display: grid; place-items: center; width: 26px; height: 26px; border-radius: 7px; background: #f4e4e0; color: var(--red); font-size: 12px; font-weight: 500; }

        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 16px; }
        .grid .full { grid-column: 1 / -1; }

        .field label { display: flex; align-items: baseline; justify-content: space-between; gap: 8px; margin-bottom: 6px; font-size: 12px; font-weight: 500; color: var(--ink); }
        .field label small { font-weight: 400; color: var(--muted); font-size: 10.5px; }
        .field .hint { margin: 5px 0 0; font-size: 10.8px; line-height: 1.5; color: var(--muted); }
        .field .error { margin: 5px 0 0; font-size: 10.8px; font-weight: 500; color: var(--red); }

        .control {
            width: 100%;
            height: 42px;
            padding: 0 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fdfbf7;
            color: var(--ink);
            outline: none;
            transition: border .16s ease, box-shadow .16s ease;
        }

        textarea.control { height: auto; min-height: 72px; padding: 10px 12px; resize: vertical; }
        .control:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(165, 28, 28, .12); }
        .control::placeholder { color: #b3a99b; }
        .control[readonly] { background: #f7f4ee; color: var(--muted); }

        .lang-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .choice { position: relative; cursor: pointer; }
        .choice input { position: absolute; opacity: 0; pointer-events: none; }
        .choice-box { display: flex; gap: 11px; align-items: flex-start; padding: 14px; border: 1px solid var(--line); border-radius: 10px; background: #fdfbf7; transition: border .16s ease, background .16s ease; }
        .choice input:checked + .choice-box { border-color: var(--red); background: #fbf1ee; }
        .choice-icon { flex: 0 0 auto; width: 36px; height: 36px; display: grid; place-items: center; border-radius: 8px; background: #f7f4ee; font-size: 16px; }
        .choice input:checked + .choice-box .choice-icon { background: #f4e4e0; }
        .choice-box strong { display: block; font-size: 12.5px; font-weight: 500; }
        .choice-box small { display: block; margin-top: 3px; color: var(--muted); font-size: 10.5px; line-height: 1.45; }

        .switch { display: flex; align-items: flex-start; gap: 11px; padding: 13px 14px; border: 1px solid var(--line); border-radius: 10px; background: #f7f4ee; }
        .switch input { width: 17px; height: 17px; margin: 1px 0 0; accent-color: var(--red); }
        .switch strong { display: block; font-size: 12.2px; font-weight: 500; }
        .switch small { display: block; margin-top: 3px; color: var(--muted); font-size: 10.8px; line-height: 1.5; }

        .upload {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px;
            border: 1px dashed var(--line);
            border-radius: 10px;
            background: #f7f4ee;
        }
        .upload-preview { flex: 0 0 auto; width: 54px; height: 54px; display: grid; place-items: center; overflow: hidden; border-radius: 8px; border: 1px solid var(--line); background: #fdfbf7; color: var(--red); font-weight: 500; font-size: 16px; }
        .upload-preview img { width: 100%; height: 100%; object-fit: contain; }
        .upload-copy { min-width: 0; flex: 1; }
        .upload-copy strong { display: block; font-size: 12px; font-weight: 500; }
        .upload-copy small { display: block; margin-top: 3px; color: var(--muted); font-size: 10.5px; }
        .file-input { width: 100%; margin-top: 8px; font-size: 10.5px; color: var(--muted); }
        .file-input::file-selector-button { margin-right: 9px; padding: 7px 10px; border: 1px solid var(--line); border-radius: 6px; background: #fdfbf7; color: var(--ink); font-weight: 500; cursor: pointer; }

        .divider { height: 1px; margin: 22px 0; background: var(--line); }

        .smtp { display: none; margin-top: 4px; padding: 16px; border: 1px solid var(--line); border-radius: 10px; background: #f7f4ee; }
        .smtp.on { display: block; }

        .notice { display: flex; gap: 10px; padding: 13px 14px; border-radius: 8px; border: 1px solid var(--line); border-left: 2px solid var(--ink-muted); background: #f7f4ee; color: var(--muted); font-size: 11.5px; line-height: 1.55; }
        .notice strong { color: var(--ink); font-weight: 500; }
        .notice.warn { border-left-color: var(--amber); background: #f8efd9; color: #6b4e16; }
        .notice.warn strong { color: #6b4e16; }

        .actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--line); }
        .actions .right { display: flex; align-items: center; gap: 9px; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 17px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-size: 12.2px;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s ease, border-color .15s ease, color .15s ease;
        }
        .btn-primary { background: var(--ink); color: #f7f4ee; }
        .btn-primary:hover { background: #2b2620; }
        .btn-ghost { border-color: var(--line); background: #fdfbf7; color: var(--muted); }
        .btn-ghost:hover { background: #f7f4ee; color: var(--ink); }
        .btn[disabled] { opacity: .55; cursor: not-allowed; }

        .summary { display: grid; gap: 9px; margin: 14px 0 0; }
        .summary-row { display: flex; justify-content: space-between; gap: 16px; padding: 9px 11px; border-radius: 7px; background: #f7f4ee; font-size: 11.5px; }
        .summary-row span { color: var(--muted); }
        .summary-row b { color: var(--ink); text-align: right; overflow-wrap: anywhere; font-weight: 500; }

        .review-checks { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin-top: 10px; }
        .review-check { display: flex; align-items: center; gap: 8px; padding: 9px 10px; border: 1px solid var(--line); border-radius: 7px; background: #fdfbf7; font-size: 10.8px; color: var(--muted); }
        .review-check i { width: 7px; height: 7px; border-radius: 50%; background: var(--green); }
        .review-check.bad i { background: var(--red); }
        .review-check b { margin-left: auto; font-size: 9.8px; color: var(--muted); font-weight: 500; }

        .footnote { margin: 18px 0 0; color: var(--muted); font-size: 10.8px; line-height: 1.6; text-align: center; }
        .footnote b { color: var(--ink); font-weight: 500; }

        @media (max-width: 900px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { position: static; }
            .steps { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 5px; padding: 4px 12px 14px; }
            .step-link { justify-content: center; padding: 8px 4px; }
            .step-copy { display: none; }
            .checks, .help { display: none; }
        }

        @media (max-width: 640px) {
            .shell { width: min(100% - 20px, 1120px); padding-top: 18px; }
            .topbar { margin-bottom: 14px; }
            .pill { display: none; }
            .panel-head, .panel-body { padding-left: 18px; padding-right: 18px; }
            .grid, .lang-grid, .review-checks { grid-template-columns: 1fr; }
            .actions { align-items: stretch; flex-direction: column-reverse; }
            .actions .right { justify-content: flex-end; }
            .actions .btn-primary, .actions #nextBtn { flex: 1; }
        }
    </style>
</head>
<body>
<div class="shell">
    <header class="topbar">
        <div class="brand">
            <div class="brand-mark">BB</div>
            <div>
                <h1>One Blood Bank</h1>
                <p>First-time installation wizard · {{ now()->format('d M Y') }}</p>
            </div>
        </div>
        <div class="pill"><i></i> Fresh installation</div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-head">
                <span>Setup progress</span>
                <h2>Let’s make it yours</h2>
            </div>

            <ol class="steps">
                <li>
                    <button type="button" class="step-link active" data-goto="1">
                        <span class="step-num">1</span>
                        <span class="step-copy"><strong>Language & region</strong><small>Country, timezone, currency</small></span>
                    </button>
                </li>
                <li>
                    <button type="button" class="step-link" data-goto="2">
                        <span class="step-num">2</span>
                        <span class="step-copy"><strong>Site identity</strong><small>Name, logo and contact</small></span>
                    </button>
                </li>
                <li>
                    <button type="button" class="step-link" data-goto="3">
                        <span class="step-num">3</span>
                        <span class="step-copy"><strong>Administrator</strong><small>Your secure login</small></span>
                    </button>
                </li>
                <li>
                    <button type="button" class="step-link" data-goto="4">
                        <span class="step-num">4</span>
                        <span class="step-copy"><strong>Security & mail</strong><small>Verification and delivery</small></span>
                    </button>
                </li>
                <li>
                    <button type="button" class="step-link" data-goto="5">
                        <span class="step-num">5</span>
                        <span class="step-copy"><strong>Review & install</strong><small>Final check</small></span>
                    </button>
                </li>
            </ol>

            <div class="checks">
                <h3>Server readiness</h3>
                @foreach ($checks as $check)
                    <div class="check-row">
                        <i class="check-dot {{ $check['ok'] ? '' : 'bad' }}"></i>
                        <div><b>{{ $check['label'] }}</b><br>{{ $check['detail'] }}</div>
                    </div>
                @endforeach
            </div>

            <p class="help">
                Need a hand? The installer creates the database tables, stores your settings, creates the first admin, and locks itself when finished.
            </p>
        </aside>

        <main class="panel">
            <div class="panel-head">
                <div class="eyebrow"><span></span> Installer</div>
                <h2 id="panelTitle">Choose language &amp; region</h2>
                <p id="panelLead">Pick the language your visitors will see and the country your blood bank serves.</p>
                <div class="progress" aria-hidden="true">
                    <span class="on"></span><span></span><span></span><span></span><span></span>
                </div>
            </div>

            <div class="panel-body">
                @if ($errors->any())
                    <div class="alert">
                        <strong>Please fix the highlighted fields.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (! $checks['database']['ok'])
                    <div class="alert soft">
                        <strong>Database not connected.</strong> Update <code>.env</code> with your MySQL database name, user and password, then refresh this page.
                    </div>
                @endif

                <form method="POST" action="{{ route('install.store') }}" id="installForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="step" id="stepField" value="{{ old('step', 1) }}">

                    {{-- Step 1 --}}
                    <section class="step" data-step="1">
                        <h3 class="section-title"><span>১</span> Language &amp; region</h3>

                        <div class="lang-grid" style="margin-bottom: 18px;">
                            <label class="choice">
                                <input type="radio" name="language" value="bn" required @checked(old('language', 'bn') === 'bn')>
                                <span class="choice-box">
                                    <span class="choice-icon">অ</span>
                                    <span><strong>বাংলা</strong><small>বাংলাদেশ ও বাংলাভাষী দর্শকদের জন্য</small></span>
                                </span>
                            </label>
                            <label class="choice">
                                <input type="radio" name="language" value="en" @checked(old('language') === 'en')>
                                <span class="choice-box">
                                    <span class="choice-icon">A</span>
                                    <span><strong>English</strong><small>International visitors and donors</small></span>
                                </span>
                            </label>
                        </div>
                        @error('language')<p class="error">{{ $message }}</p>@enderror

                        <div class="grid">
                            <div class="field">
                                <label for="country">Country <small>দেশ</small></label>
                                <select class="control" id="country" name="country" required>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country }}" @selected(old('country', 'Bangladesh') === $country)>{{ $country }}</option>
                                    @endforeach
                                </select>
                                @error('country')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field">
                                <label for="currency">Currency <small>মুদ্রা</small></label>
                                <input class="control" id="currency" name="currency" value="{{ old('currency', 'BDT') }}" maxlength="10" required placeholder="BDT">
                                @error('currency')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field full">
                                <label for="timezone">Timezone <small>টাইমজোন</small></label>
                                <input class="control" id="timezone" name="timezone" value="{{ old('timezone', 'Asia/Dhaka') }}" list="timezones" required placeholder="Asia/Dhaka">
                                <datalist id="timezones">
                                    @foreach ($timezones as $timezone)
                                        <option value="{{ $timezone }}"></option>
                                    @endforeach
                                </datalist>
                                <p class="hint">Used for OTP expiry, donation dates, blog timestamps and admin activity logs.</p>
                                @error('timezone')<p class="error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="notice" style="margin-top: 17px;">
                            <span><strong>Multi-country ready.</strong> Donors can enter international state/city details. Bangladesh divisions, districts and upazilas are seeded automatically when Bangladesh is selected.</span>
                        </div>
                    </section>

                    {{-- Step 2 --}}
                    <section class="step" data-step="2">
                        <h3 class="section-title"><span>২</span> Site identity</h3>

                        <div class="grid">
                            <div class="field full">
                                <label for="site_name">Site name <small>সাইটের নাম</small></label>
                                <input class="control" id="site_name" name="site_name" value="{{ old('site_name', 'One Blood Bank') }}" maxlength="80" required placeholder="One Blood Bank">
                                @error('site_name')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field full">
                                <label for="site_tagline">Tagline <small>ট্যাগলাইন</small></label>
                                <input class="control" id="site_tagline" name="site_tagline" value="{{ old('site_tagline', 'রক্ত দিন, জীবন বাঁচান') }}" maxlength="160" placeholder="রক্ত দিন, জীবন বাঁচান">
                                @error('site_tagline')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field">
                                <label for="site_email">Contact email <small>যোগাযোগ</small></label>
                                <input class="control" id="site_email" type="email" name="site_email" value="{{ old('site_email') }}" maxlength="120" placeholder="hello@example.com">
                                @error('site_email')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field">
                                <label for="site_phone">Contact phone <small>ফোন</small></label>
                                <input class="control" id="site_phone" name="site_phone" value="{{ old('site_phone') }}" maxlength="30" placeholder="+8801712345678">
                                @error('site_phone')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field full">
                                <label for="site_address">Address <small>ঠিকানা</small></label>
                                <input class="control" id="site_address" name="site_address" value="{{ old('site_address') }}" maxlength="200" placeholder="Dhaka, Bangladesh">
                                @error('site_address')<p class="error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="divider"></div>

                        <div class="grid">
                            <div class="field">
                                <label>Logo <small>লোগো</small></label>
                                <div class="upload">
                                    <div class="upload-preview" id="logoPreview">BB</div>
                                    <div class="upload-copy">
                                        <strong>Upload your logo</strong>
                                        <small>PNG, JPG, WebP or SVG · max 2 MB</small>
                                        <input class="file-input" id="logo" type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                                    </div>
                                </div>
                                @error('logo')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field">
                                <label>Favicon <small>ফ্যাভিকন</small></label>
                                <div class="upload">
                                    <div class="upload-preview" id="faviconPreview">◈</div>
                                    <div class="upload-copy">
                                        <strong>Upload a favicon</strong>
                                        <small>Square PNG or ICO · max 1 MB</small>
                                        <input class="file-input" id="favicon" type="file" name="favicon" accept="image/png,image/jpeg,image/webp,image/svg+xml,image/x-icon">
                                    </div>
                                </div>
                                @error('favicon')<p class="error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <p class="hint" style="margin-top: 12px;">If you skip the uploads, the built-in blood drop mark will be used everywhere.</p>
                    </section>

                    {{-- Step 3 --}}
                    <section class="step" data-step="3">
                        <h3 class="section-title"><span>৩</span> Administrator account</h3>

                        <div class="notice" style="margin-bottom: 17px;">
                            <span><strong>This is the owner account.</strong> It has full access to donors, users, settings, blog posts, donations and activity logs. Use a password you do not reuse anywhere else.</span>
                        </div>

                        <div class="grid">
                            <div class="field full">
                                <label for="admin_name">Full name <small>পূর্ণ নাম</small></label>
                                <input class="control" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" maxlength="120" required placeholder="Your name">
                                @error('admin_name')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field">
                                <label for="admin_username">Username <small>ইউজারনেম</small></label>
                                <input class="control" id="admin_username" name="admin_username" value="{{ old('admin_username') }}" maxlength="30" required placeholder="admin">
                                <p class="hint">Letters, numbers, dashes and underscores. Lowercase only.</p>
                                @error('admin_username')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field">
                                <label for="admin_phone">Phone <small>মোবাইল</small></label>
                                <input class="control" id="admin_phone" name="admin_phone" value="{{ old('admin_phone') }}" maxlength="30" required placeholder="+8801712345678">
                                @error('admin_phone')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field full">
                                <label for="admin_email">Email <small>ইমেইল</small></label>
                                <input class="control" id="admin_email" type="email" name="admin_email" value="{{ old('admin_email') }}" maxlength="255" required placeholder="you@example.com">
                                @error('admin_email')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field">
                                <label for="admin_password">Password <small>পাসওয়ার্ড</small></label>
                                <input class="control" id="admin_password" type="password" name="admin_password" required autocomplete="new-password" placeholder="At least 8 characters">
                                <p class="hint">Minimum 8 characters, including letters and numbers.</p>
                                @error('admin_password')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field">
                                <label for="admin_password_confirmation">Confirm password <small>আবার লিখুন</small></label>
                                <input class="control" id="admin_password_confirmation" type="password" name="admin_password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                            </div>
                        </div>
                    </section>

                    {{-- Step 4 --}}
                    <section class="step" data-step="4">
                        <h3 class="section-title"><span>৪</span> Security &amp; mail</h3>

                        <div class="grid">
                            <label class="switch">
                                <input type="checkbox" name="require_phone_verification" value="1" @checked(old('require_phone_verification', true))>
                                <span><strong>Require phone verification</strong><small>Donors verify their mobile number with a one-time code before they can reveal contacts.</small></span>
                            </label>
                            <label class="switch">
                                <input type="checkbox" name="require_email_verification" value="1" @checked(old('require_email_verification'))>
                                <span><strong>Require email verification</strong><small>Donors must click a signed link before applying. Recommended when email delivery is reliable.</small></span>
                            </label>
                        </div>

                        <div class="divider"></div>

                        <h3 class="section-title">Email delivery</h3>

                        <div class="grid">
                            <div class="field">
                                <label for="mail_driver">Mail driver <small>ড্রাইভার</small></label>
                                <select class="control" id="mail_driver" name="mail_driver">
                                    <option value="log" @selected(old('mail_driver', 'log') === 'log')>Log only — no real email yet</option>
                                    <option value="smtp" @selected(old('mail_driver') === 'smtp')>SMTP — send real email</option>
                                </select>
                                <p class="hint">You can change this later in Admin → Settings → Mail.</p>
                                @error('mail_driver')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field">
                                <label for="mail_from_name">From name <small>প্রেরকের নাম</small></label>
                                <input class="control" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name') }}" maxlength="120" placeholder="One Blood Bank">
                                @error('mail_from_name')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field full">
                                <label for="mail_from_address">From address <small>প্রেরকের ইমেইল</small></label>
                                <input class="control" id="mail_from_address" type="email" name="mail_from_address" value="{{ old('mail_from_address') }}" maxlength="180" placeholder="noreply@example.com">
                                @error('mail_from_address')<p class="error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="smtp {{ old('mail_driver') === 'smtp' ? 'on' : '' }}" id="smtpPanel">
                            <div class="grid">
                                <div class="field">
                                    <label for="mail_host">SMTP host</label>
                                    <input class="control" id="mail_host" name="mail_host" value="{{ old('mail_host') }}" maxlength="180" placeholder="smtp.example.com">
                                    @error('mail_host')<p class="error">{{ $message }}</p>@enderror
                                </div>
                                <div class="field">
                                    <label for="mail_port">Port</label>
                                    <input class="control" id="mail_port" name="mail_port" type="number" min="1" max="65535" value="{{ old('mail_port', 587) }}" placeholder="587">
                                    @error('mail_port')<p class="error">{{ $message }}</p>@enderror
                                </div>
                                <div class="field">
                                    <label for="mail_username">SMTP username</label>
                                    <input class="control" id="mail_username" name="mail_username" value="{{ old('mail_username') }}" maxlength="180" autocomplete="off" placeholder="user@example.com">
                                    @error('mail_username')<p class="error">{{ $message }}</p>@enderror
                                </div>
                                <div class="field">
                                    <label for="mail_password">SMTP password</label>
                                    <input class="control" id="mail_password" type="password" name="mail_password" value="{{ old('mail_password') }}" maxlength="255" autocomplete="new-password" placeholder="••••••••">
                                    @error('mail_password')<p class="error">{{ $message }}</p>@enderror
                                </div>
                                <div class="field">
                                    <label for="mail_encryption">Encryption</label>
                                    <select class="control" id="mail_encryption" name="mail_encryption">
                                        <option value="tls" @selected(old('mail_encryption', 'tls') === 'tls')>TLS (587)</option>
                                        <option value="ssl" @selected(old('mail_encryption') === 'ssl')>SSL (465)</option>
                                        <option value="starttls" @selected(old('mail_encryption') === 'starttls')>STARTTLS</option>
                                    </select>
                                    @error('mail_encryption')<p class="error">{{ $message }}</p>@enderror
                                </div>
                                <div class="field">
                                    <label>Tip</label>
                                    <div class="notice warn" style="padding: 10px 11px;">If unsure, leave mail on “Log only”. You can configure SMTP after logging in.</div>
                                </div>
                            </div>
                        </div>

                        <div class="divider"></div>

                        <label class="switch">
                            <input type="checkbox" name="install_base_data" value="1" @checked(old('install_base_data', true))>
                            <span><strong>Create starting content</strong><small>Adds privacy, terms and about pages so the footer is complete on day one.</small></span>
                        </label>
                    </section>

                    {{-- Step 5 --}}
                    <section class="step" data-step="5">
                        <h3 class="section-title"><span>৫</span> Review &amp; install</h3>

                        <div class="notice" style="margin-bottom: 14px;">
                            <span><strong>You’re one click away.</strong> The installer will run migrations, seed defaults, create your admin account, save these settings, and lock the installer.</span>
                        </div>

                        <div class="summary" id="summary">
                            <div class="summary-row"><span>Site</span><b data-summary="site_name">—</b></div>
                            <div class="summary-row"><span>Language</span><b data-summary="language">—</b></div>
                            <div class="summary-row"><span>Country / currency</span><b data-summary="region">—</b></div>
                            <div class="summary-row"><span>Admin</span><b data-summary="admin">—</b></div>
                            <div class="summary-row"><span>Mail</span><b data-summary="mail">—</b></div>
                            <div class="summary-row"><span>Phone verification</span><b data-summary="phone">—</b></div>
                        </div>

                        <div class="review-checks" style="margin-top: 14px;">
                            @foreach ($checks as $check)
                                <div class="review-check {{ $check['ok'] ? '' : 'bad' }}">
                                    <i></i> {{ $check['label'] }} <b>{{ $check['ok'] ? 'Ready' : 'Fix' }}</b>
                                </div>
                            @endforeach
                        </div>

                        <p class="footnote">
                            By installing you confirm that you own this server and the domain it runs on.
                            <br><b>The installer will be locked automatically.</b>
                        </p>

                        <div class="actions">
                            <button type="button" class="btn btn-ghost" id="backBtn">← Back</button>
                            <button type="submit" class="btn btn-primary" id="installBtn" @disabled(! $checks['database']['ok'])>Install One Blood Bank</button>
                        </div>
                    </section>

                    <div class="actions" id="stepActions">
                        <button type="button" class="btn btn-ghost" id="backBtnBottom" style="visibility: hidden;">← Back</button>
                        <div class="right">
                            <span style="font-size: 10.8px; color: #94a3b8;" id="stepHint">Step 1 of 5</span>
                            <button type="button" class="btn btn-primary" id="nextBtn">Continue →</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <p class="footnote">One Blood Bank · Laravel installer · Your data stays on your server.</p>
</div>

<script>
    (() => {
        const form = document.getElementById('installForm');
        const sessions = Array.from(document.querySelectorAll('.step'));
        const links = Array.from(document.querySelectorAll('.step-link'));
        const progress = Array.from(document.querySelectorAll('.progress span'));
        const stepField = document.getElementById('stepField');
        const stepActions = document.getElementById('stepActions');
        const nextBtn = document.getElementById('nextBtn');
        const backTop = document.getElementById('backBtn');
        const backBottom = document.getElementById('backBtnBottom');
        const stepHint = document.getElementById('stepHint');
        const titles = [
            ['Choose language & region', 'Pick the language your visitors will see and the country your blood bank serves.'],
            ['Make it feel like yours', 'Add your site name, helpful contact details and a logo. You can refine everything later.'],
            ['Create the owner account', 'This administrator can manage donors, staff, settings and every part of the public site.'],
            ['Set trust & delivery', 'Choose how donors verify themselves and how the site sends email and one-time codes.'],
            ['Review before installing', 'One final look. Then the installer creates the database, your account and the lock file.'],
        ];
        const presets = @json($presets);
        const country = document.getElementById('country');
        const currency = document.getElementById('currency');
        const timezone = document.getElementById('timezone');
        const mailDriver = document.getElementById('mail_driver');
        const smtpPanel = document.getElementById('smtpPanel');
        let current = Math.min(Math.max(parseInt(stepField.value || '1', 10), 1), 5);

        const fieldsForStep = (step) => {
            const root = document.querySelector(`.step[data-step="${step}"]`);
            if (! root) return [];

            return Array.from(root.querySelectorAll('input, select, textarea')).filter((field) => {
                if (field.type === 'hidden' || field.type === 'file' || field.disabled) return false;
                return field.required;
            });
        };

        const validateStep = (step) => {
            const fields = fieldsForStep(step);
            for (const field of fields) {
                if (! field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }
            return true;
        };

        const render = () => {
            sessions.forEach((section) => section.classList.toggle('on', Number(section.dataset.step) === current));
            links.forEach((link) => {
                const step = Number(link.dataset.goto);
                link.classList.toggle('active', step === current);
                link.classList.toggle('done', step < current);
                link.querySelector('.step-num').textContent = step < current ? '✓' : String(step);
            });
            progress.forEach((bar, index) => bar.classList.toggle('on', index < current));
            document.getElementById('panelTitle').textContent = titles[current - 1][0];
            document.getElementById('panelLead').textContent = titles[current - 1][1];
            stepField.value = String(current);
            stepActions.style.display = current === 5 ? 'none' : 'flex';
            backBottom.style.visibility = current === 1 ? 'hidden' : 'visible';
            stepHint.textContent = `Step ${current} of 5`;
            if (current === 5) updateSummary();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        const go = (step) => {
            if (step > current && ! validateStep(current)) return;
            current = Math.min(Math.max(step, 1), 5);
            render();
        };

        links.forEach((link) => link.addEventListener('click', () => go(Number(link.dataset.goto))));
        nextBtn.addEventListener('click', () => go(current + 1));
        backTop.addEventListener('click', () => go(current - 1));
        backBottom.addEventListener('click', () => go(current - 1));

        form.addEventListener('submit', (event) => {
            for (let step = 1; step <= 4; step++) {
                if (! validateStep(step)) {
                    event.preventDefault();
                    current = step;
                    render();
                    return;
                }
            }
            stepField.value = '5';
            const button = document.getElementById('installBtn');
            button.disabled = true;
            button.textContent = 'Installing…';
        });

        country.addEventListener('change', () => {
            const preset = presets[country.value];
            if (! preset) return;
            timezone.value = preset[0];
            currency.value = preset[1];
        });

        mailDriver.addEventListener('change', () => {
            smtpPanel.classList.toggle('on', mailDriver.value === 'smtp');
        });

        const preview = (input, target, fallback) => {
            input.addEventListener('change', () => {
                const file = input.files && input.files[0];
                if (! file) return;
                if (! file.type.startsWith('image/')) return;
                const url = URL.createObjectURL(file);
                target.innerHTML = `<img src="${url}" alt="Preview">`;
            });
        };

        preview(document.getElementById('logo'), document.getElementById('logoPreview'), 'BB');
        preview(document.getElementById('favicon'), document.getElementById('faviconPreview'), '◈');

        const updateSummary = () => {
            const value = (name) => (form.querySelector(`[name="${name}"]`)?.value || '').trim();
            const checkedName = (name) => form.querySelector(`[name="${name}"]`)?.checked;
            const language = form.querySelector('[name="language"]:checked')?.value === 'en' ? 'English' : 'বাংলা';
            const mail = mailDriver.value === 'smtp' ? 'SMTP' : 'Log only';

            document.querySelector('[data-summary="site_name"]').textContent = value('site_name') || '—';
            document.querySelector('[data-summary="language"]').textContent = language;
            document.querySelector('[data-summary="region"]').textContent = `${value('country')} · ${value('currency')}`;
            document.querySelector('[data-summary="admin"]').textContent = value('admin_email') || value('admin_username') || '—';
            document.querySelector('[data-summary="mail"]').textContent = mail;
            document.querySelector('[data-summary="phone"]').textContent = checkedName('require_phone_verification') ? 'Required' : 'Optional';
        };

        form.querySelectorAll('input, select').forEach((field) => {
            field.addEventListener('change', () => {
                if (current === 5) updateSummary();
            });
        });

        if (current < 1 || current > 5) current = 1;
        render();
    })();
</script>
</body>
</html>
