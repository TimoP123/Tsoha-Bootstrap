<?php

  class BaseModel{
    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null){
      // Käydään assosiaatiolistan avaimet läpi
      foreach($attributes as $attribute => $value){
        // Jos avaimen niminen attribuutti on olemassa...
        if(property_exists($this, $attribute)){
          // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
          $this->{$attribute} = $value;
        }
      }
    }

    public function errors(){
      // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
      $errors = array();

      foreach($this->validators as $validator){
        $validator_errors = $this->{$validator}();
        $errors = array_merge($errors, $validator_errors);
      }

      return $errors;
    }


	// Validaattorifunktio, joka tarkistaa että merkkijono on vähintään halutun mittainen.
    public function validate_string_length($string, $length, $type){
      $errors = array();
      if($string == '' || $string == null){
        $errors[] = $type.' ei saa olla tyhjä!';
      }
      if(strlen($string) < $length){
        $errors[] = $type.'-merkkijonon pituuden tulee olla vähintään '.$length.' merkkiä!';
      }

      return $errors;
    }
	
	// Validaattorifunktio, joka tarkistaa ettei merkkijono ylitä annettua maksimipituutta.
	public function validate_string_max_length($string, $max_length, $type) {
		$errors = array();
		if(strlen($string) > $max_length){
			$errors[] = $type.'-merkkijonon pituus saa olla korkeintaan '.$length.' merkkiä!';
		}

		return $errors;
	}
	
  }
  
// usort-funktion käyttämä callback-funktio reseptien järjestämiseksi.
function sortByName($a, $b) {
	return strcmp(strtolower($a->nimi), strtolower($b->nimi));
}
