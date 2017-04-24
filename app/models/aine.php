<?php

class Aine extends BaseModel {
	
	const NIMEN_MINIMIPITUUS = 3;
	
	public $id, $nimi;
	
	public function __construct($attributes) {
		parent::__construct($attributes);
		$this->validators = array('validate_nimi');
	}
	
	public static function all() {
		$query = DB::connection()->prepare('SELECT * FROM aine');
		$query->execute();
		$rows = $query->fetchAll();
		$reseptit = array();
		
		foreach($rows as $row) {
			$aineet[] = new Aine(array(
				'id' => $row['id'],
				'nimi' => $row['nimi']
			));
		}
		
		return $aineet;
	}
	
	public static function find($id) {
		$query = DB::connection()->prepare('SELECT * FROM aine WHERE id = :id LIMIT 1');
		$query->execute(array('id' => $id));
		$row = $query->fetch();
		
		if($row) {
			$aine = new Aine(array(
				'id' => $row['id'],
				'nimi' => $row['nimi']
			));
			
			return $aine;
		}
		
		return null;
	}
	
	
	public static function findByName($name) {
		$name = strtolower($name);
		$query = DB::connection()->prepare('SELECT * FROM aine WHERE nimi = :nimi LIMIT 1');
		$query->execute(array('nimi' => $name));
		$row = $query->fetch();
		
		if($row) {
			$aine = new Aine(array(
				'id' => $row['id'],
				'nimi' => $row['nimi']
			));
			
			return $aine;
		}
		
		return null;
	}
	
	
	public static function nimiTaulukko() {
		$aineet = self::all();
		$array = array();
		
		foreach($aineet as $aine) {
			$array[] = $aine->nimi;
		}
		return $array;
	}
	
	
	public function save() {
		$query = DB::connection()->prepare('INSERT INTO aine (nimi) VALUES (:nimi) RETURNING id');
		$query->execute(array(
			'nimi' => $this->nimi
		));
		
		$row = $query->fetch();
		
		$this->id = $row['id'];
	}


	public function checkAndSave($name) {
		// Tarkistetaan loytyyko aine jo valmiiksi tietokannasta.
		$aine = self::findByName(strtolower($name));
		if($aine) {
			return $aine->id;
		}

		// Jos aine ei ole jo valmiiksi olemassa, luodaan talletetaan sellainen tietokantaan
		$aine = new Aine(array('nimi' => $name));
		$aine->save();
		return $aine->id;
	}
	
	
	public function destroy() {
        $query = DB::connection()->prepare('DELETE FROM aine WHERE id = :id');
        $query->execute(array(
            'id' => $this->id
        ));
    }
	
	
	public function validate_nimi(){
		return parent::validate_string_length($this->nimi, self::NIMEN_MINIMIPITUUS, 'Nimi');
	}
	
	
}