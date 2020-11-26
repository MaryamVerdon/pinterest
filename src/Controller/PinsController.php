<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="pins_show", methods={"GET", "POST"})
     */
    public function show(Pin $pin): Response
    {
        return $this->render('pins/show.html.twig', compact("pin"));
    }


    /**
     * @Route("/pins/create", name="pins_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em): Response {

        //utiliser un tableau
        /**$form = $this->createFormBuilder()
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->getForm()
        ;

        $form->handleRequest($request); //récupérer les data remplie du form
        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $pin = new Pin;
            $pin->setTitle($data['title']);
            $pin->setDescription($data['description']);
            $em->persist($pin);
            $em->flush();

            return  $this->redirectToRoute('home');
         **/

        //utiliser un objet
        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request); //récupérer les data remplie du form

        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($pin);
            $em->flush();

            return  $this->redirectToRoute('home');

        }
        return $this->render('pins/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="pins_edit", methods={"GET", "PUT"})
     */
    public function edit(Request $request, EntityManagerInterface $em, Pin $pin): Response
    {
        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return  $this->redirectToRoute('home');

        }

        return $this->render('pins/edit.html.twig', [
            'form' => $form->createView(),
            'pin' => $pin
        ]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="pins_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Pin $pin): Response
    {
        //verificatin du token
        if ($this->isCsrfTokenValid('pin_deletion' . $pin->getId(),  $request->request->get('csrf_token') ))
        {
            $em->remove($pin);
            $em->flush();
        }


        return  $this->redirectToRoute('home');
    }


}
