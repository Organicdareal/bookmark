<?php

namespace App\Controller;

use App\Entity\Keyword;
use App\Form\KeywordType;
use App\Repository\KeywordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/keyword")
 */
class KeywordController extends Controller
{
    /**
     * @Route("/", name="keyword_index", methods="GET")
     */
    public function index(KeywordRepository $keywordRepository): Response
    {
        return $this->render('keyword/index.html.twig', ['keywords' => $keywordRepository->findAll()]);
    }

    /**
     * @Route("/new", name="keyword_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $keyword = new Keyword();
        $form = $this->createForm(KeywordType::class, $keyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($keyword);
            $em->flush();

            return $this->redirectToRoute('keyword_index');
        }

        return $this->render('keyword/new.html.twig', [
            'keyword' => $keyword,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="keyword_show", methods="GET")
     */
    public function show(Keyword $keyword): Response
    {
        return $this->render('keyword/show.html.twig', ['keyword' => $keyword]);
    }

    /**
     * @Route("/{id}/edit", name="keyword_edit", methods="GET|POST")
     */
    public function edit(Request $request, Keyword $keyword): Response
    {
        $form = $this->createForm(KeywordType::class, $keyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('keyword_edit', ['id' => $keyword->getId()]);
        }

        return $this->render('keyword/edit.html.twig', [
            'keyword' => $keyword,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="keyword_delete", methods="DELETE")
     */
    public function delete(Request $request, Keyword $keyword): Response
    {
        if ($this->isCsrfTokenValid('delete'.$keyword->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($keyword);
            $em->flush();
        }

        return $this->redirectToRoute('keyword_index');
    }
}
