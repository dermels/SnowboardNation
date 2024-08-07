<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FigureController extends AbstractController
{
    #[Route('/figure', name: 'app_figure')]
    public function index(): Response
    {
        return $this->render('figure/index.html.twig', [
            'controller_name' => 'FigureController',
        ]);
    }

    #[Route("/figure/new", name:"figure_new")]
    public function new(Request $request)
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // Handle the uploaded images data

            // Ideally, you should upload them in the service layer, not in the controller
            // You may want to create a uploadFile service and inject it into the controller

            foreach ($figure->getIllustrations() as $illustration) {

                $file = $illustration->getImageFile();

                // overwrite 'imageFile' with the path of the file
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // Move the file to the directory where images are stored
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );

                $illustration->setUrl($fileName);

            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($figure);
            $entityManager->flush();

            return $this->redirectToRoute('figure_show', ['id' => $figure->getId()]);
        }

        return $this->render('figure/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
