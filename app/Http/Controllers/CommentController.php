<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // get all comments of a post
    public function index($id){

        $post = Post::find($id);

        if($post){
            return response()->json([
                'comments' => $post->comments()->with('user:id,name,image')->get()
            ], 200);
        }else{
            return response()->json([
                'message' => 'Post not found!'
            ], 403);
        }
    }


    // Create comment
    public function store(Request $request, $id){

        $post = Post::find($id);

        if($post){

            // validate fields
            $attrs = $request->validate([
                'comment' => 'required|string'
            ]);

            Comment::create([
                'comment' => $attrs['comment'],
                'post_id' => $id,
                'user_id' => auth()->user()->id
            ]);
                
            return response()->json([
                'message' => 'Comment created.'
            ], 200);

        }else{
            return response()->json([
                'message' => 'Post not found!'
            ], 403);
        }
    }

    // update comment
    public function update(Request $request, $id){

        $comment = Comment::find($id);

        if($comment){

            if($comment->user_id != auth()->user()->id){
                return response()->json([
                    'message' => 'Permissions denied.'
                ]);
            }else{
            
                // validate fields
                    $attrs = $request->validate([
                        'comment' => 'required|string'
                    ]);

                    $comment->update([
                        'comment' => $attrs['comment'],
                    ]);
                        
                    return response()->json([
                        'message' => 'Comment updated.'
                    ], 200);
            
                }

        }else{
            return response()->json([
                'message' => 'Comment not found!'
            ], 403);
        }
    }
    
    // delete comment
    public function delete($id){

        $comment = Comment::find($id);

        if($comment){

            if($comment->user_id != auth()->user()->id){
                return response()->json([
                    'message' => 'Permissions denied.'
                ]);
            }else{

                    $comment->delete();

                    return response()->json([
                        'message' => 'Comment deleted.'
                    ], 200);
            
                }

        }else{
            return response()->json([
                'message' => 'Comment not found!'
            ], 403);
        }
    }
}
