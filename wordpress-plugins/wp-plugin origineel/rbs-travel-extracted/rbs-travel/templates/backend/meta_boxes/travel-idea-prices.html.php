<?php
$meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields();
?>

<div id="rbstravel-travel-idea-prices">
    <p>
        <label for="price_per_person" class="rbstravel-side-metabox-label"><?php _e('Price per Person', 'rbs-travel'); ?>:</label>
        <input type="number" id="price_per_person" name="price_per_person" value="<?php echo esc_attr($meta_fields['travel_price_per_person']); ?>" step="0.01" min="0" />
    </p>

    <p>
        <label for="total_price" class="rbstravel-side-metabox-label"><?php _e('Total Price', 'rbs-travel'); ?>:</label>
        <input type="number" id="total_price" name="total_price" value="<?php echo esc_attr($meta_fields['travel_price_total']); ?>" step="0.01" min="0" /> 
    </p>

</div>
