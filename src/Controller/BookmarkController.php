<?php

namespace App\Controller;

use App\Entity\Link;
use App\Form\LinkType;
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
     * @Route("/", name="home", defaults={"page" = 1})
     * @Route("/{page}", name="home_paginated")
     */
    public function index(Request $request, int $page, LinkRepository $linkRepository): Response
    {
        $bookmarks = $linkRepository->findAll($page);
        return $this->render('bookmark/index.html.twig', ['links' => $bookmarks]);
    }

    /**
     * @Route("/new_bookmark", name="new_bookmark", methods="GET|POST")
     */
    public function newBookmark(Request $request, OEmbedFetcher $fetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(LinkType::class, null, array(
                'action' => $this->generateUrl('new_bookmark'),
                'method' => 'POST',
                'attr' => array('id' => 'linkForm'),
                'entity_manager' => $em,
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $link = $fetcher->newUrl($data['url']);

            foreach ($data["keywords"] as $keyword){
                $link->addKeyword($keyword);
            }

            $em->persist($link);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return new JsonResponse(array(
            'message' => 'Created form',
            'form' => $this->renderView('bookmark/form.html.twig', array('form' => $form->createView()))), 200);
    }

    /**
     * @Route("/bookmark/{id}", name="bookmark_view", methods="GET")
     */
    public function viewBookmark(Request $request, Link $link, OEmbedFetcher $fetcher):JsonResponse
    {
        $infos = $fetcher->fetchUrl($link->getUrl());

        return new JsonResponse(array(
            'message' => 'Bookmark fetched',
            'title' => $infos->getTitle(),
            'code' => $infos->getCode()), 200);
    }

    /**
     * @Route("/edit_bookmark/{id}", name="bookmark_edit", methods="GET|POST")
     */
    public function editBookmark(Request $request, Link $link)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(LinkType::class, $link, array(
                'action' => $this->generateUrl('bookmark_edit', array(
                    'id' => $link->getId()
                )),
                'method' => 'PUT',
                'entity_manager' => $em,
            )
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return new JsonResponse(array(
            'message' => 'Created form',
            'form' => $this->renderView('bookmark/form.html.twig', array('form' => $form->createView()))), 200);
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
