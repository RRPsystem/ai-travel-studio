<?php
if (!defined('ABSPATH')) {
    exit;
}

// echo '<pre>' . print_r($idea, true) . '</pre>';

?>
<div data-postid="<?php echo $idea['post']->ID; ?>" class="idealist_overview_item">
    <div  class="rbs-travel-ovr-left">
        <div style="background: url('<?php echo $idea['idea']['travel_image_url']; ?>')" class="left-image">
            <!-- Favorite Heart -->
            <button class="rbs-travel-favorite-btn" data-post-id="<?php echo $idea['post']->ID; ?>" aria-label="Voeg toe aan favorieten">
                <svg class="heart-icon" width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="heart-fill" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="white"/>
                    <path class="heart-stroke" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="#E91E63" stroke-width="2"/>
                </svg>
            </button>
        </div>
        <!-- <img class="image" src="<?php //echo $idea['idea']['travel_image_url']; ?>"> -->
    </div>


    <div class="rbs-travel-ovr-middle">
        <div class="ovr-middle-title">
            <div class="ovr-middle-title-2"><a href="<?php echo get_permalink($idea['post']->ID);?>" class="rbs-travel-link--primary"><?php echo $idea['post']->post_title;?></a></div>
            <!-- <img class="ovr-middle-icon" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-middle-icon.svg" /> -->
        </div>
        <div class="ovr-middle-features">
        <div class="feature-item">
            <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
            <div class="text-wrapper-2">Zwembad</div>
        </div>
        <div class="feature-item">
            <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
            <div class="text-wrapper-2">Kindvriendelijk</div>
        </div>
        <div class="feature-item">
            <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
            <div class="text-wrapper-2">WIFI</div>
        </div>
        <div class="feature-item">
            <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
            <div class="text-wrapper-2">Goed bereikbaar</div>
        </div>
        </div>

        <div class="ovr-middle">
            <?php //echo apply_filters('the_content', $idea['post']->post_content); ?>
            <?php //echo $idea['post']->post_content;?>
            <?php 
                // Display post content before the "read more" tag
                $content = apply_filters('the_content', $idea['post']->post_content);
                $split_content = explode('<!--more-->', $content);
                echo wpautop($split_content[0]); // Output content before the "read more"
            ?>            
        </div>

        <!-- <div class="ovr-middle-bottom">
        <div class="ovr-middle-bb">
            <div class="ovr-mbb-text">BESCHIKBAARHEID</div>
            <img class="ovr-mbb-icon" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-mbb-icon.svg" />
        </div>
        </div> -->


    </div>
    <div class="rbs-travel-ovr-right">
        <?php /*
        <img class="ovr-right-map" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-right-map@2x.png" />
        */ ?>
        <div class="ovr-right-top">
            <div style="background: url('<?php echo rbstravel_get_map_image($idea['post']->ID, false);?>');" class="ovr-right-map">
                
            <?php //rbstravel_get_map_image($idea['post']->ID); ?>
            </div>
        </div>


        
        <div class="ovr-right-bottom">
            <div class="ovr-right-cleft">
                <div class="ovr-right-price"><?php echo $idea['idea']['travel_price_per_person']; ?></div>
                <div class="ovr-right-price-text">EURO (per persoon)</div>
                
            </div>
            <div class="ovr-right-cright">
                <div class="ovr-right-button">   
                    <a href="<?php echo get_permalink($idea['post']->ID);?>  "><span>bekijken</span></a>   
                
                </div>
            </div>

        </div>
 

        <div class="ovr-right-cright-no-style">

        </div>
                     
    </div>

   

</div>