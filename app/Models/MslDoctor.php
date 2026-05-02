<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MslDoctor extends Model
{
    use HasFactory;

    protected $table = 'msl_doctors';
    protected $fillable = ['msl_code', 'name', 'degree'];

        public function user()
    {
        return $this->belongsTo(User::class, 'employee_code', 'position_code');
    }

}

