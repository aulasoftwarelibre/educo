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

namespace App\Controller;

use App\DTO\AnswerDTO;
use App\DTO\QuestionDTO;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Session;
use App\Factory\QuestionDTOFactory;
use App\Form\QuestionDTOType;
use App\Message\Question\EnableQuestionMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/session/{session}/question")
 */
class QuestionController extends AbstractController
{
    /**
     * @var MessageBusInterface
     */
    private $bus;
    /**
     * @var QuestionDTOFactory
     */
    private $questionDTOFactory;

    public function __construct(MessageBusInterface $bus, QuestionDTOFactory $questionDTOFactory)
    {
        $this->bus = $bus;
        $this->questionDTOFactory = $questionDTOFactory;
    }

    /**
     * @Route("/{question}", name="question_show", methods={"GET"})
     */
    public function show(Session $session, Question $question): Response
    {
        return $this->render('question/show.html.twig', [
            'session' => $session,
            'question' => $question,
        ]);
    }

    /**
     * @Route("/new", name="question_new", methods={"GET","POST"})
     */
    public function new(Request $request, Session $session): Response
    {
        $questionDTO = $this->questionDTOFactory->newQuestionDTO();

        $form = $this->createForm(QuestionDTOType::class, $questionDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question = $this->questionDTOFactory->newQuestionFromDTO($questionDTO);
            $question->setSession($session);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('session_show', ['id' => $session->getId()]);
        }

        return $this->render('question/new.html.twig', [
            'session' => $session,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{question}/edit", name="question_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Session $session, Question $question): Response
    {
        $questionDTO = $this->questionDTOFactory->newQuestionDTOFromQuestion($question);

        $form = $this->createForm(QuestionDTOType::class, $questionDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updateQuestion($question, $questionDTO);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('session_show', ['id' => $session->getId()]);
        }

        return $this->render('question/edit.html.twig', [
            'session' => $session,
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{question}", name="question_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Session $session, Question $question): Response
    {
        if ($this->isCsrfTokenValid('delete' . $question->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('session_show', ['id' => $session->getId()]);
    }

    /**
     * @Route("/{question}/enable", name="question_enable", methods={"GET","POST"})
     */
    public function enable(Request $request, Session $session, Question $question): Response
    {
        $this->bus->dispatch(
            new EnableQuestionMessage($question->getId())
        );

        return $this->redirectToRoute('session_show', ['id' => $session->getId()]);
    }

    private function updateQuestion(Question $question, QuestionDTO $questionDTO): void
    {
        $question->setContent($questionDTO->getContent());
        $question->setDuration($questionDTO->getDuration());

        array_map(static function (Answer $answer, AnswerDTO $answerDTO): void {
            $answer->setContent($answerDTO->getContent());
            $answer->setIsCorrect($answerDTO->isCorrect());
        }, $question->getAnswers()->toArray(), $questionDTO->getAnswers()->toArray());
    }
}
