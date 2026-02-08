<?php
/**
 * @todo:
 * - [ ] change 'min' / 'max' age to a slider??
 * - [ ] add filters for??
 *	- [ ] !!!! "leverbaar" (ja/nee)		
 *	    >> deze moest dacht ik nog gemaakt worden
 *	- [ ] id_author				(need to get list through api)
 *	- [ ] publisher_id			(need to get list through api)
 *	- [ ] ?? id_series
 *	- [ ] ?? release_number
 *	- [ ] language				(??? can be searched on language)
 *	- [ ] ?? no "book_genre" 
 *	    >> book_type is different   
 * - [ ] move 'book_types' array to a "filter class"
 */

/**
 * @ref:
 * - https://editor.swagger.io/		(use that 'OPENAPI3.yml')
 */


//LEESBOEK | INFORMATIEF | PRENTENBOEK | STRIPBOEK | DICHTBUNDEL | VOORLEESBOEK | ORIENTATIE_OP_LEZEN | ZOEKBOEK | AANWIJSBOEK
//READING BOOK | INFORMATIVE | PICTURE BOOK | COMIC BOOK | POET BUNDLE | READING BOOK | ORIENTATION_OP_READ | SEARCH BOOK | POINT BOOK
$book_types = array(
    'LEESBOEK' => __('Reading books', 'rbs-books'),
    'INFORMATIEF' => __('Informative books', 'rbs-books'),
    'PRENTENBOEK' => __('Picture books', 'rbs-books'),
    'STRIPBOEK' => __('Comic books', 'rbs-books'),
    'DICHTBUNDEL' => __('Poet bundle books', 'rbs-books'),
    'VOORLEESBOEK' => __('Telling books', 'rbs-books'),
    'ORIENTATIE_OP_LEZEN' => __('Reading orientation books', 'rbs-books'),
    'ZOEKBOEK' => __('Searching books', 'rbs-books'),
    'AANWIJSBOEK' => __('Point books', 'rbs-books')
);

$posted_filters = array(
    'search' => filter_input(INPUT_POST, 'search_book'),
    'filters' => filter_input(INPUT_POST, 'filter', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)
);
echo '<pre>' . print_r($posted_filters, true) . '</pre>';

?>

<div id="rbsbooks-remote-books__filters">

	<div class="filter-row">
	    <label>
		<?php _e('Search', 'rbs-books'); ?>
		<input type="text" name="search_book" value="" />
	    </label>
	    <p><?php _e('Search in book-title, series-title, author or isbn13.', 'rbs-books'); ?></p>
	</div>
	
	<div class="filter-row">
	    <label>		
		<input type="checkbox" name="filter[dyslexia]" value="1" title="<?php _e('Whether the book is written for dyslectic people.', 'rbs-books'); ?>" />
		<?php _e('Dyslexia', 'rbs-books'); ?>
	    </label>
	</div>	

	<div class="filter-row">
	    <label>
		<?php _e('Minimum Release Date', 'rbs-books'); ?>
		<input type="date" name="filter[min_release_date]" value="" title="<?php _e('The mininum date that the book was published.', 'rbs-books'); ?>" />
	    </label>
	</div>	
	
	<div class="filter-row">
	    <label>
		<?php _e('Maximum Release Date', 'rbs-books'); ?>
		<input type="date" name="filter[max_release_date]" value="" title="<?php _e('The maximum date that the book was published.', 'rbs-books'); ?>" />
	    </label>
	</div>	
	
	<div class="filter-row">
	    <label>
		<?php _e('Minimum Age', 'rbs-books'); ?>
		<input type="number" name="filter[min_age]" value="" min="1" title="<?php _e('The minimum age that the book was written for.', 'rbs-books'); ?>" />
	    </label>
	</div>	
	
	<div class="filter-row">
	    <label>
		<?php _e('Maximum Age', 'rbs-books'); ?>
		<input type="number" name="filter[max_age]" value="" min="1" title="<?php _e('The maximum age that the book was written for.', 'rbs-books'); ?>" />
	    </label>
	</div>	
		
	<div class="filter-row">
	    <?php _e('Book Type', 'rbs-books'); ?>
	    <?php foreach($book_types as $book_type_key => $book_type_label): ?>
	    <label>
		<input type="checkbox" name="filter[book_type][]" value="<?php echo $book_type_key;?>" />
		<?php echo $book_type_label; ?>
	    </label>
	    <?php endforeach; ?>
	</div>	

	
	<div class="filter-row filter-buttons">
	    <button type="submit" name="submit_filter" value="1"><?php _e('Search Books', 'rbs-books'); ?></button>
	    <button type="reset" name="reset_filter" value="1"><?php _e('Reset', 'rbs-books'); ?></button>
	</div>

</div>