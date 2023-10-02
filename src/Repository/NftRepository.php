<?php

namespace App\Repository;

use App\Entity\Nft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Nft>
 *
 * @method Nft|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nft|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nft[]    findAll()
 * @method Nft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nft::class);
    }

//    /**
//     * @return Nft[] Returns an array of Nft objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Nft
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function searchEngine($filters)
    {
        $query = $this->createQueryBuilder('n')
            ->leftJoin('n.gallery', 'gallery')
            ->leftJoin('gallery.owner', 'user')
            ->leftJoin('n.category', 'category');

        if (!is_null($filters['searchBar'])) {
            $query->andWhere('n.name LIKE :search')
                ->setParameter(':search', '%' . $filters['searchBar'] . '%');
        }

        if (!is_null($filters['category'])) {
            $query->andWhere('category = :category')
                ->setParameter(':category', $filters['category']);
        }

        if (!is_null($filters['categoryChild'])) {
            $query->andWhere('n.category = :categoryChild')
                ->setParameter(':categoryChild', $filters['categoryChild']);
        }

        if (!is_null($filters['valueMin'])) {
            $query->andWhere('n.price >= :valueMin')
                ->setParameter(':valueMin', $filters['valueMin']);
        }

        if (!is_null($filters['valueMax'])) {
            $query->andWhere('n.price <= :valueMax')
                ->setParameter(':valueMax', $filters['valueMax']);
        }

        if (!is_null($filters['userAjout'])) {
            $query->andWhere('user.id = :userId')
                ->setParameter(':userId', $filters['userAjout']->getId());
        }

        return $query->getQuery()->getResult();
    }


}
