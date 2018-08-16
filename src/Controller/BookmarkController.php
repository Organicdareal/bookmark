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
     * @param Request $request
     * @param int $page
     * @param LinkRepository $linkRepository
     * @return Response
     * @Route("/", name="home", defaults={"page" = 1})
     * @Route("/{page}", requirements={"page" = "\d+"}, name="home_paginated")
     *
     * Displays a list of all the bookmarks in a paginated table.
     */
    public function index(Request $request, int $page, LinkRepository $linkRepository): Response
    {
        $bookmarks = $linkRepository->findAllPaginated($page);
        return $this->render('bookmark/index.html.twig', ['links' => $bookmarks]);
    }

    /**
     * @param Request $request
     * @param OEmbedFetcher $fetcher
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/new_bookmark", name="new_bookmark", methods="GET|POST")
     *
     * Gets a LinkType form with url only (jQuery loads it in modal), then persist entities on submit
     */
    public function newBookmark(Request $request, OEmbedFetcher $fetcher)
    {
        $em = $this->getDoctrine()->getManager();

        //create a form with data-class = null to return only url field
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
            //fetch oEmbed parameters with service
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
     * @param Request $request
     * @param Link $link
     * @param OEmbedFetcher $fetcher
     * @return JsonResponse
     * @Route("/bookmark/{id}", name="bookmark_view", methods="GET")
     *
     * Fetches oEmbed parameters and gets the embed code (jQuery loads it in modal)
     */
    public function viewBookmark(Request $request, Link $link, OEmbedFetcher $fetcher):JsonResponse
    {
        //fetch oEmbed parameters with service
        $infos = $fetcher->fetchUrl($link->getUrl());

        return new JsonResponse(array(
            'message' => 'Bookmark fetched',
            'title' => $infos->getTitle(),
            'code' => $infos->getCode()), 200);
    }

    /**
     * @param Request $request
     * @param Link $link
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/edit_bookmark/{id}", name="bookmark_edit", methods="GET|PUT")
     *
     * Gets a LinkType form and fill it with selected bookmark infos (jQuery loads it in modal), then flush entities on submit
     */
    public function editBookmark(Request $request, Link $link)
    {
        $em = $this->getDoctrine()->getManager();
        //create a form with data-class = entityClass to return the right fields
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
     * @param Request $request
     * @param Link $link
     * @return Response
     * @Route("/delete_bookmark/{id}", name="bookmark_delete", methods="DELETE")
     *
     * Deletes a bookmark
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
