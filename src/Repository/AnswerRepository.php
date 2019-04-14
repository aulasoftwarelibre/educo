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
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    /**
     * @param Question $question
     *
     * @return Collection|Answer[]
     */
    public function calculateRates(Question $question): Collection
    {
        $total = $this->getEntityManager()
            ->createQuery('
                SELECT COUNT(v.id)
                FROM \App\Entity\Vote v
                LEFT JOIN v.answer a
                WHERE a.question = :question
            ')
            ->setParameter(':question', $question)
            ->getSingleScalarResult()
        ;

        return $question->getAnswers()->map(function (Answer $answer) use ($total) {
            $numOfVotes = $this->getEntityManager()
                ->createQuery('
                    SELECT COUNT(v.id)
                    FROM \App\Entity\Vote v
                    WHERE v.answer = :answer
                ')
                ->setParameter('answer', $answer)
                ->getSingleScalarResult()
            ;

            $answer->setRate($total ? $numOfVotes / $total * 100 : 0);

            return $answer;
        });
    }
}
