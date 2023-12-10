<?php

namespace App\Controller\Admin;

use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;

#[Route('/admin/',name:'admin_users_')]
class UsersController extends AbstractController
{
    #[Route('/utilisateurs',name:'index')]
    public function index(UsersRepository $usersRepository): Response
    // Afficher la liste de tous les utilisateurs
    {   $users = $usersRepository->findBy([],['nom'=>'asc']);
        return $this->render('admin/users/index.html.twig', compact('users'));
    }

    

    #[Route('verification/{id}',name:'verif')]
    public function bool($id, EntityManagerInterface $em): RedirectResponse
    {   
        $user = $em->getRepository(Users::class)->find($id);
         if($user->isVerified()){
            $user->setIsverified(false);
         }else{
            $user->setIsverified(true);
         }
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('admin_users_index');

    }
}