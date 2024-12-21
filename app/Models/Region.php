<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regions';

    protected $fillable = [
        'region_id',
        'code',
        'remark',
        'country',
    ];

    public function getRouteKeyName()
    {
        return 'region_id';
    }
}
