<div class="idealist_wrapper">

    <div class="column_left">Left</div>
    <div class="column_right">Right</div>


</div>

<?php 

$template = 'frontend' . DIRECTORY_SEPARATOR . 'idealistfilter';
$args = array(
    'add_wrapper' => false,                
);
echo rbstravel_template_loader($template, $vars, null, $args);  
?>      




<style>


    .idealist_wrapper {
        display: flex;
        flex-direction: row;
        background: red;
      
    }

    .column_left {
        background: green;
        width: 50%;
    }

    @media only screen and (min-width: 601px) {

    }        

    @media only screen and (max-width: 600px) {
        body {
            color: red !important;
        }
    }    
</style>