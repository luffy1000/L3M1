<?php

namespace App\Controller\Admin;

use App\Entity\Reservations;
use App\Entity\Users;
use App\Form\AssignationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class ReservationController extends AbstractController
{    

    #[Route('/reservation', name: 'reservation')]
    public function reservation(EntityManagerInterface $em): Response
    {
        if(!$this->getUser()){
            return  $this->redirectToRoute('main');
        }

        //les reservations confirme
        $reservations = $em->getRepository(Reservations::class)->findBy(['isPaid'=>'true'],['created_at'=>'asc']);
        
       
        return $this->render('admin/reservation/index.html.twig',[
    
            'reservations'=>$reservations
        ]);
    }

    #[Route('/assigner_form/{reference}',name:'assignation_form')]
    public function assign_form($reference,EntityManagerInterface $em, Request $request): Response
    {   
        if(!$this->getUser()){
            return  $this->redirectToRoute('main');
        }

        // listes des instructeurs
        $users = $em->getRepository(Users::class)->findInstructors();

       


        $assignationForm = $this->createForm(AssignationType::class, null, [
            'action' => $this->generateUrl('admin_assignation', ['reference'=>$reference]),
            'method'=>'POST',
            'users' => $users
        ]);

       


        return $this->render('admin/reservation/assignation.html.twig',[
            'assignationform' => $assignationForm->createView(),
        ]);
    }

    #[Route('/assigner/{reference}',name:'assignation', methods:['POST'])]
    public function assign($reference,EntityManagerInterface $em, Request $request): Response
    {   
        if(!$this->getUser()){
            return  $this->redirectToRoute('main');
        }

        // on recupere la reservation courante
        $reservation = $em->getRepository(Reservations::class)->findBy(['reference'=>$reference]);
        

        // listes des instructeurs
        $users = $em->getRepository(Users::class)->findInstructors();

        $assignationForm = $this->createForm(AssignationType::class, null, [
            'users' => $users
        ]);


         // on traite la requete du formulaire
         $assignationForm->handleRequest($request);

         //on recupere l'instructeur selectionner
        $instructeur = $assignationForm->get('users')->getData();
 
         //on assigne l'instructeur a la reservation

         $reservation[0]->addUser($instructeur);
         $reservation[0]->setIsAssign(1);

         $em->persist($reservation[0]);
         $em->flush();

         $this->addFlash('success', 'Instructeur Assigne avec success');

        



         return $this->redirectToRoute('admin_reservation');
    }


    
}