<?php

namespace ES\PlatformBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends \Doctrine\ORM\EntityRepository
{
	public function getAdvertWithCategories(array $categoryNames) 
	{
		$qb = $this->createQueryBuilder('a')->innerJoin('a.categories', 'c')->addSelect('c');

		$qb->where($qb->expr()->in('c.name', $categoryNames));

		return $qb->getQuery()->getResult();
	}

	public function getAdverts($page, $nbPerPage)
	{
		$qb = $this->createQueryBuilder('a');
		$qb->andWhere('a.published = 1');
		$qb->leftJoin('a.image', 'i')->addSelect('i');
		$qb->leftJoin('a.categories', 'c')->addSelect('c');
		$qb->orderBy('a.date', 'DESC');

		$query = $qb->getQuery();

		$query->setFirstResult(($page-1) * $nbPerPage)->setMaxResults($nbPerPage);

		return new Paginator($query, true);
	}
}