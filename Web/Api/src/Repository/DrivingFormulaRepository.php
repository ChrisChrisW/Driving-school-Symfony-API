<?php

namespace App\Repository;

use App\Entity\DrivingFormula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DrivingFormula>
 *
 * @method DrivingFormula|null find($id, $lockMode = null, $lockVersion = null)
 * @method DrivingFormula|null findOneBy(array $criteria, array $orderBy = null)
 * @method DrivingFormula[]    findAll()
 * @method DrivingFormula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DrivingFormulaRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DrivingFormula::class);
    }

    /**
     * @param DrivingFormula $entity
     * @param bool $flush
     * @return void
     */
    public function save(DrivingFormula $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param DrivingFormula $entity
     * @param bool $flush
     * @return void
     */
    public function remove(DrivingFormula $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
