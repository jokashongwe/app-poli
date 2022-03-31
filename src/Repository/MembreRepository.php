<?php

namespace App\Repository;

use App\Entity\Membre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Membre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Membre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Membre[]    findAll()
 * @method Membre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Membre::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Membre $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Membre $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function compter()
    {
        $qb = $this->createQueryBuilder('m');
        return $qb
            ->select('count(m.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByProvince($value =0): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT p.nom, count(*) frequence
            FROM membre m, federation f, province p
            WHERE m.federation_id = f.id and f.province_id = p.id
            GROUP BY p.nom
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function countByGenre($value = 0):array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT m.genre, count(*) frequence
            FROM membre m
            GROUP BY m.genre
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findByDiffusion($province, $federaion){
        return $this->createQueryBuilder('m')
            ->addSelect('f')
            ->innerJoin("m.federation", "f")
            ->where("f.id IN(:fede)")
            ->orWhere("f.province IN(:prov)")
            ->setParameter('fede', $federaion)
            ->setParameter('prov', $province)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Membre[] Returns an array of Membre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Membre
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
