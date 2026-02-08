


<div class="idealist_overview_item">
    <div class="item_header">
        <h2><?php echo $vars['post']->post_title; ?></h2>
    </div>

    <div class="item_content_wrapper">
        <div class="item_content">        
            <?php echo $vars['post']->post_content; ?>
        </div>   
     

        <div class="item_nav">
            <a href="<?php echo get_permalink(10); ?>">Reis bekijken</a>
        </div>        
    </div>

    
</div>

<style>
    .idealist_overview_item {
        display: flex;
        flex-direction: column;
      
    }  

    .item_nav {
        margin-top: 50px;
    }

    .item_image {
        width: 100%;
    }

    .item_content_wrapper {
        padding: 50px;
        
    }

    .item_price {
 

        
    }

    
</style>