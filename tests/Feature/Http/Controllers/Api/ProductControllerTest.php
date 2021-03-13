<?php

namespace Tests\Feature\Http\Controller\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;
class ProductControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_create_a_product()
    {
        // Given :  user is authenticated

        // When :  create a product
        $faker =  \Faker\Factory::create();
        $response = $this->json('POST','/api/products', [
           'name' => $name  = $faker->company,
           'slug' => Str::slug($name),
           'price' => $price =random_int(10,200)
        ]);

        // Then  : product exists in d
        $response->assertJsonStructure(['id','name','slug','price','created_at'])->assertJson(
            [ 
            'name' => $name ,
            'slug' => Str::slug($name),
            'price' => $price 
            ]);
        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'name' => $name ,
            'slug' => Str::slug($name),
            'price' => $price 
        ] );
    }
}
