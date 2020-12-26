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

    public function pay(int $orderId, array $product): void
    {
        $em = $this->managerRegistry->getManager();
        $order = current($this->orderRepository->findBy(['id' => $orderId, 'status' => false])) ?? [];

        if (!$order) {
            throw new RuntimeException('Не найден заказ: ' . $order->getName() . '.');
        }

        if ($order->getProduct()->count() === 0) {
            throw new RuntimeException('В заказ не добавлены продукты.');
        }

        foreach ($order->getProduct() as $itemProduct) {
            $invoiceForProduct = current($product[$itemProduct->getId()] ?? []);

            $invoice = $this->invoiceRepository->find($invoiceForProduct);
            if (!$invoice) {
                throw new RuntimeException('Не найден счет.');
            }

            $invoiceBalance = $invoice->getBalance() ?? 0;

            if ($invoiceBalance < $itemProduct->getPrice()) {
                throw new RuntimeException('Не достаточно средств на счете: ' . $invoice->getName() . '.');
            }

            $setBalance = $invoiceBalance - $itemProduct->getPrice();

            $invoice->setBalance($setBalance);

            $em->persist($invoice);
        }

        $order->setStatus(true);

        $em->persist($order);
        $em->flush();
    }
}
