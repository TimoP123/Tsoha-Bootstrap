<?php

class Kayttaja extends BaseModel {
	
	const NIMEN_MINIMIPITUUS = 3;
	const NIMEN_MAKSIMIPITUUS = 30;
	const EMAIL_MAKSIMIPITUUS = 50;
	const SALASANAN_MINIMIPITUUS = 6;
	const SALASANAN_MAKSIMIPITUUS = 30;
	
	public $id, $nimi, $email, $salasana, $taso;
	
	public function __construct($attributes) {
		parent::__construct($attributes);
		$this->validators = array('validate_nimen_minimipituus', 'validate_nimen_maksimipituus', 'validate_email', 'validate_salasanan_minimipituus', 'validate_salasanan_maksimipituus');
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
				'email' => $row['email'],
				'salasana' => $row['salasana'],
				'taso' => $row['taso']
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
				'email' => $row['email'],
				'salasana' => $row['salasana'],
				'taso' => $row['taso']
			));
			
			return $kayttaja;
		}
		
		return null;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function save() {
		$query = DB::connection()->prepare('INSERT INTO kayttaja (nimi, email, salasana, taso) VALUES (:nimi, :email, :salasana, :taso) RETURNING id');
		$query->execute(array(
			'nimi' => $this->nimi,
			'email' => $this->email,
			'salasana' => $this->salasana,
			'taso' => $this->taso
		));
		
		$row = $query->fetch();
		
		$this->id = $row['id'];
	}
	
	
	public function update() {
        $query = DB::connection()->prepare('UPDATE kayttaja SET nimi = :nimi, email = :email, salasana = :salasana, taso = :taso WHERE id = :id');
        $query->execute(array(
            'nimi' => $this->nimi,
			'email' => $this->email,
			'salasana' => $this->salasana,
			'taso' => $this->taso,
			'id' => $this->id
        ));
    }
	
	
	public function destroy() {
        $query = DB::connection()->prepare('DELETE FROM kayttaja WHERE id = :id');
        $query->execute(array(
            'id' => $this->id
        ));
    }


	public static function authenticate($email, $salasana) {
		$query = DB::connection()->prepare('SELECT * FROM kayttaja WHERE email = :email AND salasana = :salasana LIMIT 1');
		$query->execute(array('email' => $email, 'salasana' => $salasana));
		$row = $query->fetch();
		if($row){
			$kayttaja = new Kayttaja(array(
				'id' => $row['id'],
				'nimi' => $row['nimi'],
				'email' => $row['email'],
				'taso' => $row['taso']
			));

			return $kayttaja;
		} else {
			// Kayttajaa ei loytynyt
			return null;
		}
	}
	
	
	public static function resepteja($id) {
		$query = DB::connection()->prepare('SELECT COUNT(*) AS lkm FROM resepti WHERE tekijaid = :tekijaid');
		$query->execute(array('tekijaid' => $id));
		$row = $query->fetch();
		if($row) {
			return $row['lkm'];
		}
		return 0;
	}
	
	
	public function validate_nimen_minimipituus() {
		return parent::validate_string_length($this->nimi, self::NIMEN_MINIMIPITUUS, 'Nimi');
	}
	
	public function validate_nimen_maksimipituus() {
		return parent::validate_string_max_length($this->nimi, self::NIMEN_MAKSIMIPITUUS, 'Nimi');
	}
	
	public function validate_email() {
		$email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
		$errors = parent::validate_string_max_length($email, self::EMAIL_MAKSIMIPITUUS, 'Email');

		if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			// Email on ok.
		} else {
			$errors[] = 'Email-osoite ei ole oikein!';
		}
		
		return $errors;
	}
	
	public function validate_salasanan_minimipituus() {
		return parent::validate_string_length($this->salasana, self::NIMEN_MINIMIPITUUS, 'Salasana');
	}
	
	public function validate_salasanan_maksimipituus() {
		return parent::validate_string_max_length($this->salasana, self::NIMEN_MAKSIMIPITUUS, 'Salasana');
	}
	
}
	