<?php

namespace App\Models;

use App\Models\Like;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'body',
        'image'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function comments(){
        return $this->hasMany(Comment::class);
    }
    
    public function likes(){
        return $this->hasMany(Like::class);
    }

}
