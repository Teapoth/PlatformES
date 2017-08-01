<?php

namespace ES\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ES\PlatformBundle\Entity\Category;

class LoadCategory implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    $names = array(
      'Bâtiment',
      'Développement durable',
      'Éducation',
      'Santé',
    );

    foreach ($names as $name) {
      $category = new Category();
      $category->setName($name);

      $manager->persist($category);
    }

    $manager->flush();
  }
}