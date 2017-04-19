<?php

class AineController extends BaseController {

	public static function listJSON() {
		$aineet = Aine::JsonTaulukko();
		header("content-type: application/json");
		echo json_encode($aineet);
	}
	
}