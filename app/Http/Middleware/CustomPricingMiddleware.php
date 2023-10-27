<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use function Laravel\Prompts\select;

class CustomPricingMiddleware
{
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
            // Get RequestURI
            $requestURI = $request->getRequestUri();
            // Check if request contains list of products or only one product
            if (str_contains($requestURI, 'list')) {
                // Calculate and update the prices (modify the data as needed)
                foreach ($data['data'] as &$product) {
                    $product['price'] = $this->calculatePrice($product['price'], $user_type);
                }
            } else {
                // Calculate and update the price (modify the data as needed)
                $data['data']['price'] = $this->calculatePrice($data['data']['price'], $user_type);
            }

            // Encode the modified data back to JSON
            $content = json_encode($data);

            // Update the response content with the modified JSON
            $response->setContent($content);
        }
        // Return edited response
        return $response;
    }

    private function calculatePrice($price, $user_type): float
    {
        if ($user_type === 'gold') {
            $price *= 0.9; // 10% discount for gold users
        } elseif ($user_type === 'silver') {
            $price *= 0.95; // 5% discount for silver users
        }
        return $price;
    }
}
