<?php
namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Site;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SortieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        //Créer 10 participants fakés



        for($i=1;$i<=10;$i++){

            $sites = $manager->getRepository(Site::class) ->findAll();
            $key = array_rand($sites);
            $site = $sites[$key];

            $participant = new Participant();
            $participant->setNom($faker->lastName())
                ->setPrenom($faker->firstName(null))
                ->setTelephone($faker->phoneNumber())
                ->setMail($faker->email())
                ->setActif(true)
                ->setAdministrateur(false)
                ->setSite($site)
                ->setPseudo($faker->userName())
                ->setPasswword ($faker->password())
                ->setRoles(["ROLE_USER"]);




            $manager->persist($participant);


            //Créer entre 4 et 6 sorties, .= pour prendre ce qu'il y a dans $description et lui rajouter quelque chose

            for($j=1;$j<=mt_rand(4,6);$j++) {
                $sortie = new Sortie();

                /*  $description = '<p>';
                  $description .= join($faker->paragraphs(5), '</p><p>');
                  $description='</p>'; */

                //On donne des lieux avec ville à la sortie ainsi que son état

                $ville = new Ville();
                $ville->setNom($faker->city ())
                    ->setCodePostal($faker->postcode());

                $manager->persist($ville);

                $lieu= new Lieu();
                $lieu->setNom($faker->word())
                    ->setRue($faker->streetAddress())
                    ->setVille($ville)
                    ->setLatitude($faker->latitude())
                    ->setLongitude($faker->longitude())
                    ->setVille($ville);


                $manager->persist($lieu);


               $description = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

               $etats = $manager->getRepository(Etat::class) ->findAll();
               $key = array_rand($etats);
               $etat = $etats[$key];

                $sortie->setNom($faker->word())
                    ->setDateHeureDebut($faker->dateTimeBetween('now', '+ 6 month'))
                    ->setDuree($faker->randomDigitNotNull() )
                    ->setDateLimiteInscription($faker->dateTimeBetween('-6 month', ($sortie->getDateHeureDebut())))
                    ->setNbrInscriptionMax(($faker->randomDigitNotNull())*4)
                    ->setInfosSortie($description)
                    ->setEtat( $etat)
                    ->setOrganisateur($participant)
                    ->setLieu($lieu)
                    ->setSiteOrganisateur($site);

                $manager->persist($sortie);

            }
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [SiteFixtures::class, EtatFixtures::class];
    }
}
