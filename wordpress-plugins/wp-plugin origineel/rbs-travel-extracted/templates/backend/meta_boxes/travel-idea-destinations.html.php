<?php
$travel_destinations = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields(null, array('travel_destinations'));

// echo '<pre>' . print_r($travel_destinations, true) . '</pre>';
// return;
?>


<div id="rbstravel-travel-idea-destinations">
    
    <table class="wp-list-table widefat fixed striped">
    <?php foreach($travel_destinations as $destination): ?>
        <tr class="rbstravel-idea-meta-field-row">
            <th class="rbstravel-idea-meta-field_label">
                <?php _e('Name', 'rbs-travel'); ?>  
            </th>

            <td class="rbstravel-idea-meta-field_value">
                <input type="text" name="rbstravel_destination[name]" value="<?php echo $destination['name']; ?>" />
            </td>
        </tr>   
        
        <tr class="rbstravel-idea-meta-field-row">
            <th class="rbstravel-idea-meta-field_label">
                <?php _e('Code', 'rbs-travel'); ?>  
            </th>

            <td class="rbstravel-idea-meta-field_value">
                <input type="text" name="rbstravel_destination[code]" value="<?php echo $destination['code']; ?>" />
            </td>
        </tr>      
        
        <tr class="rbstravel-idea-meta-field-row">
            <th class="rbstravel-idea-meta-field_label">
                <?php _e('Country', 'rbs-travel'); ?>  
            </th>

            <td class="rbstravel-idea-meta-field_value">
                <input type="text" name="rbstravel_destination[country]" value="<?php echo $destination['country']; ?>" />
            </td>
        </tr>    

        <tr class="rbstravel-idea-meta-field-row">
            <th class="rbstravel-idea-meta-field_label">
                <?php _e('Description', 'rbs-travel'); ?>  
            </th>

            <td class="rbstravel-idea-meta-field_value">
                <textarea name="rbstravel_destination[destination>]"><?php echo $destination['description']; ?></textarea>
            </td>
        </tr>        
        
        <tr class="rbstravel-idea-meta-field-row">
            <th colspan="2" class="rbstravel-idea-meta-field_label"><hr></th>
        </tr>        
    <?php endforeach; ?>
    </table>

</div>

<?php /*
<input type="text" name="rbstravel_meta[<?php echo $meta_key;?>]" readonly="" value="<?php echo $meta_value; ?>" />
                        <textarea name="rbstravel_meta[<?php echo $meta_key;?>]" readonly><?php echo print_r($meta_value, true); ?></textarea>
                        */ ?>
