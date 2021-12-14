<?php

namespace App\Repository;

use App\Data\ArticleData;
use App\Entity\Article;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     *
     * @return Article[]
     */
    public function findLatest()
    {
        return $this->createQueryBuilder('article')
        ->orderBy('article.created_at', 'DESC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();
    } 

    private function getSearchQuery(ArticleData $search, $ignoreSalary = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('Article')
            ->orderBy('Article.created_at', 'DESC');

            if(!empty($search->job)) {
                $query = $query 
                        ->andWhere('Article.title LIKE :job')
                        ->setParameter('job', "%{$search->job}%");
            } 
            
            if(!empty($search->city)) {
                $query = $query 
                        ->andWhere('Article.city LIKE :city')
                        ->setParameter('city', "%{$search->city}%");
            }

            if(!empty($search->min) && $ignoreSalary = false){
                $query = $query 
                ->andWhere('Article.salary >= :min')
                ->setParameter('min', $search->min);
            }
            

            if(!empty($search->max)){
                $query = $query 
                ->andWhere('Article.salary <= :max')
                ->setParameter('max', $search->max);
            }
            return $query;
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
