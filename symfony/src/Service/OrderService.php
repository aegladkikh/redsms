<?php

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

    public function getAllOrders(): array
    {
        return $this->orderRepository->findAll();
    }

    public function pay(int $orderId, array $product): void
    {
        $em = $this->managerRegistry->getManager();
        $findOrder = $this->orderRepository->find($orderId);

        foreach ($findOrder->getProduct() as $itemProduct) {
            $invoiceProduct = current($product[$itemProduct->getId()]);

            $invoice = $this->invoiceRepository->find($invoiceProduct);
            $invoiceBalance = $invoice->getBalance();

            if ($invoiceBalance < $itemProduct->getPrice()) {
                throw new RuntimeException('Не достаточно средств на счете: ' . $invoice->getName());
            }

            $setBalance = $invoiceBalance - $itemProduct->getPrice();

            $invoice->setBalance($setBalance);

            $em->persist($invoice);
        }
        $em->flush();
    }
}
