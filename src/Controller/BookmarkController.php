<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\PhotoType;
use App\Repository\LinkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookmarkController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(LinkRepository $linkRepository): Response
    {
        return $this->render('bookmark/index.html.twig', ['links' => $linkRepository->findAll()]);
    }

    /**
     * @Route("/new_photo", name="new_photo")
     */
    public function newPhoto(Request $request): JsonResponse
    {
        $pic = new Photo();
        $form = $this->createForm(PhotoType::class, $pic);
        $form->add('submit', SubmitType::class, array(
            'label' => 'Create',
            'attr' => array('class' => 'btn btn-success')
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pic);
            $em->flush();

            return new JsonResponse(array('message' => 'Bookmark inserted in database'), 200);
        }

        return new JsonResponse(array(
            'message' => 'Created form',
            'form' => $this->renderView('form.html.twig', array('form' => $form->createView()))), 200);
    }
}
