<?php

class Tag extends BaseModel {
	
	const NIMEN_MINIMIPITUUS = 3;
	
	public $id, $nimi;
	
	public function __construct($attributes) {
		parent::__construct($attributes);
		$this->validators = array('validate_nimi');
	}
	
	public static function all() {
		$query = DB::connection()->prepare('SELECT * FROM tag');
		$query->execute();
		$rows = $query->fetchAll();
		$tagit = array();
		
		foreach($rows as $row) {
			$tagit[] = new Tag(array(
				'id' => $row['id'],
				'nimi' => $row['nimi']
			));
		}
		
		return $tagit;
	}
	
	public static function find($id) {
		$query = DB::connection()->prepare('SELECT * FROM tag WHERE id = :id LIMIT 1');
		$query->execute(array('id' => $id));
		$row = $query->fetch();
		
		if($row) {
			$tag = new Tag(array(
				'id' => $row['id'],
				'nimi' => $row['nimi']
			));
			
			return $tag;
		}
		
		return null;
	}
	
	
	public static function findByName($name) {
		$name = strtolower($name);
		$query = DB::connection()->prepare('SELECT * FROM tag WHERE nimi = :nimi LIMIT 1');
		$query->execute(array('nimi' => $name));
		$row = $query->fetch();
		
		if($row) {
			$tag = new Tag(array(
				'id' => $row['id'],
				'nimi' => $row['nimi']
			));
			
			return $tag;
		}
		
		return null;
	}
	
	
	public static function nimiTaulukko() {
		$tagit = self::all();
		$array = array();
		
		foreach($tagit as $tag) {
			$array[] = $tag->nimi;
		}
		return $array;
	}
	
	
	public function save() {
		$query = DB::connection()->prepare('INSERT INTO tag (nimi) VALUES (:nimi) RETURNING id');
		$query->execute(array(
			'nimi' => $this->nimi
		));
		
		$row = $query->fetch();
		
		$this->id = $row['id'];
	}
	

	public function checkAndSave($name) {
		// Tarkistetaan loytyyko tag jo valmiiksi tietokannasta.
		$tag = self::findByName(strtolower($name));
		if($tag) {
			return $tag->id;
		}

		// Jos tagia ei ole jo valmiiksi olemassa, luodaan talletetaan sellainen tietokantaan
		$tag = new Tag(array('nimi' => $name));
		$tag->save();
		return $tag->id;
	}

	
	public function destroy() {
        $query = DB::connection()->prepare('DELETE FROM tag WHERE id = :id');
        $query->execute(array(
            'id' => $this->id
        ));
    }
	
	
	public function validate_nimi(){
		return parent::validate_string_length($this->nimi, self::NIMEN_MINIMIPITUUS, 'Tag-nimi');
	}
}