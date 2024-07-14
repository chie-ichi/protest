<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Restaurant;

class Area extends Model
{
    use HasFactory;

    public function restaurant(){
        return $this->hasMany(Restaurant::class);
    }

    public static function getIdByName($name)
    {
        $area = self::where('name', $name)->first();
        return $area ? $area->id : null;
    }
}
