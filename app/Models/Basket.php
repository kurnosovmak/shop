<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    use HasFactory;


    public function basketsItems(){
//        return '123';
        return $this->hasMany(BasketItem::class,'basket_id', 'id');
    }

}
