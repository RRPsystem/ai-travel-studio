<!-- <form class="save-me" id="save-me">

	<label for="name">Name</label>
	<input type="text" name="name" id="name" value="Mike Wazowski">

	<label for="address">Address</label>
	<input type="text" name="address" id="address" value="123 Scare Avenue, Monstropolis">

	<label for="email">Email</label>
	<input type="email" name="email" id="email" value="mikew@monstersinc.com">

	<label for="hear-about-us">How did you hear about us?</label>
	<select name="hear-about-us" id="hear-about-us">
		<option value=""></option>
		<option value="google">Google</option>
		<option value="referral">Referred by a Friend</option>
		<option value="tv" selected>A TV Ad</option>
		<option value="radio">A Radio Ad</option>
	</select>
  
  	<label for="hear-about-us-multi">How did you hear about us?</label>
	<select name="hear-about-us-multi" id="hear-about-us-multi" multiple>
		<option value=""></option>
		<option value="google">Google</option>
		<option value="referral" selected>Referred by a Friend</option>
		<option value="tv" selected>A TV Ad</option>
		<option value="radio">A Radio Ad</option>
	</select>

	<label id="more">Additional thoughts?</label>
	<textarea name="more" id="more">Laughter produces more energy than screams!</textarea>

	<p><strong>Do you agree to our terms of service?</strong></p>
	<label class="label-plain">
		<input type="radio" name="tos" value="yes" checked>
		Yes
	</label>
	<label class="label-plain">
		<input type="radio" name="tos" value="no">
		No
	</label>

	<p><strong>Pick your favorite university.</strong></p>
  
  <label class="label-plain">
		<input type="checkbox" name="scare-tech">
		Scare Tech
	</label>

	<label class="label-plain">
		<input type="checkbox" name="mu" checked>
		Monster University
	</label>

	<p><button type="submit">Submit</button></p>

</form> -->
<a id="formtrigger">temp trigger</a>

<div class="idealist_filter">

    <div class="filter_item">
        <h3>Bestemming</h3>
        <ul>
            <li>Afrika</li>
            <li>Azie</li>
            <li>Europa</li>
            <li>Wereld</li>
        </ul>
    </div>   
    <div class="filter_item">
        <h3>Prijs</h3>
        <div class="demo">
            <div class="range_wrapper">
            <h4>Minimale prijs</h4>
            <input type="range" min="500" max="10000" oninput="showValMin(this.value)" onchange="showValMin(this.value)" step="100" value="500">
            <div id="valBoxMin" class="valBox">500</div>
            </div>
        </div>        

        <div class="demo">
            <div class="range_wrapper">
            <h4>Maximale prijs</h4>
            <input type="range" min="500" max="10000" oninput="showValMax(this.value)" onchange="showValMax(this.value)" step="100" value="10000">
            <div id="valBoxMax" class="valBox">10000</div>
            </div>
        </div>           
    </div> 
    <div class="filter_item">

    </div> 
</div>

<script>




    function showValMin(val) {  document.getElementById("valBoxMin").innerHTML = val};
    function showValMax(val) {  document.getElementById("valBoxMax").innerHTML = val};
</script>    


<style>
    .idealist_filter {
        display: flex;
        flex-direction: column;
        padding: 20px;
        background: #ffffff;
      
    }  

    .filter_item ul {
        list-style-type:none;
    }





    
</style>



<style>
    /**
 * Add box sizing to everything
 * @link http://www.paulirish.com/2012/box-sizing-border-box-ftw/
 */
/* line 38, src/sass/components/_normalize.scss */
*,
*:before,
*:after {
	box-sizing: border-box;
}

/**
 * Layout
 */
body {
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
	font-size: 112.5%;
	margin-left: auto;
	margin-right: auto;
	max-width: 40em;
	width: 88%;
}

/**
 * Forms
 */
[type="text"],
[type="email"],
select,
textarea {
	display: block;
	margin-bottom: 1em;
	width: 100%;
}

textarea {
	height: 8em;
}

label {
	display: block;
	font-weight: bold;
	margin-bottom: 0.25em;
}

.label-plain {
	font-weight: normal;
}
</style>


<script>

window.addEventListener("load", (event) => {
    var serializeArray = function (form) {

        // Setup our serialized data
        var serialized = [];

        // Loop through each field in the form
        for (var i = 0; i < form.elements.length; i++) {

            var field = form.elements[i];

            // Don't serialize fields without a name, submits, buttons, file and reset inputs, and disabled fields
            if (!field.name || field.disabled || field.type === 'file' || field.type === 'reset' || field.type === 'submit' || field.type === 'button') continue;

            // If a multi-select, get all selections
            if (field.type === 'select-multiple') {
                for (var n = 0; n < field.options.length; n++) {
                    if (!field.options[n].selected) continue;
                    serialized.push({
                        name: field.name,
                        value: field.options[n].value
                    });
                }
            }

            // Convert field data to a query string
            else if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
                serialized.push({
                    name: field.name,
                    value: field.value
                });
            }
        }

        return serialized;

    };

    var formtrigger = document.getElementById('formtrigger'); 
    var form = document.querySelector('#save-me');
    formtrigger.addEventListener("click", function(evt){
        evt.preventDefault();
        // var  myJsonString= JSON.stringify(arr_data);
        var postdata = serializeArray(form);
        //var postdata = JSON.stringify(form);
        console.log(postdata);
        my_arr = [];
        my_arr['name'] = 'My name';
        my_arr['address'] = 'Address';
        fetchFullCalendarInfo(JSON.stringify(postdata));        
    });


});


</script>    
