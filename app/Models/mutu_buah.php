<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mutu_buah extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'mutu_buah';
}
