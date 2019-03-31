<?php

declare(strict_types=1);

/*
 * This file is part of the `UCOtrivia` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineORMContext implements Context
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @BeforeScenario
     */
    public function purgeDatabase(): void
    {
        $this->manager->getConnection()->getConfiguration()->setSQLLogger(null);
        $purger = new ORMPurger($this->manager);
        $purger->purge();
        $this->manager->clear();
    }
}
