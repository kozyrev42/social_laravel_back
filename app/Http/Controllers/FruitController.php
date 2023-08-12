<?php

namespace App\Http\Controllers;

use App\Http\Resources\FruitResource;
use App\Models\Fruit;

class FruitController
{
    // метод для получения фруктов
    public function getFruits()
    {
        $fruits = Fruit::all();

        return FruitResource::collection($fruits);
    }
}
