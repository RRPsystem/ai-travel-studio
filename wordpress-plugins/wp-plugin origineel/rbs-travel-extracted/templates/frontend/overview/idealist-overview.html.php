
sss
<div class="idealist_overview">
    <div class="idea_list_items">

    <?php   
        $template = 'frontend' . DIRECTORY_SEPARATOR . 'idealist-overview-item';
        $args = array(
            'add_wrapper' => false,                
        );        
        
        foreach ($ideas as $idea) {
           
            echo rbstravel_template_loader($template, $idea, null, $args);   
        }
    ?>
    </div>

</div>

<style>
    .idealist_items {
        background: #ededed;
        display: flex;
        flex-direction: column;
     
      
    }  

    
</style>