<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    // /**
    //  * @return Blog[] Returns an array of Blog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Blog
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    // src/Repository/BlogRepository.php
   /* public function findOneByIdJoinedToCategory($blogId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p, c
        FROM App\Entity\Product p
        INNER JOIN p.category c
        WHERE p.id = :id'
        )->setParameter('id', $blogId);

        return $query->getOneOrNullResult();
    }
    */
    public  function getAllBlogs() :array
    {

        $conn= $this->getEntityManager()->getConnection();

        $sql= '
  SELECT b.*,c.title as catname,u.name,u.surname FROM blog b 
  JOIN  category c ON c.id=b.category_id
  JOIN  user u ON u.id=b.user_id
  ORDER BY c.title ASC
    ' ;
        $stmt=$conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }





}
