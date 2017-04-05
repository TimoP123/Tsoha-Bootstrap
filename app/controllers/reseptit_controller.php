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

		$attributes = array(
			'nimi' => $params['nimi'],
			'ohje' => $params['ohje']
		);
		
		$resepti = new Resepti(array(
			'tekijaId' => 2,			// Oletusarvo, kunnes kayttajan id:n haku saadaan toimintaan.
			'nimi' => $params['nimi'],
			'ohje' => $params['ohje']
		));

		$errors = $resepti->errors();

		if(count($errors) == 0) {
			$resepti->save();
			Redirect::to('/resepti/' . $resepti->id, array('message' => 'Resepti on lisätty reseptitietokantaan!'));
		} else {
			View::make('resepti/new.html', array('errors' => $errors, 'attributes' => $attributes));
		}
	}


	public static function edit($id){
		$resepti = Resepti::find($id);
		
		View::make('resepti/edit.html', array('attributes' => $resepti));
	}


	public static function update($id){
		$params = $_POST;

		$attributes = array(
			'id' => $id,
			'tekijaId' => 2,			// Oletusarvo, kunnes kayttajan id:n haku saadaan toimintaan.
			'nimi' => $params['nimi'],
			'ohje' => $params['ohje']
		);

		$resepti = new Resepti($attributes);
		$errors = $resepti->errors();

		if(count($errors) > 0){
			View::make('resepti/edit.html', array('errors' => $errors, 'attributes' => $attributes));
		}else{
			$resepti->update();
			Redirect::to('/resepti/' . $resepti->id, array('message' => 'Reseptiä on muokattu onnistuneesti!'));
		}
	}


	public static function destroy($id){
		$resepti = Resepti::find($id);
		$resepti->destroy();

		Redirect::to('/resepti', array('message' => 'Resepti on poistettu onnistuneesti!'));
	}
}