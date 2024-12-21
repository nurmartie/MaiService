<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = [
        'id_room',
        'hotel_id',
        'code',
        'remark',
        'quota',
        'on_request',
        'min_paid_adult',
        'max_adult',
        'max_child_age',
        'description',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function boards()
    {
        return $this->hasMany(Board::class);
    }
}