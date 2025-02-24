<?php
// tests/Entity/UserTest.php
namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetterAndSetter()
    {
        // Création d'une instance de l'entité User
        $user = new User();

        // Définition de données de test
        $email = 'test@test.com';
        $username = 'test';
        $name = ['test', 'test'];
        $role = 'ROLE_USER';


        // Utilisation des setters
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setLastname($name[0]);
        $user->setFirstname($name[1]);
        $user->setPassword('password');
        $user->setRole($role);

        // Vérification des getters
        $this->assertEquals($email, $user->getEmail());
    }
}
