<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     *
     * @param Product $product
     * @return void
     */
    public function updated(Product $product): void
    {
        // check if image is updated to delete old image file from storage
        if ($product->isDirty('image')) {
            // get old image path
            $old_image = $product->getOriginal('image');

            // check if old image path is not empty
            if (!empty($old_image)) {
                // get old image path
                $old_image_path = asset('storage/' . $old_image);

                // check if old image file exists
                if (file_exists($old_image_path)) {
                    // delete old image file
                    unlink($old_image_path);
                }
            }
        }
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param Product $product
     * @return void
     */
    public function deleted(Product $product): void
    {
        // delete image file from storage
        $image = $product->image;

        // check if product has image
        if (!empty($image)) {
            // get image path
            $image_path = asset('storage/' . $image);
            // check if image file exists
            if (file_exists($image_path)) {
                // delete image file
                unlink($image_path);
            }
        }
    }
}
