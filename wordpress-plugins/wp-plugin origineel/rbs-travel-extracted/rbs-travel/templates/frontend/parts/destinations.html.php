<?php 
/**
 * Single Travel Idea - Destinations part
 * 
 * This template can be overridden by copying it to yourtheme/rbstravel/destinations.html.php.
 * 
 * @version: 1.0.0 
 */
if (!defined('ABSPATH')) {
    exit;
}
global $travel_meta_fields;
?>

<div class="rbs-travel-idea__destinations">
<?php foreach($travel_meta_fields['travel_destinations'] as $destination): ?>
    <div id="rbs-travel-idea__destination-<?php echo $destination['code'];?>" class="rbs-travel-idea__destination-row">
	<h3><?php echo $destination['country'] . ' - ' . $destination['name']; ?></h3>
	<p><?php printf(__('Van dag: %s - Tot dag: %s', 'rbs-travel'), $destination['fromDay'], $destination['toDay']); ?></p>
	<p><?php echo $destination['description'];?></p>
	<h3>Foto's van deze Bestemming</h3>
	<div class="rbs-travel-idea__gallery rbs-travel-idea__destination-gallery">
	    <?php foreach($destination['imageUrls'] as $idx => $image_url): ?>
            <a href="#" class="rbs-travel-idea__gallery-image-link">
                <img src="<?php echo $image_url;?>" class="rbs-travel-idea__gallery-image rbs-travel-idea__destination-gallery-image" alt="<?php echo 'Image-' . $destination['code'] . '-' . $idx; ?>" />
            </a>
	    <?php endforeach; ?>
	</div>
	<h3>Locatie van deze Bestemming</h3>		
	<div style="width: 100%" class="rbs-travel-idea__map rbs-travel-idea__destination-map">
        DISABLED GOOGLEMAP!!
        <?php /*
	    <iframe width="100%" height="600" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=<?php echo $destination['geolocation']['latitude'];?>,<?php echo $destination['geolocation']['longitude'];?>&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
        */ ?>
	</div>
	
    <?php if (isset($destination['moreInfoUrl']) && strlen($destination['moreInfoUrl']) !== 0): ?>
	    <a href="<?php echo $destination['moreInfoUrl']; ?>" target="_blank"><?php _e('More information', 'rbs-travel');?></a>
    <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>