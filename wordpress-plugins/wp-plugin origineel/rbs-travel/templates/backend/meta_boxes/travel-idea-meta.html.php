<?php
/*** TODO: Refine $fields / combined with 'RBS_TRAVEL_Meta::GetMetaKeys()' ***/
//$fields = array(
//    'travel_id' => array('type' => 'input', 'readonly' => true, 'label' => __('ID', 'rbs-travel')),
//    'travel_user' => array('type' => 'input', 'readonly' => true, 'label' => __('User', 'rbs-travel')),
//    'travel_email' => array('type' => 'input', 'readonly' => true, 'label' => __('Email', 'rbs-travel')),
//    'travel_large_title' => array('type' => 'input', 'readonly' => true, 'label' => __('Large Title', 'rbs-travel')),
//    'travel_remarks' => array('type' => 'textarea', 'readonly' => true, 'label' => __('Remarks', 'rbs-travel')),
//    'travel_creation_date' => array('type' => 'date', 'readonly' => true, 'label' => __('Creation Date', 'rbs-travel')),
//    'travel_departure_date' => array('type' => 'date', 'readonly' => true, 'label' => __('Departure Date', 'rbs-travel')),
//    
////    'travel_themes' => array('type' => 'input', 'readonly' => true, 'label' => __('Themes', 'rbs-travel')),
//    
//    'travel_price_per_person' => array('type' => 'input', 'readonly' => true, 'label' => __('Price per Person', 'rbs-travel')),
//    'travel_total_price' => array('type' => 'input', 'readonly' => true, 'label' => __('Total Price', 'rbs-travel')),
//    'travel_destinations' => array('type' => 'textarea', 'readonly' => true, 'label' => __('Destinations', 'rbs-travel')),
//    
////    'itinerary' => array('type' => 'input', 'readonly' => true, 'label' => __('Travel ID', 'rbs-travel')),
////    'userB2c' => array('type' => 'input', 'readonly' => true, 'label' => __('Travel ID', 'rbs-travel')),
////    'counters' => array('type' => 'input', 'readonly' => true, 'label' => __('Travel ID', 'rbs-travel')),  
//);


$meta_field_types = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFieldTypes();


/*
            "id": 6081870,
            "user": "Alex",
            "email": "alex@reisplanner.online",
            "title": "Combinatiereis Napels, Capri en Amalfi",
            "largeTitle": "Combinatiereis Napels, Capri en Amalfi",
            "description": "<p>Ontdek de betoverende schoonheid van Zuid-Italië tijdens onze onvergetelijke rondreis door Napels, Capri en Amalfi! Verlies jezelf in de eeuwenoude straten van Napels, waar geschiedenis en cultuur samensmelten. Vaar naar het schilderachtige eiland Capri en laat je betoveren door de azuurblauwe zee en majestueuze kliffen.</p><p><br></p><p>Geniet van adembenemende uitzichten terwijl we langs de beroemde Amalfikust reizen, met zijn pittoreske dorpjes die tegen de rotsachtige kust zijn aangebouwd. Proef heerlijke lokale gerechten, bereid met de beste ingrediënten uit de Middellandse Zee.</p><p><br></p><p>Bezoek historische bezienswaardigheden, bewonder prachtige architectuur en maak onvergetelijke herinneringen op deze onvergelijkbare reis. Ons deskundige team zorgt voor een zorgeloze ervaring, zodat je optimaal kunt genieten van deze onvergetelijke bestemmingen. Boek nu en beleef de magie van Napels, Capri en Amalfi!</p>",
            "remarks": "<p>Dit reisvoorstel is speciaal voor u op maat gemaakt, ovv actuele beschikbaarheid, tussentijdse prijswijzigingen en typefouten.&nbsp;</p><p><br></p>",
            "imageUrl": "https://tr2storage.blob.core.windows.net/imagenes/o0f9z4HkDDrvW6eRUDfMuMsl.jpg",
            "creationDate": "2023-07-26",
            "departureDate": "2023-10-18",
            "ideaUrl": "https://online.travelcompositor.com/idea/brochure.xhtml?id=6081870&title=combinatiereis-napels-capri-en-amalfi&lang=EN&agency=rondreis-planner",
            "themes": [
                "From Italy",
                "Combinations"
            ],
            "pricePerPerson": {
                "amount": 1615.38,
                "currency": "EUR"
            },
            "totalPrice": {
                "amount": 3230.76,
                "currency": "EUR"
            },
            "destinations": [
                {
                    "code": "NAP-1",
                    "name": "Naples"
                },
                {
                    "code": "PRJ",
                    "name": "Capri"
                },
                {
                    "code": "RNP-5",
                    "name": "Amalfi Coast"
                }
            ],
            "itinerary": [],
            "userB2c": false,
            "counters": {
                "adults": 2,
                "children": 0,
                "destinations": 3,
                "closedTours": 0,
                "hotelNights": 7,
                "transports": 4,
                "hotels": 3,
                "cars": 1,
                "tickets": 0,
                "transfers": 4,
                "insurances": 0,
                "manuals": 0,
                "cruises": 0,
                "rideHailings": 0
            }
        },
*/
?>


<div id="rbstravel-travel-idea-meta-fields">
    
    <table class="wp-list-table widefat fixed striped">
    <?php foreach( RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaKeys() as $meta_key => $meta_label): ?>
    <tr class="rbstravel-idea-meta-field-row">
	<th class="rbstravel-idea-meta-field_label">
	    <?php echo $meta_label; ?>  
	</th>
	<td class="rbstravel-idea-meta-field_value">
	    <?php if (isset($meta_field_types[$meta_key])): ?>
		<?php $meta_value = get_post_meta($post->ID, $meta_key, true) ;?>
	    
		<?php if ($meta_field_types[$meta_key]['type'] === 'input'): ?>
            <?php if (is_array($meta_value)): ?>
                <?php foreach($meta_value as $meta_value_key => $meta_value_value): ?>
                    <label style="display: block; margin-bottom: 5px;">
                        <span style="width: 80px; display: inline-block;"><?php echo $meta_value_key;?></span>
                        <input type="text" name="rbstravel_meta[<?php echo $meta_value_key;?>]" readonly="" value="<?php echo $meta_value_value; ?>" />
                    </label>
                <?php endforeach; ?>
            <?php else: ?>
                <input type="text" name="rbstravel_meta[<?php echo $meta_key;?>]" readonly="" value="<?php echo $meta_value; ?>" />
            <?php endif;?>
		<?php elseif ($meta_field_types[$meta_key]['type'] === 'textarea'): ?>
		    <textarea name="rbstravel_meta[<?php echo $meta_key;?>]" readonly><?php echo print_r($meta_value, true); ?></textarea>
		    <?php else: ?>
		    <?php echo 'TEMP - Unknown_field_type: ' . $meta_value;?>
		<?php endif; ?>
		
	    <?php endif; ?>	
		    
	    <p style="font-size: 0.8em; color: orange; font-style: italic;"><?php echo $meta_key; ?> </p>    
	</td>
    </tr>    
    <?php endforeach; ?>

<?php /*
    <tr class="rbstravel-idea-meta-field-row">
        <th class="rbstravel-idea-meta-field_label">
            <?php echo '__TEMP_Contact_Form'; ?>  
        </th>
        <td class="rbstravel-idea-meta-field_value">
            <input type="text" name="rbstravel_meta[contact_form]" value="null" />
        </td>
    </tr>
*/ ?>    
    </table>

</div>
