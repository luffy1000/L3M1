<?php

namespace App\Tests\Unit;

use App\Entity\Reservations;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReservationsTest extends KernelTestCase
{
    public function getEntity(): Reservations{
       return (new Reservations())->setCreatedAt(new \DateTimeImmutable())
       ->setMethod('paypal')
       ->setReference("jfkjehfhek")
       ->setStripeSessionId("ut2u4tut4ut24")
       ->setPaypalOrderId("3hg3hgh3g43h")
       ->setPrice(40)
       ->setIsAssign(1)
       ->setIsPaid(1);

    }
    public function testSomething(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $reservation = $this->getEntity();
             
       
        $errors = $container->get('validator')->validate($reservation);
        $this->assertCount(0,$errors);

       
    }

    public function testReservation() {
        self::bootKernel();
        $container = static::getContainer();
        
        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(Users::class,1);
        $reservation = $this->getEntity()
        ->addUser($user);
       

        $errors = $container->get('validator')->validate($reservation);
        $this->assertCount(0,$errors);
       
        
       
    }
}
