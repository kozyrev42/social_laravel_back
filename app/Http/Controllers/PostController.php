<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function createPost(StoreRequest $request)
    {
        // берём данные из запроса, без шляпы и воды
        $data = $request->validated();

        try {
            //  операции с базой данных в блоке "try", будут выполнены как единое целое.
            //  если что-то пойдет не так, все изменения будут откачены
            DB::beginTransaction();

            // Извлекается ID изображения из данных запроса
            $imageId = $data['image_id'];

            //  ID изображения удаляется из данных запроса, так как он больше не требуется
            unset($data['image_id']);

            $data['user_id'] = auth()->id();

            $post = Post::create($data);

            // если предоставлен ID изображения
            if (isset($imageId)) {
                $this->attachImageToPost($post, $imageId);
            }

            PostImage::clearStorage();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['error' =>$exception->getMessage()]);
        }

        return new PostResource($post);
    }

    public function attachImageToPost($post, $imageId)
    {
        $image = PostImage::find($imageId);

        // к записи выбранного изображения, добавляем ID поста, флаг об активности
        $image->update([
            'active' => true,
            'post_id' => $post->id
        ]);
    }
}
