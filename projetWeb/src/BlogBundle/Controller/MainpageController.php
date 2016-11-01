<?php

namespace BlogBundle\Controller;


use PDO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request;
use BlogBundle\GestionBDD\Gestion;

class MainpageController extends Controller
{

    public function ajoutAction(Request $request)
    {

        if (isset ($_FILES['userFile']))
        {
            print_r($_FILES);
            foreach ($_FILES['userFile']['error'] as $file => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['userFile']['tmp_name'][$file];
                    $name = iconv("utf-8", "cp1258", $_FILES['userFile']['name'][$file]);
                    $extensionFile = new SplFileInfo($name, null, null);
                    echo($extensionFile);
                    if ($extensionFile->getExtension() != 'torrent') {
                        echo 'veuillez uploader un fichier .torrent';
                    } else {
                        //on stock le fichier
                        move_uploaded_file($tmp_name,'../uploads/torrent/' . $name);
                        $serverName = 'localhost';
                        $portNumber = '-1';
                        $dbName = 'Symfony';
                        $userName = 'root';
                        $password = 'root';
                        $author = 'root';
                        $description = 'Coucou';
                        $commande = "java -jar ../bin/TorrentParser.jar ../uploads/torrent/\"" . $name . "\" " . $serverName . " " . $portNumber . " " . $dbName . " " . $userName . " " . $password . " " . $author . " " . $description;
                        $output = array();
                        exec($commande, $output);
                        //print_r($output);

                    }
                }
            }
            $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('BlogBundle:Advert');
            $advert = $repository->findAll();
            return $this->render('BlogBundle:Mainpage:liste.html.twig', array('advert' => $advert));

        }
        return $this->render('BlogBundle:Mainpage:ajout.html.twig');
    }


    public function listeAction(Request $request)
    {

        //gestion de la BDD
        // On récupère le repository
        //$this->createBDD(); // A utiliser si la BDD est vide et qu'il y a des .torrent dans uploads
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository('BlogBundle:Advert');

        // On récupère l'entité correspondante à l'id $id
        $advert = $repository->findAll();

        // Le render ne change pas, on passait avant un tableau, maintenant un objet
        return $this->render('BlogBundle:Mainpage:liste.html.twig', array('advert' => $advert));

    }

    public function gestionAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('BlogBundle:Advert');
        $em = $this->getDoctrine()->getManager();
        $gestion = new Gestion;
        $checkedBoxArray = array();
        //print_r($_POST);
        if (isset ($_POST['search']) && $_POST['search'] != NULL )
        { // Fonction de recherche

            $search = htmlspecialchars($_POST['search']);
            $advert = $em->createQueryBuilder()->select("a")->from('BlogBundle:Advert', 'a')->where("a.nomTorrent LIKE '%$search%'")->orderBy('a.id', 'ASC');

            return $this->render('BlogBundle:Mainpage:liste.html.twig', array('advert' => $advert->getQuery()->getResult()));
        }

        else if (isset ($_POST['checklist'])) {
            $checkedBoxArray = $_POST['checklist'];
        }

        if (isset($_POST['id'])) { // Une case ou plus a été cochée
            if (isset($_POST['update'])) {
                foreach ($_POST['id'] as $id) {
                    $update = $gestion->UpdateDescription($repository, $id, $checkedBoxArray[$id]['description']);
                    $em->persist($update);
                    $em->flush();
                    $update = $gestion->UpdateTorrentName($repository, $id, $checkedBoxArray[$id]['nomTorrent']);
                    $em->persist($update);
                    $em->flush();
                }
            } else if (isset($_POST['suppr'])) {
                foreach ($_POST['id'] as $id) {
                    $gestion->supprimer($repository, $em, $id, $checkedBoxArray[$id]['path']);
                }
            }
        } else { // si il n'a pas coche la case
            if (isset($_POST['suppr']) || isset($_POST['update'])) {
                echo("veuillez cocher une case");
            }
        }


        $advert = $repository->findAll();

        return $this->render('BlogBundle:Mainpage:liste.html.twig', array('advert' => $advert));
    }


    public function createBDD()
    { // Initialise rapidement la BDD avec tous les .torrent présent dans upload
        if ($handle = opendir('../uploads/torrent/'))
        {
            echo "Gestionnaire du dossier : $handle\n";
            echo "Entrées :\n";
            $bdd = new PDO('mysql:host=localhost;dbname=Symfony;charset=utf8', 'root', 'root');
            $bdd->query('ALTER TABLE Advert AUTO_INCREMENT=0');
            /* Ceci est la façon correcte de traverser un dossier. */
            foreach (glob("../uploads/torrent/*.torrent") as $filename)
            {
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