<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Http\Resources\Product as ProductResource;
use App\Product;
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function create(string $model , array $attributes = [] )
    {
        $product = factory('App\\'.$model)->create($attributes);
        return new ProductResource($product);
    }
}
