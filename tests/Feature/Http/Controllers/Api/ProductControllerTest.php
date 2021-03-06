<?php

namespace Tests\Feature\Http\Controller\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;
class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
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

    public function test_will_fail_with_404_if_user_product_we_want_to_update_is_not_found(){
        $response = $this->json('PUT', 'api/products/-1');
        $response->assertStatus(404);
    }

    public function test_can_update_a_product(){
        $product = $this->create('Product');
        $response =$this->json('PUT','api/products/'.$product->id,[
            'name' =>$product->name.'_updated',
            'slug' =>Str::slug( $product->name.'_updated'),
            'price'=>(int) $product->price + 10
        ]);
        $response->assertStatus(200);

        $response->assertExactJson([
            'id' => $product->id,
            'name' =>$product->name.'_updated',
            'slug' =>Str::slug( $product->name.'_updated'),
            'price'=> (int) $product->price +10,
            'created_at' =>  $product->created_at
        ]);
    }

    public function test_will_fail_with_404_if_user_product_we_want_to_delete_is_not_found(){
        $response = $this->json('DELETE', 'api/products/-1');
        $response->assertStatus(404);
    }
    public function test_can_delete_a_product(){
        $product = $this->create('Product');
        $response = $this->json('DELETE', 'api/products/'.$product->id);
        $response->assertStatus(204)->assertSee(null);
        $this->assertDatabaseMissing('products', ['id',$product->id]);
    }

    public function test_can_return_a_collection_of_paginated_products(){
        $product1 = $this->create('Product');
        $product2 = $this->create('Product');
        $product3 = $this->create('Product');
        $response = $this->Json('GET','/api/products');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data'=>[
                        '*'=> [ 'id','name','slug','price','created_at']
                     ],
                     'links' => ['first','last','prev','next'],
                     'meta' => ['current_page','last_page','from','to','path','per_page','total']
                 ]);
    }
}
