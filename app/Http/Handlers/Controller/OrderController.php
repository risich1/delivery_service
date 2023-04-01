<?php

namespace App\Http\Handlers\Controller;

use App\Http\Request\AuthRequest;
use App\Http\Request\CalculateOrderRequest;
use App\Http\Request\CreateOrderRequest;
use App\Http\Request\HandOrderToCourierRequest;
use App\Http\Response\Response;
use App\Service\OrderService;
use App\Transformer\OrderTransformer;
use App\Transformer\ShortOrderTransformer;

class OrderController extends Controller {

    public function __construct(
        protected readonly OrderService $orderService,
        protected readonly OrderTransformer $orderTransformer,
        protected readonly ShortOrderTransformer $shortOrderTransformer
    ) {}

    public function getOrder (AuthRequest $request, $id): Response {
        $order = $this->orderService->getOrderById($id, $request->getUser());
        return new Response($this->orderTransformer->transform($order));
    }

    public function getAllOrder (AuthRequest $request): Response {
        $result = $this->orderService->getOrderList($request->getUser());
        return new Response($this->shortOrderTransformer->transform($result));
    }

    public function createOrder (CreateOrderRequest $request): Response {
        $this->orderService->createOrder($request->getUser(), $request->getBody());
        return new Response('Order created', [], Response::HTTP_CREATED_CODE);
    }

    public function calculateOrder (CalculateOrderRequest $request): Response {
        $body = $request->getBody();
        return new Response(['cost' => $this->orderService->calculateCost($request->getUser(), $body['address_a_id'], $body['address_b_id'])]);
    }

    public function sendToCourier (HandOrderToCourierRequest $request, int $id): Response {
        $this->orderService->handOrderToCourier($id, $request->getUser(), $request->getBody()['courier_id']);
        return new Response('Order has been handed');
    }

}
