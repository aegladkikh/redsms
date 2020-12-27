<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\InvoiceRepository;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

class OrderService
{
    private ManagerRegistry $managerRegistry;
    private OrderRepository $orderRepository;
    private InvoiceRepository $invoiceRepository;

    public function __construct(
        ManagerRegistry $managerRegistry,
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
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

        $sum = 0;
        foreach ($order->getProduct() as $itemProduct) {
            $sum += $itemProduct->getPrice();
        }

        foreach ($order->getClient()->getInvoice() as $invoice) {
            if ($invoice->getBalance() >= $sum) {
                $sum = $invoice->getBalance() - $sum;
                $invoice->setBalance($sum);
                $em->persist($invoice);
                break;
            }

            if ($invoice->getBalance() < $sum) {
                $sum -= $invoice->getBalance();
                $invoice->setBalance(0);
                $em->persist($invoice);
            }
        }

        if ($sum > 0) {
            throw new RuntimeException('Не достаточно средств.');
        }

        $order->setStatus(true);

        $em->persist($order);
        $em->flush();
    }
}
