<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rate extends Model
{
    use HasFactory;
    protected $fillable = [
        'section',
        'rate_tuktuk',
        'rate_truck',
        'rate_car',
        'rate_bike',
        'rate_bus'

    ];
}
