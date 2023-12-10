<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Entity\Reservations;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use Symfony\Component\HttpFoundation\RequestStack;

class PaymentController extends AbstractController
{
   //PAYPAL
  public function getPaypalClient(): PayPalHttpClient
    {
      $clientId = "AQBaSXh1vyfPXbhBfhEu-fa0YvtDC9xC9PBxQuH3Yap9AiL287b9sf7KHDeHtxeEQIr0lUI3OXGc93sL";
      $clientSecret = "ELpbyJdTX2t_now-NW9E50LPgZOaTLUe7cX8cPFvCERpCwe6BLpGjv9xO8VCpXOP0HC4XwV0bhsY95wo";
      $environment = new SandBoxEnvironment($clientId, $clientSecret);
      return new PayPalHttpClient($environment);
    }

   

    #[Route('/reservation/create-session-paypal/{reference}', name:'payment_paypal', methods:['POST'])]
    public function createSessionPaypal(EntityManagerInterface $em, $reference, UrlGeneratorInterface $url): RedirectResponse
    {
       $reservation = $em->getRepository(Reservations::class)->findOneBy(['reference'=>$reference]);

       if(!$reservation){
           return $this->redirectToRoute('profile_index');
       }

       $items = [];
       

      
       
          $items[] = [
             'name'=> $reservation->getReference(),
             'quantity'=>'1',
             'unit_amount'=>[
              'value'=> $reservation->getPrice(),
              'currency_code'=> 'EUR'
             ]
    ];


    $request = new OrdersCreateRequest();
    $request->prefer('return=representation');
    $request->body =[
      'intent'=> 'CAPTURE',
      'purchase_units'=>[
         [
            'amount'=>[
              'currency_code'=> 'EUR',
              'value'=> $reservation->getPrice(),
              'breakdown'=>[
                'item_total'=> [
                  'currency_code'=>'EUR',
                  'value'=> $reservation->getPrice()
                ]
              ]
                ],
                'items'=>$items
         ]
              ],
              'application_context' =>[
                'return_url'=> $url->generate('payment_success_paypal',
                ['reference'=>$reservation->getReference()],
                UrlGeneratorInterface:: ABSOLUTE_URL
              ),
              'cancel_url'=> $url->generate(
                'payment_error',
                ['reference'=>$reservation->getReference()],
                  UrlGeneratorInterface::ABSOLUTE_URL
                )
              ]
        ];

    $client = $this->getPaypalClient();
    $response = $client->execute($request);

    if($response->statusCode != 201){
        return $this->redirectToRoute('cart_index');
    }
    
    $approvalLink = '';
    foreach($response->result->links as $link) {
       if($link->rel === 'approve'){
          $approvalLink = $link->href;
          break;
       }
    }

    if(empty($approvalLink)){
        return $this->redirectToRoute('profile_index');
    }

    $reservation->setPaypalOrderId($response->result->id);

    $em->flush();

    return new RedirectResponse($approvalLink);


  

    }


    #[Route('/reservation/success-paypal/{reference}', name:'payment_success_paypal')]
    public function successPaypal($reference, EntityManagerInterface $em, RequestStack $requestStack): Response
    {
      $reservation = $em->getRepository(Reservations::class)->findOneBy(['reference'=> $reference]);
        
        if(!$reservation || $reservation->getUser() !== $this->getUser()){
            return $this->redirectToRoute('profile_index');
        }

        
        if(!$reservation->isIsPaid()){
          
           $reservation->setIsPaid(1);
           $em->flush();
        }

      return $this->render('profile/reservation/success.html.twig',[
        'reservation'=> $reservation
      ]);
    }


    #[Route('/reservation/error-paypal/{reference}', name:'payment_error_paypal')]
    public function errorPaypal(EntityManagerInterface $em, $reference): Response
    {
      $reservation = $em->getRepository(Reservations::class)->findOneBy(['reference'=> $reference]);
      if(!$reservation || $reservation->getUser() !== $this->getUser()){
        return $this->redirectToRoute('profile_index');
    }

      return $this->render('profile/reservation/error.html.twig');
    }


    //Stripe

    #[Route('/reservation/create-session-stripe/{reference}', name:'payment_stripe')]

    public function stripeCheckout($reference, EntityManagerInterface $em, UrlGeneratorInterface $url): RedirectResponse
    {
        $productStripe =[];
        $reservation = $em->getRepository(Reservations::class)->findOneBy(['reference'=> $reference]);
        
        if(!$reservation){
            return $this->redirectToRoute('profile_index');
        }

       
    
         
            $productStripe[] = [
               'price_data'=>[
                'currency'=>'EUR',
                'unit_amount'=>$reservation->getPrice()*100,
                'product_data'=>[
                   'name'=> 'Reservation'
                    ]
                ],
          'quantity'=> '1'
      ];

  

       
        Stripe::setApiKey('sk_test_51Jw8FlKNgWxmv3tU6OWDt9pGbKWmPcVtrKqd0vpms8qUcBuohW38QpEmGPBaMxpN5cFWmW3VlxAItnv7zfN25IvH00qfs1eHsr');
        
        
        
        $checkout_session = Session::create([
          'customer_email'=> $this->getUser()->getEmail(),
           'payment_method_types' => ['card'],
          'line_items' => [[
             $productStripe
          ]],
          'mode' => 'payment',
          'success_url' => $url->generate('payment_success',[
            'reference' => $reservation->getReference()
          ],UrlGeneratorInterface::ABSOLUTE_URL),
          'cancel_url' => $url->generate('payment_error',
          ['reference'=>$reservation->getReference()],
            UrlGeneratorInterface::ABSOLUTE_URL
          )
        ]);

        $reservation->setStripeSessionId((string)$checkout_session->id);

        $em->flush();


        return new RedirectResponse($checkout_session->url);
        
        
    }

    #[Route('/reservation/success/{reference}', name:'payment_success')]
    public function StripeSuccess($reference, EntityManagerInterface $em,RequestStack $requestStack): Response
    {
      $reservation = $em->getRepository(Reservations::class)->findOneBy(['reference'=> $reference]);
        

      if(!$reservation->isIsPaid()){
        $reservation->setIsPaid(1);
         $em->flush();
       
      }


    return $this->render('profile/reservation/success.html.twig',[
      'reservation'=> $reservation
    ]);
    }

    #[Route('/order/error/{reference}', name:'payment_error')]
    public function StripeError($reference, EntityManagerInterface $em): Response
    {
      $reservation = $em->getRepository(Reservations::class)->findOneBy(['reference'=> $reference]);
        
        if(!$reservation || $reservation->getUser() !== $this->getUser()){
            return $this->redirectToRoute('profile_index');
        }
      return $this->render('profile/reservation/error.html.twig');
    }


}