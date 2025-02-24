<?php
// tests/Entity/UserTest.php
namespace App\Tests\Entity;

use App\Entity\Subscription;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    public function testGetterAndSetter()
    {
        // Création d'une instance de l'entité File
        $subscription = new Subscription();

        // Définition de données de test
        $name = 'Test';
        $description = 'Test description';
        $price = 100;
        $maxUsage = 10;
        $subscription->setName($name);
        $subscription->setDescription($description);
        $subscription->setPrice($price);
        $subscription->setMaxPdf($maxUsage);

        // Vérification des getters
        $this->assertEquals($name, $subscription->getName());
    }
}
