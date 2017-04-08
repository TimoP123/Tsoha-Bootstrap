<?php

class Kayttaja extends BaseModel {
	
	public $id, $nimi, $email;
	
	public function __construct($attributes) {
		parent::__construct($attributes);
	}
	
	public static function all() {
		$query = DB::connection()->prepare('SELECT * FROM kayttaja');
		$query->execute();
		$rows = $query->fetchAll();
		$kayttajat = array();
		
		foreach($rows as $row) {
			$kayttajat[] = new Kayttaja(array(
				'id' => $row['id'],
				'nimi' => $row['nimi'],
				'email' => $row['email']
			));
		}
		
		return $kayttajat;
	}
	
	
	public static function find($id) {
		$query = DB::connection()->prepare('SELECT * FROM kayttaja WHERE id = :id LIMIT 1');
		$query->execute(array('id' => $id));
		$row = $query->fetch();
		
		if($row) {
			$kayttaja = new Kayttaja(array(
				'id' => $row['id'],
				'nimi' => $row['nimi'],
				'email' => $row['email']
			));
			
			return $kayttaja;
		}
		
		return null;
	}


	public static function authenticate($email, $salasana) {
		$query = DB::connection()->prepare('SELECT * FROM kayttaja WHERE email = :email AND salasana = :salasana LIMIT 1');
		$query->execute(array('email' => $email, 'salasana' => $salasana));
		$row = $query->fetch();
		if($row){
			$kayttaja = new Kayttaja(array(
				'id' => $row['id'],
				'nimi' => $row['nimi'],
				'email' => $row['email']
			));

			return $kayttaja;
		} else {
			// Kayttajaa ei loytynyt
			return null;
		}
	}
	
}
	