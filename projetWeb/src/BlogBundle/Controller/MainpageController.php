<?php


// src/BlogBundle/Controller/AdvertController.php


namespace BlogBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use BlogBundle\Entity\Advert;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BlogBundle\Entity\Torrent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class MainpageController extends Controller
{

  public function indexAction(Request $request)
  {

  		  $form = $this->createFormBuilder()
        ->add('attachment', FileType::class)
        ->add('save', SubmitType::class, array('label' => 'upload torrent'))
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
        $file = $form->get('attachment')->getData();               	 
        $extensionFile= $file->guessExtension();
        echo ($extensionFile);
        if ($extensionFile!='torrent'){
         echo 'veuillez uploader un fichier .torrent';
        }
        else{
          //on stock le fichier 
          $file->move('uploads/torrent',$file->getClientOriginalName());
          //on stock toutes les données dans la BDD

        } 
    }
        return $this->render('BlogBundle:Mainpage:index.html.twig',array('form'=> $form ->createView()));


  		//gestion de la BDD
  	    // On récupère le repository
   /* $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('BlogBundle:Advert')
    ;

    // On récupère l'entité correspondante à l'id $id
    $advert = $repository->findAll();

    // Le render ne change pas, on passait avant un tableau, maintenant un objet
    return $this->render('BlogBundle:Mainpage:index.html.twig', array( 'advert' => $advert));*/
  }


  public function ajoutAction(Request $request){

 
  	//gestion de la BDD
  	/*$advert = new Advert();
  	$advert->setNomTorrent('xxx');
  	$advert->setTailleFichier('5');
  	$advert->setAuteur('cyril');
  	$advert->setDescription('cela va t-il marcher ?');

  	$em = $this->getDoctrine()->getManager();
  	 // Étape 1 : On « persiste » l'entité
    $em->persist($advert);
    // Étape 2 : On « flush » tout ce qui a été persisté avant
    $em->flush();

    // Reste de la méthode qu'on avait déjà écrit
    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      return $this->redirect($this->generateUrl('blog_ListeDinosaures', array('id' => $advert->getId())));

    }*/

    //return $this->render('BlogBundle:Mainpage:ajout.html.twig');


  }

}