<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'id_hotel',
        'code',
        'remark',
        'region_id',
        'main_region_id',
        'category_id',
        'address',
    ];

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }
}