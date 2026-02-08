<?php
namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

if (!class_exists('\WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


/**
 * @todo:
 * - [ ] 
 */

/**
 * @ref:
 * - https://maheshwaghmare.com/wordpress/how-to/create-table-with-wp_list_table/
 */

if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Remote_Travels')) {
    class RBS_TRAVEL_Remote_Travels extends \WP_List_Table {
	
//	private $imported_books;
	private $travel_mappings;
	
	
	
	function __construct() {
	    parent::__construct();
	    	   
	    $this->travel_mappings = RBS_TRAVEL_Settings::GetMapping();
	    
	}
	
	public function get_columns() {
	    $columns = array(
		'cb' => '<input type="checkbox" />',
		'travel_id' => __('ID', 'rbs-travel'),
		'travel_title' => __('Title', 'rbs-travel'),
		'travel_destinations' =>  __('Destinations', 'rbs-travel'),
		'travel_total_price' =>  __('Total Price', 'rbs-travel'),		
		'travel_create_date' => __('Creation Date', 'rbs-travel'),
		'travel_user' => __('User', 'rbs-travel'),
		'has_local' =>  __('Local', 'rbs-travel'),
		//'travel_link_url' =>  __('Link', 'rbs-travel')
		'actions' => __('Actions', 'rsb-travel')
	    );
	    
	    return apply_filters('rbs_travel_remote_travel_columns', $columns);
	}

	
	
	public function prepare_items() {
//	    parent::prepare_items();
	    $columns = $this->get_columns();
	    $hidden = array();
	    $sortable = array();
	    $primary = 'travel_title';
	    $this->_column_headers = array($columns, $hidden, $sortable, $primary);
	}
	
	
	private function get_destinations_value($destinations) {
	    if (is_array($destinations) && count($destinations) !== 0) {
		$return = array();
		$return[] = '<ul>';
		foreach($destinations as $destination) {		    
		    $return[] = '<li>' . $destination['name'] . '</li>';		    
		}
		$return[] = '</ul>';
		return implode("\r\n", $return);
	    } else {
		return __('None', 'rbs-travel');
	    }
	}
	
	
	function single_row($item) {
	    echo '<tr class="rbstravel-remote-travel-row">';
	    $this->single_row_columns( $item );
	    echo '</tr>';  
	}
	
	
	function column_default($item, $column_name) {
//	    //parent::column_default($item, $column_name);
//	    print_r($item);
//	    print_r($column_name);
//	    die();
	    
//	    if (isset($item[$column_name])) {
//		return $item[$column_name];
//	    }	    
	    
//	    if ($column_name === 'travel_id') {
//		return '<a href="#" data-travel-id="' . $item['id'] . '">' . $item['id'] . '</a>';
//	    }	    
	    
	    if ($column_name === 'travel_title') {
		    return $item['title'];
	    }
	    
	    if ($column_name === 'travel_destinations') {
		    return $this->get_destinations_value($item['destinations']);
	    }	    
	    
	    if ($column_name === 'travel_total_price') {
		    return $item['totalPrice']['amount'] . ' ' . $item['totalPrice']['currency'];
	    }	   
	    
	    if ($column_name === 'travel_create_date') {
		    return $item['creationDate'];
	    }
	    
	    if ($column_name === 'has_local') {
            if (isset($this->travel_mappings[$item['id']])) {
                return '<a href="' . admin_url('post.php?post=' . $this->travel_mappings[$item['id']] . '&action=edit') . '" target="_blank">' . $this->travel_mappings[$item['id']] . '</a>';
            }
		return '-';
	    }
	    
	    if ($column_name === 'travel_user') {
		    return $item['user'] . ' <span>(' . $item['email'] . ')</span>';
	    }	    
	    
	    /** Disabled, moved to "actions" **/
//	    if ($column_name === 'travel_link_url') {
//		return '<a href="' . $item['ideaUrl'] . '" target="_blank">' . __ ('Open link', 'rbs-travel') . '</a>';
//	    }
	    
	    if ($column_name === 'actions') {
		$return = array();
		
		if (isset($this->travel_mappings[$item['id']])) {
		    $return[] = '<a href="' . admin_url('post.php?post=' . $this->travel_mappings[$item['id']] . '&action=edit') . '" target="_blank" title="' . __ ('Edit import travel post', 'rbs-travel') . '">' . '<img src="' . RBS_TRAVEL_PLUGIN_URL_ASSETS . 'images/edit_travel_post.png" alt="' . __ ('Edit import travel post', 'rbs-travel') . '"/>' . '</a>';
		    //$return[] = ' | ';
		    $return[] = '<a href="#" data-travel-id="' . $item['id'] . '" data-action="remove_mapping" title="' . __ ('Remove travel post mapping', 'rbs-travel') . '">' . '<img src="' . RBS_TRAVEL_PLUGIN_URL_ASSETS . 'images/remove_mapping.png" alt="' . __ ('Remove travel post mapping', 'rbs-travel') . '"/>' . '</a>';
		    //$return[] = ' | ';
		}		
		
		$return[] = '<a href="' . $item['ideaUrl'] . '" target="_blank" title="' . __ ('Open remote link', 'rbs-travel') . '">' . '<img src="' . RBS_TRAVEL_PLUGIN_URL_ASSETS . 'images/remote_travel_link.png" alt="' . __ ('Open remote link', 'rbs-travel') . '"/>' . '</a>';
		
		
		return implode("\r\n", $return);
	    }

	    return 'TEMP: invalid data (' . $column_name . ')';
	}
	
	function column_travel_id($item) {
	    $output = array();

	    $output[] = '<strong>' . $item['id'] . '</strong>';
	    
	    $output[] = '<div id="rbstravel-import-error-' . $item['id'] . '" class="rbstravel-import-error">';	    
	    $output[] = '<p>ERROR message for/when import does fail......</p>';
	    $output[] = '<button type="button" class="rbstravel-import-error-close button-cancel">' . __('Close', 'rbs-travel') . '</button>';
	    $output[] = '</div>';
	    
	    $output[] = '<img id="rbstravel-import-busy-' . $item['id'] . '" src="' . RBS_TRAVEL_PLUGIN_URL_ASSETS . 'images/busy.gif' . '" class="rbstravel-import-busy" />';
	    
	    $output[] = '<div class="row-actions">';
	    $output[] = '<span class="view-info">' .   '<a href="#" class="rbstravel-row-action-link" data-travel-id="' . $item['id'] . '" data-action="view_info">' .__('View Info', 'rbs-travel') . '</a>' . '</span>';
	    $output[] = ' | ';
	    $output[] = '<span class="view-details">' .   '<a href="#" class="rbstravel-row-action-link" data-travel-id="' . $item['id'] . '" data-action="view_details">' .__('View Details', 'rbs-travel') . '</a>' . '</span>';
	    $output[] = ' | ';
	    $output[] = '<span class="import">' .   '<a href="#" class="rbstravel-row-action-link" data-travel-id="' . $item['id'] . '" data-action="import">' .__('Import', 'rbs-travel') . '</a>' . '</span>';
	    $output[] = '</div>';

		//return '<a href="#" data-travel-id="' . $item['id'] . '">' . $item['id'] . '</a>';
	    
	    return implode("\r\n", $output);
	}
	
	
	function column_cb($item) {
	    return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', 'travel_id', $item['id']);
	}
	
	
//	function row_actions($actions, $always_visible = false): string {
//	    return parent::row_actions($actions, $always_visible);
//	}

    }

}


