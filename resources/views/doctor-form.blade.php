<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>World Hypertension Day</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <link rel="stylesheet" href="{{asset('css/poster.css')}}"/>

    <style>
        /* ── Multi-select badge counter ── */
        .selected-count-badge {
            display: inline-block;
            background: #009639;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            border-radius: 12px;
            padding: 2px 10px;
            margin-left: 8px;
            vertical-align: middle;
        }

        /* ── Progress bar area ── */
        #downloadProgress {
            display: none;
            margin-top: 14px;
        }

        #downloadProgress .progress-bar-wrap {
            background: #f0f0f0;
            border-radius: 8px;
            height: 10px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        #downloadProgress .progress-bar-fill {
            height: 100%;
            background: #009639;
            border-radius: 8px;
            transition: width 0.3s ease;
            width: 0%;
        }

        #downloadProgress .progress-label {
            font-size: 13px;
            color: #555;
            text-align: center;
        }

        /* ── Preview note when multiple selected ── */
        #multiPreviewNote {
            display: none;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 13px;
            color: #856404;
            margin-top: 10px;
        }

        /* Select2 multi-select tag styling tweak */
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            border-radius: 6px;
            min-height: 42px;
            padding: 4px 6px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #009639;
            border: none;
            color: #fff;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 13px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            margin-right: 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ffcdd2;
        }
    </style>
</head>
<body>

<header class="topbar">
  <div class="topbar-inner">
    
        <div class="brand1">
      <img src="{{ asset('images/sartel.jpg') }}" alt="Sartel Logo" class="brand1-logo"/>
    </div>

        <div class="brand">
      <img src="{{ asset('images/hypertension day logo.jpg') }}" alt="Hypertension Day Logo"
        class="brand-logo"/>
    </div>

        <div class="profile-dropdown">
            <button class="profile-toggle" type="button" aria-label="Profile Menu">
                <i class="fa-solid fa-circle-user"></i>
                <i class="fa-solid fa-chevron-down" style="font-size: 16px; margin-left: 8px;"></i>
            </button>
            <div class="dropdown-menu">
                <div class="dropdown-header">
                    <p>Welcome,</p>
                    <span class="dropdown-username">{{ $user->name ?? session('emp_code') }}</span>
                </div>
                <button id="logoutBtn" class="dropdown-logout" type="button" aria-label="Logout">
                    <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Logout
                </button>
            </div>
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
                        @for($i=1; $i<=31; $i++)
                            <option value="{{ $i }}">Day {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="step">
                    <div class="step-title">Step 2: Select Doctor(s)</div>
                    <label class="label" for="doctor_name">
                        You can select <strong>multiple doctors</strong> — one poster will be downloaded per doctor.
                        Leave empty for a poster without a doctor name.
                    </label>
                    <label class="label" for="doctor_name">Search &amp; Select Doctor(s)</label>
                    <div class="field with-icon">
                        {{-- multiple attribute added; select2 will handle tags --}}
                        <select id="doctor_name" class="form-select" multiple="multiple">
                            @foreach($doctors as $doctor)
                                <option
                                    value="{{ $doctor->id }}"
                                    data-name="{{ $doctor->name }}"
                                    data-degree="{{ $doctor->degree }}"
                                >
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Shows count badge when >0 selected --}}
                    <div style="margin-top:8px; font-size:13px; color:#555;">
                        Selected: <span id="selectedCountBadge" class="selected-count-badge">0</span> doctor(s)
                    </div>

                    <div id="multiPreviewNote">
                        <i class="fa-solid fa-circle-info"></i>
                        Preview shows the <strong>first selected doctor</strong>. All selected doctors will be
                        downloaded.
                    </div>

                    {{-- Hidden fields kept for single-doctor legacy flow compatibility --}}
                    <input type="hidden" id="hidden_day" name="day">
                    <input type="hidden" name="doctor_id" id="doctor_id">
                    <input type="hidden" id="doctor_name_text" name="doctor_name">
                    <input type="hidden" id="degree" name="degree">
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
                            <i class="fa-solid fa-download"></i> Save &amp; Download
                        </button>
                    </div>

                    {{-- Progress bar (shown during multi-download) --}}
                    <div id="downloadProgress">
                        <div class="progress-bar-wrap">
                            <div class="progress-bar-fill" id="progressBarFill"></div>
                        </div>
                        <div class="progress-label" id="progressLabel">Preparing...</div>
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
    document.getElementById('backBtn').addEventListener('click', function () {
        window.location.href = "{{ route('dashboard') }}";
    });

    // Dono Logout buttons ke liye logic (Header ka dropdown + Bottom ki button)
    document.querySelectorAll('.dropdown-logout, .logout-btn').forEach(function(btn) {
        btn.addEventListener('click', function () {
            window.location.href = "{{ route('logout') }}";
        });
    });

    // Profile Dropdown Toggle Logic (Jo dashboard me tha)
    const profileToggle = document.querySelector('.profile-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    if (profileToggle && dropdownMenu) {
        profileToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        document.addEventListener('click', function (e) {
            if (!profileToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
</script>

<script nonce="{{ $cspNonce }}">
    $(document).ready(function () {

        // ── Init Select2 as MULTI-SELECT ─────────────────────────────────────
        $('#doctor_name').select2({
            placeholder: "Search and select doctor(s)...",
            allowClear: true,
            multiple: true
        });

        // ── Helpers ───────────────────────────────────────────────────────────
        function formatDoctorName(name) {
            return name ? name.trim() : null;
        }

        /**
         * Returns array of selected doctor objects:
         * [{ id, name, degree }, ...]
         * Returns empty array if none selected (→ no-name poster).
         */
        function getSelectedDoctors() {
            let selected = $('#doctor_name').val(); // array of option values (ids as strings)
            if (!selected || selected.length === 0) return [];

            return selected.map(function (id) {
                let opt = $('#doctor_name').find('option[value="' + id + '"]');
                return {
                    id: id,
                    name: opt.data('name') || '',
                    degree: opt.data('degree') || ''
                };
            });
        }

        // ── Abort controller for in-flight preview requests ───────────────────
        let previewController = null;

        // ── PREVIEW — always shows first selected doctor (or blank) ───────────
        function updatePosterPreview() {
            let day = $('#day_select').val();
            let language = $('#language').val();
            if (!day) return;

            let doctors = getSelectedDoctors();
            // Preview uses first doctor's name (or empty for no-name)
            let previewName = doctors.length > 0 ? doctors[0].name : '';

            // Update hidden fields to reflect first selected (legacy compat)
            if (doctors.length > 0) {
                $('#doctor_id').val(doctors[0].id);
                $('#doctor_name_text').val(doctors[0].name);
                $('#degree').val(doctors[0].degree);
            } else {
                $('#doctor_id').val('');
                $('#doctor_name_text').val('');
                $('#degree').val('');
            }

            // Badge count
            $('#selectedCountBadge').text(doctors.length);

            // Show/hide multi-note
            if (doctors.length > 1) {
                $('#multiPreviewNote').show();
            } else {
                $('#multiPreviewNote').hide();
            }

            // Cancel previous in-flight preview
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
                    day: day,
                    doctor_name: formatDoctorName(previewName),
                    degree: doctors.length > 0 ? doctors[0].degree : '',
                    language: language,
                    hospital: '',
                    city: '',
                    country: ''
                })
            })
                .then(r => {
                    if (!r.ok) throw new Error('Preview error');
                    return r.json();
                })
                .then(data => {
                    $('#posterImage').attr('src', data.path + '?t=' + Date.now());
                    $('#pvDay').text('Day ' + day);
                })
                .catch(err => {
                    if (err.name === 'AbortError') return;
                    console.error('Preview failed:', err);
                });
        }

        // ── Event listeners for preview ───────────────────────────────────────
        $('#day_select').on('change', updatePosterPreview);
        $('#language').on('change', updatePosterPreview);
        $('#doctor_name').on('change', updatePosterPreview);   // fires on add/remove in multi-select

        // ── Helper: download one poster for a single doctor ───────────────────
        async function downloadOneDoctor(day, language, doctor) {
            /**
             * doctor = { id, name, degree } or null/undefined for no-name poster
             */
            let doctorName = doctor ? formatDoctorName(doctor.name) : null;
            let doctorId = doctor ? doctor.id : '';
            let degree = doctor ? doctor.degree : '';

            const response = await fetch("{{ route('doctor.banner.preview') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    day: day,
                    doctor_id: doctorId,
                    doctor_name: doctorName,
                    degree: degree,
                    language: language,
                    hospital: '',
                    city: '',
                    country: ''
                })
            });

            if (!response.ok) throw new Error('Store error for ' + (doctorName || 'no-name'));

            const data = await response.json();

            // Fetch the actual file from S3
            const fileResponse = await fetch(data.path);
            if (!fileResponse.ok) throw new Error('File download failed for ' + (doctorName || 'no-name'));

            const blob = await fileResponse.blob();
            const blobUrl = window.URL.createObjectURL(blob);

            // Build filename
            let safeName = doctorName
                ? doctorName.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '')
                : '';
            let fileName = safeName ? `${safeName}_Day${day}.jpg` : `Day${day}.jpg`;

            // Trigger download
            let a = document.createElement('a');
            a.href = blobUrl;
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            a.remove();

            // Small delay before revoking so browser can start the download
            setTimeout(() => window.URL.revokeObjectURL(blobUrl), 1000);
        }

        // ── SAVE & DOWNLOAD button ────────────────────────────────────────────
        $('#previewBtn1').on('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            let day = $('#day_select').val();
            let language = $('#language').val();

            if (!day) {
                alert('⚠️ Please select a day');
                return;
            }

            let doctors = getSelectedDoctors();

            // If no doctor selected → download single no-name poster
            let downloadList = doctors.length > 0 ? doctors : [null];

            let btn = $('#previewBtn1');
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Downloading...');

            // Show progress bar
            let total = downloadList.length;
            $('#downloadProgress').show();
            $('#progressBarFill').css('width', '0%');
            $('#progressLabel').text('Starting download...');

            let failed = [];

            for (let i = 0; i < downloadList.length; i++) {
                let doctor = downloadList[i];
                let label = doctor ? ('Dr. ' + doctor.name) : 'Poster (no name)';

                $('#progressLabel').text(`Downloading ${i + 1} of ${total}: ${label}`);
                $('#progressBarFill').css('width', Math.round(((i) / total) * 100) + '%');

                try {
                    await downloadOneDoctor(day, language, doctor);

                    // Small gap between consecutive downloads so browser doesn't block them
                    if (i < downloadList.length - 1) {
                        await new Promise(res => setTimeout(res, 800));
                    }
                } catch (err) {
                    console.error('Download failed for', label, err);
                    failed.push(label);
                }
            }

            // Complete progress bar
            $('#progressBarFill').css('width', '100%');

            if (failed.length > 0) {
                $('#progressLabel').text('⚠️ Some downloads failed: ' + failed.join(', '));
                alert('⚠️ Download failed for:\n' + failed.join('\n') + '\n\nPlease try again.');
            } else {
                $('#progressLabel').text(`✅ All ${total} poster(s) downloaded successfully!`);
            }

            btn.prop('disabled', false).html('<i class="fa-solid fa-download"></i> Save &amp; Download');

            // Hide progress bar after a moment
            setTimeout(() => {
                $('#downloadProgress').fadeOut(500);
            }, 3000);
        });

        // ── Auto-select day from URL ──────────────────────────────────────────
        const parts = window.location.href.split('/');
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
