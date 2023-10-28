<?php

namespace App\Http\Repositories;

use App\Models\Product;

class ProductRepository
{
    /** Product variable to store product model
     * @var Product
     */
    protected Product $product;

    /** List function to list all products
     * @param bool $active // if true, only active products will be returned
     */
    public function list(bool $active = true)
    {
        // Create a query builder instance
        $query = Product::query();

        // If active is true, only active products will be returned
        if ($active) {
            // Add where clause to the query to apply the filter
            $query->where('is_active', true);
        }

        // Return the query result
        return $query->get();
    }

    /** Get function to get product by id
     * @param int $id
     * @return Product
     */
    public function get(int $id): Product
    {
        if (!isset($this->product)) $this->set($id);
        return $this->product;
    }

    /** Function to search for multiple products by ids
     * @param array $ids
     */
    public function searchByIds(array $ids)
    {
        // apply filter to the products by ids
        return Product::whereIn('id', $ids)->get();
    }

    /** Set function to set the repository product by id
     * @param int $id
     * @return Product
     */
    public function set(int $id): Product
    {
        // If the product is not set or the id is not the same as the product id
        if (empty($this->product) || $id != $this->product->id) {
            // use find or fail to get the product by id or throw an exception
            $this->product = Product::findOrFail($id);
        }
        // return the product
        return $this->product;
    }

    /** Create function to create product with given data
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product
    {
        return $this->product = Product::create($data);
    }

    /** Update function to update product with given data
     * @param int $id
     * @param array $data
     * @return Product
     */
    public function update(int $id, array $data): Product
    {
        $this->set($id);
        $this->product->update($data);
        return $this->product;
    }

    /** Delete function to delete product by id
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->set($id);
        return $this->product->delete();
    }
}
