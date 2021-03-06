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
		$aineet = array();
		$maarat = array();

		if(isset($params['tagit'])) {
			$tagit = $params['tagit'];
		}

		if(isset($params['aineet']) && isset($params['maarat'])) {
			$aineet = $params['aineet'];
			$maarat = $params['maarat'];
		}

		$attributes = array(
			'nimi' => $params['nimi'],
			'ohje' => $params['ohje'],
			'tagit' => array(),
			'aineet' => array()
		);
		
		$resepti = new Resepti(array(
			'tekijaId' => self::get_user_logged_in()->getId(),
			'nimi' => $params['nimi'],
			'ohje' => $params['ohje']
		));

		$errors = $resepti->errors();

		// Käydään reseptiin mahdollisesti liitetyt tagit läpi ja lisätään ne tietokantaan, jos ne eivät ole jo siellä.
		foreach($tagit as $tagnimi) {
			if(strlen($tagnimi) == 0) {
				continue;
			}
			$tagId = Tag::checkAndSave(strtolower($tagnimi));
			$tag = Tag::find($tagId);
			$attributes['tagit'][] = $tag;
			$errors = array_merge($errors, $tag->errors());
		}

		// Käydään myös reseptin valmistusaineet läpi ja lisätään tietokantaan ne aineet, jotka eivät siellä vielä ole.
		for($i = 0; $i < sizeof($aineet); $i++) {
			if(strlen($aineet[$i]) == 0 || strlen($maarat[$i]) == 0) {
				continue;
			}
			$aineId = Aine::checkAndSave(strtolower($aineet[$i]));
			$aine = Aine::find($aineId);
			$aine->maara = strtolower($maarat[$i]);
			$attributes['aineet'][] = $aine;
			$errors = array_merge($errors, $aine->errors());
		}

		$resepti->tagit = $attributes['tagit'];
		$resepti->aineet = $attributes['aineet'];

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
		
		// Varmistetaan, että reseptiä pääsee muokkaamaan vain reseptin luoja tai admin.
		if(self::get_user_logged_in()->getId() == $resepti->tekijaId || self::is_admin()) {
			View::make('resepti/edit.html', array('attributes' => $resepti));
		} else {
			Redirect::to('/resepti/' . $resepti->id, array('message' => 'Et voi muokata toisten reseptejä!'));
		}
	}


	public static function update($id){
		self::check_logged_in();
		
		$params = $_POST;
		$tagit = array();
		$aineet = array();
		$maarat = array();

		if(isset($params['tagit'])) {
			$tagit = $params['tagit'];
		}

		if(isset($params['aineet']) && isset($params['maarat'])) {
			$aineet = $params['aineet'];
			$maarat = $params['maarat'];
		}

		$attributes = array(
			'id' => $id,
			'tekijaId' => $params['tekijaId'],
			'nimi' => $params['nimi'],
			'ohje' => $params['ohje'],
			'tagit' => array(),
			'aineet' => array()
		);

		$resepti = new Resepti($attributes);
		$errors = $resepti->errors();


		foreach($tagit as $tagnimi) {
			if(strlen($tagnimi) == 0) {
				continue;
			}
			$tagId = Tag::checkAndSave(strtolower($tagnimi));
			$tag = Tag::find($tagId);
			$attributes['tagit'][] = $tag;
			$errors = array_merge($errors, $tag->errors());
		}

		for($i = 0; $i < sizeof($aineet); $i++) {
			if(strlen($aineet[$i]) == 0 || strlen($maarat[$i]) == 0) {
				continue;
			}
			$aineId = Aine::checkAndSave(strtolower($aineet[$i]));
			$aine = Aine::find($aineId);
			$aine->maara = strtolower($maarat[$i]);
			$attributes['aineet'][] = $aine;
			$errors = array_merge($errors, $aine->errors());
		}

		$resepti->tagit = $attributes['tagit'];
		$resepti->aineet = $attributes['aineet'];

		if(count($errors) > 0){
			View::make('resepti/edit.html', array('errors' => $errors, 'attributes' => $attributes));
		}else{
			$resepti->update();
			Redirect::to('/resepti/' . $resepti->id, array('message' => 'Reseptiä on muokattu onnistuneesti!'));
		}
	}


	public static function destroy($id){
		self::check_logged_in();

		$resepti = Resepti::find($id);

		// Varmistetaan, että reseptin pystyy poistamaan vain sen luoja tai admin.
		if(self::get_user_logged_in()->id == $resepti->tekijaId || self::is_admin()) {
			$resepti = Resepti::find($id);
			$resepti->destroy();

			Redirect::to('/resepti', array('message' => 'Resepti on poistettu onnistuneesti!'));
		} else {
			Redirect::to('/resepti/' . $resepti->id, array('message' => 'Et voi poistaa toisten reseptejä!'));
		}
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
	
	// Funktio palauttaa reseptien nimet JSON-taulukkona hakusivun autocomplete-toimintoa varten.
	public static function listJSON() {
		$nimet = Resepti::nimiTaulukko();
		header("content-type: application/json");
		echo json_encode($nimet);
	}
	
}