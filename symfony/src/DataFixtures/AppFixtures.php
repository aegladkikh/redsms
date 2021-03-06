<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Invoice;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->addClient($manager);
    }

    public function addClient($manager)
    {
        $product1 = new Product();
        $product1->setName('Продукт 1');
        $product1->setPrice(1800);

        $product2 = new Product();
        $product2->setName('Продукт 2');
        $product2->setPrice(1500);

        $product3 = new Product();
        $product3->setName('Продукт 3');
        $product3->setPrice(1200);

        $invoice1 = new Invoice();
        $invoice1->setName('Счет 1');
        $invoice1->setBalance(2000);

        $invoice2 = new Invoice();
        $invoice2->setName('Счет 2');
        $invoice2->setBalance(3000);

        $client = new Client();
        $client->setName('Jony Сache');
        $client->addInvoice($invoice1);
        $client->addInvoice($invoice2);

        $order = new Order();
        $order->setName('Супер заказ');
        $order->setClient($client);
        $order->setStatus(false);
        $order->addProduct($product1);
        $order->addProduct($product2);
        $order->addProduct($product3);
        $client->addOrder($order);

        $manager->persist($order);
        $manager->flush();
    }
}
