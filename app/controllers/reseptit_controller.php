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
		View::make('resepti/new.html');
	}
	
	
	public static function store() {
		$params = $_POST;
		
		$resepti = new Resepti(array(
			'tekijaId' => 2,			// Oletusarvo, kunnes kayttajan id:n haku saadaan toimintaan.
			'nimi' => $params['nimi'],
			'ohje' => $params['ohje']
		));
		
		$resepti->save();
		
		Redirect::to('/resepti/' . $resepti->id, array('message' => 'Resepti on nyt lisätty tietokantaan.'));
	}
}