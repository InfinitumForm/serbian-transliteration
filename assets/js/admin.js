/**
 * JavaScript for the Serbian Transliteration Plugin
 *
 * @link              http://infinitumform.com/
 * @since             1.0.1
 * @package           Serbian_Transliteration
 * @autor             Ivijan-Stefan Stipic
 */
;(function(){
	
	/*
	 * Fix special characters
	 */
	var decodeHtmlCharCodes = function decodeHtmlCharCodes(string, quoteStyle) { 
		//       discuss at: https://locutus.io/php/htmlspecialchars_decode/
		//      original by: Mirek Slugen
		//      improved by: Kevin van Zonneveld (https://kvz.io)
		//      bugfixed by: Mateusz "loonquawl" Zalega
		//      bugfixed by: Onno Marsman (https://twitter.com/onnomarsman)
		//      bugfixed by: Brett Zamir (https://brett-zamir.me)
		//      bugfixed by: Brett Zamir (https://brett-zamir.me)
		//         input by: ReverseSyntax
		//         input by: Slawomir Kaniecki
		//         input by: Scott Cariss
		//         input by: Francois
		//         input by: Ratheous
		//         input by: Mailfaker (https://www.weedem.fr/)
		//       revised by: Kevin van Zonneveld (https://kvz.io)
		// reimplemented by: Brett Zamir (https://brett-zamir.me)
		//        example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES')
		//        returns 1: '<p>this -> &quot;</p>'
		//        example 2: htmlspecialchars_decode("&amp;quot;")
		//        returns 2: '&quot;'
		let optTemp = 0;
		let i = 0;
		let noquotes = false;
		if (typeof quoteStyle === 'undefined') {
			quoteStyle = 2;
		}
		string = string.toString()
			.replace(/&lt;/g, '<')
			.replace(/&gt;/g, '>');
		const OPTS = {
			ENT_NOQUOTES: 0,
			ENT_HTML_QUOTE_SINGLE: 1,
			ENT_HTML_QUOTE_DOUBLE: 2,
			ENT_COMPAT: 2,
			ENT_QUOTES: 3,
			ENT_IGNORE: 4
		}
		if (quoteStyle === 0) {
			noquotes = true;
		}
		if (typeof quoteStyle !== 'number') {
			// Allow for a single string or an array of string flags
			quoteStyle = [].concat(quoteStyle)
			for (i = 0; i < quoteStyle.length; i++) {
				// Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
				if (OPTS[quoteStyle[i]] === 0) {
					noquotes = true;
				} else if (OPTS[quoteStyle[i]]) {
					optTemp = optTemp | OPTS[quoteStyle[i]];
				}
			}
			quoteStyle = optTemp;
		}
		if (quoteStyle & OPTS.ENT_HTML_QUOTE_SINGLE) {
			// PHP doesn't currently escape if more than one 0, but it should:
			string = string.replace(/&#039;/g, "'");
			// This would also be useful here, but not a part of PHP:
			// string = string.replace(/&apos;|&#x0*27;/g, "'");
		}
		if (!noquotes) {
			string = string.replace(/&quot;/g, '"');
		}
		// Put this in last place to avoid escape being double-decoded
		string = string.replace(/&amp;/g, '&');
		return string;
	}

	
	/*
	 * AJAX request
	 */
	var xhttp_transient, xhttp_transient_timeout,
		ajax = (method, src, object, headers) => {
			if(xhttp_transient_timeout) clearTimeout(xhttp_transient_timeout);
			
			var xhttp = new XMLHttpRequest(), data = [], o=0;
			
			xhttp_transient = xhttp;
			
			xhttp_transient.onreadystatechange = () => {
				if (xhttp_transient.readyState == 4 && xhttp_transient.status == 200) {
					xhttp_transient_timeout = setTimeout(()=>{xhttp_transient = null;}, 3e3);
				}
			}
			
			xhttp.open(method, src, true);
			
			if(headers)
			{
				for(header in headers)
				{
					xhttp.setRequestHeader(header, headers[header]);
				}
			}
			
			if(object) {
				for(key in object)
				{
					data[o]=key + '=' + object[key];
					o++;
				}
				xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xhttp.send(data.join('&', data));
			} else {
				xhttp.send();
			}			
		},
		ajax_anytime = (callback) => {
			xhttp_transient.onreadystatechange = () => callback(xhttp_transient);
		},
		ajax_done = (callback, is_json) => {
			if(!xhttp_transient) return;
			xhttp_transient.onreadystatechange = () => {
				if (xhttp_transient.readyState == 4 && xhttp_transient.status == 200) {
					if(is_json) {
						callback(JSON.parse(xhttp_transient.responseText), xhttp_transient);
					} else {
						callback(xhttp_transient.responseText, xhttp_transient);
					}
				}
			}
		},
		ajax_error = (callback) => {
			if(!xhttp_transient) return;
			xhttp_transient.onreadystatechange = () => {
				if (typeof xhttp_transient.readyState == 'undefined' || xhttp_transient.status != 200) {
					callback(xhttp_transient.status, xhttp_transient);
				}
			}
		};
	
	/* Display mode info */
	(function(mode, info){
		var info = document.getElementById(info),
			options = document.getElementsByName(mode),
			filters = document.getElementById('rstr-filter-mode-options'),
			i;
			
		if (options, info) {
			for (i = 0; i < options.length; i++) {
				if (options[i].checked){
					 if(options[i].value == 'forced'){
						info.style.display = null;
					} else {
						info.style.display = 'none';
					}
				}
			}
			
			document.addEventListener('input',(e)=>{
				if(e.target.getAttribute('name') === mode) {
					if(e.target.value == 'forced'){
						info.style.display = null;
					} else {
						info.style.display = 'none';
					}
					
					ajax('POST', RSTR.ajax, {
						'action' : 'rstr_filter_mode_options',
						'nonce'  : e.target.dataset.nonce,
						'mode' : e.target.value,
						'rstr_skip' : true
					}, {
						'Accept' : 'text/html'
					});
					
					filters.innerHTML = '<div class="col"><b style="color:#cc0000;">' + RSTR.label.loading + '</b></div>';
					
					ajax_done(function(data){
						filters.innerHTML = data;
					});
				}
			});
		}
	}('serbian-transliteration[mode]', 'forced-transliteration'));



	/*
	 * TOOLS: Transliterator
	 */
	(function(button, textarea, result){
		button = document.getElementsByClassName(button);
		
		if( button )
		{
			var transliterator_timeout;
			
			textarea = document.getElementById(textarea);
			result = document.getElementById(result);
			
			for(var i = 0; i < button.length; i++) {
				(function(index) {
					button[index].addEventListener("click", () => {
						
						if(transliterator_timeout) clearTimeout(transliterator_timeout);
						
						result.value = RSTR.label.loading;
													
						ajax('POST', RSTR.ajax, {
							'action' : 'rstr_transliteration_letters',
							'mode'   : button[index].dataset.mode,
							'nonce'  : button[index].dataset.nonce,
							'value'  : textarea.value,
							'rstr_skip' : true
						}, {
							'Accept' : 'text/plain'
						});
						
						ajax_done(function(data){
							result.value = decodeHtmlCharCodes(data);
							if(transliterator_timeout) clearTimeout(transliterator_timeout);
						});
						
						transliterator_timeout = setTimeout(function(){
							result.value = ' ';
						},1e4);
					})
				})(i);
			}
		}
	}('button-transliteration-letters', 'rstr-transliteration-letters', 'rstr-transliteration-letters-result'));




	/*
	 * TOOLS: Transliterate permalinks
	 */
	(function(checkbox, button, progress, disclaimer){
		// Get objects
		checkbox = document.getElementById(checkbox),
		button = document.getElementById(button);
		progress = document.getElementById(progress);
		disclaimer = document.getElementById(disclaimer);
		
		if(checkbox && button)
		{
			var progress_bar = (number)=>{
				number = Math.round(number);
				
				var progress_value = progress.children,
					pr = progress_value[0],
					bar = progress_value[1],
					span = bar.children[0];
					
				pr.style.width = number + '%';
				pr.dataset.value = number;
				
				bar.value=number;
				
				span.style.width = number + '%';
				span.innerHTML = number + '%';
				
				if(number>=100)
				{
					progress_value[2].innerHTML = RSTR.label.done;
				}
				else
				{
					progress_value[2].innerHTML = RSTR.label.progress_loading;
				}
			};
			
			// Confirm checkbox
			checkbox.onchange = () => {
				button.disabled = !checkbox.checked;
				disclaimer.style.display = 'block';
			};
			// Click on the button
			button.addEventListener("click", e => {
				e.preventDefault();
				
				var do_ajax = (dataset) => {
					
					if(dataset)
					{
						ajax('POST', RSTR.ajax, dataset, {
							'Accept' : 'application/json'
						});
					}
					else
					{
						ajax('POST', RSTR.ajax, {
							'action' : 'rstr_run_permalink_transliteration',
							'nonce'  : e.target.dataset.nonce,
							'post_type' : Array.from(document.querySelectorAll("input.tools-transliterate-permalinks-post-types:checked")).map(e => e.value),
							'rstr_skip' : true
						}, {
							'Accept' : 'application/json'
						});
					}
					
					ajax_done(function(data){
						if(!data.error)
						{
							progress_bar(data.percentage);
							
							if(data.done)
							{
								button.disabled = false;
								checkbox.disabled = false;
								Array.from(document.querySelectorAll("input.tools-transliterate-permalinks-post-types:checked")).map(e => {e.disabled = false});
							}
							else
							{
								do_ajax(data);
							}
						}
						else
						{
							progress_bar(0);
						}
						
					}, true);
				};
				
				
				button.disabled = true;
				checkbox.disabled = true;
				
				progress.style.display = "block";
				
				progress_bar(1);
				
				do_ajax();
				Array.from(document.querySelectorAll("input.tools-transliterate-permalinks-post-types:checked")).map(e => {e.disabled = true});
				
			});
		}
	}('serbian-transliteration-tools-check', 'serbian-transliteration-tools-transliterate-permalinks', 'rstr-progress-bar', 'rstr-disclaimer'));
	
	/* Accordion */
	(function(c){
		var acc = document.getElementsByClassName(c), i;
		if(acc) {
			for (i = 0; i < acc.length; i++) {
				acc[i].addEventListener("click", function () {
					this.classList.toggle("active");
					var panel = this.nextElementSibling;
					if (panel.style.display === "block") {
						panel.style.display = "none";
					} else {
						panel.style.display = "block";
					}
				});
			}
		}
	}("accordion-link"))
	
	
	console.log("%c\n\nHey, are you are developer? Cool!!!\n\nJoin our team:\n\n%chttps://github.com/InfinitumForm/serbian-transliteration\n\n", "color: #cc0000; font-size: x-large;", "color: #cc0000; font-size: 18px");
}());