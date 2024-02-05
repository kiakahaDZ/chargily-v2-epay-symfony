
# chargily-v2-epay-symfony

Symfony Plugin for Chargily v2 ePay Gateway
Installation

![Chargily ePay Gateway](https://raw.githubusercontent.com/Chargily/epay-gateway-php/main/assets/banner-1544x500.png "Chargily ePay Gateway")

# Installation
1. Via Composer (Recomended)
```bash
composer require kiakaha/chargily-v2
```

2. Register the bundle, add this line at the end of the file config/bundles.php
```bash
Chargily\V2Bundle\ChargilyV2Bundle::class => ['all' => true]
```

3. Import the services, Add the follow line in config/services.yml
```bash
imports:
services:
....
Chargily\V2Bundle\Service\HandleRequest\ChargilySendRequest:
        public: true
```

4. Configure the api keys Add the follow line in config/services.yml
```bash
parameters:
    .......
    sandbox_url: 'Your test URL'
    live_url: 'Your Live URL'
    public_key: "Your Public KEY"
    secret_key: "Your Secret KEY"
    dev: Boolean true|flase
```

5. Create Product Method

```bash
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

```

6. Create Price Method
```bash
    #[Route('/chargily/create/price', name: 'create_price', methods: ['GET'])]
    public function createPrice(ChargilySendRequest $sendRequest)
    {
        $payload = json_encode(["amount" => 5000,
            "currency" => "dzd",
            "product_id" => "01hnwn32spw57bz2b4m52rdjrh"]);
        return $sendRequest->createPrice($payload);
    }
```

7. Create checkout Method
```bash
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
```
8. Webhook Method
```bash
      /**
     * @Route("/chargily/webhook/checkout",name="webhook_checkout")
     * @throws \Exception
     */
    public function webhookCheckout(ChargilySendRequest $sendRequest, Request $request)
    {
        $response = $sendRequest->webhookCheckout($request);
        return $response;
    }  
```

8. this is a full controller for the implementations
```bash
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

```

# Contribution tips
1. Make a fork of this repo.
2. Take a tour to our [API documentation here](https://dev.chargily.com/pay-v2/)
3. Get your API Key/Secret from [ePay by Chargily V2 For test Mode](https://pay.chargily.com/test/dashboard/developers-corner) [ePay by Chargily V2 For live Mode](https://pay.chargily.com/live/dashboard/developers-corner) dashboard for free.
4. Start developing.
5. Finished? Push and merge.
