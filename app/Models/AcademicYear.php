<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{


    protected $table = 'academic_years'; // match Oracle table name
    protected $primaryKey = 'ay_id';
    public $timestamps = false; // if Oracle table doesn’t use created_at/updated_at
}
