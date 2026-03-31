<?php

use Illuminate\Support\Facades\Storage;



function getS3Url($filePath, $expiryMinutes = 60)
{
    return (!empty($filePath)) ? Storage::disk('s3')->url($filePath) : '';
}

function upload_file_to_s3($file, $path = 'uploads/cards/')
{
    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
    $filePath = $path . $filename;

    Storage::disk('s3')->putFileAs($path, $file, $filename, 'public');

    return $filePath; // ✅ S3 path
}


function pre($data = null, $exit = true)
{
    echo '<pre>';
    print_r($data);
    echo '<pre>';
    if ($exit)
        exit;
}
