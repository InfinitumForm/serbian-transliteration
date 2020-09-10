/**
 * JavaScript for the Serbian Transliteration Plugin
 *
 * @link              http://infinitumform.com/
 * @since             1.0.0
 * @package           Serbian_Transliteration
 * @autor             Ivijan-Stefan Stipic
 */
;(function(){
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
		}

	/*
	 * TOOLS: Transliterate permalinks
	 */
	(function(checkbox, button, progress, disclaimer){
		// Get objects
		checkbox = document.getElementById(checkbox),
		button = document.getElementById(button);
		progress = document.getElementById(progress);
		disclaimer = document.getElementById(disclaimer);
		
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
						'nonce'  : e.target.dataset.nonce
					}, {
						'Accept' : 'application/json'
					});
				}
				
				ajax_done(function(data){
					console.log(data);
					
					if(!data.error)
					{
						progress_bar(data.percentage);
						
						if(data.done)
						{
							button.disabled = false;
							checkbox.disabled = false;
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
			
		});
		
	}('serbian-transliteration-tools-check', 'serbian-transliteration-tools-transliterate-permalinks', 'rstr-progress-bar', 'rstr-disclaimer'));
}());