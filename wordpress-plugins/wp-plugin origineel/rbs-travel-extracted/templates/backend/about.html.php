<?php
$readme_contents = file_get_contents(RBS_TRAVEL_PLUGIN_PATH . 'readme.txt');

?>

<div class="wrap">
    <h1><?php _e('About RRP System', 'rbs-travel'); ?></h1>

    <h2><?php _e('Description', 'rbs-travel');?></h2>
    <div class="about-plugin about-plugin--description">
        <p style="font-size: 14px;">Met onze nieuwe reisplanner-plugin (in ontwikkeling) voor WordPress creëer je in enkele klikken inspirerende pagina’s vol reisideeën en routes, volledig op maat voor jouw websitebezoekers. Deze handige plugin is speciaal ontwikkeld om reiservaringen moeiteloos te presenteren, waardoor bezoekers direct een goed overzicht krijgen van mogelijke rondreizen.
        Dus je maakt ene reis idee in de Rondreis Planner, en met 2 klikken staat hij op je wordpress website!</p>
    </div>

    <hr>

    <h2>README.TXT</h2>
    <div class="about-plugin about-plugin--readme">
        <pre style="padding: 15px; background-color: #ffffff;"><?php echo esc_html($readme_contents); ?></pre>
    </div>
</div>