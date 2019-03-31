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

namespace App\DTO;

use App\Entity\Question;

final class SessionOutput
{
    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $slug;

    /**
     * @var bool|null
     */
    public $isActive;

    /**
     * @var Question|null
     */
    public $activeQuestion;
}
