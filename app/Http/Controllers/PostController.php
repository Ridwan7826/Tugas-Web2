<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get posts
        $posts = Post::latest()->paginate(5);

        // Render view with posts
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate form
        $request->validate([
            'image'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'   => 'required|min:5',
            'content' => 'required|min:10'
        ]);

        // Upload image
        $image = $request->file('image');
        $imageName = $image->hashName();
        Storage::putFileAs('public/posts', $image, $imageName);

        // Create post
        Post::create([
            'image'   => $imageName,
            'title'   => $request->title,
            'content' => $request->content
        ]);

        // Redirect to index with success message
        return redirect()->route('posts.index')->with('success', 'Data Berhasil Disimpan!');
    }

    /**
     * Display the specified post.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Get post by ID
        $post = Post::findOrFail($id);

        // Return view
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     *
     * @param  Post  $post
     * @return \Illuminate\View\View
     */
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Post  $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Post $post)
    {
        // Validate form
        $request->validate([
            'image'   => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'   => 'required|min:5',
            'content' => 'required|min:10'
        ]);

        // Check if image is uploaded
        if ($request->hasFile('image')) {
            // Upload new image
            $image = $request->file('image');
            $imageName = $image->hashName();
            Storage::putFileAs('public/posts', $image, $imageName);

            // Delete old image
            Storage::delete('public/posts/'.$post->image);

            // Update post with new image
            $post->update([
                'image'   => $imageName,
                'title'   => $request->title,
                'content' => $request->content
            ]);
        } else {
            // Update post without image
            $post->update([
                'title'   => $request->title,
                'content' => $request->content
            ]);
        }

        // Redirect to index with success message
        return redirect()->route('posts.index')->with('success', 'Data Berhasil Diubah!');
    }

    /**
     * Remove the specified post from storage.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Post $post)
    {
        // Delete image
        Storage::delete('public/posts/'.$post->image);

        // Delete post
        $post->delete();

        // Redirect to index with success message
        return redirect()->route('posts.index')->with('success', 'Data Berhasil Dihapus!');
    }
}