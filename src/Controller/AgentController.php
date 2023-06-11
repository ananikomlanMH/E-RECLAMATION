<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Form\AgentType;
use App\Repository\AgentRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/agent')]
class AgentController extends AbstractController
{

    #[Route('/print', name: 'agent.print', methods: ['GET'])]
    public function print(AgentRepository $agentRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = [
            'imageSrc'  => $this->imageToBase64($this->getParameter('kernel.project_dir') . '/public/assets/img/top.png'),
            'agents' => $agentRepository->findAll(),
        ];
        $html =  $this->renderView('agent/print.html.twig', $data);
        $options = new Options();
//        $options->set('defaultFont', 'Century Gothic');
        $options->set('dpi', 150);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return new Response (
            $dompdf->stream('Liste des agents', ["Attachment" => false]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }

    #[Route('/', name: 'agent.index', methods: ['GET'])]
    public function index(AgentRepository $agentRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('agent/index.html.twig', [
            'agents' => $agentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'agent.new', methods: ['GET', 'POST'])]
    public function new(Request $request, AgentRepository $agentRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $agent = new Agent();
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agentRepository->save($agent, true);

            return $this->redirectToRoute('agent.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agent/new.html.twig', [
            'agent' => $agent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'agent.show', methods: ['GET'])]
    public function show(Agent $agent): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('agent/show.html.twig', [
            'agent' => $agent,
        ]);
    }

    #[Route('/{id}/edit', name: 'agent.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Agent $agent, AgentRepository $agentRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agentRepository->save($agent, true);

            return $this->redirectToRoute('agent.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agent/edit.html.twig', [
            'agent' => $agent,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'agent.delete', methods: ['POST'])]
    public function delete(Request $request, Agent $agent, AgentRepository $agentRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete'.$agent->getId(), $request->request->get('_token'))) {
            $agentRepository->remove($agent, true);
        }

        return $this->redirectToRoute('agent.index', [], Response::HTTP_SEE_OTHER);
    }

    private function imageToBase64($path): string
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
