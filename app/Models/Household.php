<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Household extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'households';

    protected $fillable = [
        'owner_name',
        'address',
        'block',
        'no',
    ];
}
