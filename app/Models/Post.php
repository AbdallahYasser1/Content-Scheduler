<?php

namespace App\Models;

use App\Enums\PostStatusEnum;
use App\Traits\HasFilterTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFilterTrait;
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image_url',
        'scheduled_time',
        'status',
    ];

    protected $casts = [
        'status' => PostStatusEnum::class,
    ];


    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'platform_post')
            ->withPivot('platform_status')
            ->withTimestamps();
    }

}
