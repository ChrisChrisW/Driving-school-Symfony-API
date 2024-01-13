<?php

namespace App\Repository;

use App\Entity\CourseDates;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CourseDates>
 *
 * @method CourseDates|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourseDates|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourseDates[]    findAll()
 * @method CourseDates[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseDatesRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseDates::class);
    }

    /**
     * @param CourseDates $entity
     * @param bool $flush
     * @return void
     */
    public function save(CourseDates $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param CourseDates $entity
     * @param bool $flush
     * @return void
     */
    public function remove(CourseDates $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param $numPlate
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function findByVehicleAndStartDateAndEndDate($numPlate, $startDate, $endDate): array
    {
        return $this->createQueryBuilder('cd')
            ->addSelect(['v'])
            ->innerJoin('cd.vehicle', 'v')
            ->where("v.numPlate != :numPlate")
            ->setParameter('numPlate', $numPlate)
            ->andWhere('cd.startDate >= :startDate')
            ->setParameter('startDate', $startDate)
            ->andWhere('cd.endDate <= :endDate')
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param string $candidateEmail
     * @param string $formulaSlug
     * @return array
     */
    public function findNumberOfCoursesByCandidateAndFormula(string $candidateEmail, string $formulaSlug): array
    {
        return $this->createQueryBuilder('cd')
            ->addSelect(['f', 'c'])
            ->where("cd.isConfirm = true")
            ->innerJoin('cd.formula', 'f')
            ->innerJoin('cd.candidate', 'c')
            ->andWhere('c.email = :email')
            ->setParameter('email', $candidateEmail)
            ->andWhere('f.slug = :slug')
            ->setParameter('slug', $formulaSlug)
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
