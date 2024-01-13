<?php

namespace App\PersistProcessor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class VehiclePersistProcessor
 */
class VehiclePersistProcessor implements ProcessorInterface
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
     * @return Vehicle|null
     * @throws \Exception
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): ?Vehicle
    {
        $content = $this->_request->toArray(); // get content

        $vehicleRepository = $this->_entityManager->getRepository(Vehicle::class);
        $vehicle = $vehicleRepository->findOneByNumPlate($content["cuurentNumPlate"]);

        if($vehicle) {
            if($data->getPurchaseDate() !== null) {
                $vehicle->setPurchaseDate($data->getPurchaseDate());
            }

            if($data->getPower() !== null) {
                $vehicle->setPower($data->getPower());
            }

            $this->_entityManager->persist($vehicle);
            $this->_entityManager->flush();
            return $data;
        }

        throw new \Error("Vous vous êtes trompés de véhicule !");
    }
}