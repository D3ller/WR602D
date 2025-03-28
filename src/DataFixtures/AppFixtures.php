<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $subscription1 = new Subscription();
        $subscription1->setName('Gratuit');
        $subscription1->setId(1);
        $subscription1->setPrice(0);
        $subscription1->setMaxPdf(12);
        $subscription1->setDescription("Pour les gens broke");
        $manager->persist($subscription1);

        $subscription2 = new Subscription();
        $subscription2->setName('Preemium');
        $subscription2->setId(2);
        $subscription2->setPrice(3.99);
        $subscription2->setMaxPdf(50);
        $subscription2->setDescription("Pour les gens qui vivent");
        $manager->persist($subscription2);

        $subscription3 = new Subscription();
        $subscription3->setName('Pro');
        $subscription3->setId(3);
        $subscription3->setPrice(10.99);
        $subscription3->setMaxPdf(1000);
        $subscription3->setDescription("Pour les becteurs. Les billets font des heureux donc il en faut plein");
        $manager->persist($subscription3);

        $manager->flush();
    }
}
