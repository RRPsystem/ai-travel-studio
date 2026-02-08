<?php
$meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields();
?>



<div id="rbstravel-travel-idea-details">
    <p>
        <label for="min_persons" class="rbstravel-side-metabox-label"><?php _e('Minimum Persons', 'rbs-travel'); ?>:</label>
        <input type="number" id="min_persons" name="min_persons" value="<?php echo esc_attr($meta_fields['travel_min_persons']); ?>" min="1" />
    </p>

    <p>
        <label for="max_persons" class="rbstravel-side-metabox-label"><?php _e('Maximum Persons', 'rbs-travel'); ?>:</label>
        <input type="number" id="max_persons" name="max_persons" value="<?php echo esc_attr($meta_fields['travel_max_persons']); ?>" min="1" />
    </p>
    
    <p>
        <label for="num_nights" class="rbstravel-side-metabox-label"><?php _e('Number of Nights', 'rbs-travel'); ?>:</label>
        <input type="number" id="num_nights" name="num_nights" value="<?php echo esc_attr($meta_fields['travel_number_of_nights']); ?>" min="1" />
    </p>

</div>
