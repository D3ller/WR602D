<?php
// tests/Entity/UserTest.php
namespace App\Tests\Entity;

use App\Entity\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testGetterAndSetter()
    {
        // Création d'une instance de l'entité File
        $file = new File();


        // Définition de données de test
        $name = 'name';
        $file->setName($name);

        // Vérification des getters
        $this->assertEquals($name, $file->getName());
    }
}
