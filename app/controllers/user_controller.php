<?php

class UserController extends BaseController {

	public static function login(){
		if(self::get_user_logged_in()) {
			Redirect::to('/', array('message' => 'Olet jo kirjautunut sisään!'));
		} else {
			View::make('kayttaja/login.html');
		}
	}

	public static function handle_login(){
		$params = $_POST;
		
		$kayttaja = Kayttaja::authenticate($params['email'], $params['salasana']);

		if(!$kayttaja){
			View::make('kayttaja/login.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'email' => $params['email']));
		} else {
			$_SESSION['kayttaja'] = $kayttaja->id;
			$_SESSION['taso'] = $kayttaja->taso;

			Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $kayttaja->nimi . '!'));
		}
	}

	public static function logout(){
		$_SESSION['kayttaja'] = null;
		$_SESSION['taso'] = null;
		Redirect::to('/login', array('message' => 'Olet kirjautunut ulos!'));
	}
	
	public static function index(){
		self::check_logged_in();
		$kayttajat = Kayttaja::all();
		View::make('kayttaja/index.html', array('kayttajat' => $kayttajat));
	}
	
	public static function show($id) {
		self::check_logged_in();
		$kayttaja = Kayttaja::find($id);
		$resepteja = Kayttaja::resepteja($id);
		View::make('kayttaja/kayttajasivu.html', array('kayttaja' => $kayttaja, 'resepteja' => $resepteja));
	}

	public static function create() {
		if(self::get_user_logged_in()) {
			Redirect::to('/kayttaja', array('error' => 'Sinulla on jo tunnukset!'));
		}
		View::make('kayttaja/new.html');
	}

	public static function store() {
		if(self::get_user_logged_in()) {
			Redirect::to('/kayttaja', array('message' => 'Sinulla on jo tunnukset!'));
		}

		$params = $_POST;

		$attributes = array(
			'nimi' => $params['nimi'],
			'email' => $params['email'],
			'salasana' => $params['salasana'],
			'taso' => 1 // Lomakkeen avulla luodaan vain peruskäyttäjiä
		);
		
		if(!strcmp($params['salasana'], $params['salasana2']) == 0) {
			View::make('kayttaja/new.html', array('errors' => array('Salasanat ovat erilaiset.'), 'attributes' => $attributes));
		}

		$kayttaja = new Kayttaja($attributes);

		$errors = $kayttaja->errors();

		if(count($errors) == 0) {
			$kayttaja->save();
			Redirect::to('/login', array('message' => 'Tunnukset luotiin onnistuneesti!'));
		} else {
			View::make('kayttaja/new.html', array('errors' => $errors, 'attributes' => $attributes));
		}
	}

	public static function edit($id) {
		self::check_logged_in();

		$kayttaja = Kayttaja::find($id);

		if((self::get_user_logged_in()->id == $id) || self::is_admin()) {
			View::make('kayttaja/edit.html', array('attributes' => $kayttaja));	
		} else {
			Redirect::to('/', array('error' => 'Voit muokata vain omia tietojasi!'));
		}

	}

	public static function update($id) {
		self::check_logged_in();

		$params = $_POST;

		$attributes = array(
			'id' => $id,
			'nimi' => $params['nimi'],
			'email' => $params['email'],
			'salasana' => $params['salasana'],
		);
		
		if(!strcmp($params['salasana'], $params['salasana2']) == 0) {
			View::make('kayttaja/edit.html', array('errors' => array('Salasanat ovat erilaiset.'), 'attributes' => $attributes));
		}

		if(self::is_admin()) {
			$attributes['taso'] = 2;
		} else {
			$attributes['taso'] = 1;
		}

		$kayttaja = new Kayttaja($attributes);
		$errors = $kayttaja->errors();

		if($kayttaja->id == $id) {
			if(count($errors) == 0) {
				$kayttaja->update();
				Redirect::to('/kayttaja/'.$kayttaja->id, array('message' => 'Tietojasi muokattiin onnistuneesti!'));
			} else {
				View::make('kayttaja/edit.html', array('errors' => $errors, 'attributes' => $attributes));
			}
		} else {
			Redirect::to('/', array('error' => 'Voit muokata vain omia tietojasi!'));
		}
	}

	public static function destroy($id) {
		if(!(self::is_admin())) {
			Redirect::to('/', array('error' => 'Kuulehan.. Et voi poistaa käyttäjiä!'));
		}

		$kayttaja = Kayttaja::find($id);
		// Varmistetaan vielä, että poistettava käyttäjä on peruskäyttäjätasoinen.
		if($kayttaja->taso == 1) {
			
			// Poistetaan poistettavan käyttäjän reseptit ensin.
			$reseptit = Resepti::findByOwner($id);
			foreach($reseptit as $resepti) {
				$resepti->destroy();
			}
			
			$kayttaja->destroy();
			Redirect::to('/kayttaja', array('message' => 'Käyttäjä on poistettu onnistuneesti!'));
		} else {
			Redirect::to('/kayttaja', array('message' => 'Et voi poistaa admineja!'));
		}
	}
}
