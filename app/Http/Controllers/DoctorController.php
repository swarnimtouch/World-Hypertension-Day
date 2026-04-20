<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\MslDoctor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class DoctorController extends Controller
{
    private $dayMessages = [
        1 => "Celebrate Day 1 with gratitude and joy!",
        2 => "Day 2 – Keep your heart healthy with exercise.",
        3 => "Day 3 – Stay hydrated, drink water often.",
        4 => "Day 4 – Eat fresh fruits and vegetables.",
        5 => "Day 5 – Spend time with loved ones.",
        6 => "Day 6 – Practice mindfulness & meditation.",
        7 => "Day 7 – A strong heart needs quality sleep.",
        8 => "Day 8 – Take a walk and breathe fresh air.",
        9 => "Day 9 – Laugh more, stress less!",
        10 => "Day 10 – Spread kindness everywhere.",
        11 => "Day 11 – Avoid junk food for a healthy you.",
        12 => "Day 12 – Challenge yourself with fitness.",
        13 => "Day 13 – Celebrate small wins today.",
        14 => "Day 14 – Share positivity with others.",
        15 => "Day 15 – Stay consistent with good habits.",
        16 => "Day 16 – Rest, recharge, rejuvenate.",
        17 => "Day 17 – A healthy mind = healthy heart.",
        18 => "Day 18 – Inspire someone with your story.",
        19 => "Day 19 – Cook a healthy meal at home.",
        20 => "Day 20 – Avoid smoking for a stronger heart.",
        21 => "Day 21 – Take deep breaths, release stress.",
        22 => "Day 22 – Check your blood pressure today.",
        23 => "Day 23 – Stay active, avoid long sitting hours.",
        24 => "Day 24 – Encourage others to eat healthy.",
        25 => "Day 25 – Dance for fun and fitness.",
        26 => "Day 26 – Share knowledge, spread awareness.",
        27 => "Day 27 – Make today a sugar-free day.",
        28 => "Day 28 – Focus on gratitude and peace.",
        29 => "Day 29 – Inspire others with your actions.",
        30 => "Day 30 – Celebrate health, celebrate life!",
        31 => "Celebrate Day 1 with gratitude and joy!",
    ];

    // =========================================================================
    // Show form
    // =========================================================================
    public function create($day)
    {
        $user = Auth::user();
        $empCode = $user->emp_code;
        $doctors = MslDoctor::where('employee_code', $empCode)->get();

        return view('doctor-form', compact('day', 'doctors', 'user'));
    }

    // =========================================================================
    // Store (legacy form submit)
    // =========================================================================
    public function store(Request $request)
    {
        $request->validate([
            'day' => 'required|integer',
            'name' => 'required|string|max:255',
            'speciality' => 'required|string|max:255',
            'hospital' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $bannerPath = $this->createBanner($request->day, $request->name);

        if (!$bannerPath) {
            return back()->with('error', 'Banner not found for Day ' . $request->day);
        }

        Doctor::create([
            'day' => $request->day,
            'name' => $request->name,
            'speciality' => $request->speciality,
            'hospital' => $request->hospital,
            'city' => $request->city,
            'country' => $request->country,
            'banner_path' => str_replace(public_path(), '', $bannerPath),
        ]);

        return redirect()->route('dashboard')->with('success', 'Doctor poster created successfully!');
    }

    // =========================================================================
    // PRIVATE: Build image object with doctor name text — no saving anywhere
    // =========================================================================
    private function buildImage($day, $doctorName, $language = 'english')
    {
        $bannerMap = [
            'english' => "banners/day{$day}.jpg",
            'malayalam' => "banners/malayalam_day{$day}.jpg",
            'kannada' => "banners/World Heart Day Poster_Kannada_page-00{$day}.jpg",
            'tamil' => "banners/tamil_day{$day}.jpg",
            'odia' => "banners/oriya_day{$day}.jpg",
            'punjabi' => "banners/Punjabi_day{$day}.jpg",
            'telugu' => "banners/Telugu_day{$day}.jpg",
            'bengali' => "banners/Bengali_day{$day}.jpg",
            'gujarati' => "banners/Gujarati_day{$day}.jpg",
            'hindi' => "banners/hindi_day{$day}.jpg",
            'marathi' => "banners/marathi_day{$day}.jpg",
        ];

        if (!isset($bannerMap[$language])) return null;

        $bannerPath = public_path($bannerMap[$language]);
        if (!file_exists($bannerPath)) return null;

//        $safeDoctorName = !empty($doctorName) ? 'Dr. ' . trim($doctorName) : '';
        $safeDoctorName = !empty($doctorName) ? trim($doctorName) : '';

        $x = 0;
        $y = 1680;
        $textBoxWidth = 1080;
        $color = '#ba131a';

        if ($language === 'malayalam') {
            $y = 3366;
            $textBoxWidth = 2160;
        } elseif (in_array($language, ['tamil', 'kannada'])) {
            $y = 1690;
            $textBoxWidth = 1084;
        } elseif ($language === 'english') {
            $y = 5024;
            $textBoxWidth = 3240;
        }

        $manager = new ImageManager(new Driver());
        $img = $manager->read($bannerPath);

        if (!empty($safeDoctorName)) {
            $img->text(
                $safeDoctorName,
                $x + ($textBoxWidth / 2),
                $y,
                function ($font) use ($language, $color) {
                    $font->file(public_path('fonts/Poppins-Bold.ttf'));

                    if ($language === 'malayalam') {
                        $font->size(80);
                    } elseif (in_array($language, ['tamil', 'kannada'])) {
                        $font->size(40);
                    } elseif ($language === 'english') {
                        $font->size(120);
                    } else {
                        $font->size(40);
                    }

                    $font->color($color);
                    $font->align('center');
                    $font->valign('top');
                }
            );
        }

        return $img;
    }

    // =========================================================================
    // PRIVATE: Save to LOCAL /public/temp/ — NO S3, NO DB
    // Used only by preview1() for live preview
    // =========================================================================
    private function createBannerTemp($day, $doctorName, $language = 'english')
    {
        $img = $this->buildImage($day, $doctorName, $language);
        if (!$img) return null;

        // Create temp folder if not exists
        $tempDir = public_path('temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Session-based filename so multiple users don't clash
        $sessionId = session()->getId();
        $fileName = "preview_{$sessionId}_day{$day}_{$language}.jpg";
        $filePath = $tempDir . '/' . $fileName;

        $img->toJpeg(80)->save($filePath);

        // Return public URL only — no S3
        return asset('temp/' . $fileName);
    }

    // =========================================================================
    // PRIVATE: Upload to S3 — used only by preview() on download button click
    // =========================================================================
    private function createBanner($day, $doctorName, $language = 'english')
    {
        $img = $this->buildImage($day, $doctorName, $language);
        if (!$img) return null;

//        $safeDoctorName = !empty($doctorName) ? 'Dr. ' . trim($doctorName) : 'no_doctor';
        $safeDoctorName = !empty($doctorName) ? trim($doctorName) : 'no_doctor';

        $fileName = "day{$day}_{$safeDoctorName}_{$language}.jpg";
        $s3Path = "generated/{$fileName}";

        Storage::disk('s3')->put(
            'World-Hypertension-Day/' . $s3Path,
            $img->toJpeg(90),
            'public'
        );

        return Storage::disk('s3')->url('World-Hypertension-Day/' . $s3Path);
    }

    
    public function preview1(Request $request)
    {
        $request->validate([
            'day' => 'required|integer',
            'doctor_name' => 'nullable|string|max:255',
            'degree' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:50',
        ]);

        $day = $request->day;
        $doctorName = $request->doctor_name ?? '';
        $language = $request->language ?? 'english';

        // Local temp only — nothing on S3 or DB
        $previewUrl = $this->createBannerTemp($day, $doctorName, $language);

        if (!$previewUrl) {
            return response()->json(['error' => 'Banner not found for Day ' . $day], 404);
        }

        return response()->json(['path' => $previewUrl]);
    }

    // =========================================================================
    // preview — SAVE & DOWNLOAD
    // Called ONLY when user clicks "Save & Download" button
    // S3 upload + DB store happens here
    // =========================================================================
    public function preview(Request $request)
    {
        $request->validate([
            'day' => 'required|integer',
            'doctor_name' => 'nullable|string|max:255',
            'degree' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:50',
            'hospital' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'doctor_id' => 'nullable|integer',
        ]);

        $day = $request->day;
        $doctorName = $request->doctor_name ?? '';
        $degree = $request->degree;
        $language = $request->language ?? 'english';

        // S3 upload — only on button click
        $outputPath = $this->createBanner($day, $doctorName, $language);

        if (!$outputPath) {
            return response()->json(['error' => 'Banner not found for Day ' . $day], 404);
        }

        $userId = Auth::id();
        $userEmpCode = Auth::user()->emp_code;

        // Update MslDoctor name/degree if doctor_id given
        if ($request->doctor_id) {
            $mslDoctor = MslDoctor::where('id', $request->doctor_id)
                ->where('employee_code', $userEmpCode)
                ->first();
            if ($mslDoctor) {
                $mslDoctor->name = $doctorName;
                $mslDoctor->degree = $degree;
                $mslDoctor->save();
            }
        }

        // DB store — only on button click
        Doctor::create([
            'user_id' => $userId,
            'name' => $doctorName,
            'day' => $day,
            'degree' => $degree,
            'language' => $language,
            'hospital' => $request->hospital,
            'city' => $request->city,
            'country' => $request->country,
            'banner_path' => $outputPath,
        ]);

        return response()->json(['path' => $outputPath]);
    }

    // =========================================================================
    // Legacy: generate (view in browser)
    // =========================================================================
    public function generate(Request $request)
    {
        $request->validate([
            'day' => 'required|integer',
            'doctor_name' => 'required|string|max:255',
        ]);

        $outputPath = $this->createBanner($request->day, $request->input('doctor_name'));

        if (!$outputPath) {
            return back()->with('error', 'Banner not found for Day ' . $request->day);
        }

        return response()->file($outputPath);
    }

    // =========================================================================
    // Legacy: download
    // =========================================================================
    public function download(Request $request)
    {
        $request->validate([
            'day' => 'required|integer',
            'doctor_name' => 'required|string|max:255',
            'speciality' => 'nullable|string|max:255',
            'hospital' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $outputPath = $this->createBanner($request->day, $request->doctor_name);

        if (!$outputPath) {
            return back()->with('error', 'Banner not found for Day ' . $request->day);
        }

        Doctor::updateOrCreate(
            ['name' => $request->doctor_name, 'day' => $request->day],
            [
                'speciality' => $request->speciality ?? 'General Physician',
                'hospital' => $request->hospital,
                'city' => $request->city,
                'country' => $request->country,
                'banner_path' => str_replace(public_path(), '', $outputPath),
            ]
        );

        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->doctor_name);

        return response()->download($outputPath, "{$safeName}_Day{$request->day}.jpg")
            ->deleteFileAfterSend(false);
    }

    // =========================================================================
    // Get doctor name by MSL code
    // =========================================================================
    public function getDoctorName(Request $request)
    {
        $msl_code = $request->input('msl_code');

        if (!$msl_code) {
            return response()->json(['error' => 'MSL code required'], 400);
        }

        $doctor = MslDoctor::where('msl_code', $msl_code)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        return response()->json([
            'doctor_name' => $doctor->name,
            'degree' => $doctor->degree ?? ''
        ]);
    }
}
