<?php

namespace ES\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ES\PlatformBundle\Entity\Category;
use ES\PlatformBundle\Entity\Advert;

class LoadAdvert implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
  	$advert1 = new Advert();
  	$advert1->setTitle("Construction de Maison");
  	$advert1->setContent("Ce projet a un objectif double, limiter le coût de fabrication des maisons et valoriser les méthodes traditionnelles et les matériaux locaux. Après 1 an et demi à chercher des partenaires et à définir les matériaux utilisables au Nicaragua, Esteli Solidarité a financé la formation de deux maçons au sein de l’association « Las Mujeres constructadores de Condega » pour apprendre à construire des maisons en Adobe antisismique. Ces constructions à base d’argile et de matériaux végétaux peuvent être faites dans certains villages proches d’Esteli. Ces maçons travaillant déjà l’Adobe, ont pu parfaire leur connaissance sur ces matériaux. L’objectif est maintenant de réaliser une maison prototype pour montrer la viabilité de ce type de maison au sein de la population locale. Ce projet nécessite 6 à 8 personnes pour 1 à 2 mois.");
  	$manager->persist($advert1);
    $manager->flush();
  }
}