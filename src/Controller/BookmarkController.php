<?php

namespace App\Controller;

use App\Entity\Keyword;
use App\Entity\Link;
use App\Form\LinkType;
use App\Form\PhotoType;
use App\Form\VideoType;
use App\Repository\LinkRepository;
use App\Services\OEmbedFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/new_bookmark", name="new_bookmark", methods="GET|POST")
     */
    public function newBookmark(Request $request, OEmbedFetcher $fetcher)
    {
        $form = $this->createForm(LinkType::class, null, array(
                'action' => $this->generateUrl('new_bookmark'),
                'method' => 'POST',
                'attr' => array('id' => 'linkForm')
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $link = $fetcher->newUrl($data['url']);

            foreach ($data["keywords"] as $keyword){
                $link->addKeyword($keyword);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($link);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return new JsonResponse(array(
            'message' => 'Created form',
            'form' => $this->renderView('bookmark/form.html.twig', array('form' => $form->createView()))), 200);
    }

    /**
     * @Route("/edit_bookmark/{id}", name="bookmark_edit", methods="GET|POST")
     */
    public function editBookmark(Request $request, Link $link)
    {
        $em = $this->getDoctrine()->getManager();
        $className = $em->getMetadataFactory()->getMetadataFor(get_class($link))->getName();

        if ($className == "App\\Entity\\Photo"){
            $form = $this->createForm(PhotoType::class, $link);
        } elseif ($className == "App\\Entity\\Video"){
            $form = $this->createForm(VideoType::class, $link);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('home');
        }

        return new JsonResponse(array(
            'message' => 'Created form',
            'form' => $this->renderView('bookmark/totalform.html.twig', array('form' => $form->createView()))), 200);
    }

    /**
     * @Route("/delete_bookmark/{id}", name="bookmark_delete", methods="DELETE")
     */
    public function deleteBookmark(Request $request, Link $link): Response
    {
        if ($this->isCsrfTokenValid('delete'.$link->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($link);
            $em->flush();
        }

        return $this->redirectToRoute('home');
    }
}
