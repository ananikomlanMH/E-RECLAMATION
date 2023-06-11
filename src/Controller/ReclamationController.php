<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\UserRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->getUser()->getType() == 0) {
            $data = $reclamationRepository->findBy(['usager_id' => $this->getUser()->getId()]);
        } else {
            $data = $reclamationRepository->findAll();
        }

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $data,
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReclamationRepository $reclamationRepository, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $pieceJointe */
            $pieceJointe = $form->get('file')->getData();

            // Vérifier si un fichier a été téléchargé
            if ($pieceJointe) {
                $originalFilename = pathinfo($pieceJointe->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $pieceJointe->guessExtension();

                // Déplacer le fichier vers le répertoire où vous souhaitez le stocker
                $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/uploads/reclamations';

                try {
                    $pieceJointe->move(
                        $uploadsDirectory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'erreur en conséquence
                }

                // Mettre à jour l'attribut "pieceJointe" de l'entité avec le nom du fichier
                $reclamation->setFile($newFilename);
            }
            $reclamation->setUsagerId($userRepository->find($this->getUser()->getId()));
            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/print', name: 'app_reclamation_printAll', methods: ['GET'])]
    public function printAll(ReclamationRepository $reclamationRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = [
            'imageSrc' => $this->imageToBase64($this->getParameter('kernel.project_dir') . '/public/assets/img/top.png'),
            'reclamations' => $reclamationRepository->findAll(),
        ];
        $html = $this->renderView('reclamation/printAll.html.twig', $data);
        $options = new Options();
//        $options->set('defaultFont', 'Century Gothic');
        $options->set('dpi', 150);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response (
            $dompdf->stream('Reclamations', ["Attachment" => false]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/print', name: 'app_reclamation_print', methods: ['GET'])]
    public function print(Reclamation $reclamation): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = [
            'imageSrc' => $this->imageToBase64($this->getParameter('kernel.project_dir') . '/public/assets/img/top.png'),
            'reclamation' => $reclamation,
        ];
        $html = $this->renderView('reclamation/print.html.twig', $data);
        $options = new Options();
//        $options->set('defaultFont', 'Century Gothic');
        $options->set('dpi', 150);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response (
            $dompdf->stream('Reclamation #' . $reclamation->getId(), ["Attachment" => false]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }


    public function getPieceJointe($filename): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/uploads/reclamations/' . $filename;

        // Vérifier si le fichier existe
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Fichier non trouvé.');
        }

        // Renvoyer le fichier en tant que réponse
        return new Response(
            file_get_contents($filePath),
            200,
            [
                'Content-Type' => 'application/pdf', // Remplacez par le type MIME approprié pour votre fichier
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $reclamationRepository->remove($reclamation, true);
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }


    private function imageToBase64($path): string
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
