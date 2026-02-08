
idealist-overview-item.html

<div data-postid="<?php echo $vars['post']->ID; ?>" class="idealist_overview_item">
    
    <div class="item_image_column">
        <img src="<?php echo $vars['idea']['travel_image_url']; ?>">
    </div>

    <div class="item_details_column">
        <div class="item_header">
            <h2><?php echo $vars['post']->post_title; ?></h2>
        </div>       
        <div class="item_header">
            More....?
        </div>           
    </div>    

    <div class="item_price_column">
        <div class="item_price"><h2>EURO <?php echo $vars['idea']['travel_price_per_person']['amount']; ?></h2></div>
        <div class="item_nav">
            <a href="<?php echo get_permalink( $vars['post']->ID); ?>">Reis bekijken</a>
        </div> 
    </div>    
    
</div>

<style>

    .item_image_column {
        width: 30%;
    }      

    .item_details_column {
        display: flex;
        width: auto;
        flex-direction: column;
        width: 50%;
    }

    .item_price_column {
        width: 20%;
        display: flex;
        flex-direction: column;
        padding-right: 20px;
    }    



    .idealist_overview_item {
        background: white;
        display: flex;
        gap: 10%;
        flex-direction: row;
        padding: 10px;
        width: 60%;
        margin-bottom: 50px;
       
        border: 1px solid #ededed;
      
    }  

    .item_nav {
        margin-top: 50px;
    }

    .item_image_column img {
        width: 100%;
    }

    .item_content_wrapper {
        padding: 50px;
        
    }

    .item_price {
 

        
    }

  

    
</style>