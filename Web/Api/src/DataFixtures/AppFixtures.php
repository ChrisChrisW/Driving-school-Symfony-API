<?php

namespace App\DataFixtures;

use App\Entity\Candidate;
use App\Entity\CourseDates;
use App\Entity\DrivingFormula;
use App\Entity\Formula;
use App\Entity\Trainer;
use App\Entity\User;
use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AppFixture for
 */
class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @return void
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        // Create Vehicles
        $vehicleA = new Vehicle();
        $vehicleA->setNumPlate("852-KFC");
        $vehicleA->setPurchaseDate(new \DateTime());
        $vehicleA->setPower(80);
        $manager->persist($vehicleA);
        $vehicleB = new Vehicle();
        $vehicleB->setNumPlate("256-KFC");
        $vehicleB->setPurchaseDate(new \DateTime());
        $vehicleB->setPower(30);
        $manager->persist($vehicleB);
        $vehicleC = new Vehicle();
        $vehicleC->setNumPlate("950-KFC");
        $vehicleC->setPurchaseDate(new \DateTime());
        $vehicleC->setPower(90);
        $manager->persist($vehicleC);
        $vehicleBbis = new Vehicle();
        $vehicleBbis->setNumPlate("123-KFC");
        $vehicleBbis->setPurchaseDate(new \DateTime());
        $vehicleBbis->setPower(90);
        $manager->persist($vehicleBbis);

        // Create Formulas
        // Create Formula Code
        // Formule Code
        $formula = new Formula();
        $wording = "Formule Code";
        $formula->setWording($wording);
        $formula->setSlug($wording);
        $formula->setPrice(313);
        $manager->persist($formula);
        // Formule Code illimité
        $formula = new Formula();
        $wording = "Formule Code illimité";
        $formula->setWording($wording);
        $formula->setSlug($wording);
        $formula->setPrice(1252);
        $manager->persist($formula);
        // Formule Formule A
        $formula = new Formula();
        $wording = "Formule A";
        $formula->setWording($wording);
        $formula->setSlug($wording);
        $formula->setPrice(1200);
        $formula->setMinAge(18);
        $drivingFormula = new DrivingFormula();
        $drivingFormula->setNbHours(24);
        $drivingFormula->setFormula($formula);
        $drivingFormula->addVehicle($vehicleA);
        $manager->persist($drivingFormula);
        $manager->persist($formula);
        // Formule Formule B
        $formulaB = new Formula();
        $wording = "Formule B";
        $formulaB->setWording($wording);
        $formulaB->setSlug($wording);
        $formulaB->setPrice(1200);
        $drivingFormula = new DrivingFormula();
        $drivingFormula->setNbHours(20);
        $drivingFormula->setFormula($formulaB);
        $drivingFormula->addVehicle($vehicleB);
        $manager->persist($drivingFormula);
        $manager->persist($formulaB);
        // Formule Formule C
        $formula = new Formula();
        $wording = "Formule C";
        $formula->setWording($wording);
        $formula->setSlug($wording);
        $formula->setPrice(3600);
        $formula->setMinAge(21);
        $drivingFormula = new DrivingFormula();
        $drivingFormula->setNbHours(70);
        $drivingFormula->setFormula($formula);
        $drivingFormula->addVehicle($vehicleC);
        $manager->persist($drivingFormula);
        $manager->persist($formula);
        // Formule Conduite accompagnée B
        $formula = new Formula();
        $wording = "Formule Conduite accompagnée B";
        $formula->setWording($wording);
        $formula->setSlug($wording);
        $formula->setPrice(1000);
        $drivingFormula = new DrivingFormula();
        $drivingFormula->setNbHours(22);
        $drivingFormula->setFormula($formula);
        $drivingFormula->addVehicle($vehicleBbis);
        $manager->persist($drivingFormula);
        $manager->persist($formula);
        // Formule Conduite normale B
        $formula = new Formula();
        $wording = "Formule Conduite normale B";
        $formula->setWording($wording);
        $formula->setSlug($wording);
        $formula->setPrice(1100);
        $formula->setMinAge(18);
        $drivingFormula = new DrivingFormula();
        $drivingFormula->setNbHours(20);
        $drivingFormula->setFormula($formula);
        $drivingFormula->addVehicle($vehicleB);
        $manager->persist($drivingFormula);
        $manager->persist($formula);
        // Formule cours supplémentaire
        $formula = new Formula();
        $wording = "Formule cours supplémentaire au formule A";
        $formula->setWording($wording);
        $formula->setSlug($wording);
        $formula->setPrice(38);
        $formula->setMinAge(16);
        $drivingFormula = new DrivingFormula();
        $drivingFormula->setNbHours(1);
        $drivingFormula->setFormula($formula);
        $drivingFormula->addVehicle($vehicleA);
        $manager->persist($drivingFormula);
        $manager->persist($formula);
        $formula = new Formula();
        $wording = "Formule cours supplémentaire au formule B";
        $formula->setWording($wording);
        $formula->setSlug($wording);
        $formula->setPrice(38);
        $formula->setMinAge(16);
        $drivingFormula = new DrivingFormula();
        $drivingFormula->setNbHours(1);
        $drivingFormula->setFormula($formula);
        $drivingFormula->addVehicle($vehicleB);
        $drivingFormula->addVehicle($vehicleBbis);
        $manager->persist($drivingFormula);
        $manager->persist($formula);
        $formula = new Formula();
        $wording = "Formule cours supplémentaire au formule C";
        $formula->setWording($wording);
        $formula->setSlug($wording);
        $formula->setPrice(38);
        $formula->setMinAge(16);
        $drivingFormula = new DrivingFormula();
        $drivingFormula->setNbHours(1);
        $drivingFormula->setFormula($formula);
        $drivingFormula->addVehicle($vehicleC);
        $manager->persist($drivingFormula);
        $manager->persist($formula);

        // Create Trainer User
        $userTrainer = new User();
        $userTrainer->setFirstName("Christophe");
        $userTrainer->setLastName("WANG");
        $manager->persist($userTrainer);
        // Create Trainer
        $trainer = new Trainer();
        $trainer->addFormula($formula);
        $trainer->addFormula($formulaB);
        $trainer->setNumSs("2990820910000");
        $trainer->setIdentity($userTrainer);
        $manager->persist($trainer);



        // Create Candidate User
        $userCandidate = new User();
        $userCandidate->setFirstName("Sami");
        $userCandidate->setLastName("Kanaïe Atrian");
        $manager->persist($userCandidate);
        // Create Candidate
        $candidate1 = new Candidate();
        $candidate1->setIdentity($userCandidate);
        $candidate1->setEmail("sami.ka@ensiie.fr");
        $candidate1->setAddress("1 Rue de la Résistance, 91000 Évry-Courcouronnes");
        $candidate1->setAge(20);
        $manager->persist($candidate1);

        // Create Candidate User
        $userCandidate = new User();
        $userCandidate->setFirstName("Usman");
        $userCandidate->setLastName("MOHAMMAD");
        $manager->persist($userCandidate);
        // Create Candidate
        $candidate2 = new Candidate();
        $candidate2->setIdentity($userCandidate);
        $candidate2->setEmail("usman.mohammad@ensiie.fr");
        $candidate2->setAddress("1 Rue de la Résistance, 91000 Évry-Courcouronnes");
        $candidate2->setAge(23);
        $manager->persist($candidate2);

        // Create CourseDates
        $courseDates = new CourseDates();
        $courseDates->setStartDate(new \DateTime("now"));
        $courseDates->setEndDate(new \DateTime("tomorrow"));
        $courseDates->setIsConfirm(true);
        $courseDates->setIsAchieve(false);
        $courseDates->setIsRedirectedToAnotherTrainer(false);
        $courseDates->setTrainer($trainer);
        $courseDates->setCandidate($candidate1);
        $courseDates->setVehicle($vehicleB);
        $manager->persist($courseDates);

        $manager->flush();
    }
}
