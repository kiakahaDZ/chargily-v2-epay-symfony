<?php

namespace App\Service\HandleRequest;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ChargilySendRequest
{
    private $params;
    private $dev;
    private $secret_key;
    private $url;
    private $curl;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->dev = $this->params->get('dev');
        $this->secret_key = $this->params->get('secret_key');
        $this->dev ? $this->url = $this->params->get('sandbox_url') : $this->url = $this->params->get('live_url');
        $this->curl = curl_init();
    }

    public function createNewProduct(mixed $payload): JsonResponse
    {
        $environment_url = $this->url . "/products";


        curl_setopt_array($this->curl, [
            CURLOPT_URL => $environment_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $this->secret_key",
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($this->curl);
        $statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);
        if ($statusCode != 200) {
            $response = "There is issue for connecting payment gateway. Sorry for the inconvenience";
            return new JsonResponse($response, 400);
        } else {
            $response = json_decode($response, true);
            return new JsonResponse($response, 200);
        }

    }

    public function createPrice(mixed $payload): JsonResponse
    {
        $environment_url = $this->url . "/prices";

        curl_setopt_array($this->curl, [
            CURLOPT_URL => $environment_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $this->secret_key",
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($this->curl);
        $statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);
        if ($statusCode != 200) {
            $response = "There is issue for connecting payment gateway. Sorry for the inconvenience";
            return new JsonResponse($response, 400);
        } else {
            $response = json_decode($response, true);
            return new JsonResponse($response, 200);
        }
    }

    public function createCheckout(mixed $payload): JsonResponse
    {
        $environment_url = $this->url . "/checkouts";
        curl_setopt_array($this->curl, [
            CURLOPT_URL => $environment_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $this->secret_key",
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($this->curl);
        $statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);
        if ($statusCode != 200) {
            $response = "There is issue for connecting payment gateway. Sorry for the inconvenience";
            return new JsonResponse($response, 400);
        } else {
            $response = json_decode($response, true);
            return new JsonResponse($response, 200);
        }
    }

    public function webhookCheckout(Request $request): bool
    {
        // Your Chargily Pay Secret key, will be used to calculate the Signature
        $apiSecretKey = $this->secret_key;
        // Extracting the 'signature' header from the HTTP request
        $signature = $request->headers->get('signature') ?? null;

// Getting the raw payload from the request body
        $payload = file_get_contents('php://input');

// If there is no signature, exit the script (we will never send requests without a signature - a request without a signature is always a fake request so just ignore it)
        if (!$signature) {
            exit;
        }

// Calculate the signature
        $computedSignature = hash_hmac('sha256', $payload, $apiSecretKey);

// If the calculated signature doesn't match the received signature, exit the script (a request with a wrong signature means that the request has been tampered with so just ignore it)
        if (!hash_equals($signature, $computedSignature)) {
            exit();
        } else {
            // If the signatures match, proceed to decode the JSON payload
            $event = json_decode($payload);

            // Switch based on the event type
            switch ($event->type) {
                case 'checkout.paid':
                    $checkout = $event->data;
                    // Handle the successful payment.
                    return http_response_code(200);
                    break;
                case 'checkout.failed':
                    $checkout = $event->data;
                    // Handle the failed payment.
                    return http_response_code(400);
                    break;
            }
        }

// Respond with a 200 OK status code to let us know that you've received the webhook
        return http_response_code(200);
    }
}
