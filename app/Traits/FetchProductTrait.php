<?php

namespace App\Traits;

use App\Services\OpenApiService;
use Exception;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;

trait FetchProductTrait
{
    private $key = 'hamadakr2005';
    private $secret = 'sfjkewaggrenwe';

    /**
     * Fetch models of a product using its URL.
     *
     * @param string $url
     * @return array
     */
    public function fetchProductsModels($url)
    {
        $itemNo = $this->fetchItemNoFromUrl($url);

        $originalProductResponse = $this->fetchProduct($url, $itemNo);

        if ($originalProductResponse['status'] === false) {
            return $originalProductResponse;
        }

        $originalProductData = $originalProductResponse['data'];

        $models = [
            $originalProductData
        ];

        foreach ($originalProductData['modelList'] as $model) {
            $modelItemNo = $model['key'];

            if ($modelItemNo != $itemNo) {
                $modelResponse = $this->fetchProduct(null, $modelItemNo);

                if ($modelResponse['status'] === false) {
                    return $modelResponse;
                }

                $modelData = $modelResponse['data'];

                $models[] = $modelData;
            }
        }

        return [
            'status' => true,
            'data' => $models
        ];
    }

    /**
     * Fetch a product's details in multiple languages (Arabic and English).
     *
     * @param string|null $url
     * @param string|null $item_no
     * @return array
     */
    public function fetchProduct($url, $item_no = null)
    {
        $itemNo = $item_no ?? $this->fetchItemNoFromUrl($url);

        // Fetch product data in Arabic first
        $result_ar = $this->getProductDataInLang('ar', $itemNo);

        // Check if there's an error in the Arabic response
        if ($result_ar->original['result'] === 'error') {
            return [
                'status' => false,
                'message' => $result_ar->original['messages'][0] ?? 'An error occurred.',
            ];
        }

        // Fetch product data in English only if no error in Arabic
        $result_en = $this->getProductDataInLang('en', $itemNo);

        $data = $this->formatProductData($result_en->original['data']);
        $data_ar = $this->formatProductData($result_ar->original['data']);

        $data['name_ar'] = $data_ar['name'];
        $data['description_ar'] = $data_ar['description'];

        return [
            'status' => true,
            'data' => $data
        ];
    }

    /**
     * Fetch product data in a specified language.
     *
     * @param string $language
     * @param string $itemNo
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductDataInLang($language, $itemNo)
    {
        $apiUrl = "https://open.sunsky-online.com/openapi/product!detail.do";
        $parameters = [
            'key'    => $this->key,
            'itemNo' => $itemNo,
            'lang' => $language,
        ];

        $result = OpenApiService::call($apiUrl, $this->secret, $parameters);
        return response()->json(json_decode($result, true));
    }

    /**
     * Format the product data to a unified structure.
     *
     * @param array $data
     * @return array
     */
    protected function formatProductData(array $data)
    {
        return [
            'itemNo' => $data['itemNo'] ?? null,
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'leadTime' => $data['leadTime'] ?? null,
            'gmtModified' => $data['gmtModified'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'modelList' => $data['modelList'] ?? [],
            'optionList' => $data['optionList'] ?? [],
            'price' => $data['price'] ?? 0,
            'orgPrice' => $data['orgPrice'] ?? null,
            'priceExpired' => $data['priceExpired'] ?? null,
            'packQty' => $data['packQty'] ?? null,
            'unitLength' => $data['unitLength'] ?? null,
            'unitWidth' => $data['unitWidth'] ?? null,
            'unitHeight' => $data['unitHeight'] ?? null,
            'packWeight' => $data['packWeight'] ?? null,
            'packLength' => $data['packLength'] ?? null,
            'packWidth' => $data['packWidth'] ?? null,
            'packHeight' => $data['packHeight'] ?? null,
        ];
    }

    /**
     * Extract the item number from a given URL.
     *
     * @param string $url
     * @return string|null
     */
    private function fetchItemNoFromUrl(string $url): ?string
    {
        try {
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception("Invalid URL provided.");
            }

            // Get the HTML content
            $htmlContent = file_get_contents($url);

            if ($htmlContent === false) {
                throw new Exception("Failed to fetch the URL content.");
            }

            // Suppress warnings during DOMDocument load
            libxml_use_internal_errors(true);

            // Load the HTML content into DOMDocument
            $dom = new DOMDocument();
            $dom->loadHTML($htmlContent);

            // Clear libxml errors
            libxml_clear_errors();

            // Use DOMXPath to search for the first input with class .itemNo
            $xpath = new DOMXPath($dom);
            $inputNode = $xpath->query('//input[contains(@key, "ITEM_NO")]')->item(0);

            // Return the value attribute if the input node exists
            if ($inputNode && $inputNode->hasAttribute('value')) {
                return $inputNode->getAttribute('value');
            }

            return null; // Return null if no matching input is found
        } catch (Exception $e) {
            // Handle exceptions (log, rethrow, or return a message)
            logger()->error("Error in FetchProductTrait: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a signature for API authentication.
     *
     * @param array $parameters
     * @param string $secret
     * @return string
     */
    public static function sign($parameters, $secret)
    {
        $signature = '';
        ksort($parameters);
        foreach ($parameters as $key => $value) {
            $signature .= $value;
        }
        $signature .= '@' . $secret;
        return md5($signature);
    }
}
