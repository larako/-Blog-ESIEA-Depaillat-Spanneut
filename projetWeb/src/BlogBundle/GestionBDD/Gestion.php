<?php
namespace BlogBundle\GestionBDD;

use BlogBundle\Entity\Advert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Gestion extends Controller
{

	public function insertionBDD($nomTorrent, $path)
	{
		$advert = new Advert();
		$advert->setNomTorrent($nomTorrent);
		$advert->setTailleFichier('5');
		$advert->setAuteur("moi");
		$advert->setPathToFile($path);
		return $advert;
	}

	public function supprimer($repository, $em, $id, $path)
	{
		// On supprime toutes les entrées sélectionnées
		if (file_exists($path)) {
			unlink($path); // Supprime le fichier du dossier
			$em->remove($repository->findOneBy(array('id' => $id))); // Supprime le fichier de la BDD
			$em->flush();
		}
		else{
			echo ("extiste pathhhhh");
			$em->remove($repository->findOneBy(array('id' => $id))); // Supprime le fichier de la BDD
			$em->flush();
		}
	}


	public function UpdateDescription($repository, $torrentId, $contenu)
	{
		$torrent = $repository->find($torrentId);

		if (!$torrent) {
			throw $this->createNotFoundException('No torrent found for id ' . $torrentId);
		}
		$torrent->setDescription($contenu);
		return $torrent;
	}

	public function UpdateTorrentName($repository, $torrentId, $contenu)
	{
		$torrent = $repository->find($torrentId);

		if (!$torrent) {
			throw $this->createNotFoundException('No torrent found for id ' . $torrentId);
		}
		$torrent->setNomTorrent($contenu);
		return $torrent;
	}

}
?>