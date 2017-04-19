<?php

class TagController extends BaseController {

	public static function listJSON() {
		$tagit = Tag::JsonTaulukko();
		header("content-type: application/json");
		echo json_encode($tagit);
	}
	
}