<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ProductRepository;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\EditProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Responses\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // get active_only query parameter
        $active_only = $request->query('active_only', true);
        // get products from repository
        $products = (new ProductRepository())->list($active_only);
        // return products
        return APIResponse::DataResponse(ProductResource::collection($products));

    }

    public function show($id): JsonResponse
    {
        $id = (int)$id;
        $product = (new ProductRepository())->get($id);
        return APIResponse::DataResponse(new ProductResource($product));
    }

    public function searchByIds(Request $request): JsonResponse
    {
        $ids = $request->query('ids', '');
        $ids = explode(',', $ids);
        $products = (new ProductRepository())->searchByIds($ids);
        return APIResponse::DataResponse(ProductResource::collection($products));
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
