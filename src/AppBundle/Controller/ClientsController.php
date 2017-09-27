<?php
// src/AppBundle/Controller/AdminController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Client;

class ClientsController extends Controller
{

    private $titles = ['mr', 'ms', 'mrs', 'dr', 'mx'];
    /**
     * @Route("/guests", name="index_clients")
     **/
    public function showIndex()
    {

        $logger = $this->get('logger');
        $logger->info('User has opened the guest page');

        $data = [];

        $clients = $this->getDoctrine()
            ->getRepository('AppBundle:Client')
            ->findAll();

        $data["clients"] = $clients;

        return $this->render('clients/index.html.twig', $data);
                                
    }

    /**
     * @Route("/github")
     **/
    public function github()
    {
        $client = new \GuzzleHttp\Client();

        $res = $client->request("GET", "https://api.github.com/users/johfes");
        return new Response('<pre>'.$res->getBody());

    }

    /**
     * @Route("/about")
     **/
    public function about()
    {;
        return new Response('<h1>About</h1>');

    }

    /**
     * @Route("/guests/modify/{client_id}", name="modify_client")
     **/
    public function showDetails(Request $request, $client_id)
    {
        $data = [];
        $data["mode"] = "modify";

        $client_repo =  $this->getDoctrine()
            ->getRepository('AppBundle:Client');

        $data["form"] = [];
        $data["titles"] = $this->titles;

        $form = $this   ->createFormBuilder()
            ->add('name')
            ->add('lastName')
            ->add('title')
            ->add('address')
            ->add('zipCode')
            ->add('city')
            ->add('state')
            ->add('email')
            ->getForm()
        ;

        $form->handleRequest($request);

        $client = $client_repo->find($client_id);
        if($form->isSubmitted())
        {
            $form_data = $form->getData();
            $data["form"] = $form_data;

            $client->setTitle($form_data["title"]);
            $client->setName($form_data["name"]);
            $client->setLastName($form_data["lastName"]);
            $client->setAddress($form_data["address"]);
            $client->setZipCode($form_data["zipCode"]);
            $client->setCity($form_data["city"]);
            $client->setState($form_data["state"]);
            $client->setEmail($form_data["email"]);

            $em =  $this->getDoctrine()
                ->getManager();
            $em->flush();

            $this->addFlash(
                'success',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('index_clients');

        } else {

            $data["form"] = $client;
        }

        return $this->render('clients/form.html.twig', $data);

    }

    /**
     * @Route("/guests/new", name="new_client")
     **/
    public function showNew(Request $request)
    {
        $data = [];
        $data["mode"] = "new";
        $data["titles"] = $this->titles;
        $data["form"] = [];
        $data["form"]["title"] = '';

        $form = $this   ->createFormBuilder()
            ->add('name')
            ->add('lastName')
            ->add('title')
            ->add('address')
            ->add('zipCode')
            ->add('city')
            ->add('state')
            ->add('email')
            ->getForm()
        ;

        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $form_data = $form->getData();
            $data["form"] = $form_data;

            $em = $this->getDoctrine()->getManager();

            $client = new Client();
            $client->setTitle($form_data["title"]);
            $client->setAddress($form_data["address"]);
            $client->setCity($form_data["city"]);
            $client->setEmail($form_data["email"]);
            $client->setLastName($form_data["last_name"]);
            $client->setName($form_data["name"]);
            $client->setState($form_data["state"]);
            $client->setZipCode($form_data["zip_code"]);

            $em->persist($client);
            $em->flush();


            $this->addFlash(
                'success',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('index_clients');

        }

        return $this->render('clients/form.html.twig', $data);

    }
}