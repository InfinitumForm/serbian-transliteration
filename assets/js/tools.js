(function(DOC){
	// Fix special characters
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
	};
	
	// AJAX request
	var xhttp_transient, xhttp_transient_timeout,
		ajax = (method, src, object, headers) => {
			if(xhttp_transient_timeout) clearTimeout(xhttp_transient_timeout);
			
			var xhttp = new XMLHttpRequest(), data = [], o=0;
			
			xhttp_transient = xhttp;
			
			xhttp_transient.onreadystatechange = () => {
				if (xhttp_transient.readyState == 4) {
					xhttp_transient_timeout = setTimeout(()=>{xhttp_transient = null;}, 3e3);
				}
			}
			
			xhttp.open(method, src, true);
			
			if(headers) {
				for(header in headers) {
					xhttp.setRequestHeader(header, headers[header]);
				}
			}
			
			if(object) {
				for(key in object) {
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
			let original_onreadystatechange = xhttp_transient.onreadystatechange;
			xhttp_transient.onreadystatechange = () => {
				if(original_onreadystatechange) original_onreadystatechange();
				if (xhttp_transient.readyState == 4 && xhttp_transient.status == 200) {
					if(is_json) {
						callback(JSON.parse(xhttp_transient.responseText), xhttp_transient);
					} else {
						callback(xhttp_transient.responseText, xhttp_transient);
					}
				}
			};
		},
		ajax_error = (callback) => {
			if(!xhttp_transient) return;
			let original_onreadystatechange = xhttp_transient.onreadystatechange;
			xhttp_transient.onreadystatechange = () => {
				if(original_onreadystatechange) original_onreadystatechange();
				if (xhttp_transient.readyState == 4 && xhttp_transient.status != 200) {
					callback(xhttp_transient.status, xhttp_transient);
				}
			};
		};
		
	DOC.addEventListener('DOMContentLoaded', function() {	

		// Funkcija za dodavanje transliterate dugmadi
		function addTransliterateButtons() {
			// Pronađi sva input i textarea polja sa specifičnim imenima
			var postTitleInputs = DOC.querySelectorAll('input[name="post_title"], .table-view-list .inline-edit-col input[name="name"], #edittag .term-name-wrap input[name="name"], #edittag .term-description-wrap textarea[name="description"], #wpcontent .woocommerce input[name="attribute_label"], #wpcontent .woocommerce input[name^="woocommerce_"][name$="_subject"], #wpcontent .woocommerce input[name^="woocommerce_"][name$="_heading"], #wpcontent .woocommerce textarea[name^="woocommerce_"][name$="_additional_content"]');

			postTitleInputs.forEach(function(postTitleInput) {
				// Proveri da li dugmad već postoji kako bi se izbeglo dupliranje
				if (!postTitleInput.nextElementSibling || !postTitleInput.nextElementSibling.classList.contains('transliterate-admin-fields')) {
					// Kreiraj wrapper div sa klasom "transliterate-fields"
					var transliterateWrapper = DOC.createElement('div');
					transliterateWrapper.className = 'transliterate-admin-fields';

					// Kreiraj span label
					var translHead = DOC.createElement('span');
					translHead.textContent = RSTR_TOOL.label.Transliterate;
					translHead.className = 'transl-label';

					// Kreiraj link za Latinicu
					var latinicaLink = DOC.createElement('a');
					latinicaLink.href = '#latin';
					latinicaLink.textContent = RSTR_TOOL.label.Latin;
					latinicaLink.className = 'transl-field-to-latin';
					latinicaLink.dataset.mode = 'cyr_to_lat';

					// Kreiraj link za Ćirilicu
					var cirilicaLink = DOC.createElement('a');
					cirilicaLink.href = '#cyrillic';
					cirilicaLink.textContent = RSTR_TOOL.label.Cyrillic;
					cirilicaLink.className = 'transl-field-to-cyrillic';
					cirilicaLink.dataset.mode = 'lat_to_cyr';

					// Dodaj linkove u wrapper
					transliterateWrapper.appendChild(translHead);
					transliterateWrapper.appendChild(latinicaLink);
					transliterateWrapper.appendChild(cirilicaLink);

					// Umetni wrapper sa linkovima odmah nakon input/textarea polja
					postTitleInput.parentNode.insertBefore(transliterateWrapper, postTitleInput.nextSibling);
				}
			});
		}

		// Pokreni funkciju za inicijalno učitane elemente
		addTransliterateButtons();

		// MutationObserver za praćenje promena u DOM-u
		var observer = new MutationObserver(function(mutations) {
			mutations.forEach(function(mutation) {
				// Ponovno pokreni funkciju za dodavanje dugmadi kada se otkriju promene
				addTransliterateButtons();
			});
		});

		// Opcije za MutationObserver
		var config = { childList: true, subtree: true };

		// Posmatraj body za promene
		observer.observe(DOC.body, config);

		// Detekcija klikova na linkove unutar .transliterate-admin-fields
		DOC.addEventListener('click', function(event) {
			// Proveri da li je kliknuti element link unutar .transliterate-admin-fields
			if (event.target.matches('.transliterate-admin-fields > a')) {
				event.preventDefault(); // Spreči podrazumevanu akciju linka

				// Pronađi input ili textarea polje koje je pre .transliterate-admin-fields kontejnera
				var inputField = event.target.closest('.transliterate-admin-fields').previousElementSibling;

				// Proveri da li je pronađeni element input ili textarea polje
				if (inputField && ['input', 'textarea'].indexOf(inputField.tagName.toLowerCase()) > -1) {
					var inputValue = inputField.value; // Dobavi vrednost iz polja
					
					event.target.classList.add('disabled');

					ajax('POST', RSTR_TOOL.ajax, {
						'action' : 'rstr_transliteration_letters',
						'mode'   : event.target.dataset.mode,
						'nonce'  : RSTR_TOOL.nonce,
						'value'  : inputValue,
						'rstr_skip' : true
					}, {
						'Accept' : 'text/plain'
					});
					
					ajax_done(function(data){
						inputField.value = decodeHtmlCharCodes(data);
						console.log(data, inputField.value);
						event.target.classList.remove('disabled');
					});
					
					ajax_error(function(data){
						event.target.classList.remove('disabled');
					});
				}
			}
		});
	});
	
}(document));