<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicle>
 *
 * @method Vehicle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicle[]    findAll()
 * @method Vehicle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehicleRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    /**
     * @param Vehicle $entity
     * @param bool $flush
     * @return void
     */
    public function save(Vehicle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Vehicle $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Vehicle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $numPlate
     * @return Vehicle|null
     * @throws NonUniqueResultException
     */
    public function findOneByNumPlate(string $numPlate): ?Vehicle
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.numPlate = :numPlate')
            ->setParameter('numPlate', $numPlate)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
