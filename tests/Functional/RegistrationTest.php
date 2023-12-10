<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegistrationTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', "/inscription/");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Faire une réservation');

        //Récuperer le formulaire
        $submitButton = $crawler->selectButton('Reserver');
        $form = $submitButton->form();
        $form["registration_form[roles]"] = "ROLE_ETUDIANT";
        $form["registration_form[nom]"] = "Ibrahim";
        $form["registration_form[prenom]"] = "amadou";
        $form["registration_form[telephone]"] ="9020038";
        $form["registration_form[email]"] ="ibrahim@gmail.com";
        $form["registration_form_plainPassword"] ="90200381";
        $form["registration_form[agreeTerms]"] = 1;


        //Soumettre le formulaire
        $client->submit($form);

        //Verifier le statut Http
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // verifier l'envoie du mail
        $this->assertEmailCount(1);
        $client->followRedirect();
       

   


    }
}
