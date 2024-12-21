<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = [
        'id_board',
        'room_type_id',
        'code',
        'remark',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}