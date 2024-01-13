<?php

namespace App\Repository;

use App\Entity\FormulaCodeDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormulaCodeDate>
 *
 * @method FormulaCodeDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormulaCodeDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormulaCodeDate[]    findAll()
 * @method FormulaCodeDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormulaCodeDateRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormulaCodeDate::class);
    }

    /**
     * @param FormulaCodeDate $entity
     * @param bool $flush
     * @return void
     */
    public function save(FormulaCodeDate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param FormulaCodeDate $entity
     * @param bool $flush
     * @return void
     */
    public function remove(FormulaCodeDate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
