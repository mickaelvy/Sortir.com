<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $site = new Site();
        $site -> setNom('Saint-Herblain');
        $manager->persist($site);


        $site = new Site();
        $site -> setNom('Niort');
        $manager->persist($site);

        $site = new Site();
        $site -> setNom('Angers');
        $manager->persist($site);

        $site = new Site();
        $site -> setNom('Rennes');
        $manager->persist($site);

        $site = new Site();
        $site -> setNom('La Roche sur Yon');
        $manager->persist($site);



        $manager->flush();
    }
}
