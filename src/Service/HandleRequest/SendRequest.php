<?php

namespace App\Service\HandleRequest;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SendRequest
{
    private $params;
    private $dev;
    private $secret_key;
    private $url;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->dev = $this->params->get('dev');
        $this->secret_key = $this->params->get('secret_key');
        $this->dev ? $this->url = $this->params->get('sandbox_url') : $this->url = $this->params->get('live_url');
    }

    public function createNewProduct(array $payload): JsonResponse
    {
        $environment_url = $this->url . "/products";

        $client = new \GuzzleHttp\Client(['verify' => false]);

        try {
            $response = $client->post($environment_url, [
                RequestOptions::HEADERS => [
                    "Authorization: Bearer $this->secret_key",
                    "Content-Type: application/json"
                ],
                //RequestOptions::FORM_PARAMS => $payload,
                RequestOptions::BODY => json_encode(
                    $payload
                ),
                RequestOptions::TIMEOUT => 30
            ]);

            $response = json_decode($response->getBody()->getContents(), true);
            return new JsonResponse(['response' => $response], 200);
        } catch (\Exception $exception) {
            return new JsonResponse(['response' => "There is issue for connecting payment gateway. Sorry for the inconvenience. {$exception->getMessage()}"], 400,);
        } catch (GuzzleException $e) {
            return new JsonResponse(['response' => "There is issue for connecting payment gateway. Sorry for the inconvenience. {$e->getMessage()}"], 400,);
        }
    }

    public function createPrice(array $payload): JsonResponse
    {
        $environment_url = $this->url . "/prices";

        $client = new \GuzzleHttp\Client(['verify' => false]);

        try {
            $response = $client->post($environment_url, [
                RequestOptions::HEADERS => [
                    "Authorization: Bearer $this->secret_key",
                    "Content-Type: application/json"
                ],
                RequestOptions::BODY => json_encode(
                    $payload
                ),
                RequestOptions::TIMEOUT => 30
            ]);

            $response = json_decode($response->getBody()->getContents(), true);
            return new JsonResponse(['response' => $response], 200);
        } catch (\Exception $exception) {
            return new JsonResponse(['response' => "There is issue for connecting payment gateway. Sorry for the inconvenience. {$exception->getMessage()}"], 400,);
        } catch (GuzzleException $e) {
            return new JsonResponse(['response' => "There is issue for connecting payment gateway. Sorry for the inconvenience. {$e->getMessage()}"], 400,);
        }
    }

    public function createCheckout(array $payload): JsonResponse
    {
        $environment_url = $this->url . "/checkouts ";

        $client = new \GuzzleHttp\Client(['verify' => false]);

        try {
            $response = $client->post($environment_url, [
                RequestOptions::HEADERS => [
                    "Authorization: Bearer $this->secret_key",
                    "Content-Type: application/json"
                ],
                RequestOptions::BODY => json_encode(
                    $payload
                ),
                RequestOptions::TIMEOUT => 30
            ]);

            $response = json_decode($response->getBody()->getContents(), true);
            $redirectUrl = $response['checkout_url'];
            return new JsonResponse(['response' => $redirectUrl], 200);
        } catch (\Exception $exception) {
            return new JsonResponse(['response' => "There is issue for connecting payment gateway. Sorry for the inconvenience. {$exception->getMessage()}"], 400,);
        } catch (GuzzleException $e) {
            return new JsonResponse(['response' => "There is issue for connecting payment gateway. Sorry for the inconvenience. {$e->getMessage()}"], 400,);
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
