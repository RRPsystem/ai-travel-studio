<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div data-postid="<?php echo $idea['post']->ID; ?>" class="idealist_overview_item">
    <div class="rbs-travel-ovr-left">
        <img class="image" src="<?php echo $idea['idea']['travel_image_url']; ?>">
    </div>

    <div class="rbs-travel-ovr">
        <div class="ovr-middle-title">
            <div class="ovr-middle-title-2"><?php echo $idea['post']->post_title;?></div>
            <img class="ovr-middle-icon" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-middle-icon.svg" />
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
        <p class="ovr-middle">
        <?php echo $idea['post']->post_content; ?>

        </p>
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
        <?php rbstravel_get_map_image($idea['post']->ID); ?>
        <div class="ovr-right-bottom">
        <div class="ovr-right-cleft">
            <div class="ovr-right-price-text">EURO</div>
            <div class="ovr-right-price"><?php echo $idea['idea']['travel_total_price']['amount']; ?></div>
        </div>

        
        </div>
        <div class="ovr-right-bottom--no-style">

        <div class="ovr-right-cright-no-style">
            <div class="ovr-right-button">            
            <a href="<?php echo get_permalink($idea['post']->ID);?>  "><span>bekijken</span></a>   
            <img class="ovr-right-icon" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-right-icon.svg" />
            </div>
        </div>
        </div>                  
    </div>
</div>