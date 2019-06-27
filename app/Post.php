<?php

namespace App;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use Sluggable;
    protected $fillable = ['title', 'content', 'date', 'description'];
    const IS_PUBLIC = 0;
    const IS_DRAFT = 1;
    const IS_FEATURED = 1;
    const IS_STANDART = 0;
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getCategoryTitle()
    {
        return ($this->category != null)
                ?   $this->category->title
                :   'Нет категории';
    }

    public function getTagsTitles()
    {
        return (!$this->tags->isEmpty())
            ?   implode(', ' , $this->tags->pluck('title')->all())
            :   'Нет тегов';
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'post_tags',
            'post_id',
            'tag_id'
        );
    }
    
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public static function add($fields)
    {
        $post = new static;
        $post->fill($fields);
        $post->user_id = Auth::user()->id;
        $post->save();
        return $post;
    }

    public function edit($fields)
    {
        $this->sluggable();
        $this->fill($fields);
        $this->save();
    }

    public function uploadImage($image)
    {
        if($image == null) return;
        $this->removeImage();
        $filename = str_random(10) .'.'. $image->extension();
        $image->storeAs('uploads', $filename);
        $this->image = $filename;
        $this->save();
    }

    public function remove()
    {
        $this->removeImage();
        $this->delete();
    }

    public function removeImage()
    {
        if($this->image != null) {
            Storage::delete('/uploads/' . $this->image);
        }
    }

    public function getImage()
    {
        return $this->image ? '/uploads/' . $this->image : '/img/no-image.png';
    }

    public function setCategory($id)
    {
        if($id == null) return;
        $this->category_id = $id;
        $this->save();
    }

    public function setTags($ids)
    {
//        if($ids == null) return;
        $this->tags()->sync($ids);
    }

    public function setDraft()
    {
        $this->status = self::IS_DRAFT;
        $this->save();
    }

    public function setPublic()
    {
        $this->status = self::IS_PUBLIC;
        $this->save();
    }

    public function toggleStatus($value)
    {
        return ($value != null) ?  $this->setDraft() : $this->setPublic();
    }
//    ===========================
    public function setFeatured()
    {
        $this->is_featured = self::IS_FEATURED;
        $this->save();
    }

    public function setStandart()
    {
        $this->is_featured = self::IS_STANDART;
        $this->save();
    }

    public function toggleFeatured($value)
    {
        return ($value == null) ? $this->setStandart() : $this->setFeatured();
    }

    public function setDateAttribute($value)
    {
        $date = Carbon::createFromFormat('d/m/y', $value)->format('Y-m-d');
        $this->attributes['date'] = $date;
    }

    public function getDateAttribute($value)
    {
        $date = Carbon::createFromFormat('Y-m-d', $value)->format('d/m/y');
        return $date;
    }

    public function getCategoryID()
    {
        return $this->category != null ? $this->category->id : null;
    }

    public function getDate()
    {
        return Carbon::createFromFormat('d/m/y', $this->date)->format('F d, Y');
    }

    public function hasPrevios()
    {
        return self::where('id', '<', $this->id)->max('id');
    }

    public function getPrevios()
    {
        $postID = $this->hasPrevios();
        return self::find($postID);
    }

    public function hasNext()
    {
        return self::where('id', '>', $this->id)->min('id');
    }

    public function getNext()
    {
        $postID = $this->hasNext();
        return self::find($postID);
    }

    public function related()
    {
        return self::all()->except($this->id);
    }

    public function hasCategory()
    {
        return $this->category != null ? true : false;
    }

    public static function getPopularPosts($limit)
    {
        return self::orderBy('views', 'desc')->take($limit)->get();
    }

    public static function featuredPosts($limit)
    {
        return self::where('is_featured', 1)->take($limit)->get();
    }

    public static function recentPosts($limit)
    {
        return self::orderBy('date', 'desc')->take($limit)->get();
    }

    public function getComments()
    {
        return $this->comments()->where('status', 1)->get();
    }
}
