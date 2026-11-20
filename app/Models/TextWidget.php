<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextWidget extends Model
{
    /** @use HasFactory<\Database\Factories\TextWidgetFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'image',
        'title',
        'content',
        'active'
    ];

    public static function getTitle(string $key): string
    {
        return self::findActive($key)->title ?? '';
    }

    public static function getContent(string $key): string
    {
        return self::findActive($key)->content ?? '';
    }

    private static function findActive(string $key): ?self
    {
        return self::query()->where('active', true)->where('key', $key)->first();
    }
}
