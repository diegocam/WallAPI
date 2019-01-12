<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $appends = ['updated_when'];
    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUpdatedWhenAttribute($value)
    {
        return $this->updated_at->diffForHumans();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
