
<nav id="tabs">
  <!-- <li><a href="#tab1">Overzicht</a></li> -->
  <li><a href="#tab2">Dag bij Dag beschrijving</a></li>
  <li class="active"><a href="#tab3">Accommodaties</a></li>
  <!-- <li><a href="#tab4">Foto's</a></li> -->

</nav>
<section id="tab-contents">
  <div id="tab1" class="tab-contents">
    <p>TODO</p>
  </div>
  <div id="tab2" class="tab-contents">
    <section class="rbs-travel-idea__content">
	<h2 id="travel-destinations" class="rbs-travel-idea__subtitle"><?php _e('Bestemmingen', 'rbs-travel'); ?></h2>
	<?php rbstravel_template_part('destinations'); ?>
    </section>    
  </div>
  <div id="tab3" class="tab-contents active">
  <section class="rbs-travel-idea__content">
	<h2 id="travel-hotels" class="rbs-travel-idea__subtitle"><?php _e('Hotels', 'rbs-travel'); ?></h2>
    <?php rbstravel_template_part('hotels'); ?>
  </section> 
  </div>
  <div id="tab4" class="tab-contents">
    <p>TODDO</p>
  </div>  
</section>

<style>
#tabs {
margin-top: 100px;
list-style: none;

display:flex;
gap: 30px;
/* justify-content: space-between; */
    align-items: center;

/* display: grid;
grid-template-columns: 1fr 1fr 1fr 1fr;
border: 1px solid #000000;
height: 40px;
line-height: 40px;
list-style-type: none; */

}


#tabs  a {
  display: inline-block;
  width: 350px;
  text-align: center;
  font-size: 24px;
  height: 40px;
  padding: 5px;
  background: #ededed;

  text-decoration: none;
}

#tab-contents {
border: 1px solid #ccc;
background-color: #efefef;
.tab-contents {
padding: 10px;
display: none;
&.active {
    display: block;
}
}
}

#tabs .active a {
 
  text-decoration: underline;
}

</style>

<script>

// Define variables
var tabLabels = document.querySelectorAll("#tabs li");
var tabPanes = document.getElementsByClassName("tab-contents");

function activateTab(e) {
  e.preventDefault();
  
  // Deactivate all tabs
  tabLabels.forEach(function(label, index){
    label.classList.remove("active");
  });
  [].forEach.call(tabPanes, function(pane, index){
    pane.classList.remove("active");
  });
  
  // Activate current tab
  e.target.parentNode.classList.add("active");
  var clickedTab = e.target.getAttribute("href");
  document.querySelector(clickedTab).classList.add("active");
}

// Apply event listeners
tabLabels.forEach(function(label, index){
  label.addEventListener("click", activateTab);
});
</script> 




