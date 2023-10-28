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
    /**
     * Display a list of the products.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // get active_only query parameter
        $active_only = $request->query('active_only', true);

        // get products from repository
        $productRepo = new ProductRepository();
        $products = $productRepo->list($active_only);

        // return products
        return APIResponse::DataResponse(ProductResource::collection($products));

    }

    /**
     * Display the specified product.
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        // Cast id to integer
        $id = (int)$id;

        // Get product from the repository
        $productRepo = new ProductRepository();
        $product = $productRepo->get($id);

        // Return a data response with the ProductResource
        return APIResponse::DataResponse(ProductResource::make($product));

    }

    /**
     * Search products by ids.
     * @param Request $request
     * @return JsonResponse
     */
    public function searchByIds(Request $request): JsonResponse
    {
        // Get the 'ids' query parameter and default to an empty string
        $ids = $request->query('ids', '');

        // Explode the comma-separated ids into an array
        $idsArray = explode(',', $ids);

        // Get products from the repository
        $productRepo = new ProductRepository();
        $products = $productRepo->searchByIds($idsArray);

        // Return a data response with the ProductResource collection
        return APIResponse::DataResponse(ProductResource::collection($products));

    }

    /**
     * Search products by name.
     * @param CreateProductRequest $request
     * @return JsonResponse
     */
    public function store(CreateProductRequest $request): JsonResponse
    {
        // Get validated data
        $productData = $request->validated();

        // Create productRepository instance and create product
        $productRepo = new ProductRepository();
        $createdProduct = $productRepo->create($productData);

        // Return a data response with the created product
        return APIResponse::DataResponse(ProductResource::make($createdProduct));
    }

    /**
     * Update the specified product.
     * @param string $id
     * @param EditProductRequest $request
     * @return JsonResponse
     */
    public function update(string $id, EditProductRequest $request): JsonResponse
    {
        // cast id to integer
        $id = (int)$id;
        // get validated data
        $productData = $request->validated();

        // Check if image is uploaded
        if (isset($productData['image'])) {
            // Store avatar in storage/public/avatars folder and return its path
            $productData['image'] = $productData['image']->store('products', 'public');
        }

        //create productRepository instance and update product
        $productRepo = new ProductRepository();
        $product = $productRepo->update($id, $productData);

        // return a data response with the updated product
        return APIResponse::DataResponse(ProductResource::make($product));
    }

    /**
     * delete the specified product by id.
     * @param string $id
     * @return JsonResponse
     */
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
        $productRepo = new ProductRepository();
        $deleted = $productRepo->delete($id);

        if ($deleted) {
            // return success response if product deleted successfully
            return APIResponse::SuccessResponse('Product deleted successfully');
        }

        // return error response if product not deleted
        return APIResponse::ErrorsResponse('Error deleting product', '');
    }
}
