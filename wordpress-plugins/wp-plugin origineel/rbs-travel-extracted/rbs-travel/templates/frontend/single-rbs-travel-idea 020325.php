<!-- single-rbs-travel-idea.php
<div class="work-sans-light">work-sans-light</div>   
<div style="width: 300px;" class="work-sans-regular">work-sans-regular Diese Mietwagenreise führt Sie zu den Höhepunkten zwischen Porto und Lissabon. Diese Region bietet so viel: die historischen Altstädte und Bauwerke in Porto, Coimbra, Obidos, Lissabon und Sintra sowie die malerischen Landschaften im Duoro Tal mit den zahlreichen Weingütern. Weinverkostungen, Mittagessen in lokalen Restaurants sowie ein Fado Abend in Lissabon runden das Erlebnis Portugal perfekt ab.</div> 
<div class="work-sans-dark">work-sans-dark</div>   -->
  
<?php


if (!defined('ABSPATH')) {
    exit;
}
global $travel_meta_fields;
$travel_meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields(); 
$vars = array();
$vars['travel_meta_fields'] = $travel_meta_fields;

?>
<?php //echo $vars['travel_meta_fields']['travel_large_title'];?>
<?php //echo '<pre>' . print_r($vars['travel_meta_fields']['travel_price_per_person']['amount'], true) . '</pre>'; ?>
<?php //echo '<pre>' . print_r($vars['travel_meta_fields']['travel_counters']['hotelNights'], true) . '</pre>'; ?>
<?php //echo '<pre>' . print_r($vars['travel_meta_fields']['travel_counters']['hotelNights'], true) . '</pre>'; ?>
<?php //e/cho '<pre>' . print_r($vars['travel_meta_fields'], true) . '</pre>'; ?>

<?php
$url = RBS_TRAVEL_PLUGIN_URL . 'assets/images/location_share.svg';
?>




<?php rbstravel_get_header(); ?>
<!-- <div id="slider-container-1" class="slider-container" item-display-d="1" item-display-t="1" item-display-m="1">
        <div id="slider-width-1" class="slider-width">
        <?php //foreach($travel_meta_fields['travel_destinations'] as $destination): ?>  
          <?php //foreach($destination['imageUrls'] as $image_url): ?> 
            <div style="background-size: cover;background-position: center;background-repeat: no-repeat;background: url('<?php //echo $image_url; ?>')" class="item item-1">slide 1</div>
            <?php //endforeach; ?>     
        <?php //endforeach; ?> 

        </div>
    </div>
    <button id="nextbutton-1">next 1</button>
    <button id="prevbutton-1">prev 1</button> -->




    <?php //echo '<pre>' . print_r($vars, true) . '</pre>'; ?>
<div id="rbstravel-content">
    <div class="single-screen">
      <div class="content-left">
          <div class="header-full-width">
            
              <div class="hero-content single-screen-container">
                  <div class="hero-title single-screen-container-inside">
                    <h1><?php echo $travel_meta_fields['travel_destinations'][0]['name']; ?></h1>
                    <h2><?php echo $vars['travel_meta_fields']['travel_large_title'];?></h2>                    
                  </div>
                </div>                            
          </div>
          <div class="single-screen-container">
            <div class="breadcrumbs single-screen-container-inside">
              breadcrumbs?
            </div>  

          </div>

          <div class="single-screen-container">
            <div class="breadcrumbs single-screen-container-inside">              
            <h2>Mietwagenreise</h2>
            <h3>11 Tage Hotelklasse wählbarHotelklasse wählbar </h3>
            Kapstadt & Kap-HalbinselStellenbosch & WinelandsKleine Karoo & OudtshoornGarden RouteAddo Elephant ParkPort Elizabeth
            </div>  

          </div>  
          
          <div class="destination-slider">
          <?php foreach ($destination['imageUrls'] as $destination_img_url) : ?>
            <div style="background-repeat: no-repeat; background: url('<?php echo $destination_img_url; ?>')" class=""></div>
          <?php endforeach; ?>
          </div>


        

      </div>
      <div class="content-right">right</div>
    </div>        

</div>
    <div class="content-wrapper">
          <div style="background: url('<?php echo $vars['travel_meta_fields']['travel_image_url'];?>');width: 100%;min-height: 100vh;background-repeat: no-repeat;background-size: 100%;margin: 0 auto;" class="content-wrapper">
            <div class="hero-content">
              <div class="hero-title">
                <h1><?php echo $travel_meta_fields['travel_destinations'][0]['name']; ?></h1>
                <h2><?php echo $vars['travel_meta_fields']['travel_large_title'];?></h2>
                
              </div>
            </div>
          </div>  
    </div>
      

    <div class="content-wrapper">
          <div class="rbs-travel-container">
            <div class="content-left">       
              <div class="content-left-title"><p><?php echo $vars['travel_meta_fields']['travel_large_title'];?></p></div>
              <div id="rbs-tablink-wrapper_1" class="rbs-tablink-wrapper">
                <div class="rbsd-tabmenu">
                  <div class="rbsd-tab rbs-tablink tl1 rbs-activetablink">
                    <img class="img" src="<?php echo RBS_TRAVEL_PLUGIN_URL . 'assets/images/information.svg';?>" />
                    <p class="rbsd-tabtext">
                      <span class="span">&nbsp;</span>
                      <span class="text-wrapper-2">Information</span>
                    </p>
                  </div>
                  <div class="rbsd-tab rbs-tablink tl2 rbs-activetablink">              
                    <img class="img" src="<?php echo RBS_TRAVEL_PLUGIN_URL . 'assets/images/tour_planning.svg';?>" />
                    <p class="rbsd-tabtext">
                      <span class="span">&nbsp;</span>
                      <span class="text-wrapper-2">Tourplanning</span>
                    </p>
                  </div>
                  <div class="rbsd-tab rbs-tablink tl3 rbs-activetablink">
                    <img class="img" src="<?php echo RBS_TRAVEL_PLUGIN_URL . 'assets/images/location_share.svg';?>" />
                    <div class="text-wrapper-3">Location share</div>
                  </div>
                  <div class="rbsd-tab rbs-tablink tl4 rbs-activetablink">
                    <img class="img" src="<?php echo RBS_TRAVEL_PLUGIN_URL . 'assets/images/shot_gallery.svg';?>" />
                    <p class="p">
                      <span class="text-wrapper-4">&nbsp;</span>
                      <span class="text-wrapper-2">Prijzen</span>
                      <span class="span">&nbsp;&nbsp;</span>
        
                    </p>
                  </div>
                </div>


              </div>  

              <div id="rbs-tabcontent-wrapper_1" class="rbs-tabcontent-wrapper">
            
                  <div class="tl1 rbs-tabcontentitem rbs-activetabcontent">
                  <div class="basic-tab-content">
                  <?php echo the_content(); ?>
                  </div>
                  
                  </div>    
                  <div class="tl2 rbs-tabcontentitem rbs-inactivetabcontent">
                  <div class="days-column">  
                  <?php foreach($travel_meta_fields['travel_destinations'] as $destination): ?>          
                    <div class="rbsd-day-row">
                      <div class="rbsd-day-date">

                      <?php //echo '<pre>' . print_r($destination) . '</pre>' ; ?>
                        <div class="text-wrapper-5"><?php printf(__('Van dag: %s - Tot dag: %s', 'rbs-travel'), $destination['fromDay'], $destination['toDay']); ?></div>
                        <img class="rbsd-day-open" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-day-open.svg" />
                      </div>

                      <?php foreach($travel_meta_fields['travel_transports'] as $travel_transport): ?>
                        <?php if ($destination['fromDay'] === $travel_transport['day']): ?>                
                          <div class="rbsd-day-contentrow">
                            <?php //echo '<pre>' . print_r($travel_transport); ?>
                            <p class="rbsd-day-crtitle">Vluchtgegevens<?php //echo $travel_transport['transportType']; ?></p>
                            <div class="rbsd-day-crbottom">
                              <img class="rbsd-day-crbottom-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-day-crbottom-img.png" />
                              <div class="rbsd-day-crbottom-3">
                                <div class="text-wrapper-6"><?php echo $travel_transport['originDestinationCode']; ?> <?php echo $travel_transport['company']; ?> <?php echo $travel_transport['transportNumber']; ?></div>
                                <div class="element-orange"><?php echo $travel_transport['departureTime']; ?> <?php echo $travel_transport['originDestinationCode']; ?><br /><?php echo $travel_transport['arrivalTime']; ?> <?php echo $travel_transport['targetDestinationCode']; ?></div>

                              </div>
                            </div>
                          </div>
                      <?php endif; ?>
                      <?php endforeach; ?> 


                      <?php foreach($travel_meta_fields['travel_cars'] as $travel_car): ?>
                      <?php if ($destination['fromDay'] === $travel_car['pickupDay']): ?>  
                        <div class="rbsd-day-contentrow-2">
                          <div class="rbsd-day-crtitle">Auto verhuur</div>
                          <div class="rbsd-day-crbottom">
                            <img class="rbsd-day-crbottom-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-day-crbottom-img-1.png" />
                            <div class="rbsd-day-crbottom-5">
                              <div class="text-wrapper-6"><?php echo $travel_car['product'];?></div>
                              <p class=" element-orange">
                              <?php
                              echo '<span class="strong-style">Pickup:</span> ' . $travel_car['pickupDate'] . ' ' . $travel_car['pickupTime'] . ', ' . $travel_car['pickupLocation'] . '</br>';
                              echo '<span class="strong-style">Dropoff:</span> ' . $travel_car['dropoffDate'] . ' ' . $travel_car['dropoffTime'] . ', ' . $travel_car['dropoffLocation'] . '</br>';
                              ?>
                              </p>
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>
                      <?php endforeach; ?> 
                      <?php //echo '<pre>' . print_r($destination['imageUrls'], true) . '</pre>';?>  
                      <div class="rbsd-day-contentrow-3">

                        <div class="rbsd-day-crtitle">Over: <?php echo $destination['name'];?></div>
                        <div class="frame">
                          <div class="frame-2">
                          
                            <p class="text-wrapper-8"><?php echo strip_tags($destination['description']);?></p>
                            <div id="rbs-modal-1" class="rbs-modal-wrapper">

                            <div class="image-gallery">
                                <div style="background: url('<?php echo $destination['imageUrls'][0]; ?>')" class="rbs-modal-open bgsettings gallery-item"></div>
                                <div style="background: url('<?php echo $destination['imageUrls'][1]; ?>')" class="rbs-modal-openbgsettings gallery-item"></div>
                              </div>

                              <div class="image-gallery">
                                <div style="background: url('<?php echo $destination['imageUrls'][2]; ?>')" class="rbs-modal-open bgsettings gallery-item"></div>
                                <div style="background: url('<?php echo $destination['imageUrls'][3]; ?>')" class="rbs-modal-open bgsettings gallery-item"></div>
                              </div>  
                              
                              
                              <!-- <div class="rbsd-dest-images-row">
                                <img class="rbs-modal-open rbs-slider-item img-2" src="<?php //echo $destination['imageUrls'][0]; ?>" />
                                <img class="rbs-modal-open rbs-slider-item rbsd-dest-image" src="<?php echo $destination['imageUrls'][1]; ?>" />
                              </div>
                              <div class="rbsd-dest-images-row">
                                <img class="rbs-modal-open rbs-slider-item img-2" src="<?php //echo $destination['imageUrls'][2]; ?>" />
                                <img class="rbs-modal-open rbs-slider-item rbsd-dest-image" src="<?php echo $destination['imageUrls'][3]; ?>" />
                              </div>                           -->
                                  <div class="rbs-modal">
                                  <div class="rbs-modal-header">
                                  <div class="rbs-modal-title">Destination Images</div>
                                  <button class="rbs-modal-close">&times;</button>
                                  </div>
                                  <div class="rbs-modal-body">
                                  <div class="rbs-slider-wrapper">
                                      <div class="rbs-slider-container" item-display-d="1" item-display-t="1" item-display-m="1">
                                          <div class="rbs-slider-width">
                                              <?php foreach ($destination['imageUrls'] as $destination_img_url) : ?>
                                              <div style="background-repeat: no-repeat; background: url('<?php echo $destination_img_url; ?>')" class="bgsettings rbs-slider-item item-1"></div>
                                              <?php endforeach; ?>

                                          </div>
                                      </div>
                                      <button class="rbs-slider-next">next 1</button>
                                      <button class="rbs-slider-prev">prev 1</button>
                                  </div>
                                  </div>
                                  </div>
                            </div>

                          </div>
                        </div>                    
                      
                      </div>

                      <?php foreach($travel_meta_fields['travel_hotels'] as $travel_hotel): ?>

                      <?php //echo '<pre>' . print_r($travel_hotel['hotelData'], true) . '</pre>';?>  

                      
                      
                      <?php if ($destination['fromDay'] === $travel_hotel['day']): ?>                
                        <div class="rbsd-day-contentrow-4">
                          <div class="rbsd-day-crtitle">Geselecteerd hotel</div>
                          <div class="rbsd-day-crbottom-6">
                            <img class="rbsd-day-crbottom-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-day-crbottom-img-2.png" />
                            <div class="rbsd-day-crbottom-5">
                              <div class="text-wrapper-7"><?php echo $travel_hotel['hotelData']['name'];?></div>
                              <div class="text-wrapper-8"><?php echo $travel_hotel['hotelData']['description'];?></div>

                            </div>
                          </div>
                          <div id="rbs-modal-2" class="rbs-modal-wrapper">

                              <div class="image-gallery">
                                <div style="background: url('<?php echo $travel_hotel['hotelData']['imageUrls'][0]; ?>')" class="rbs-modal-open bgsettings gallery-item"></div>
                                <div style="background: url('<?php echo $travel_hotel['hotelData']['imageUrls'][1]; ?>')" class="rbs-modal-openbgsettings gallery-item"></div>
                              </div>

                              <div class="image-gallery">
                                <div style="background: url('<?php echo $travel_hotel['hotelData']['imageUrls'][2]; ?>')" class="rbs-modal-open bgsettings gallery-item"></div>
                                <div style="background: url('<?php echo $travel_hotel['hotelData']['imageUrls'][3]; ?>')" class="rbs-modal-open bgsettings gallery-item"></div>
                              </div>                          


                              <div class="rbsd-day-images-row">
                                <!-- <img class="rbs-modal-open rbs-slider-item img-2" src="<?php echo $travel_hotel['hotelData']['imageUrls'][0]; ?>" />
                                <img class="rbs-modal-open rbs-slider-item rbsd-dest-image" src="<?php echo $travel_hotel['hotelData']['imageUrls'][1]; ?>" /> -->
                              </div>
                              <div class="rbsd-day-images-row">
                                <!-- <img class="rbs-modal-open rbs-slider-item img-2" src="<?php echo $travel_hotel['hotelData']['imageUrls'][3]; ?>" />
                                <img class="rbs-modal-open rbs-slider-item rbsd-dest-image" src="<?php echo $travel_hotel['hotelData']['imageUrls'][4]; ?>" /> -->
                              </div>                          
                                  <div class="rbs-modal">
                                      <div class="rbs-modal-header">
                                      <div class="rbs-modal-title">Hotel Images</div>
                                      <button class="rbs-modal-close">&times;</button>
                                      </div>
                                      <div class="rbs-modal-body">body
                                      <div class="rbs-slider-wrapper">wrapper
                                          <div class="rbs-slider-container" item-display-d="1" item-display-t="1" item-display-m="1">
                                              <div class="rbs-slider-width">
                                                  <?php foreach ($travel_hotel['hotelData']['imageUrls'] as $hotel_img_url) : ?>
                                                  <div style="background: url('<?php echo $hotel_img_url; ?>')" class="bgsettings rbs-slider-item item-1"></div>
                                                  <?php endforeach; ?>

                                              </div>
                                          </div>
                                          <div class="rbs-slider-nav">
                                            <button class="rbs-slider-next">next 1</button>
                                            <button class="rbs-slider-prev">prev 1</button>
                                          </div>

                                      </div>
                                      </div>
                                  </div>
                            </div>


                        </div>
                      <?php endif; ?>
                      <?php endforeach; ?>    
                    </div>
                  <?php endforeach; ?>  
                </div>  
                  </div>    
                  <div class="tl3 rbs-tabcontentitem rbs-inactivetabcontent">
                      <div class="basic-tab-content">
                        <section class="rbs-travel-idea__section">
                        <?php
                            $template = 'frontend' . DIRECTORY_SEPARATOR . 'single-idea' . DIRECTORY_SEPARATOR .  'fullmap';
                            echo rbstravel_template_loader($template, $vars, null, array('add_wrapper' => false));  
                        ?>
                        </section>                
                      </div>

                  </div>  
                  <div class="tl4 rbs-tabcontentitem rbs-inactivetabcontent">
                  <div class="basic-tab-content">
                    <div class="rbsd-tt-bottom">EURO <?php echo $vars['travel_meta_fields']['travel_price_per_person']; ?></div>
                  </div>
                  
                  </div>                              

              </div>






            </div>
            <div class="content-right">
              <!-- <div class="rectangle">
              <section class="rbs-travel-idea__section">
              <?php
                  //$template = 'frontend' . DIRECTORY_SEPARATOR . 'single-idea' . DIRECTORY_SEPARATOR .  'fullmap';
                  //echo rbstravel_template_loader($template, $vars, null, array('add_wrapper' => false));  
              ?>
              </section>
              </div> -->
              <div class="rbsd-idea-info">
                <div class="rbsd-idea-info-item">
                  <img class="rbsd-idea-info-icon" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-idea-info-icon.svg" />
                  <div class="rbsd-tour-type">
                    <div class="rbsd-tt-top">From</div>
                    <div class="rbsd-tt-bottom">EURO <?php echo $vars['travel_meta_fields']['travel_price_per_person']; ?></div>
                  </div>
                </div>
              
                <div class="rbsd-idea-info-item">
                  <img class="rbsd-idea-info-icon-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-idea-info-icon-1.svg" />
                  <div class="rbsd-tour-type-2">
                    <div class="rbsd-tt-top">Duration</div>
                    <div class="text-wrapper-3"><?php echo $vars['travel_meta_fields']['travel_number_of_nights']; ?> days</div>
                  </div>
                </div>
                <div class="rbsd-idea-info-item">
                  <img class="rbsd-idea-info-icon-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-idea-info-icon-2.svg" />
                  <div class="rbsd-tour-type-2">
                    <div class="rbsd-tt-top">Tour Type</div>
                    <div class="text-wrapper-3">Vliegreis</div>
                  </div>
                </div>
              </div>
              <div class="rbsd-idea-contact-form">
                <div class="rbsd-book-title">Boeken</div>
                <?php rbstravel_get_contact_form(); ?>
                <?php //echo do_shortcode('[contact-form-7 id="552711b" title="Contact form 1"]'); ?>            
                <!-- <div class="checkitem">
                  <img class="img-3" src="https://c.animaapp.com/IqJ2Y8e7/img/calendar-days.svg" />
                  <div class="text-wrapper-10">Periode</div>
                </div>
                <div class="checkitem">
                  <img class="img-3" src="https://c.animaapp.com/IqJ2Y8e7/img/user.svg" />
                  <div class="text-wrapper-10">Aantal personen</div>
                </div>
                <div class="rbsd-book-button">
                  <div class="rbsd-button-text">Verder</div>
                  <img class="img" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-button-icon.svg" />
                </div>
                <div class="check"></div> -->
              </div>
            </div>
          </div>
        </div>


<?php 
// if (WP_DEBUG) {
//     global $post;
//     $post_id = $post->ID;
//     echo '<pre>' . print_r(rbstravel_get_single_idea($post_id), true) . '</pre>'; 
// }
?>


<?php rbstravel_get_footer(); ?>
