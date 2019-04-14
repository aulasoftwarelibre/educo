<?php

declare(strict_types=1);

/*
 * This file is part of the `edUCO` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    public function cleanVotes(Question $question): void
    {
        $answers = $question->getAnswers()
            ->map(function (Answer $answer) {
                return $answer->getId();
            })
        ;

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete('\App\Entity\Vote', 'o')
            ->where($qb->expr()->in('o.answer', ':answers'))
            ->setParameter('answers', $answers)
            ->getQuery()
            ->execute()
        ;
    }
}
