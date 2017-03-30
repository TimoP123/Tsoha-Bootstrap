<?php

class Aine extends BaseModel {
	
	public $id, $nimi;
	
	public function __construct($attributes) {
		parent::__construct($attributes);
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
	
}