<?php

namespace Chargily\SymfonyBundle\Controller;

use App\Service\HandleRequest\SendRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Payum\Core\Reply\HttpRedirect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    /**
     * @Route("/chargily/create/product",name="create_new_product")
     * @throws \Exception
     */
    public function createNewProduct()
    {
        $payload = ["name"=> "Super Product"];
        return $this->sendRequest->createNewProduct($payload);
    }

    /**
     * @Route("/chargily/create/price",name="create_price")
     * @throws \Exception
     */
    public function createPrice()
    {
        $payload = [  "amount"=> 5000,
  "currency"=> "dzd",
  "product_id"=> "01hhyjnrdbc1xhgmd34hs1v3en"];
        return $this->sendRequest->createPrice($payload);
    }

    /**
     * @Route("/chargily/create/checkout",name="create_checkout")
     * @throws \Exception
     */
    public function createCheckout()
    {
        $payload = [  "amount"=> 5000,
            "currency"=> "dzd",
            "product_id"=> "01hhyjnrdbc1xhgmd34hs1v3en"];
        $response =  $this->sendRequest->createCheckout($payload);
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
        $response =  $this->sendRequest->webhookCheckout($request);
        return $response;
    }
}
