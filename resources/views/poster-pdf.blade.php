<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; text-align: center; }
        .banner img { width: 100%; }
    </style>
</head>
<body>
    <h2>Day {{ $data['day'] }}</h2>
    <div class="banner">
        <img src="{{ public_path('banners/day'.$data['day'].'.jpg') }}" alt="Banner">
    </div>
    <h3>{{ $data['doctor_name'] ?? '' }} {{ $data['doctor_degree'] ?? '' }}</h3>
    <p>{{ $data['language'] ?? '' }}</p>
</body>
</html>
