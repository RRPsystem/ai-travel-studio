
<!-- idealist-filter.html -->
<?php
// Get a list of all terms in a taxonomy
$terms = get_terms( "location", array(
    'parent' => 0,          // !! REMOVE TO SHOW CHILDREN (contintent -> country -> destination)
    'hide_empty' => 0,
) );
$locations = array();
if ( count($terms) > 0 ):
  foreach ( $terms as $term )
    $locations[] = $term->name;

  $locations_str = implode(', ', $locations);


  $the_query = new WP_Query( array(
    'post_type' => 'rbs-travel',
    'tax_query' => array(
        array (
            'taxonomy' => 'location',
            'field' => 'slug',
            'terms' => 'Europa',
        )
    ),
) );

while ( $the_query->have_posts() ) :
    $the_query->the_post();

    var_dump($the_query->the_post());
    // Show Posts ...
endwhile;

/* Restore original Post Data 
 * NB: Because we are using new WP_Query we aren't stomping on the 
 * original $wp_query and it does not need to be reset.
*/
wp_reset_postdata();  
?>


<?php endif; ?>

<form class="travel-filter" id="travelfilter">
    <div class="idealist_filter">
        <div class="filter_item">
            <h3>Bestemmingen</h3>
            <p>Wij hebben o.a. reizen naar de volgende bestemmingen: <?php echo $locations_str; ?>. Indien uw regio er niet bij staat, laat het aub even weten! </p>            
            <ul>
                <?php foreach ($locations as $location ) : ?>
                    <li><label><input type="checkbox" name="destination" value="<?php echo $location; ?>"><?php echo $location; ?></li>
                <?php endforeach; ?>                                
            </ul>
        </div>   
        <div class="filter_item">
            <h3>Prijs</h3>
            <div class="demo">
                <div class="range_wrapper">
                <h4>Minimale prijs</h4>
                <input type="range" min="500" max="10000" oninput="showValMin(this.value)" onchange="showValMin(this.value)" step="100" value="500">
                <div id="valBoxMin" class="valBox">500</div>
                </div>
            </div>        

            <div class="demo">
                <div class="range_wrapper">
                <h4>Maximale prijs</h4>
                <input type="range" min="500" max="10000" oninput="showValMax(this.value)" onchange="showValMax(this.value)" step="100" value="10000">
                <div id="valBoxMax" class="valBox">10000</div>
                </div>
            </div>           
        </div> 
        <div class="filter_item">

        </div> 
        <button id="formtrigger" class="button">
            <div class="zoeken">ZOEKEN!</div>
            <div class="magnifying-glass"></div>
        </button>

     
        
    </div>
</form>
<script>
function showValMin(val) {  document.getElementById("valBoxMin").innerHTML = val};
function showValMax(val) {  document.getElementById("valBoxMax").innerHTML = val};
</script>    

<style>
    .idealist_filter {
        display: flex;
        flex-direction: column;
        padding: 20px;
        background: #ffffff;
      
    }  

    .filter_item ul {
        list-style-type:none;
    }

</style>

