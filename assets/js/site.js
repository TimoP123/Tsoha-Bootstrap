$(document).ready(function(){
	
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
  
	// Suoritetaan nimihakukentan klilkkaus, jos ollaan hakusivulla.
	if($('#nimihaku').length) {
		$('#nimihaku').click();
	}

	$('#addTagField').click(function() {
		$('#tagFields').append('<br><input type="text" class="form-control" name="tagit[]">');
	});
});
