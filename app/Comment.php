<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    const IS_ALLOW = 1;
    const IS_DISALLOW = 0;
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function allow()
    {
        $this->status = self::IS_ALLOW;
        $this->save();
    }

    public function disAllow()
    {
        $this->status = self::IS_DISALLOW;
        $this->save();
    }

    public function toggleStatus()
    {
        return ($this->status == 0) ? $this->allow() : $this->disAllow();
    }

    public function remove()
    {
        $this->delete();
    }
}
