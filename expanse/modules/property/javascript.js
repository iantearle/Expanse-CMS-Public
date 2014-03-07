(function($){var defaults={google:window.google};$.handleError=function(err){if(window.console){console.log(err);}else{alert('Error: '+err);}
 return false;};$.translate=function(element,options){if(typeof options.target=='undefined'){$.handleError('You need to set a target element for the text to display.');return false;}
 if(typeof options.targetLang=='undefined'){$.handleError('You need to set a target language for the text to be translated into.');return false;}
 if(typeof options.apiKey=='undefined'){$.handleError('You need to set a valid Google API key.');return false;}
 var input=$(element).html();var outputElement=options.target;var source='https://www.googleapis.com/language/translate/v2'
 +'?key='+options.apiKey
 if(options.sourceLang)
 source+='&source='+options.sourceLang;source+='&target='+options.targetLang
 +'&q='+input
 +'&output=json'
 +'&callback=?';$.getJSON(source,function(result){var output=result.data.translations[0].translatedText;$(outputElement).html(output);});};$.fn.googleTranslate=function(options,callback){var options=$.extend({},$.defaults,options);return this.each(function(){new $.translate(this,options);});};})(jQuery);


function Expanse() {
	var localSearch = new GlocalSearch();

	function usePointFromPostcode(postcode, callbackFunction) {
	    localSearch.setSearchCompleteCallback(null,
	        function() {

	            if (localSearch.results[0]) {
	                var resultLat = localSearch.results[0].lat;
	                var resultLng = localSearch.results[0].lng;

	                document.forms['post'].latitude.value=resultLat;
	                document.forms['post'].longitude.value=resultLng;
	            }else{
	                alert("Postcode not found!");
	            }
	        });

	    localSearch.execute(postcode);
	}

		$('#title').focus();
		$('#postcode').blur(function() {
			var postcode = $("input#postcode").val()
			usePointFromPostcode(postcode);
		});
		if(!$('#latitude').val() && !$('#longitude').val() && $('input#postcode').val()) {
			var postcode = $("input#postcode").val()
			usePointFromPostcode(postcode);
		}
		$('#getPostcode').click(function() {
			var postcode = $("input#postcode").val()
			usePointFromPostcode(postcode);
		});

	if(!$("#descr-i18n").val()) {
		$('.descr-raw').googleTranslate({
			target : '#descr-i18n',
			targetLang : 'fr',
			apiKey : 'AIzaSyAamgi_kSl0Gcmq-e-81wmG-UvwsueDewo'//'AIzaSyD0w9xewbK6EC3INLMEvzPR-CP8Rcy7SYg'
		});
	}
}