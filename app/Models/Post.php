<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    const IS_DRAFT = 0;
    const IS_PUBLIC = 1;

    protected $fillable = [
        'title',
        'content',
        'date',
        'slug',
        'category_id',
        'user_id',
        'status',
        'is_featured',
        'views'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'post_tags',
            'post_id',
            'tag_id'
        )->withTimestamps();
    }

    public function sluggable()
    {
        $this->slug = Str::slug($this->title);
        $this->save();
    }

    public static function add($fields)
    {
        $post = new static();
        $post->fill($fields);
        $post->user_id = 1;
        $post->save();

        return $post;
    }

    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    public function remove()
    {
        $this->removeImage();
        $this->delete();
    }

    public function removeImage()
    {
        if ($this->image != null){
            Storage::delete('public/' . $this->image);
        }
    }

    public function uploadImage($image)
    {
        if ($image == null) {return;}
        $this->removeImage();

        //$filename = str_random(10) . '.' . $image->extension();
        //$image->storeAs('uploads', $filename);
        $filename = Storage::put('public/images', $image);
        $filename = Str::after($filename, 'public/');
        $this->image = $filename;
        $this->save();
    }

    public function getImage()
    {
        if ($this->image == null){
            return '/img/no-image.png';
        }
        return asset('storage/' . $this->image);
    }

    public function setCategory($id)
    {
        if ($id == null) {return;}
        $this->category_id = $id;
        $this->save();
    }

    public function setTags($ids)
    {
        if ($ids == null) {return;}
        $this->tags()->sync($ids);
    }

    public function setDraft()
    {
        $this->status = Post::IS_DRAFT;
        $this->save();
    }

    public function setPublic()
    {
        $this->status = Post::IS_PUBLIC;
        $this->save();
    }

    public function toggleStatus($value)
    {
        if ($value == null){
            return $this->setDraft();
        }
        return $this->setPublic();

    }

    public function setFeatured()
    {
        $this->is_featured = 1;
        $this->save();
    }

    public function setStandart()
    {
        $this->is_featured = 0;
        $this->save();
    }

    public function toggleFeatured($value)
    {
        if ($value == null){
            return $this->setStandart();
        }
        return $this->setFeatured();

    }

    public function getCategoryTitle()
    {
        if ($this->category != null){
            return $this->category->title;
        }
        return 'Нет категории';
    }

    public function getTagsTitles()
    {
        if (!$this->tags->isEmpty()){
            return implode(', ', $this->tags->pluck('title')->all());
        }
        return 'Нет тегов';
    }


}
