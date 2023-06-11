<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/dashboard', name: 'home.index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Reclamation::class);

        $qb = $repository->createQueryBuilder('r');
        $qb->select('COUNT(r.id)')->where("r.etat = 'En Attente'");
        if ($this->getUser()->getType() == 0){
            $qb->andWhere('r.usager_id = '. $this->getUser()->getId());
        }
        $count_attente = $qb->getQuery()->getSingleScalarResult();

        $count_encours = $qb->select('COUNT(r.id)')->where("r.etat = 'En Cours'");
        if ($this->getUser()->getType() == 0){
            $qb->andWhere('r.usager_id = '. $this->getUser()->getId());
        }
        $count_encours = $qb->getQuery()->getSingleScalarResult();

        $count_traite = $qb->select('COUNT(r.id)')->where("r.etat = 'Traité'");
        if ($this->getUser()->getType() == 0){
            $qb->andWhere('r.usager_id = '. $this->getUser()->getId());
        }
        $count_traite = $qb->getQuery()->getSingleScalarResult();

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('home/index.html.twig',[
            'count_attente' => $count_attente,
            'count_encours' => $count_encours,
            'count_traite' => $count_traite,
        ]);
    }
    #[Route('/', name: 'acceuil')]
    public function acceuil(): Response
    {
        return $this->render("accueil.html.twig");
    }
}
