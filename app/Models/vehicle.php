<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        
        
        'reg_number_plate',
        'vehicle_type',
        'client_ref',
        
    ];
}
