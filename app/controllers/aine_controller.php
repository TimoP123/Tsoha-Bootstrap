<?php

class AineController extends BaseController {

	public static function listJSON() {
		$aineet = Aine::nimiTaulukko();
		header("content-type: application/json");
		echo json_encode($aineet);
	}
	
}