<?php


// src/BlogBundle/Controller/AdvertController.php


namespace BlogBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BlogBundle\Entity\Advert;
use BlogBundle\GestionBDD\Gestion;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

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
          $file->move('../uploads/torrent',$file->getClientOriginalName());
          //on stock toutes les données dans la BDD
          $gestion = new Gestion ;
          $advert = $gestion->insertionBDD($file->getClientOriginalName(), '../uploads/torrent/'.$file->getClientOriginalName());
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
      //$this->createBDD(); // A utiliser si la BDD est vide et qu'il y a des .torrent dans uploads
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
      ->getRepository('BlogBundle:Advert');

      if (is_array($_POST['sup']))
      {
          // On supprime toutes les entrées sélectionnées
          foreach ($_POST['sup'] as $id) {
              $em = $this->getDoctrine()->getManager();
              $em->remove($repository->findOneBy(array('id' => $id)));
              $em->flush();
          }
      }
    $advert = $repository->findAll();

    return $this->render('BlogBundle:Mainpage:liste.html.twig', array( 'advert' => $advert ));
  }


    public function createBDD ()
    { // Initialise rapidement la BDD avec tous les .torrent présent dans upload
        if ($handle = opendir('../uploads/torrent/')) {
            echo "Gestionnaire du dossier : $handle\n";
            echo "Entrées :\n";

            /* Ceci est la façon correcte de traverser un dossier. */
            foreach (glob("../uploads/torrent/*.torrent") as $filename)
            {
                //echo $filename;
                //on stock toutes les données dans la BDD
                $gestion = new Gestion;
                $advert = $gestion->insertionBDD(basename($filename), $filename);
                $em = $this->getDoctrine()->getManager();
                // Étape 1 : On « persiste » l'entité
                $em->persist($advert);
                // Étape 2 : On « flush » tout ce qui a été persisté avant
                $em->flush();
            }
            closedir($handle);
        }
    }

}