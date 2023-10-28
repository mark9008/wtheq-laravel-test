<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ProductRepository;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\EditProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Responses\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // get active_only query parameter
        $active_only = $request->query('active_only', true);

        // get products from repository
        $productRepository = new ProductRepository();
        $products = $productRepository->list($active_only);

        // return products
        return APIResponse::DataResponse(ProductResource::collection($products));

    }

    public function show($id): JsonResponse
    {
        // Cast id to integer
        $id = (int)$id;

        // Get product from the repository
        $productRepository = new ProductRepository();
        $product = $productRepository->get($id);

        // Return a data response with the ProductResource
        return APIResponse::DataResponse(new ProductResource($product));

    }

    public function searchByIds(Request $request): JsonResponse
    {
        // Get the 'ids' query parameter and default to an empty string
        $ids = $request->query('ids', '');

        // Explode the comma-separated ids into an array
        $idsArray = explode(',', $ids);

        // Get products from the repository
        $productRepository = new ProductRepository();
        $products = $productRepository->searchByIds($idsArray);

        // Return a data response with the ProductResource collection
        return APIResponse::DataResponse(ProductResource::collection($products));

    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        // Get validated data
        $productData = $request->validated();

        // Create productRepository instance and create product
        $productRepository = new ProductRepository();
        $createdProduct = $productRepository->create($productData);

        // Return a data response with the created product
        return APIResponse::DataResponse(new ProductResource($createdProduct));
    }


    public function update($id, EditProductRequest $request): JsonResponse
    {
        // cast id to integer
        $id = (int)$id;
        // get validated data
        $productData = $request->validated();

        //create productRepository instance and update product
        $productRepository = new ProductRepository();
        $product = $productRepository->update($id, $productData);

        // return a data response with the updated product
        return APIResponse::DataResponse(new ProductResource($product));
    }

    public function destroy($id): JsonResponse
    {
        // get authenticated user type
        $userType = auth('api')->user()->type;

        // check if user type is not gold
        if ($userType != 'gold') {
            // return error response with 403 status code (Forbidden)
            return APIResponse::ErrorsResponse('You are not allowed to delete products', '', status: Response::HTTP_FORBIDDEN);
        }
        // cast id to integer
        $id = (int)$id;

        // create productRepository instance and delete product
        $productRepository = new ProductRepository();
        $deleted = $productRepository->delete($id);

        if ($deleted) {
            // return success response if product deleted successfully
            return APIResponse::SuccessResponse('Product deleted successfully');
        }
        // return error response if product not deleted
        return APIResponse::ErrorsResponse('Error deleting product', '', status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
