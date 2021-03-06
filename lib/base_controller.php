<?php

  class BaseController{

    public static function get_user_logged_in(){
      if(isset($_SESSION['kayttaja'])){
        $kayttaja = Kayttaja::find($_SESSION['kayttaja']);
        return $kayttaja;
      }
      return null;
    }

    public static function check_logged_in(){
      if(!isset($_SESSION['kayttaja'])){
		Redirect::to('/login', array('message' => 'Kirjaudu ensin sisään!'));
	  }
    }
	
	public static function is_admin() {
		if(isset($_SESSION['kayttaja'])) {
			$kayttaja = Kayttaja::find($_SESSION['kayttaja']);
			if($kayttaja->taso == 2) {
				return true;
			}
		}
		return false;
	}

  }
