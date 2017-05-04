$(document).ready(function(){
	
	// Autocomplete-skriptit, jotka hoitavat hakukenttien sanaehdotuksia.
	$('#nimihaku').click(function() {
		$.getJSON("http://tpudas.users.cs.helsinki.fi/reseptit/nimet", function(data) {
			$('#hakusana').autocomplete({
				source: data
			});
		});
	});
	
	$('#ainehaku').click(function() {
		$.getJSON("http://tpudas.users.cs.helsinki.fi/reseptit/aineet", function(data) {
			$('#hakusana').autocomplete({
				source: data
			});
		});
	});
  
	$('#tagihaku').click(function() {
		$.getJSON("http://tpudas.users.cs.helsinki.fi/reseptit/tagit", function(data) {
			$('#hakusana').autocomplete({
				source: data
			});
		});
	});
  
	// Suoritetaan nimihakukentan klikkaus oletukseksi, jos ollaan hakusivulla.
	if($('#nimihaku').length) {
		$('#nimihaku').click();
	}

	// Tag- ja raaka-ainekenttien lisääminen
	$('#addTagField').click(function() {
		$('#tagFields').append('<br><i>Tag</i><input type="text" class="form-control" title="Kirjoita kenttään yksi tagi. Lisää kenttiä saat alla olevasta nappulasta." name="tagit[]">');
	});

	$('#addAineField').click(function() {
		$('#aineFields').append('<br><i>Aine</i><input type="text" class="form-control" title="Kirjoita aine perusmuodossa (esim. kananmuna)." name="aineet[]"><i>M&auml;&auml;r&auml;</i><input type="text" class="form-control" title="Voit kirjoittaa raaka-ainemäärän missä muodossa haluat (esim. hyppysellinen, 3, 4dl, jne.)." name="maarat[]">');
	});

	// Poistamisen varmistaminen
	$('form.destroy-form').on('submit', function(submit){
		var confirm_message = $(this).attr('data-confirm');
		if(!confirm(confirm_message)){
			submit.preventDefault();
		}
	});
	
	// Varmistetaan ennen lomakkeen lähettämistä, että salasana on riittävän pitkä ja että se on syötetty kaksi kertaa samalla tavalla.
	$('form.user_form').on('submit', function(submit){
		
		if($('#salasana').val().length < 6) {
			submit.preventDefault();
			alert("Salasanan tulee olla vähintään kuusi merkkiä pitkä!");
			return false;
		}
		
		if(!($('#salasana').val() === $('#salasana2').val())) {
			submit.preventDefault();
			alert("Salasanat eivät ole samanlaiset!");
			return false;
		}		
	});
});

