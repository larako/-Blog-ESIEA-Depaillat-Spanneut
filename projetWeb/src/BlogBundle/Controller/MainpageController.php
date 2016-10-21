<?php


// src/BlogBundle/Controller/AdvertController.php


namespace BlogBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use BlogBundle\Entity\Advert;
use BlogBundle\GestionBDD\Gestion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BlogBundle\Entity\Torrent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class MainpageController extends Controller
{

  public function ajoutAction(Request $request)
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
          $gestion=new Gestion ;
          $advert = $gestion ->insertionBDD($file->getClientOriginalName());
            $em = $this->getDoctrine()->getManager();
            // Étape 1 : On « persiste » l'entité
            $em->persist($advert);
            // Étape 2 : On « flush » tout ce qui a été persisté avant
            $em->flush();

            return $this->redirect($this->generateUrl('blog_ListeTorrent', array('id' => $advert->getId())));
        } 
    }
        return $this->render('BlogBundle:Mainpage:ajout.html.twig',array('form'=> $form ->createView()));


  }


  public function listeAction(Request $request){

        //gestion de la BDD
        // On récupère le repository
    $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('BlogBundle:Advert');

    // On récupère l'entité correspondante à l'id $id
    $advert = $repository->findAll();
 

    // Le render ne change pas, on passait avant un tableau, maintenant un objet
    return $this->render('BlogBundle:Mainpage:liste.html.twig', array( 'advert' => $advert ));

  }

  public function supprimerAction(Request $request){
        $repository = $this->getDoctrine()
      ->getManager()
      ->getRepository('BlogBundle:Advert');
    // On récupère l'entité correspondante à l'id $id
    $advert = $repository->findAll();
    
    

   if (isset($_POST['sup'])){ 
        $entity = $this->getDoctrine()->getEntityManager();
        $query = $entity->createQuery('DELETE FROM BlogBundle\Entity\Advert');
        $query->execute();
    }
    

    return $this->render('BlogBundle:Mainpage:test.html.twig');
  }

}