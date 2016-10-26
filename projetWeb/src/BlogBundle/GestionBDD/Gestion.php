<?php
namespace BlogBundle\GestionBDD;

use BlogBundle\Entity\Advert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class Gestion extends Controller{

	public function insertionBDD($nomTorrent, $path){
		$advert = new Advert();
		$advert->setNomTorrent($nomTorrent);
		$advert->setTailleFichier('5');
  		$advert->setAuteur('cyril');
  		$advert->setDescription('cela va t-il marcher ?');
		$advert->setPathToFile($path);
		return $advert;
	}

	public function supprimer($repository,$em,$post){
      if (is_array($post))
      {
          // On supprime toutes les entrées sélectionnées
          foreach ($post as $id) {
              $em->remove($repository->findOneBy(array('id' => $id)));
              $em->flush();
          }
      }
	}



	public function UpdateDescription ($repository,$torrentId,$contenu){
      $torrent=$repository->find($torrentId);

      if (!$torrent){
      	throw $this->createNotFoundException('No torrent found for id '.$torrentId);
      }
      $torrent->setDescription($contenu);
	return $torrent;	
	}

	public function UpdateAuteur ($repository,$torrentId,$contenu){
      $torrent=$repository->find($torrentId);

      if (!$torrent){
      	throw $this->createNotFoundException('No torrent found for id '.$torrentId);
      }
      $torrent->setNomTorrent($contenu);
	return $torrent;	
	}
	
}
?>