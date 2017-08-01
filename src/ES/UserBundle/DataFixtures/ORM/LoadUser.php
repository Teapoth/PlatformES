<?php

namespace ES\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ES\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    $names = array('admin', 'user');

    foreach ($names as $name) {
      $user = new User();
      $user->setUsername($name);
      $user->setPassword('7SwGNdUJ4XYHLUXaot+iydIPIOK6djHWu8/MMRwQzjaxh8htwv3jJ2t9nfYu+8J8XzZ0HO8bWKCbvKPEByw9Mw==');
      $user->setSalt('UjSICM4zSuvmJIJ9n9qxmvc7RpacIVbOsPXnsD4lyFo');
      $user->setEnabled(1);
      $user->setEmail($name.'@es.com');
      $user->addRole('ROLE_'.strtoupper($name));
      $manager->persist($user);
    }

    $manager->flush();
  }
}