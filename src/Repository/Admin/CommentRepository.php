<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\DBALException;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public  function getAllComments(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
       SELECT c.id,c.subject,c.comment,c.status,c.ip,c.user_id,c.rate,c.blog_id,u.name,u.surname,b.title 
       FROM comment c JOIN user u ON u.id = c.user_id 
       JOIN blog b ON b.id=c.blog_id 
       ORDER BY c.id DESC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        //returns an array of arrays
        return $stmt->fetchAll();
    }

    public  function getAllComments2($userId): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
       SELECT c.id,c.subject,c.comment,c.status,c.ip,c.user_id,c.rate,c.blog_id,b.title 
       FROM comment c 
       JOIN blog b ON b.id=c.blog_id
       WHERE c.user_id = :userid
       ORDER BY c.id DESC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['userid' => $userId]);

        //returns an array of arrays
        return $stmt->fetchAll();
    }



    public function  getACommentUser($userId): array {
        $qb=$this->createQueryBuilder('c')
            ->select('c.id,c.blog_id,c.rate,c.subject,c.comment,c.status,b.title')
            ->leftJoin('App\Entity\Blog','b','WITH','b.id=c.blog_id')
            ->where('c.user_id = :userid')
            ->setParameter('userid',$userId)
            ->orderBy('c.id','DESC');

        $query=$qb->getQuery();
        return $query->execute();
    }


    public function getAllCommentsUser($userId): array {
        $qb=$this->createQueryBuilder('c')
            ->select('c.id,c.subject,c.comment,c.rate,c.status,b.title,c.blog_id')
            ->leftJoin('App\Entity\Blog','b','WITH','b.id=c.blog_id')
            ->where('c.user_id = :user_id')
            ->setParameter('user_id',$userId)
            ->orderBy('c.id','DESC');
        $query=$qb->getQuery();
        return $query->execute();
    }




    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
