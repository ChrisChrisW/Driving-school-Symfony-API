<?php

namespace App\PersistProcessor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Formula;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class FormulaPersistProcessor
 */
class FormulaPersistProcessor implements ProcessorInterface
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
     * @param $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return Formula
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []) : Formula
    {
        $content = $this->_request->toArray(); // get content

        $formulaRepository = $this->_entityManager->getRepository(Formula::class);
        $formula = $formulaRepository->findOneBySlug($content['currentSlug']);

        if($formula) {
            $formula->setSlug($formula->getWording());

            if(isset($content['price'])) {
                $formula->setPrice($content['price']);
            }

            if(isset($content['nbHours']) && !is_null($formula->getDrivingFormula()->getNbHours())) {
                $formula->getDrivingFormula()->setNbHours($content['nbHours']);
            }

            if(isset($content['vehicleNumPlate'])) {
                $vehicleRepository = $this->_entityManager->getRepository(Vehicle::class);
                $vehicle = $vehicleRepository->findOneBy(["numPlate" => $content['vehicleNumPlate']]);
                $formula->getDrivingFormula()->addVehicle($vehicle);
            }

            if(isset($content['removeVehicle'])) {
                $vehicleRepository = $this->_entityManager->getRepository(Vehicle::class);
                $vehicle = $vehicleRepository->findOneBy(["numPlate" => $content['removeVehicle']]);
                $formula->getDrivingFormula()->removeVehicle($vehicle);
            }

            $this->_entityManager->persist($formula);
            $this->_entityManager->flush();

            return $data;
        }

        throw new \Error("Vous vous êtes trompés de formule !");
    }
}