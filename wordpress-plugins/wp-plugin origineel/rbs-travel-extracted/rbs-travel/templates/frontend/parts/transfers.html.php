<?php 
/**
 * Single Travel Idea - Transfers part
 * 
 * This template can be overridden by copying it to yourtheme/rbstravel/transfers.html.php.
 * 
 * @version: 1.0.0 
 */
if (!defined('ABSPATH')) {
    exit;
}
global $travel_meta_fields;
?>

<div class="rbs-travel-idea__transfers">
<?php foreach($travel_meta_fields['travel_transfers'] as $transfer): ?>
     <div id="rbs-travel-idea__transfer-<?php echo $transfer['id'];?>" class="rbs-travel-idea__part-row rbs-travel-idea__transfer-row">
	<h3><?php printf(__('Day: %s - %s', 'rbs-travel'), $transfer['day'], $transfer['type']); ?></h3>
	<p><?php printf(__('From: %s - To: %s', 'rbs-travel'), $transfer['from']['name'], $transfer['to']['name']); ?></p>
	<p><?php printf(__('Transfer Date: %s'), $transfer['dateTime']);?></p>
	<p><?php printf(__('Transfer by: %s'), $transfer['vehicleType']);?></p>
	
	
	<img src="<?php echo $transfer['imageUrl'];?>" class="" alt="<?php echo 'Image-' . $transfer['id'] . '-' . $idx; ?>" />
	
<?php /*	
	<div class="rbs-travel-idea__transfer-gallery rbs-travel-idea__gallery">
	    <?php foreach($transfer['transferData']['imageUrls'] as $idx => $image_url): ?>
	    <a href="#" class="rbs-travel-idea__gallery-image-link">
		<img src="<?php echo $image_url;?>" class="rbs-travel-idea__gallery-image rbs-travel-idea__transfer-gallery-image" alt="<?php echo 'Image-' . $transfer['id'] . '-' . $idx; ?>" />
	    </a>
	    <?php endforeach; ?>
	</div>

	<div style="width: 100%" class="rbs-travel-idea__map rbs-travel-idea__transfer-map">
	    <iframe width="100%" height="600" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=<?php echo $destination['geolocation']['latitude'];?>,<?php echo $destination['geolocation']['longitude'];?>&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
	</div>	
	
	<a href="<?php echo $transfer['moreInfoUrl']; ?>" target="_blank"><?php _e('More information', 'rbs-travel');?></a>
*/ ?>
    </div>
<?php endforeach; ?>
</div>