<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param int $howMuch
     * @return Post[]
     */
    public function findRecentPosts(int $howMuch = 10): array
    {
        return $this->findBy([], [
            'createdAt' => "DESC",
            'id' => "DESC"
        ], $howMuch);
    }


    /**
     * List all Post.author
     * @return string[][]
     */
    public function findAuthors(): array
    {
       return $this->createQueryBuilder('p')
            ->select('p.author as fullName')
            ->distinct()
            ->getQuery()
            ->getResult();
    }


    public function findMostActiveAuthors() {

        return $this->createQueryBuilder("p")
            ->select("COUNT(p.author) as nb_posts")
            ->addSelect("p.author as fullName")
            ->groupBy('p.author')
            ->orderBy('nb_posts', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findbyPosts($content) {

        return $this->createQueryBuilder("p")
            ->where("p.content LIKE :content")
            ->setParameter('content', "%$content%")
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param User|null $currentUser
     * @param int|null $limit
     * @param int|null $offset
     * @param User|null $author
     * @param bool $all
     * @param Post|null $primary
     * @return array
     */
    public function findWithCounts(
        ?User $currentUser=null,
        ?int $limit=null,
        ?int $offset=null,
        ?User $author=null,
        bool $all=true,
        ?Post $primary=null
        ): array
    {
        $entities = [];

        $queryBuilder = $this->createQueryBuilder('p')
            ->addSelect("COUNT(DISTINCT c) as commentsCount")
            ->addSelect("COUNT(DISTINCT likers) as likersCount")
            ->leftJoin('p.comments','c')
            ->leftJoin('p.likers','likers')
            ->orderBy('p.id', 'DESC')
            ->groupBy("p.id");

        if ($currentUser !== null) {
            $queryBuilder->addSelect("(SELECT 1 
                                    FROM App\Entity\Post p1
                                    JOIN p1.likers AS l1
                                    WHERE l1.id = :userId
                                    AND p1.id = p.id) AS isLiked")
                            ->setParameter('userId',$currentUser->getId());




        }

        if($limit!==null && $limit>0)
        {
            $queryBuilder->setMaxResults($limit);
            if($offset!==null && $offset>=0)
            {
                $queryBuilder->setFirstResult($offset);
            }
        }

        $where = "";
        if ($author !== null) {
            $ids = [$author->getId()];
            foreach ($author->getFollowings() as $following) {
                $ids[] = $following->getId();
            }
            $where = "p.author IN (:ids)";
            $queryBuilder->setParameter("ids", $ids);
        }

        if(!$all && $primary===null)
        {
            if(strlen($where)!==0){
                $where = $where." AND ";
            }
            $where = $where . "p.parent IS NULL";
            //$queryBuilder->where("p.parent IS NULL");
        }

        if($primary!==null)
        {
            if(strlen($where)!==0){
                $where = $where." AND ";
            }
            $where = $where ."p.parent = :primary";
            //$queryBuilder->where("p.parent = :primary");
            $queryBuilder->setParameter('primary',$primary);
        }



        if(strlen($where)!==0){
            $queryBuilder->where($where);
        }


        $results = $queryBuilder->getQuery()->getResult();

        foreach ($results as $row) {
            /** @var Post $post */
            $post = $row[0];


            $post->setCommentsCount($row['commentsCount']);
            $post->setLikersCount($row['likersCount']);
            if($currentUser!==null){
                $post->setIsLiked($row['isLiked']==1);
            }
            $entities[] = $post;
        }

        return $entities;
    }

    /**
     * @param User|null $currentUser
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findPrimariesWithCounts(?User $currentUser=null, ?int $limit=null, ?int $offset=null): array
    {
        return $this->findWithCounts($currentUser,$limit,$offset,null,false,null);
    }

    /**
     * @param User|null $currentUser
     * @param int|null $limit
     * @param int|null $offset
     * @param User $author
     * @return array
     */
    public function findPrimariesFromAuthorWithCounts(
        User $author,
        ?User $currentUser=null,
        ?int $limit=null,
        ?int $offset=null
        ): array
    {
        return $this->findWithCounts($currentUser,$limit,$offset,$author,false,null);

    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
