<?php

namespace App\DataFixtures;

use Faker;
use DateTime;
use App\Entity\Pret;
use App\Entity\Livre;
use App\Entity\Adherent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $manager;
    private $faker;
    private $repoLivres;
    private $encode;

    public function __construct(UserPasswordEncoderInterface $encode){
        $this->faker = Faker\Factory::create("fr_FR");
        $this->encode = $encode;
    }

    public function load(ObjectManager $manager)
    {

        $this->manager=$manager;
        $this->repoLivres=$this->manager->getRepository(Livre::class);

        $this->loadAdherent();
        $this->loadPret();

        $manager->flush();
    }

    /**
     * Creation d'adherent
     */
    public function loadAdherent()
    {
        $commune = [
            "78003", "78005", "78006", "78007", "78009", "78010", "78013", "78015", "78020", "78029",
            "78030", "78031", "78033", "78034", "78036", "78043", "78048", "78049", "78050", "78053", "78057",
            "78062", "78068", "78070", "78071", "78072", "78073", "78076", "78077", "78082", "78084", "78087",
            "78089", "78090", "78092", "78096", "78104", "78107", "78108", "78113", "78117", "78118"
        ];

        $genre=["male", "female"];
        for ($i=0; $i < 15; $i++) { 
            $adherent= new Adherent;
            $adherent->setNom($this->faker->lastName())
                    ->setPrenom($this->faker->firstName($genre[mt_rand(0,1)]))
                    ->setAdress($this->faker->streetAddress())
                    ->setPhone($this->faker->phoneNumber())
                    ->setCodeCommune($commune[mt_rand(0, sizeof($commune)-1)])
                    ->setMail(strtolower($adherent->getNom()).'@gmail.com')
                    ->setPassword($this->encode->encodePassword($adherent, $adherent->getNom()));
            $this->addReference("adherent".$i, $adherent);
            $this->manager->persist($adherent);
        }
        $rolesA[]=ADHERENT::ROLE_ADMIN;
        $rolesM[]=ADHERENT::ROLE_MANAGER;
        $admin= new Adherent;
        $admin->setNom("palmie")
                ->setPrenom("tanguy")
                ->setMail('tanguy@gmail.com')
                ->setPassword($this->encode->encodePassword($adherent,'tanguy'))
                ->setRoles($rolesA);
        $this->manager->persist($admin);

        $mang= new Adherent;
        $mang->setNom("palmitot7")
                ->setPrenom("palmitot7")
                ->setMail('palmitot7@gmail.com')
                ->setPassword($this->encode->encodePassword($adherent,'palmitot7'))
                ->setRoles($rolesM);
        $this->manager->persist($mang);

        $this->manager->flush();
    }

    /**
     * Creation de pret
     */
    public function loadPret()
    {
        for ($i=0; $i < 15; $i++) { 
            for ($j=0; $j < mt_rand(1,5); $j++) { 
               $pret = new Pret;
                $livre=$this->repoLivres->find(mt_rand(1,49));
                $pret->setLivre($livre)
                    ->setAdherent($this->getReference("adherent".$i))
                    ->setDatePret($this->faker->dateTimeBetween('-6 months'));
                $dateRetour=date('Y-m-d H:min', strtotime('15 days', $pret->getDatePret()->getTimestamp()));
                $dateRetour=\DateTime::createFromFormat('Y-m-d H:min', $dateRetour);
                $pret->setDateEnd($dateRetour);
                
                if(mt_rand(0,4)==4){
                    $pret->setDateFinished($this->faker->dateTimeInInterval($pret->getDatePret(),'+30 days'));
                }
                $this->manager->persist($pret);
            }
        }
        $this->manager->flush();
    }
}
