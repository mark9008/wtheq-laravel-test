<?php

namespace App\Http\Repositories;

use App\Models\Product;
use PhpParser\Node\Expr\Array_;

class ProductRepository
{
    /** Product variable to store product model
     * @var Product
     */
    protected Product $product;

    /** List function to list all products
     * @return array
     */
    public function list()
    {
        return Product::all();
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

    /** Set function to set the repository product by id
     * @param int $id
     * @return Product
     */
    public function set(int $id): Product
    {
        if (empty($this->product) || $id != $this->product->id)
            $this->product = Product::findOrFail($id);
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
    public function update(int $id,array $data): Product
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
