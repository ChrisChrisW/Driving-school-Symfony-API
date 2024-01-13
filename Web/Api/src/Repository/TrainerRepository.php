<?php

namespace App\Repository;

use App\Entity\Trainer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trainer>
 *
 * @method Trainer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trainer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trainer[]    findAll()
 * @method Trainer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainerRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trainer::class);
    }

    /**
     * @param Trainer $entity
     * @param bool $flush
     * @return void
     */
    public function save(Trainer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Trainer $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Trainer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $numSs
     * @return Trainer|null
     * @throws NonUniqueResultException
     */
    public function findOneByNumSs(string $numSs): ?Trainer
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.numSs = :numSs')
            ->setParameter('numSs', $numSs)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param string $formulaSlug
     * @return array
     */
    public function findByFormulaSlug(string $formulaSlug): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect(["f", "cd"])
            ->innerJoin("t.formulas", "f")
            ->innerJoin("f.courseDates", 'cd')
            ->andWhere('f.slug = :slug')
            ->setParameter('slug', $formulaSlug)
            ->getQuery()
            ->getResult()
        ;
    }
}
