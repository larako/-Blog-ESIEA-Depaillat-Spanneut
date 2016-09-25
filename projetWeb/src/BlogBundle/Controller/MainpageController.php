<?php


// src/BlogBundle/Controller/AdvertController.php


namespace BlogBundle\Controller;


// N'oubliez pas ce use :

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class MainpageController extends Controller
{

  public function indexAction()
  {
    $content = $this->get('templating')->render('BlogBundle:Mainpage:index.html.twig');
    return new Response($content);
  }

}