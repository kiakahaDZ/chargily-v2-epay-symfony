<?php

namespace App\Controller;

use App\Service\HandleRequest\SendRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ChargilyEpaySymfonyController extends AbstractController
{
    protected SendRequest $sendRequest;

    /**
     * @param SendRequest $sendRequest
     * @required
     */
    public function __construct(SendRequest $sendRequest)
    {
        $this->sendRequest = $sendRequest;
    }

    #[Route('/chargily/create/product', name: 'create_new_product', methods: ['POST'])]
    public function createNewProduct()
    {
        $payload = ["name" => "Super Product"];
        $response = $this->sendRequest->createNewProduct($payload);

        $new_product = [
            "id" => $response->id ?? null,
            "entity" => $response->entity ?? null,
            "livemode" => $response->livemode ?? null,
            "name" => $response->name ?? null,
            "description" => $response->description ?? null,
            "images" => $response->images ?? null,
            "metadata" => $response->metadata ?? null,
            "created_at" => $response->created_at ?? null,
            "updated_at" => $response->updated_at ?? null
        ];
        return $new_product;
    }

    #[Route('/chargily/create/price', name: 'create_price', methods: ['POST'])]
    public function createPrice()
    {
        $payload = ["amount" => 5000,
            "currency" => "dzd",
            "product_id" => "01hhyjnrdbc1xhgmd34hs1v3en"];
        return $this->sendRequest->createPrice($payload);
    }

    #[Route('/chargily/create/checkout', name: 'create_checkout', methods: ['POST'])]
    public function createCheckout()
    {
        $payload = ["amount" => 5000,
            "currency" => "dzd",
            "product_id" => "01hhyjnrdbc1xhgmd34hs1v3en"];
        $response = $this->sendRequest->createCheckout($payload);
        $status_code = $response->getStatusCode();
        $response = json_decode($response->getContent());
        if ($status_code == 200) {
            //redirect to chargily payment gateway
            return $this->redirect($response->response);
        } else {
            // This is a error message depending on issue that happen
            dd($status_code . " " . $response->response);
        }
    }

    /**
     * @Route("/chargily/webhook/checkout",name="webhook_checkout")
     * @throws \Exception
     */
    public function webhookCheckout(Request $request)
    {
        $response = $this->sendRequest->webhookCheckout($request);
        return $response;
    }
}
