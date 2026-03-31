<!DOCTYPE html>
<html>
<head><title>Poster Message</title></head>
<body>
<h2>You selected Day {{ $day }}</h2>
<p>Click below to create doctor poster for this day.</p>
<a href="{{ route('doctor.create',$day) }}">Proceed to Doctor Form</a>
</body>
</html>
