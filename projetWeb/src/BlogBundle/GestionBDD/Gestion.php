<?php
namespace BlogBundle\GestionBDD;

use BlogBundle\Entity\Advert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
	
}
?>