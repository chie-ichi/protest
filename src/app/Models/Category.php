<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Restaurant;

class Category extends Model
{
    use HasFactory;

    public function restaurant(){
        return $this->hasMany(Restaurant::class);
    }

    public static function getIdByName($name)
    {
        $category = self::where('name', $name)->first();
        return $category ? $category->id : null;
    }
}
