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
        \Log::info(1, [$response->getContent()] );

        // Then  : product exists in database
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

    public function test_can_return_a_product(){
        // given 
           $product  = $this->create('Product');
        // when 
           $response = $this->json('GET','api/products/'.$product->id); 
         // then
           $response->assertStatus(201)
                    ->assertExactJson([
                        'id'=>$product->id,
                        'name' =>$product->name,
                        'slug' => $product->slug,
                        'price'=>$product->price,
                        'created_at' =>$product->created_at
                    ]);
    }

    public function test_will_fail_with_404_product_is_not_found(){
        $response = $this->json('GET', 'api/products/-1');
        $response->assertStatus(404);
    }
}
