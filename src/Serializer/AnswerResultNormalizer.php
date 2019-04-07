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

namespace App\Serializer;

use App\Entity\Answer;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class AnswerResultNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'ANSWER_RESULT_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null, array $context = [])
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Answer;
    }

    /**
     * @param Answer $object
     * @param null   $format
     * @param array  $context
     *
     * @throws ExceptionInterface
     *
     * @return array|bool|float|int|string|void
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (false === $object->getQuestion()->getIsAcceptingAnswers()) {
            $context['groups'][] = 'can_retrieve_right_answer';
        }

        $context[self::ALREADY_CALLED] = true;

        return $this->normalizer->normalize($object, $format, $context);
    }
}
