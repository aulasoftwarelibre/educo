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

use App\Entity\Question;
use App\Entity\Session;
use App\Repository\VoteRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

final class SessionResultNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'SESSION_RESULT_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';
    /**
     * @var VoteRepository
     */
    private $voteRepository;
    /**
     * @var RequestStack
     */
    private $request;

    public function __construct(VoteRepository $voteRepository, RequestStack $request)
    {
        $this->voteRepository = $voteRepository;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $context options that normalizers have access to
     */
    public function supportsNormalization($data, $format = null, array $context = [])
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Session;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param Session $object  Object to normalize
     * @param string  $format  Format the normalization result will be encoded as
     * @param array   $context Context options for the normalizer
     *
     * @throws InvalidArgumentException   Occurs when the object given is not an attempted type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     * @throws ExceptionInterface         Occurs for all the other cases of errors
     *
     * @return array|string|int|float|bool
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        $data = $this->normalizer->normalize($object, $format, $context);

        if ($object->getActiveQuestion()) {
            $previousVote = $this->getPreviousVote($object->getActiveQuestion());

            if (false !== $previousVote) {
                $data['voted'] = $previousVote ? $previousVote->getAnswer()->getId() : null;
            }
        }

        return $data;
    }

    /**
     * @param Question $question
     *
     * @return \App\Entity\Vote|bool|null
     */
    private function getPreviousVote(Question $question)
    {
        $clientIpAddress = $this->request->getCurrentRequest()
            ? $this->request->getCurrentRequest()->getClientIp()
            : null;

        if (!$clientIpAddress) {
            return false;
        }

        return $this->voteRepository->findOneBy([
            'question' => $question,
            'clientUniqueId' => $clientIpAddress,
        ]);
    }
}
