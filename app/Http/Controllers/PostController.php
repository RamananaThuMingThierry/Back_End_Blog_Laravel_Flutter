<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // get all posts

    public function index(){

        $posts = Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
        ->with('likes', function($like){
            return $like->where('user_id', auth()->user()->id)
            ->select('id','user_id', 'post_id')->get(); 
        })
        ->get();
        
        return response()->json([
            'posts' => $posts 
        ], 200);
    }

    // get single post
    public function show($id){
        
        
        $verifier_post = Post::find($id);

        if($verifier_post){
            $post = Post::where('id', $id)->with('user:id,name,image')->withCount('comments', 'likes')->get();

            return response()->json([
                'post' => $post
            ], 200);

        }else{
            return response()->json([
                'message' => 'Post not found!'
            ]);
        }
    }

    // create a post
    public function store(Request $request){
        
        // validate fields
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);
        
        $image = $this->saveImage($request->image, 'posts');
        
        $post = Post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);

        // for now skip for post image

        return response()->json([
            'message' => 'Post Created',
            'post' => $post
        ], 200);
        
    }
    
    // update a post
    public function update(Request $request, $id){
        
        $post = Post::find($id);

        if($post){

            if($post->user_id != auth()->user()->id){
                return response()->json([
                    'message' => 'Permission denied.'
                ], 403);

            }else{
                
                // validate fields
                $attrs = $request->validate([
                    'body' => 'required|string'
                ]);


                $post->update([
                    'body' => $attrs['body'],
                ]);

                // for now skip for post image

                return response()->json([
                    'message' => 'Post Update',
                    'post' => $post
                ], 200);

            }

        }else{
            return response()->json([
                'message' => 'Post not found.'
            ], 403);
        }
    }

    // Delete post
    public function delete($id){
        $post = Post::find($id);

        if($post){

            if($post->user_id != auth()->user()->id){
                return response()->json([
                    'message' => 'Permission denied.'
                ], 403);

            }else{
            
                $post->comments()->delete();
                $post->likes()->delete();
                $post->delete();
                
                return response()->json([
                    'message' => 'Post Deleted.'
                ], 200);

            }


        }else{
            return response()->json([
                'message' => 'Post not found.'
            ], 403);
        }
    }
}
