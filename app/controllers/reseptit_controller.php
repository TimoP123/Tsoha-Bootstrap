<?php

class ReseptiController extends BaseController {
	
	public static function index() {
		$reseptit = Resepti::all();
		View::make('resepti/index.html', array('reseptit' => $reseptit));
	}
	
	
	public static function show($id) {
		$resepti = Resepti::find($id);
		View::make('resepti/reseptisivu.html', array('resepti' => $resepti));
	}
	
	
	public static function create() {
		self::check_logged_in();
		View::make('resepti/new.html');
	}
	
	
	public static function store() {
		self::check_logged_in();
		
		$params = $_POST;
		$tagit = array();
		if(isset($params['tagit'])) {
			$tagit = $params['tagit'];
		}

		$attributes = array(
			'nimi' => $params['nimi'],
			'ohje' => $params['ohje'],
			'tagit' => array()
		);
		
		$resepti = new Resepti(array(
			'tekijaId' => 2,			// Oletusarvo, kunnes kayttajan id:n haku saadaan toimintaan.
			'nimi' => $params['nimi'],
			'ohje' => $params['ohje']
		));

		$errors = $resepti->errors();

		foreach($tagit as $tagnimi) {
			if(strlen($tagnimi) == 0) {
				continue;
			}
			$tagId = Tag::checkAndSave($tagnimi);
			$tag = Tag::find($tagId);
			$attributes['tagit'][] = $tag;
			$errors = array_merge($errors, $tag->errors());
		}

		$resepti->tagit = $attributes['tagit'];

		if(count($errors) == 0) {
			$resepti->save();
			Redirect::to('/resepti/' . $resepti->id, array('message' => 'Resepti on lisätty reseptitietokantaan!'));
		} else {
			View::make('resepti/new.html', array('errors' => $errors, 'attributes' => $attributes));
		}
	}


	public static function edit($id){
		self::check_logged_in();
		
		$resepti = Resepti::find($id);
		
		View::make('resepti/edit.html', array('attributes' => $resepti));
	}


	public static function update($id){
		self::check_logged_in();
		
		$params = $_POST;
		$tagit = array();
		if(isset($params['tagit'])) {
			$tagit = $params['tagit'];
		}

		$attributes = array(
			'id' => $id,
			'tekijaId' => 2,			// Oletusarvo, kunnes kayttajan id:n haku saadaan toimintaan.
			'nimi' => $params['nimi'],
			'ohje' => $params['ohje'],
			'tagit' => array()
		);

		$resepti = new Resepti($attributes);
		$errors = $resepti->errors();


		foreach($tagit as $tagnimi) {
			if(strlen($tagnimi) == 0) {
				continue;
			}
			$tagId = Tag::checkAndSave($tagnimi);
			$tag = Tag::find($tagId);
			$attributes['tagit'][] = $tag;
			$errors = array_merge($errors, $tag->errors());
		}

		$resepti->tagit = $attributes['tagit'];

		if(count($errors) > 0){
			View::make('resepti/edit.html', array('errors' => $errors, 'attributes' => $attributes));
		}else{
			$resepti->update();
			Redirect::to('/resepti/' . $resepti->id, array('message' => 'Reseptiä on muokattu onnistuneesti!'));
		}
	}


	public static function destroy($id){
		self::check_logged_in();
		
		// Tilapäinen koodi, poistetaan myöhemmin
		if($id == 1) {
			Redirect::to('/resepti', array('message' => 'Resepti, jonka yritit poistaa, on niin ainutlaatuinen, että annetaanpa sen olla.'));
		}

		$resepti = Resepti::find($id);
		$resepti->destroy();

		Redirect::to('/resepti', array('message' => 'Resepti on poistettu onnistuneesti!'));
	}
	
	
	public static function search() {
      View::make('resepti/search.html');
	}
	
	
	public static function handle_search() {
		$params = $_POST;
		$hakusana = $params['hakusana'];

		if($params['hakutapa'] == "nimi") {
			$reseptit = Resepti::findByName($hakusana);

			if(sizeof($reseptit) > 0) {
				Redirect::to('/resepti', array('message' => 'Nimihaun tulos hakusanalle \''.$hakusana.'\'', 'reseptit' => $reseptit));
			} else {
				Redirect::to('/resepti/search', array('error' => 'Nimihaku ei tuottanut tuloksia hakusanalle \''.$hakusana.'\''));
			}

		} else if($params['hakutapa'] == "aine") {
			$reseptit = Resepti::findByIngredient($hakusana);

			if(sizeof($reseptit) > 0) {
				Redirect::to('/resepti', array('message' => 'Raaka-ainehaun tulos hakusanalle \''.$hakusana.'\'', 'reseptit' => $reseptit));
			} else {
				Redirect::to('/resepti/search', array('error' => 'Raaka-ainehaku ei tuottanut tuloksia hakusanalle \''.$hakusana.'\''));
			}

		} else if($params['hakutapa'] == "tag") {
			$reseptit = Resepti::findByTag($hakusana);
			
			if(sizeof($reseptit) > 0) {
				Redirect::to('/resepti', array('message' => 'Tägihaun tulos hakusanalle \''.$hakusana.'\'', 'reseptit' => $reseptit));
			} else {
				Redirect::to('/resepti/search', array('error' => 'Tägihaku ei tuottanut tuloksia hakusanalle \''.$hakusana.'\''));
			}
		}
	}
	
	
	public static function listJSON() {
		$nimet = Resepti::nimiTaulukko();
		header("content-type: application/json");
		echo json_encode($nimet);
	}
	
}