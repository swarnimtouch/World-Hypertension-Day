<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>World Hypertension Day</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{asset('css/poster.css')}}" />
    <style>
        .left-side-line {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 80px;
            z-index: 1;
        }
        .left-side-line img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
        .right-side-line {
            position: fixed;
            top: 0;
            right: 0;
            height: 100vh;
            width: 80px;
            z-index: 1;
        }
        .right-side-line img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
<div class="left-side-line">
    <img src="{{ asset('images/Left-Side.png') }}" alt="Left Line" />
</div>
<div class="right-side-line">
    <img src="{{ asset('images/Right-Side.png') }}" alt="Right Line">
</div>

<header class="topbar">
    <div class="topbar-inner">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-logo" />
        </div>
        <div class="welcome"></div>
        <div class="brand1">
            <img src="{{ asset('images/LIPITAS-LOGO.png') }}" alt="Logo" class="brand1-logo" />
        </div>
    </div>
</header>

<h1 class="subtitle" style="text-align:center; margin: 20px 0;">Select a day to generate a doctor poster</h1>

<main class="page">
    <section class="shell">
        <div class="grid">
            <div class="panel" aria-label="controls">

                <div class="step">
                    <div class="step-title">Step 1: Select Day</div>
                    <label class="label" for="day_select">Day</label>
                    <select id="day_select" name="day" class="form-select" required>
                        <option value="">-- Select Day --</option>
                        @for($i=1; $i<=30; $i++)
                            <option value="{{ $i }}">Day {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="step">
                    <div class="step-title">Step 2: Select Doctor</div>
                    <label class="label" for="doctor_name">If you want the poster without a doctor's name, don't select any doctor from the list.</label>
                    <label class="label" for="doctor_name">Search Doctor Name</label>
                    <div class="field with-icon">
                        <select id="doctor_name" class="form-select">
                            <option value="">-- Select Doctor --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" data-name="{{ $doctor->name }}" data-degree="{{ $doctor->degree }}">
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                        {{-- FIX: Removed duplicate id="day" hidden input. Use id="hidden_day" --}}
                        <input type="hidden" id="hidden_day" name="day">
                    </div>
                    <label class="label mt-2">Doctor Name</label>
                    <input type="text" id="doctor_name_text" name="doctor_name" class="form-control mt-2"
                           placeholder="Doctor Name (editable)">
                    <input type="hidden" name="doctor_id" id="doctor_id">
                </div>

                <div class="step">
                    <div class="step-title">Step 3: Additional Information</div>

                    <label class="label" for="language">Language</label>
                    <select id="language" name="language">
                        <option value="bengali">Bengali</option>
                        <option value="english" selected>English</option>
                        <option value="gujarati">Gujarati</option>
                        <option value="hindi">Hindi</option>
                        <option value="kannada">Kannada</option>
                        <option value="malayalam">Malayalam</option>
                        <option value="marathi">Marathi</option>
                        <option value="odia">Odia</option>
                        <option value="punjabi">Punjabi</option>
                        <option value="tamil">Tamil</option>
                        <option value="telugu">Telugu</option>
                    </select>

                    <div class="actions">
                        <button class="btn" id="previewBtn1" type="button">
                            <i class="fa-solid fa-download"></i> Save & Download
                        </button>
                    </div>
                </div>

            </div>

            <div class="preview-card" aria-label="preview">
                <div class="preview-wrap">
                    <div class="step-title">Step 4: Poster Preview</div>
                    <div class="poster-frame">
                        <div class="poster" role="img" aria-label="poster preview">
                            <div class="bar" id="pvDay">Day 8</div>
                            <div class="content">
                                <img
                                    id="posterImage"
                                    src="poster.jpg"
                                    alt="Poster preview image"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="bottom-buttons">
        <button id="backBtn" class="back-btn" type="button" aria-label="Go back">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back
        </button>
        <button id="logoutBtn" class="logout-btn" type="button" aria-label="Logout">
            <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Logout
        </button>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script nonce="{{ $cspNonce }}">
    document.getElementById('backBtn').addEventListener('click', function() {
        window.location.href = "{{ route('dashboard') }}";
    });
    document.getElementById('logoutBtn').addEventListener('click', function() {
        window.location.href = "{{ route('logout') }}";
    });
</script>

<script nonce="{{ $cspNonce }}">
    // Paste this entire <script> block replacing your existing one

    $(document).ready(function () {

        $('#doctor_name').select2({
            placeholder: "Search doctor...",
            allowClear: true
        });

        function formatDoctorName(name) {
            if (!name) return null;
            return name.trim();
        }

        // ── Abort controller so rapid changes cancel the previous preview request ──
        let previewController = null;

        // ── PREVIEW ONLY (no store, no download) ──────────────────────────────────
        function updatePosterPreview() {
            let day      = $('#day_select').val();
            let doctorName = $('#doctor_name_text').val().trim();
            let degree   = $('#degree').val() || '';
            let language = $('#language').val();

            if (!day) return;   // need a day before we can preview

            // Cancel any in-flight preview request
            if (previewController) previewController.abort();
            previewController = new AbortController();

            fetch("{{ route('doctor.banner.preview1') }}", {
                method: 'POST',
                signal: previewController.signal,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    day:         day,
                    doctor_name: formatDoctorName(doctorName),
                    degree:      degree,
                    language:    language,
                    hospital:    '',
                    city:        '',
                    country:     ''
                })
            })
                .then(response => {
                    if (!response.ok) throw new Error('Preview error');
                    return response.json();          // must return JSON, NOT a file
                })
                .then(data => {
                    // data.path = URL of the preview image (e.g. /storage/previews/xxx.jpg)
                    $('#posterImage').attr('src', data.path + '?t=' + Date.now()); // bust cache
                    $('#pvDay').text('Day ' + day);
                })
                .catch(err => {
                    if (err.name === 'AbortError') return; // ignore cancelled requests
                    console.error('Preview failed:', err);
                });
        }

        // Triggers that should ONLY update the preview image
        $('#day_select')       .on('change', updatePosterPreview);
        $('#language')         .on('change', updatePosterPreview);   // language → preview only, NO download
        $('#doctor_name_text') .on('input',  updatePosterPreview);
        $('#degree')           .on('input',  updatePosterPreview);

        $('#doctor_name').on('change', function () {
            let selected = $(this).find(':selected');
            $('#doctor_id')       .val(selected.val());
            $('#doctor_name_text').val(selected.data('name')   || '');
            $('#degree')          .val(selected.data('degree') || '');
            updatePosterPreview();
        });

        // ── SAVE & DOWNLOAD (store + download) — button click ONLY ────────────────
        $('#previewBtn1').on('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            let day        = $('#day_select').val();
            let doctorName = formatDoctorName($('#doctor_name_text').val());
            let language   = $('#language').val();

            if (!day) {
                alert('⚠️ Please select a day');
                return;
            }

            let btn = $('#previewBtn1');
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Downloading...');

            try {
                // 🔹 Step 1: API call (Laravel)
                const response = await fetch("{{ route('doctor.banner.preview') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        day: day,
                        doctor_id: $('#doctor_id').val(),
                        doctor_name: doctorName,
                        language: language,
                        hospital: '',
                        city: '',
                        country: ''
                    })
                });

                if (!response.ok) throw new Error('Store error');

                const data = await response.json();

                // 🔹 Step 2: S3 file fetch (IMPORTANT FIX)
                const fileResponse = await fetch(data.path);

                if (!fileResponse.ok) throw new Error('File download failed');

                const blob = await fileResponse.blob();
                const blobUrl = window.URL.createObjectURL(blob);

                // 🔹 Filename
                let safeName = doctorName
                    ? doctorName.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '')
                    : '';
                let fileName = safeName ? `${safeName}_Day${day}.jpg` : `Day${day}.jpg`;

                // 🔹 Force download
                let a = document.createElement('a');
                a.href = blobUrl;
                a.download = fileName;

                document.body.appendChild(a);
                a.click();
                a.remove();

                window.URL.revokeObjectURL(blobUrl);

                // 🔹 Reset button
                btn.prop('disabled', false).html('<i class="fa-solid fa-download"></i> Save & Download');

                // 🔹 optional reload
                setTimeout(() => window.location.reload(), 5000);

            } catch (error) {
                console.error('Download error:', error);
                alert('Download failed. Please try again.');

                btn.prop('disabled', false).html('<i class="fa-solid fa-download"></i> Save & Download');
            }
        });

        // ── Auto-select day from URL ───────────────────────────────────────────────
        const parts      = window.location.href.split('/');
        const dayFromUrl = parts[parts.length - 1];
        if (dayFromUrl && !isNaN(dayFromUrl)) {
            $('#day_select').val(dayFromUrl).trigger('change');
            $('.day-box').each(function () {
                if ($(this).text().includes(dayFromUrl)) $(this).addClass('active-day');
            });
        }

    });
</script>

</body>
</html>
