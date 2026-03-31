<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
    'name', 'day', 'degree', 'language', 'hospital', 'city', 'country', 'user_id', 'banner_path'
];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
