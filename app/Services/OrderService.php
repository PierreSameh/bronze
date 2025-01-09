<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OrderService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://open.sunsky-online.com'; // Replace with the actual API base URL
    }

    /**
     * Place an order via the API.
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function placeOrder(array $address, array $items, int $orderNumber): array
    {
        $url = $this->baseUrl . '/openapi/order!createOrder.do';
        $address['countryId'] = "EG";
        $postData = [
            "siteNumber" => $orderNumber,
            "useBalanceOnly" => false,
            "vatNumber" => "VAT12345678",
            "eoriNumber" => "EORI12345678",
            "iossNumber" => "IOSS12345678",
            // "coupon" => "DISCOUNT10",
            "deliveryAddress" => [$address],
            "items" => [$items],
        ];

        // Send the request using Laravel's HTTP client
        $response = Http::post($url, $postData);

        // Check for errors in the response
        if ($response->failed()) {
            throw new \Exception('API request failed with status code ' . $response->status() . ': ' . $response->body());
        }

        return $response->json();
    }
}
