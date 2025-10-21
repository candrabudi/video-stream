<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoView extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['video_id', 'ip_address', 'user_agent', 'viewed_at'];

    protected $dates = ['viewed_at'];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
