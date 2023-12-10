<?php

namespace App\Controller;

use App\Entity\Reservations;
use App\Entity\Users;
use App\Form\ReservationFormType;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/profil', name: 'profile_')]
class ReservationController extends AbstractController
{
    
    #[Route('/reservation/historique', name: 'reservation_index')]
    public function student(EntityManagerInterface $em): Response
    {
        if(!$this->getUser()){
            return  $this->redirectToRoute('app_login');
        }
        
        
        //listes des reservations de l'etudiant
        $reservations = $em->getRepository(Reservations::class)->findStudentReservation($this->getUser()->getId());
        
   
        
        return $this->render('profile/reservation/index.html.twig',[
            'reservations'=>$reservations
        ]);
    }


    #[Route('/reservation/mes-cours', name: 'reservation_instructor')]
    public function instructor(EntityManagerInterface $em): Response
    {
        if(!$this->getUser()){
            return  $this->redirectToRoute('app_login');
        }
        
        
        //listes des reservations de l'etudiant
        $reservations = $em->getRepository(Reservations::class)->findStudentReservation($this->getUser()->getId());
       
        
   
        
        return $this->render('profile/reservation/instructor.html.twig',[
            'reservations'=>$reservations
        ]);
    }


    
    #[Route('/reservation/ajout', name: 'reservation_ajout')]
    public function add(): Response
    {
        if(!$this->getUser()){
            return  $this->redirectToRoute('app_login');
        }

        // on crée le formulaire de réservation
        $reservationForm = $this->createForm(ReservationType::class,null);
        
        return $this->render('profile/reservation/add.html.twig',[
            'reservationForm'=>$reservationForm->createView()
        ]);
    }

    #[Route('/reservation/recapitulatif', name: 'reservation_recapitulatif')]
    public function prepareReservation(Request $request,EntityManagerInterface $em): Response
    {
        if(!$this->getUser()){
            return  $this->redirectToRoute('app_login');
        }

        // on crée le formulaire de réservation
        $reservationForm = $this->createForm(ReservationType::class,null);
        
         // on traite la requete du formulaire
        $reservationForm->handleRequest($request);

        //on récupere l'heure de réservation choisie
        $date = $reservationForm->get('created_at')->getData();

        //on récupere le mode de paiement choisie
        $paymentMethod = $reservationForm->get('payment')->getData();

        //on crée une "nouvelle réservation"
        $reservation = new Reservations();

        //on definit la réference de la réservation
        $reservation->setReference(uniqid());

        //on ajoute l'utilisateur courant
        $reservation->addUser($this->getUser());

        //On definit l'heure
        $reservation->setCreatedAt($date);

        //on definite le prix
        $reservation->setPrice($reservation->getPrice());

        //on definit le mode de paiement
        $reservation->setMethod($paymentMethod);

        $reservation->setIsPaid(0);

        $em->persist($reservation);
        $em->flush();

        return $this->render('profile/reservation/recap.html.twig',[
            'reservationForm'=>$reservationForm->createView(),
            'method'=>$reservation->getMethod(),
            'price'=>$reservation->getPrice(),
            'reference'=>$reservation->getReference(),
            
        ]);


    }


}