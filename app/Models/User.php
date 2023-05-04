<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const IS_BANNED = 1;
    const IS_ACTIVE = 0;


    protected $fillable = [
        'name',
        'email',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
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
        $user = new static();
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
        if ($password != null){
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
        if ($image == null) {return;}
        $this->removeAvatar();

        $filepath = Storage::put('public/avatar', $image);
        $filename = Str::after($filepath, 'public/');
        $this->avatar = $filename;
        $this->save();
    }

    public function removeAvatar()
    {
        if($this->avatar !=null){
            Storage::delete('public/' . $this->avatar);
        }
    }

    public function getImage()
    {
        if ($this->avatar == null){
            return '/img/no-user-image.png';
        }
        return asset('storage/'.$this->avatar);
    }

    public function makeAdmin()
    {
        $this->is_admin = 1;
        $this->save();
    }

    public function makeNormal()
    {
        $this->is_admin = 0;
        $this->save();
    }

    public function toggleAdmin($value)
    {
        if ($value == null){
            return $this->makeNormal();
        }
        return $this->makeAdmin();
    }

    public function ban()
    {
        $this->status = User::IS_BANNED;
        $this->save();
    }

    public function unban()
    {
        $this->status = User::IS_ACTIVE;
    }

    public function toggleBan($value)
    {
        if ($value == null){
            return $this->unban();
        }
        return $this->ban();
    }


}
