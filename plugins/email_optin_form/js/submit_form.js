document.addEventListener('submit',function(e){
    if(e.target && e.target.id== 'optin_form'){
   		e.preventDefault();

   		// Access the form element...
   		var form = new FormData;

   		var isValidated = validateFormData(form);

   		if (false === isValidated) {
   		    return;
   		}

   		sendData(form);
    }
});

function sendData(form) {
    var XHR = new XMLHttpRequest();

    // Define what happens on successful data submission
    XHR.addEventListener('load', function(event) {

        var status = event.target.status;
        var readyState = event.target.readyState;

        if (readyState === 4 && status === 200) {

            // Do your stuff
            var responseType = event.target.response;

            responseType = JSON.parse(responseType);
            
            if(responseType.error === true) {
            	// alert(responseType.message);

              iziToast.error({
                title: '',
                message: responseType.message,
                position: 'bottomLeft'
              });

            }
            
            if(responseType.success === true) {

              document.getElementById("close_newsletter").click();

              if(responseType.type === "success_message_type") {
                iziToast.success({title: '',message: responseType.message,position: 'bottomLeft'});
              }

              if(responseType.type === "redirect_url_type") {
                window.location.href = responseType.url;
              }
            }
        }

    });

    // Define what happens in case of error
    XHR.addEventListener('error', function(event) {
        console.log(oppssomethingwrong);
    });

    // Set up our request
    XHR.open( 'POST', base_url+'email_optin_form_builder/submit_optin_form_data');

    // The data sent is what the user provided in the form
    XHR.send(form);
}

function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@\"]+(\.[^<>()\[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function validateFormData(form) {

	// var form_elem = new FormData;
  	var get_inputs = document.getElementById('optin_form').elements;

  	for (item of get_inputs) {

  		var input_val = item.value;
	  	var input_type = item.type;
	  	var input_name = item.name;
	    var input_required = item.dataset.required;

	    if(input_required === 'required' && ! input_val) {
  			var input_text = item.previousSibling 
  				? item.previousSibling.innerHTML.replace('*','') + ' '+cannotbeempty
  				:thisfieldcannotbeempty;

        iziToast.error({
          title: '',
          message: input_text,
          position: 'bottomLeft'
        });

	    	return false;
	    }

	    if(input_required === 'required' && input_type === 'email') {
	    	if(!validateEmail(input_val)) {

          iziToast.error({
            title: '',
            message: providevalidemail,
            position: 'bottomLeft'
          });
	    		return false;
	    	}
	    	
	    }

	    if(input_required === 'required' && input_type === 'tel') {

	    	var phoneno = new Number(input_val);
	    	if(phoneno.toString() === 'NaN') {

          iziToast.error({
            title: '',
            message: phonemustbenumeric,
            position: 'bottomLeft'
          });

	    		return false;
	    	}	     		    
	    }

	    if(input_type === 'checkbox') {

	    	var checkbox_explore = document.getElementById('email_optin_form_checkbox');
	    	var isChecked = document.getElementById('email_optin_form_checkbox').checked;

	    	if(!isChecked) {

          iziToast.error({
            title: '',
            message: checkthecheckbox,
            position: 'bottomLeft'
          });

	    		return false;
	    	}

	    }

	    form.append(input_name,input_val);

  	}
}