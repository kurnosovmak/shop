<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Nette\Utils\Random;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('Ru_RU');

        $arIdsCatego=[];
        for ($i=0;$i<50;$i++){
            $cat = new Category();
            $cat->type = $faker->company;
            $cat->save();
            $arIdsCatego[] = $cat->id;
        }

        for ($i=0;$i<50;$i++){
            $product = new Product();
            $product->title = $faker->title;
            $product->body = $faker->text;
//            $product->image = 'https://i.postimg.cc/XvV95vDm/logo.png';
            $product->image = $faker->imageUrl(640 ,480);
            $product->price = $faker->randomFloat(2,10,9999);
            $product->save();

            $procat = new ProductCategory();
            $procat->product_id = $product->id;
            $procat->category_id = $arIdsCatego[rand(0,count($arIdsCatego)-1)];
            $procat->save();

        }
    }
}
