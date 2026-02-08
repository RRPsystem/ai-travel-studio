<?php 
/**
 * Single Travel Idea - Hotels part
 * 
 * This template can be overridden by copying it to yourtheme/rbstravel/hotels.html.php.
 * 
 * @version: 1.0.0 
 */
if (!defined('ABSPATH')) {
    exit;
}
global $travel_meta_fields;
?>

<div class="rbs-travel-idea__hotels">
<?php foreach($travel_meta_fields['travel_hotels'] as $hotel): ?>
     <div id="rbs-travel-idea__hotel-<?php echo $hotel['id'];?>" class="rbs-travel-idea__part-row rbs-travel-idea__hotel-row">
	<h3><?php echo $hotel['hotelData']['name']; ?></h3>
	<p><?php printf(__('Check-in: %s - Check-out: %s', 'rbs-travel'), $hotel['checkInDate'], $hotel['checkOutDate']); ?></p>
	<p><?php echo $hotel['hotelData']['description'];?></p>
	
    <div class="slider-wrapper">
	<h3>Foto's van deze accommodatie</h3>
        <div class="slider-container" item-display-d="3" item-display-t="3" item-display-m="1">
			
            <div class="slider-width">
				<?php foreach($hotel['hotelData']['imageUrls'] as $idx => $image_url): ?>
			
					<div style="height: 400px;background-image: url(<?php echo $image_url;?>);background-size: cover;background-repeat: no-repeat;background-position: center;" class="item">
						&nbsp;
					</div>	
				<?php endforeach; ?>				

            </div>
    
        </div>
        <div class="slider-nav">
            <button class="slider-button-next">Volgende</button>
            <button class="slider-button-prev">Vorige</button>   
        </div>
    </div>  
	
	
	<!-- <div class="rbs-travel-idea__hotel-gallery rbs-travel-idea__gallery">
	    <?php foreach($hotel['hotelData']['imageUrls'] as $idx => $image_url): ?>
	    <a href="#" class="rbs-travel-idea__gallery-image-link">
		<img src="<?php echo $image_url;?>" class="rbs-travel-idea__gallery-image rbs-travel-idea__hotel-gallery-image" alt="<?php echo 'Image-' . $hotel['id'] . '-' . $idx; ?>" />
	    </a>
	    <?php endforeach; ?>
	</div> -->
	<h3>Locatie van deze accommodatie</h3>
	<div style="width: 100%" class="rbs-travel-idea__map rbs-travel-idea__hotel-map">
        DISABLED GOOGLEMAP!!
        <?php /*
	    <iframe width="100%" height="600" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=<?php echo $destination['geolocation']['latitude'];?>,<?php echo $destination['geolocation']['longitude'];?>&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
        */ ?>
	</div>	
	
    <?php if (isset($destination['moreInfoUrl']) && strlen($destination['moreInfoUrl']) !== 0): ?>
	    <a href="<?php echo $hotel['moreInfoUrl']; ?>" target="_blank"><?php _e('More information', 'rbs-travel');?></a>
    <?php endif;?>

    </div>
<?php endforeach; ?>

</div>