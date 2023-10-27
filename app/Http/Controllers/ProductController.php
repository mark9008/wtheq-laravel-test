<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ProductRepository;
use App\Http\Resources\ProductResource;
use App\Http\Responses\APIResponse;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = (new ProductRepository())->list();
        return APIResponse::DataResponse(ProductResource::collection($products));

    }
}
