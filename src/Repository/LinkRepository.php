<?php

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
    /**
     * LinkRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Link::class);
    }


    /**
     * @param int $page
     * @return Pagerfanta
     *
     * returns paginated bookmarks, max number is defined by Link class constant
     */
    public function findAllPaginated(int $page = 1): Pagerfanta
    {
        $qb = $this->createQueryBuilder('l')->orderBy('l.date', 'DESC');
        return $this->createPaginator($qb->getQuery(), $page);
    }

    /**
     * @param Query $query
     * @param int $page
     * @return Pagerfanta
     */
    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Link::NUM_ITEMS);
        $paginator->setCurrentPage($page);
        return $paginator;
    }
}
