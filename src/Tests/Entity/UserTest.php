<?php
// tests/Entity/UserTest.php
namespace App\Tests\Entity;

use App\Entity\Subscription;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetterAndSetter()
    {
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

        // Création d'une instance de l'entité User
        $user = new User();

        // Définition de données de test
        $email = 'test@test.com';
        $name = ['test', 'test'];
        $role = ['ROLE_USER'];


        // Utilisation des setters
        $user->setEmail($email);
        $user->setLastname($name[0]);
        $user->setFirstname($name[1]);
        $user->setPassword('password');
        $user->setRoles($role);
        $user->setSubscription($subscription);

        // Vérification des getters
        $this->assertEquals($email, $user->getEmail());
    }
}
