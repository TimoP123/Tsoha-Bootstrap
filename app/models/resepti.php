<?php

class Resepti extends BaseModel {
	
	public $id, $tekijaId, $nimi, $ohje, $aineet, $tekija;
	
	
	public function __construct($attributes) {
		parent::__construct($attributes);
		$this->tekija = Kayttaja::find($this->tekijaId)->nimi;
		$this->aineet = $this->haeAineet();
	}
	
	
	private function haeAineet() {
		$query = DB::connection()->prepare("SELECT * FROM reseptiaine WHERE reseptiid = :id");
		$query->execute(array('id' => $this->id));
		$rows = $query->fetchAll();
		
		$aineet = array();
		foreach($rows as $row) {
			$aine = Aine::find($row['aineid']);
			$aine->maara = $row['maara'];
			$aineet[] = $aine;
		}
		
		return $aineet;
	}
	
	
	public static function all() {
		$query = DB::connection()->prepare('SELECT * FROM resepti');
		$query->execute();
		$rows = $query->fetchAll();
		$reseptit = array();
		
		foreach($rows as $row) {
			$reseptit[] = new Resepti(array(
				'id' => $row['id'],
				'tekijaId' => $row['tekijaid'],
				'nimi' => $row['nimi'],
				'ohje' => $row['ohje']
			));
		}
		
		return $reseptit;
	}
	
	
	public static function find($id) {
		$query = DB::connection()->prepare('SELECT * FROM resepti WHERE id = :id LIMIT 1');
		$query->execute(array('id' => $id));
		$row = $query->fetch();
		
		if($row) {
			$resepti = new Resepti(array(
				'id' => $row['id'],
				'tekijaId' => $row['tekijaid'],
				'nimi' => $row['nimi'],
				'ohje' => $row['ohje']
			));
			
			return $resepti;
		}
		
		return null;
	}
	
	
	public function save() {
		$query = DB::connection()->prepare('INSERT INTO resepti (tekijaid, nimi, ohje) VALUES (:tekijaid, :nimi, :ohje) RETURNING id');
		$query->execute(array(
			'tekijaid' => $this->tekijaId,
			'nimi' => $this->nimi,
			'ohje' => $this->ohje
		));
		
		$row = $query->fetch();
		
		$this->id = $row['id'];
	}
	
	
	
}
	