<?php
/**
 * Template Name: 🔥 TEST Template
 */

get_header();
?>

<div style="padding: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center;">
    <h1 style="font-size: 3rem; margin-bottom: 1rem;">🎉 HET WERKT!</h1>
    <p style="font-size: 1.5rem;">Als je dit ziet, accepteert je theme custom templates!</p>
    <p style="font-size: 1.2rem; margin-top: 2rem;">Theme: <?php echo wp_get_theme()->get('Name'); ?></p>
</div>

<?php
get_footer();
