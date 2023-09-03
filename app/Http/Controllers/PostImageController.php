<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostImage\StoreRequest;
use App\Http\Resources\PostImage\PostImageResource;
use App\Models\PostImage;
use Illuminate\Support\Facades\Storage;

class PostImageController extends Controller
{
    public function saveImage(StoreRequest $request)
    {
        // сохраняем загруженное изображение в директорию /images на диске "storage/app/public"
        // метод "put" возвращает путь к сохраненному файлу, который присваивается переменной $path
        $path = Storage::disk('public')->put('/images', $request['image']);

        // создает новую запись в базе данных, отдаём на запись 2 параметра
        $image = PostImage::create([
            'path' => $path,
            'user_id' => auth()->id(),
        ]);

        // преобразуем экземпляр модели $image, в JSON с использованием структуры, определенной в PostImageResource
        return new PostImageResource($image);
    }
}
