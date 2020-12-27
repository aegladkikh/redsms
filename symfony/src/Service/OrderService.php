<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

class OrderService
{
    private ManagerRegistry $managerRegistry;
    private OrderRepository $orderRepository;

    public function __construct(
        ManagerRegistry $managerRegistry,
        OrderRepository $orderRepository
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->orderRepository = $orderRepository;
    }

    public function findOrdersForClient($clientId): array
    {
        return $this->orderRepository->findBy(['client' => $clientId, 'status' => false]);
    }

    public function pay(int $orderId): void
    {
        $em = $this->managerRegistry->getManager();
        $order = $this->orderRepository->findOneBy(['id' => $orderId, 'status' => false]);

        if (!$order) {
            throw new RuntimeException('Не найден заказ.');
        }

        foreach ($order->getProduct() as $itemProduct) {
            $sum = $itemProduct->getPrice();

            $statusPay = false;
            foreach ($order->getClient()->getInvoice() as $invoice) {
                if ($invoice->getBalance() >= $sum) {
                    $invoice->setBalance($invoice->getBalance() - $sum);
                    $em->persist($invoice);
                    $statusPay = true;
                    break;
                }
            }

            if (!$statusPay) {
                throw new RuntimeException('Не достаточно средств.');
            }
        }

        $order->setStatus(true);

        $em->persist($order);
        $em->flush();
    }
}
