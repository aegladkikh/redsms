<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
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
    public function index(
        ClientRepository $clientRepository,
        InvoiceRepository $invoiceRepository
    ): Response {
        $client = current($clientRepository->findAll()) ?? [];

        if (!$client) {
            throw $this->createNotFoundException('Не найден клиент.');
        }


        return $this->render(
            'index.html.twig',
            [
                'client' => $client,
                'invoice' => $invoiceRepository->findBy(['client' => $client->getId()]) ?? [],
                'orders' => $this->orderService->findOrdersForClient($client->getId()) ?? []
            ]
        );
    }

    #[Route('/pay')]
    public function pay(
        Request $request
    ): Response {
        try {
            $this->orderService->pay((int)$request->request->get('order'));
        } catch (Throwable $e) {
            return $this->render('fail.pay.html.twig', ['error' => $e->getMessage()]);
        }

        return $this->render('success.pay.html.twig');
    }
}
