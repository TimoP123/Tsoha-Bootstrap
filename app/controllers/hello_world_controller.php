<?php

  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  View::make('home.html');
    }
	
	public static function login(){
   	  View::make('suunnitelmat/login.html');
    }
	
	public static function etusivu(){
   	  View::make('suunnitelmat/etusivu.html');
    }
	
	public static function reseptit(){
   	  View::make('suunnitelmat/reseptit.html');
    }
	
	public static function reseptiesimerkki(){
   	  View::make('suunnitelmat/reseptiesimerkki.html');
    }
	
	public static function muokkaa(){
   	  View::make('suunnitelmat/muokkaa.html');
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      //echo 'Hello World!';
	  //View::make('helloworld.html');
	  
	  $perunat = Resepti::find(1);
	  $reseptit = Resepti::all();
	  
	  Kint::dump($reseptit);
	  Kint::dump($perunat);
	  
	  $aine = Aine::find(2);
	  $aineet = Aine::all();
	  
	  Kint::dump($aineet);
	  Kint::dump($aine);
	  
	  $tag = Tag::find(2);
	  $tagit = Tag::all();
	  
	  Kint::dump($tagit);
	  Kint::dump($tag);
	  
	  $kayttaja = Kayttaja::find(2);
	  $kayttajat = Kayttaja::all();
	  
	  Kint::dump($kayttajat);
	  Kint::dump($kayttaja);

    }
  }
