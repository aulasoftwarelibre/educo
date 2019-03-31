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

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\SessionOutput;
use App\Entity\Session;

final class SessionOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param Session $object
     * @param string  $to
     * @param array   $context
     *
     * @return object|void
     */
    public function transform($object, string $to, array $context = [])
    {
        $output = new SessionOutput();
        $output->name = $object->getName();
        $output->slug = $object->getSlug();
        $output->isActive = $object->getIsActive();
        $output->activeQuestion = $object->getActiveQuestion();

        return $output;
    }

    /**
     * @param object $object
     * @param string $to
     * @param array  $context
     *
     * @return bool
     */
    public function supportsTransformation($object, string $to, array $context = []): bool
    {
        return SessionOutput::class === $to && $object instanceof Session;
    }
}
