<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class CustomProductPricingMiddleware
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
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

        if ($user_type === 'gold') {
            $data['price'] *= 0.9; // 10% discount for gold users
            $data['discount'] = '10%'; // add discount field to the response
        } elseif ($user_type === 'silver') {
            $data['price'] *= 0.95; // 5% discount for silver users
            $data['discount'] = '5%'; // add discount field to the response
        } else {
            $data['discount'] = '0%'; // add discount field to the response
        }
        return $data;
    }
}
