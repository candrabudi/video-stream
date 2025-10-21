<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'status',
        'uploaded_at',
        'views_count',
        'meta_title',
        'meta_description',
        'keywords',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($video) {
            if (empty($video->slug)) {
                $video->slug = Str::slug($video->title.'-'.Str::random(5));
            }

            if (empty($video->meta_title)) {
                $video->meta_title = $video->title;
            }

            if (empty($video->meta_description) && $video->description) {
                $video->meta_description = Str::limit(strip_tags($video->description), 155);
            }
        });
    }

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

    public function views()
    {
        return $this->hasMany(VideoView::class);
    }

    public function addView($ip = null, $userAgent = null)
    {
        $this->views()->create([
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        $this->increment('views_count');
    }

    public function totalViews()
    {
        return $this->views_count;
    }

    public function getUrlAttribute()
    {
        return url("/videos/{$this->slug}");
    }

    public function seoMeta()
    {
        return [
            'title' => $this->meta_title ?? $this->title,
            'description' => $this->meta_description ?? Str::limit(strip_tags($this->description), 155),
            'keywords' => $this->keywords ?? '',
            'url' => $this->url,
            'image' => $this->thumbnail ? url($this->thumbnail) : null,
        ];
    }
}
