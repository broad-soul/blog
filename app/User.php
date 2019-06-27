<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;
    const IS_ADMIN = 1;
    const IS_NORMAL = 0;
    const IS_ACTIVE = 0;
    const IS_BANNED = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public static function add($fields)
    {
        $user = new static;
        $user->fill($fields);
        $user->save();

        return $user;
    }

    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    public function generatePassword($password)
    {
        if($password != null) {
            $this->password = bcrypt($password);
            $this->save();
        }
    }

    public function remove()
    {
        $this->removeAvatar();
        $this->delete();
    }

    public function uploadAvatar($image)
    {
        if($image == null) return;
        $this->removeAvatar();
        $filename = str_random(10) . '.' . $image->extension();
        $image->storeAs('uploads', $filename);
        $this->avatar = $filename;
        $this->save();
    }

    public function removeAvatar()
    {
        if($this->avatar != null) {
            Storage::delete('/uploads/' . $this->avatar);
        }
    }

    public function getImage()
    {
        return $this->avatar ? '/uploads/' . $this->avatar : '/img/no-image.png';
    }

    public function makeAdmin()
    {
        $this->is_admin = self::IS_ADMIN;
        $this->save();
    }

    public function makeNormal()
    {
        $this->is_admin = self::IS_NORMAL;
        $this->save();
    }

    public function toggleAdmin($value)
    {
        return $value ? $this->makeNormal() : $this->makeAdmin();
    }

    public function ban()
    {
        $this->status = self::IS_BANNED;
        $this->save();
    }

    public function unban()
    {
        $this->status = self::IS_ACTIVE;
        $this->save();
    }

    public function toggleBan($value)
    {
        return $value ? $this->unban() : $this->ban();
    }

    public function checkUserStatus()
    {
        if($this->status == 1) {
            Auth::logout();
            return redirect()->back()->with('login', 'Вы забанены!');
        }
    }
}
