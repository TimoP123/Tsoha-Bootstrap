<?php

class Resepti extends BaseModel {

	const NIMEN_MINIMIPITUUS = 3;
	const OHJEEN_MINIMIPITUUS = 10;
	
	public $id, $tekijaId, $nimi, $ohje, $aineet, $tekija;
	
	
	public function __construct($attributes) {
		parent::__construct($attributes);
		$this->tekija = Kayttaja::find($this->tekijaId)->nimi;
		$this->aineet = $this->haeAineet();
		$this->validators = array('validate_nimi', 'validate_ohje');
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
	

	public function update() {
        $query = DB::connection()->prepare('UPDATE resepti SET nimi = :nimi, ohje = :ohje WHERE id = :id');
        $query->execute(array(
            'nimi' => $this->nimi,
            'ohje' => $this->ohje,
            'id' => $this->id
        ));
    }


    public function destroy() {
        $query = DB::connection()->prepare('DELETE FROM resepti WHERE id = :id');
        $query->execute(array(
            'id' => $this->id
        ));
    }

	
	public function validate_nimi(){
		return parent::validate_string_length($this->nimi, self::NIMEN_MINIMIPITUUS, 'Nimi');
	}

	public function validate_ohje(){
		return parent::validate_string_length($this->ohje, self::OHJEEN_MINIMIPITUUS, 'Ohje');
	}
	
}
	