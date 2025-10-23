<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'category_id',
        'title',
        'slug',
        'thumbnail',
        'video_url',
        'video_path',
        'duration',
        'description',
        'report_link',
        'status',
        'uploaded_at',
        'created_by',
    ];

    protected $dates = ['created_at', 'updated_at', 'uploaded_at'];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getUrlAttribute()
    {
        return url("/videos/{$this->slug}");
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
