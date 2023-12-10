<?php

namespace App\Controller\Admin;

use App\Entity\Reservations;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class MainController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $em): Response
    {
        if(!$this->getUser()){
            return  $this->redirectToRoute('main');
        }

        // le nombre total d'utilisateurs
        $users = $em->getRepository(Users::class)->findAll();
        $usersNumber = count($users);


        // le nombre d'etudiant
        $students = $em->getRepository(Users::class)->findStudents();
        $studentsNumber = count($students);

        // le nombre d'instructeur
        $instructors = $em->getRepository(Users::class)->findInstructors();
        $instructorsNumber = count($instructors);

        //le nombre de reservation confirme
        $reservations = $em->getRepository(Reservations::class)->findBy(['isPaid'=>'true'],['created_at'=>'asc']);
        $reservationsNumber = count($reservations);


       
        return $this->render('admin/index.html.twig',[
            'usersNumber'=>$usersNumber,
            'studentsNumber'=>$studentsNumber,
            'instructorsNumber'=>$instructorsNumber,
            'reservationsNumber'=>$reservationsNumber,
            'reservations'=>$reservations
        ]);
    }

    #[Route('/reservation', name: 'reservation')]
    public function reservation(EntityManagerInterface $em): Response
    {
        if(!$this->getUser()){
            return  $this->redirectToRoute('main');
        }

        // le nombre total d'utilisateurs
        $users = $em->getRepository(Users::class)->findAll();
        $usersNumber = count($users);


        // le nombre d'etudiant
        $students = $em->getRepository(Users::class)->findStudents();
        $studentsNumber = count($students);

        // le nombre d'instructeur
        $instructors = $em->getRepository(Users::class)->findInstructors();
        $instructorsNumber = count($instructors);

        //le nombre de reservation confirme
        $reservations = $em->getRepository(Reservations::class)->findBy(['isPaid'=>'true'],['created_at'=>'asc']);
        $reservationsNumber = count($reservations);


       
        return $this->render('admin/index.html.twig',[
            'usersNumber'=>$usersNumber,
            'studentsNumber'=>$studentsNumber,
            'instructorsNumber'=>$instructorsNumber,
            'reservationsNumber'=>$reservationsNumber,
            'reservations'=>$reservations
        ]);
    }
    
}