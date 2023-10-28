<?php

/**
 * CustomProductPricingMiddleware
 * This file requires custom_pricing.php config file to be present in config directory
 * It should contain an associative array with user types as keys and discount values as values
 * The discount values should be between 0 and 1
 * The middleware will apply the discount to the price of the product
 * The middleware will add a 'discount' field to the response
 * The middleware will add an 'original_price' field to the response
 * @example array:
 * [
 * 'gold' => 10,  // 10% discount for gold users
 * 'silver' => 5,  // 5% discount for silver users
 * 'normal' => 0,  // no discount for normal users
 * ]
 *
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class CustomProductPricingMiddleware
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Get the user type from the request
        $user_type = $request->user()->type;

        // Get the response from the next middleware in the chain
        $response = $next($request);

        // Get the content of the response (the JSON data)
        $content = $response->getContent();

        // Decode the JSON data into an associative array
        $data = json_decode($content, true);

        // Check if the data is not null and it has a 'data' key
        if ($data !== null && array_key_exists('data', $data)) {
            // Check if response contains list of products or only one product
            if (isset($data['data']['price'])) {
                // Calculate and update the price (modify the data as needed)
                $data['data'] = $this->calculatePrice($data['data'], $user_type);
            } else {
                // Calculate and update the prices (modify the data as needed)
                foreach ($data['data'] as &$product) {
                    $product = $this->calculatePrice($product, $user_type);
                }
            }

            // Encode the modified data back to JSON
            $content = json_encode($data);

            // Update the response content with the modified JSON
            $response->setContent($content);
        }
        // Return edited response
        return $response;
    }

    /**
     * Calculate the price and discount
     * @param array $data
     * @param string $user_type
     * @return array
     */
    private function calculatePrice(array $data, string $user_type): array
    {
        // Set the original price
        $data['original_price'] = $data['price'];

        $discount = config('custom_pricing.' . $user_type, 1.0);

        $data['price'] *= (100-$discount)/100; // apply discount
        $data['discount'] = str($discount).'%'; // add discount field to the response

        return $data;
    }
}
