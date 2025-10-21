<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'avatar', 'description', 'subscribers',
    ];

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
