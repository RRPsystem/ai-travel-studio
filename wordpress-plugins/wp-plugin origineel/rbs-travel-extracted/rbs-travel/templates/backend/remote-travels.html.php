<?php
//echo '<pre>' . print_r($_POST, true) . '</pre>';

$searched_value = filter_input(INPUT_POST, 'search_value');


$__temp_limit = 50;

$pagination = filter_input(INPUT_POST, 'pagination');
$pagination_page = filter_input(INPUT_POST, 'pagination_page');
$pagination_first = filter_input(INPUT_POST, 'pagination_first');
$pagination_total = filter_input(INPUT_POST, 'pagination_total');
$pagination_limit = filter_input(INPUT_POST, 'pagination_limit');

$first = $pagination_first !== null ? $pagination_first : 0;

if ($pagination === 'first') {
    $first = 0;    
} elseif ($pagination === 'prev' && $first > $pagination_limit - 1) {
    $first -= $pagination_limit;
} elseif ($pagination === 'next' && $first < $pagination_total) {
    $first += $pagination_limit;
} elseif ($pagination === 'last') {
    $first = $pagination_total - $pagination_limit;
} else {
    //$first = 0;
}




$RBS_TRAVEL_Remote_Travels = new \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Remote_Travels();
$RBS_TRAVEL_Api = new \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Api();
$travel_ideas = $RBS_TRAVEL_Api->GetIdeas($searched_value, array(), $first);
if (isset($travel_ideas['not_found'])) {
    $not_found = true;
} else {
    $not_found = false;
    $RBS_TRAVEL_Remote_Travels->items = $travel_ideas['idea'];
    $RBS_TRAVEL_Remote_Travels->prepare_items();
}



//echo '<pre>' . print_r($travel_ideas['pagination'], true) . '</pre>';


if ($not_found) {
    $pagination_page = 0;
} else {
    $pagination_pages = ceil($travel_ideas['pagination']['totalResults'] / $travel_ideas['pagination']['pageResults']);
    if ($pagination_page === null) {
        $pagination_page = 1;
    } else {
    //    if ($pagination_total !== null && $pagination_limit !== 0) {
    //	$pagination_page = 0;   // ceil($pagination_total / $pagination_limit);
    //    } else {
    //	$pagination_page = 0;
    //    }
        $pagination_page = ceil($travel_ideas['pagination']['firstResult'] / $travel_ideas['pagination']['pageResults']);
        $pagination_page += 1;
    }
}

?>    
    



<div class="wrap">
    
    <div id="rbstravel-travel-ajax-response">
	<?php require 'travel-info.html.php'; ?>
	<?php require 'travel-details.html.php'; ?>
    </div>
    
    
    <h1><?php _e('Remote Travels', 'rbs-travel'); ?></h1>
    
    <form method="post" action="">
    
	<?php //require __DIR__ . DIRECTORY_SEPARATOR . 'filters.html.php'; ?>

	<div class="rbstravel-remote-travels-header">
	    <p><?php printf(__('Total Travel Ideas: %s', 'rbs-travel'), $travel_ideas['pagination']['totalResults']); ?></p>
	    <p><?php printf(__('Current Page: %s', 'rbs-travel'), $pagination_page); ?></p>
	</div>
	
	<div class="rbstravel-remote-travels-search">
	    <input type="text" name="search_value" value="<?php echo $searched_value; ?>" class="rbstravel-input" />
	    <button type="submit" name="search_submit" value="search" class="rbstravel-button button-secondary"><?php _e('Search', 'rbs-travel'); ?></button>
	</div>
	
	<div class="rbstravel-remote-travels-pagination">
        <?php if ($not_found === false): ?>
            <button type="submit" name="pagination" value="first" class="rbstravel-button button-secondary"><?php _e('First', 'rbs-travel');?></button>
            <button type="submit" name="pagination" value="prev" class="rbstravel-button button-secondary"><?php _e('Previous', 'rbs-travel');?></button>
            <select name="pagination_page" onchange="this.form.submit();">
            <?php for($i = 1; $i <= $pagination_pages; $i++): ?>
                <?php $selected = $pagination_page == $i ? 'selected' : ''; ?>
                <option value="<?php echo $i;?>" <?php echo $selected;?>><?php echo $i; ?></option>
            <?php endfor; ?>
            </select>
            <button type="submit" name="pagination" value="next" class="rbstravel-button button-secondary"><?php _e('Next', 'rbs-travel');?></button>
            <button type="submit" name="pagination" value="last" class="rbstravel-button button-secondary"><?php _e('Last', 'rbs-travel');?></button>
            
            <input type="hidden" name="pagination_first" value="<?php echo $first;?>" /> 
            <input type="hidden" name="pagination_total" value="<?php echo $travel_ideas['pagination']['totalResults'];?>" /> 
            <input type="hidden" name="pagination_limit" value="<?php echo $travel_ideas['pagination']['pageResults'];?>" />
        <?php endif; ?>
	</div>
	
    <?php $RBS_TRAVEL_Remote_Travels->display(); ?>

	<div class="table-list-buttons">
	    <button type="submit" class="button-primary" name="import_travels" value="1"><?php _e('Import selected travel ideas', 'rbs-travel'); ?></button>
	</div>
	
    </form>
    
</div>