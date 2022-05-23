<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public $fillable = [
        'name'
    ];

    public function posts()
    {
        $this->morphedByMany(Post::class, 'taggable');
    }

    public function movies()
    {
        $this->morphedByMany(Movie::class, 'taggable');
    }
}
