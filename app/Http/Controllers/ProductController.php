<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ProductRepository;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\EditProductRequest;
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

    public function show($id): JsonResponse
    {
        $id = (int)$id;
        $product = (new ProductRepository())->get($id);
        return APIResponse::DataResponse(new ProductResource($product));
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $product = (new ProductRepository())->create($request->validated());
        return APIResponse::DataResponse(new ProductResource($product));
    }

    public function update($id, EditProductRequest $request): JsonResponse
    {
        $id = (int)$id;
        $product = (new ProductRepository())->update($id, $request->validated());
        return APIResponse::DataResponse(new ProductResource($product));
    }

    public function destroy($id): JsonResponse
    {
        if (auth('api')->user()->type != 'gold')
            return APIResponse::ErrorsResponse('You are not allowed to delete products', '', status: 403);
        $id = (int)$id;
        if ((new ProductRepository())->delete($id))
            return APIResponse::SuccessResponse('Product deleted successfully');
        return APIResponse::ErrorsResponse('Error deleting product', '', status: 500);
    }
}
