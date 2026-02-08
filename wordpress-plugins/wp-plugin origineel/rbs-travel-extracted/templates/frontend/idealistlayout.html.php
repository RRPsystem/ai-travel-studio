
<!-- idealistlayout.html -->
<?php
if (!defined('ABSPATH')) {
    exit;
}
global $travel_meta_fields;

?>

<?php rbstravel_get_header(); ?>
<div id="rbstravel-content">
    <div class="overview-screen">
        <div class="overview-wrapper">

            <div class="content-left">
                
                <div class="rbs-travel-overview">
                    <div style="background: url('<?php echo RBS_TRAVEL_PLUGIN_URL_ASSETS;?>/images/globe.png');>" class="earth-americas">
                        <!-- <img class="primary" src="<?php //echo RBS_TRAVEL_PLUGIN_URL_ASSETS;?>/images/globe.png";/> -->
                    </div>
                    <div class="text-wrapper"></div> 

                    <div class="check"></div>
                    <?php $template = 'frontend' . DIRECTORY_SEPARATOR . 'idealist-filter';
                    $args = array(
                        'add_wrapper' => false,                
                    );
                    echo rbstravel_template_loader($template, $vars, null, $args);  
                ?>  
                </div>
            </div>

                    
            
            <div class="content-right">
                <!-- Favorites Toggle Button -->
                <div class="rbs-travel-favorites-toggle">
                    <button id="rbs-show-favorites" class="rbs-favorites-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="#E91E63"/>
                        </svg>
                        <span class="btn-text">Toon Favorieten</span>
                        <span class="favorites-count">(0)</span>
                    </button>
                    <button id="rbs-show-all" class="rbs-favorites-btn" style="display:none;">
                        <span class="btn-text">Toon Alle Reizen</span>
                    </button>
                </div>
                
                <div id="rbstravel-overview-items">
                    <?php 
                    $counter = 0;
                    foreach ($vars['ideas'] as $idea) : ?>   
                <?php //echo '<pre>' . print_r($idea['idea']['travel_price_per_person'], true) . '</pre>';die('xx');?>    
                    <?php //echo get_permalink($idea['post']->ID);die('xx');?>    
                
                    <?php if($counter === 2) : ?>
                        <!-- <img class="brand-mark" src="https://c.animaapp.com/ZbVsYiqQ/img/brand-mark.svg" /> -->
                    
                        <!-- <div style="opacity: 0.5;height: 400px;width: 100%;background: url('<?php //echo RBS_TRAVEL_PLUGIN_URL . 'assets/images/brandmark.jpg';?>'); background-size: cover;background-position: right -1px;background-repeat: no-repeat;"></div>
                        <img class="img" src="<?php //echo RBS_TRAVEL_PLUGIN_URL . 'assets/images/tour_planning.svg';?>" /> -->


                    <?php endif; ?>
                    
                    <?php echo rbstravel_template_loader('frontend/idea-list-layout-single', array('idea' => $idea)); ?>
                    <?php $counter ++;endforeach; ?>
                
                    <?php 
                    $template = 'frontend' . DIRECTORY_SEPARATOR . 'idealist-overview';
                    $args = array(
                        'add_wrapper' => false,                
                    );
                    // echo rbstravel_template_loader($template, $vars, null, $args);  
                    ?>   
                </div>
                    
            </div>      




        </div>
    </div>

</div>
