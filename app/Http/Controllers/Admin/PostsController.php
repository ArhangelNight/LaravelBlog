<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $posts = Post::all();
        return  view('admin.posts.index', ['posts'=>$posts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(): View
    {
        $tags = Tag::query()->pluck('title', 'id');
        $categories = Category::all();
        return view('admin.posts.create', ['categories' => $categories, 'tags' => $tags]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'=>'required',
            'content'=>'required',
            'date'=>'required',
            'image'=>'nullable|image'
        ]);

        $post = Post::add($request->all());
        $post->sluggable();
        $post->uploadImage($request->file('image'));
        $post->setCategory($request->get('category_id'));
        $post->setTags($request->get('tags'));
        $post->toggleStatus($request->get('status'));
        $post->toggleFeatured($request->get('is_featured'));

        return redirect()->route('posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $post = Post::find($id);
        $tags = Tag::query()->pluck('title', 'id');
        $categories = Category::all();
        return view('admin.posts.edit', compact(
            'categories',
            'tags',
        'post'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
