<?php

class UserController extends BaseController{

  public static function login(){
      View::make('kayttaja/login.html');
  }

  public static function handle_login(){
    $params = $_POST;

    $kayttaja = Kayttaja::authenticate($params['email'], $params['salasana']);

    if(!$kayttaja){
      View::make('kayttaja/login.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'email' => $params['email']));
    } else {
      $_SESSION['kayttaja'] = $kayttaja->id;
      //$_SESSION['taso'] = $kayttaja->taso;

      Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $kayttaja->nimi . '!'));
    }
  }

  public static function logout(){
    $_SESSION['kayttaja'] = null;
    Redirect::to('/login', array('message' => 'Olet kirjautunut ulos!'));
  }
}