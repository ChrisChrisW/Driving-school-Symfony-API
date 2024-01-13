<?php

namespace App\Repository;

use App\Entity\Formula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formula>
 *
 * @method Formula|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formula|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formula[]    findAll()
 * @method Formula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormulaRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formula::class);
    }

    /**
     * @param Formula $entity
     * @param bool $flush
     * @return void
     */
    public function save(Formula $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Formula $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Formula $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $slug
     * @return Formula|null
     * @throws NonUniqueResultException
     */
    public function findOneBySlug(string $slug): ?Formula
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
