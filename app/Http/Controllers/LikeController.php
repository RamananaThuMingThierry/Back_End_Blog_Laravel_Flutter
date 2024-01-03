<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function likeOrDislike($id){

        $post = Post::find($id);

        if($post){

            $like = $post->likes()->where('user_id', auth()->user()->id)->first();

            // if not liked the like
            if(!$like){

                Like::create([
                    'post_id' => $id,
                    'user_id' => auth()->user()->id
                ]);

                return response()->json([
                    'message' => 'Liked'
                ], 200);
            }

            // else dislike it
            $like->delete();

            return response()->json([
                'message' => 'Disliked'
            ], 200);
        }else{
            return response()->json([
                'message' => 'Post not found.'
            ], 403);
        }
    }
}
