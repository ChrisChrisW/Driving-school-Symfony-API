<?php

namespace App\Controller;

use App\Entity\DrivingFormula;
use App\Entity\Formula;
use App\Entity\FormulaCodeDate;
use App\Entity\User;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Class PostFormulaController for API
 */
#[AsController]
class PostFormulaController extends AbstractController
{
    /**
     * @var Request|null
     */
    private ?Request $_request;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $_entityManager;

    /**
     * @param RequestStack $request
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RequestStack $request, EntityManagerInterface $entityManager)
    {
        $this->_request = $request->getCurrentRequest();
        $this->_entityManager = $entityManager;
    }

    /**
     * @param Formula $formula
     * @return Formula
     * @throws \Exception
     */
    public function __invoke(Formula $formula): Formula
    {
        $content = $this->_request->toArray(); // get content

        if (null !== $content["wording"]) {
            $formula->setSlug(str_replace(" ", "-", strtolower($content["wording"])));

            if (isset($content["minAge"])) {
                $formula->setMinAge($content["minAge"]);
            }

            if(isset($content["nbHours"])) {
                $drivingFormula = new DrivingFormula();
                $drivingFormula->setFormula($formula);
                $drivingFormula->setNbHours($content["nbHours"]);

                if(isset($content["vehiculeNumPlate"])){
                    $vehicleRepository = $this->_entityManager->getRepository(Vehicle::class);
                    $vehicle = $vehicleRepository->findOneByNumPlate($content["vehiculeNumPlate"]);

                    if($vehicle) {
                        $formula->setDrivingFormula($drivingFormula);
                    }

//                throw new \Error('Error : you need to set correct vehicle');
                }

                $this->_entityManager->persist($drivingFormula);
                $this->_entityManager->flush();
            }

            return $formula;
        }

        throw new \Error('Error : you need to set Wording');
    }
}