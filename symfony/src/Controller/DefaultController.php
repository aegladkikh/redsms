<?php

namespace App\Controller;

use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class DefaultController extends AbstractController
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    #[Route('/')]
    public function index(): Response
    {
        return $this->render(
            'index.html.twig',
            [
                'orders' => $this->orderService->getAllOrders()
            ]
        );
    }

    #[Route('/pay')]
    public function pay(
        Request $request
    ): Response {
        $orderId = $request->request->get('order');
        $productInvoice = $request->request->get('product');

        $html = '<html lang="ru"><body><div style="color: green;">Успешно оплатили</div></body></html>';

        try {
            $this->orderService->pay($orderId, $productInvoice);
        } catch (Throwable $e) {
            $html = '<div style="color: red;">' . $e->getMessage() . '</div>';
        }

        return new Response($html . ' <a href="/">Вернуться назад</a>');
    }
}
