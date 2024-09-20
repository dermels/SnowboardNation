<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Illustration;
use App\Entity\Video;
use App\Form\FigureType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class FigureController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    )
    {
    }

    #[Route("/figure/list", name: "figure_list", methods: ['GET'])]
    public function list()
    {
        $figures = $this->entityManager->getRepository(Figure::class)->findAll();
        return $this->render('figure/list.html.twig', [
            'figures' => $figures,
        ]);
    }

    #[Route("/figure/show/{id}", name: "figure_show", methods: ['GET'])]
    public function show($id)
    {
        $figure = $this->entityManager->getRepository(Figure::class)->find($id);
        return $this->render('figure/show.html.twig', [
            'figure' => $figure,
        ]);
    }

    #[Route("/figure/new", name: "figure_new", methods: ['GET'])]
    public function new()
    {
        return $this->render('figure/form.html.twig');
    }

    #[Route("/figure/new/submit", name: "figure_new_submit", methods: ['POST'])]
    public function submitFormAction(Request $request)
    {

        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? null;
        $mainIllustration = $data['mainIllustration'] ?? null;
        $description = $data['description'] ?? null;
        $figureGroup = $data['figureGroup'] ?? null;
        $illustrations = $data['illustrations'] ?? null;
        $videos = $data['videos'] ?? null;

        // Créer un nom unique pour le fichier
        if ($mainIllustration === null || $name === null || $description === null || $figureGroup === null) {
            return new Response(
                json_encode(["code" => 400, "message" => "Il manque des informations pour créer la figure"]),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json']
            );
        }
        $filename = md5(uniqid()) . '.' . pathinfo($mainIllustration["name"], PATHINFO_EXTENSION);
        $imgPath = '/uploads/images/' . $filename;
        $path = $this->getParameter('kernel.project_dir') . "/public" . $imgPath;

        $directoryPath = pathinfo($path, PATHINFO_DIRNAME);
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        $data = base64_decode(substr($mainIllustration["base64"], strpos($mainIllustration["base64"], ',') + 1));
        file_put_contents($path, $data);

        $figure = new Figure();
        $figure->setName($name);
        $figure->setDescription($description);
        $figure->setFigureGroup($figureGroup);
        $mainIllustration = (new Illustration())->setUrl($imgPath);
        $figure->setMainIllustration($mainIllustration);

        foreach ($illustrations as $illustration) {
            $filename = md5(uniqid()) . '.' . pathinfo($illustration["name"], PATHINFO_EXTENSION);
            $imgPath = '/uploads/images/' . $filename;
            $path = $this->getParameter('kernel.project_dir') . "/public" . $imgPath;
            $directoryPath = pathinfo($path, PATHINFO_DIRNAME);
            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }
            $data = base64_decode(substr($illustration["base64"], strpos($illustration["base64"], ',') + 1));
            file_put_contents($path, $data);
            $newIllustration = new Illustration();
            $newIllustration->setUrl($imgPath);
            $this->entityManager->persist($newIllustration);
            $figure->addIllustration($newIllustration);
        }

        foreach ($videos as $video) {
            $newVideo = new Video();
            $newVideo->setUrl($video);
            $this->entityManager->persist($newVideo);
            $figure->addVideo($newVideo);
        }

        $this->entityManager->persist($figure);
        $this->entityManager->flush();

        return new Response(
            json_encode(["code" => 200, "id" => $figure->getId()]),
            Response::HTTP_OK, // 200
            ['content-type' => 'application/json']
        );

    }

    #[Route("/figure/submit/success", name: "figure_submit_success")]
    public function submitSuccess(Request $request)
    {
        $id = $request->query->get('id');
        $figure = $this->entityManager->getRepository(Figure::class)->find($id);

        $this->addFlash(
            'success',
            'Ajout du tricks : ' . $figure->getName() . ' [OK]'
        );

        return $this->redirectToRoute('figure_list');
    }






}
