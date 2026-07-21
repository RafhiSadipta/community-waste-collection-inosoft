<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Household extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'households';

    protected $fillable = [
        'owner_name',
        'address',
        'block',
        'no',
    ];
}
