<?php

class Resepti extends BaseModel {

	const NIMEN_MINIMIPITUUS = 3;
	const OHJEEN_MINIMIPITUUS = 10;
	
	public $id, $tekijaId, $nimi, $ohje, $aineet, $tekija, $tagit;
	
	
	public function __construct($attributes) {
		parent::__construct($attributes);
		$this->tekija = Kayttaja::find($this->tekijaId)->nimi;
		$this->aineet = $this->haeAineet();
		$this->tagit = $this->haeTagit();
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


	private function haeTagit() {
		$query = DB::connection()->prepare("SELECT * FROM reseptitag WHERE reseptiid = :id");
		$query->execute(array('id' => $this->id));
		$rows = $query->fetchAll();
		
		$tagit = array();
		foreach($rows as $row) {
			$tag = Tag::find($row['tagid']);
			$tagit[] = $tag;
		}
		
		return $tagit;
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
		
		// Järjestetään reseptit aakkosjärjestykseen.
		usort($reseptit, "sortByName");
		
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
	
	
	public static function findByName($name) {
		$query = DB::connection()->prepare('SELECT * FROM resepti WHERE nimi LIKE :like');
		$query->execute(array('like' => '%'.$name.'%'));
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
		
		usort($reseptit, "sortByName");
		
		return $reseptit;
	}
	
	
	public static function findByIngredient($name) {
		$aine = Aine::findByName($name);
		if($aine) {
			$aineId = $aine->id;
		} else {
			return null;
		}
		
		$query = DB::connection()->prepare('SELECT * FROM resepti JOIN reseptiaine ON resepti.id = reseptiaine.reseptiId WHERE reseptiaine.aineId = :aineId');
		$query->execute(array('aineId' => $aineId));
		$rows = $query->fetchAll();
		$reseptit = array();
		
		foreach($rows as $row) {
			$reseptit[] = new Resepti(array(
				'id' => $row['reseptiid'],
				'tekijaId' => $row['tekijaid'],
				'nimi' => $row['nimi'],
				'ohje' => $row['ohje']
			));
		}
		
		usort($reseptit, "sortByName");
		
		return $reseptit;
	}
	
	
	public static function findByTag($name) {
		$tag = Tag::findByName($name);
		if($tag) {
			$tagId = $tag->id;
		} else {
			return null;
		}
		
		$query = DB::connection()->prepare('SELECT * FROM resepti JOIN reseptitag ON resepti.id = reseptitag.reseptiId WHERE reseptitag.tagId = :tagId');
		$query->execute(array('tagId' => $tagId));
		$rows = $query->fetchAll();
		$reseptit = array();

		foreach($rows as $row) {
			$reseptit[] = new Resepti(array(
				'id' => $row['reseptiid'],
				'tekijaId' => $row['tekijaid'],
				'nimi' => $row['nimi'],
				'ohje' => $row['ohje']
			));
		}
		
		usort($reseptit, "sortByName");
		
		return $reseptit;
	}
	
	
	public static function findByOwner($tekijaId) {
		$query = DB::connection()->prepare('SELECT * FROM resepti WHERE tekijaid = :tekijaid');
		$query->execute(array('tekijaid' => $tekijaId));
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
	
	
	public static function nimiTaulukko() {
		$reseptit = self::all();
		$array = array();
		
		foreach($reseptit as $resepti) {
			$array[] = $resepti->nimi;
		}
		return $array;
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

		foreach($this->tagit as $tag) {
			// Jos reseptitagi on jo olemassa, ei sitä laiteta uudestaan. Ehkäistään mahdollisuus laittaa sama tag monta kertaa.
			$query = DB::connection()->prepare('SELECT * FROM reseptitag WHERE reseptiid = :reseptiid AND tagid = :tagid');
			$query->execute(array(
				'reseptiid' => $this->id,
				'tagid' => $tag->id
			));
			$row = $query->fetch();
			if($row) {
				continue;
			}

			$query = DB::connection()->prepare('INSERT INTO reseptitag (reseptiid, tagid) VALUES (:reseptiid, :tagid)');
			$query->execute(array(
				'reseptiid' => $this->id,
				'tagid' => $tag->id
			));
		}

		foreach($this->aineet as $aine) {
			// Jos reseptiaine on jo olemassa, ei sitä laiteta uudestaan. Ehkäistään mahdollisuus laittaa sama tag monta kertaa.
			$query = DB::connection()->prepare('SELECT * FROM reseptiaine WHERE reseptiid = :reseptiid AND aineid = :aineid');
			$query->execute(array(
				'reseptiid' => $this->id,
				'aineid' => $aine->id
			));
			$row = $query->fetch();
			if($row) {
				continue;
			}

			$query = DB::connection()->prepare('INSERT INTO reseptiaine (reseptiid, aineid, maara) VALUES (:reseptiid, :aineid, :maara)');
			$query->execute(array(
				'reseptiid' => $this->id,
				'aineid' => $aine->id,
				'maara' => $aine->maara
			));
		}
	}
	

	public function update() {
        $query = DB::connection()->prepare('UPDATE resepti SET nimi = :nimi, ohje = :ohje WHERE id = :id');
        $query->execute(array(
            'nimi' => $this->nimi,
            'ohje' => $this->ohje,
            'id' => $this->id
        ));

		// 'tagit' sisältää tässä vain uudet tagit.
		foreach($this->tagit as $tag) {
			// Jos reseptitagi on jo olemassa, ei sitä laiteta uudestaan.
			$query = DB::connection()->prepare('SELECT * FROM reseptitag WHERE reseptiid = :reseptiid AND tagid = :tagid');
			$query->execute(array(
				'reseptiid' => $this->id,
				'tagid' => $tag->id
			));
			$row = $query->fetch();
			if($row) {
				continue;
			}

			// Reseptitagia ei loytynyt, joten kirjoitetaan se tietokantaan.
			$query = DB::connection()->prepare('INSERT INTO reseptitag (reseptiid, tagid) VALUES (:reseptiid, :tagid)');
			$query->execute(array(
				'reseptiid' => $this->id,
				'tagid' => $tag->id
			));
		}

		foreach($this->aineet as $aine) {
			// Jos reseptiaine on jo olemassa, ei sitä laiteta uudestaan. Ehkäistään mahdollisuus laittaa sama tag monta kertaa.
			$query = DB::connection()->prepare('SELECT * FROM reseptiaine WHERE reseptiid = :reseptiid AND aineid = :aineid');
			$query->execute(array(
				'reseptiid' => $this->id,
				'aineid' => $aine->id
			));
			$row = $query->fetch();
			if($row) {
				continue;
			}

			$query = DB::connection()->prepare('INSERT INTO reseptiaine (reseptiid, aineid, maara) VALUES (:reseptiid, :aineid, :maara)');
			$query->execute(array(
				'reseptiid' => $this->id,
				'aineid' => $aine->id,
				'maara' => $aine->maara
			));
		}
    }


    public function destroy() {
		// Tuhotaan ensin reseptiin liittyvät reseptitag-rivit.
		$query = DB::connection()->prepare('DELETE FROM reseptitag WHERE reseptiid = :id');
		$query->execute(array(
            'id' => $this->id
        ));

		// Tuhotaan myös reseptiin liittyvät reseptiaine-rivit.
		$query = DB::connection()->prepare('DELETE FROM reseptiaine WHERE reseptiid = :id');
		$query->execute(array(
            'id' => $this->id
        ));

		// Lopuksi voidaan poistaa myös reseptin tietue resepti-taulusta.
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

	