<?php

class Tag extends BaseModel {
	
	public $id, $nimi;
	
	public function __construct($attributes) {
		parent::__construct($attributes);
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
	
}