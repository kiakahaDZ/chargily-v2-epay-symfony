<?php

namespace App\Controller;

use Chargily\V2Bundle\Service\HandleRequest\ChargilySendRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ChargilyEpaySymfonyController extends AbstractController
{

    #[Route('/chargily/create/product', name: 'create_new_product', methods: ['GET'])]
    public function createNewProduct(ChargilySendRequest $sendRequest)
    {
        $payload = json_encode(["name" => "Super Product"]);
        $response = $sendRequest->createNewProduct($payload);
        if ($response->getStatusCode() == 200) {
            $response = json_decode($response->getContent());
            return new JsonResponse([
                "id" => $response->id ?? null,
                "entity" => $response->entity ?? null,
                "livemode" => $response->livemode ?? null,
                "name" => $response->name ?? null,
                "description" => $response->description ?? null,
                "images" => $response->images ?? null,
                "metadata" => $response->metadata ?? null,
                "created_at" => $response->created_at ?? null,
                "updated_at" => $response->updated_at ?? null
            ]);
        } else {
            $response = json_decode($response->getContent());
            return new JsonResponse($response);
        }
    }

    #[Route('/chargily/create/price', name: 'create_price', methods: ['GET'])]
    public function createPrice(ChargilySendRequest $sendRequest)
    {
        $payload = json_encode(["amount" => 5000,
            "currency" => "dzd",
            "product_id" => "01hnwn32spw57bz2b4m52rdjrh"]);
        return $sendRequest->createPrice($payload);
    }

    #[Route('/chargily/create/checkout', name: 'create_checkout', methods: ['GET'])]
    public function createCheckout(ChargilySendRequest $sendRequest)
    {
        $payload = json_encode(["items" =>
            [[
                "price" => "01hntrjg31kkxebqzxk37xzhp8",
                "quantity" => 1
            ]],
            "success_url" => "https://your-cool-website.com/payments/success"
        ]);
        return $sendRequest->createCheckout($payload);
    }

    /**
     * @Route("/chargily/webhook/checkout",name="webhook_checkout")
     * @throws \Exception
     */
    public function webhookCheckout(ChargilySendRequest $sendRequest, Request $request)
    {
        $response = $sendRequest->webhookCheckout($request);
        return $response;
    }
}
