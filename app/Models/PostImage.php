<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PostImage extends Model
{
    protected $table = "post_images";

    // разрешаем запись во все поля
    protected $guarded = false;

    // функциональность "Accessors & Mutators"
    // будет автоматически вызываться при обращении к атрибуту "->url" экземпляра PostImage
    public function getUrlAttribute()
    {
        return url('storage/' . $this->path);
    }

    // предназначен для очистки изображений, которые не прикреплены к какому-либо посту
    public static function clearStorage()
    {
        // выбираем изображения, принадлежащие текущему аутентифицированному
        $images = PostImage::where('user_id', auth()->id())
            ->whereNull('post_id')->get();

        foreach ($images as $image) {
            // удаление физического файла изображения
            Storage::disk('public')->delete($image->path);

            // удаление записи из базы данных
            $image->delete();
        }
    }
}
