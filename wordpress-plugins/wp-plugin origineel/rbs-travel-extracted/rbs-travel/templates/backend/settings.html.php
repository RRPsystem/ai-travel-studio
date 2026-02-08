<?php
if (!current_user_can('manage_options')) {
    wp_die('TEMP: No Access', 'rbs-travel');
}

if (!defined('RBS_TRAVEL')) {
    wp_die('TEMP: Invalid Access', 'rbs-travel');
}

use \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings;
?>


<?php
/**
 * @todo:
 * - [ ] check for more settings/options
 *	- [ ] default import post status (default: draft)
 *	- [ ] default import post author
 *	- [ ] custom rewrite slug
 *	    >> also "refresh permalinks in php"
 *	- [ ] frontend styling
 *	- [ ] download/import ALL images
 *	- [ ] ...
 * - [ ] add 'tabs'
 * - [ ] 
 */


?>

<div id="rbstravel-settings" class="wrap">
    <h1><?php _e('RRP System - Settings', 'rbs-travel'); ?></h1>

    
    <form method="post" action="<?php echo admin_url('admin.php?page=rbstravel-settings'); ?>">
    
	<?php //settings_fields('rbs_books_options_group'); ?>
	
	<?php
	// Get existing API credentials (migrate old format if needed)
	$api_credentials = RBS_TRAVEL_Settings::GetSetting('api_credentials', array());
	
	// Migrate old single credential to new format if needed
	if (empty($api_credentials)) {
	    $old_user = RBS_TRAVEL_Settings::GetSetting('api_user', '');
	    $old_pass = RBS_TRAVEL_Settings::GetSetting('api_pass', '');
	    $old_micrositeid = RBS_TRAVEL_Settings::GetSetting('api_micrositeid', '');
	    
	    if (!empty($old_user)) {
		$api_credentials['set_1'] = array(
		    'name' => 'Main API',
		    'username' => $old_user,
		    'password' => $old_pass,
		    'micrositeid' => $old_micrositeid
		);
	    }
	}
	
	// Ensure at least one set exists
	if (empty($api_credentials)) {
	    $api_credentials['set_1'] = array(
		'name' => 'API Set 1',
		'username' => '',
		'password' => '',
		'micrositeid' => ''
	    );
	}
	
	$active_api_set = RBS_TRAVEL_Settings::GetSetting('active_api_set', 'set_1');
	?>
	
	<table class="form-table" role="presentation">
	    <tbody>
		<tr>
		    <th scope="row" colspan="2">
			<h2 style="margin-top: 0;"><?php _e('🔌 API Credentials', 'rbs-travel'); ?></h2>
			<?php if (is_multisite()): ?>
			    <?php 
			    // Get allowed APIs for this site from network settings
			    $site_id = get_current_blog_id();
			    $network_credentials = get_site_option('rbstravel_network_api_credentials', array());
			    // Note: Network Settings class saves to 'rbstravel_network_site_permissions'
			    $site_permissions = get_site_option('rbstravel_network_site_permissions', array());
			    $allowed_apis = isset($site_permissions[$site_id]) ? $site_permissions[$site_id] : array();
			    $allowed_count = count($allowed_apis);
			    ?>
			    <div style="background: #e7f5ff; border-left: 4px solid #0073aa; padding: 12px 15px; margin: 10px 0 20px 0;">
				<strong style="color: #0073aa;">🌐 Multisite Modus Actief</strong>
				<p style="margin: 8px 0 0 0; color: #333;">
				    API credentials worden centraal beheerd door de Network Administrator.<br>
				    <?php if ($allowed_count > 0): ?>
					<span style="color: #059669;">✓ Je hebt toegang tot <strong><?php echo $allowed_count; ?></strong> API<?php echo $allowed_count > 1 ? '\'s' : ''; ?>:</span>
					<ul style="margin: 5px 0 0 20px; padding: 0;">
					    <?php foreach ($allowed_apis as $api_key): ?>
						<?php if (isset($network_credentials[$api_key])): ?>
						    <li><strong><?php echo esc_html($network_credentials[$api_key]['name']); ?></strong></li>
						<?php endif; ?>
					    <?php endforeach; ?>
					</ul>
				    <?php else: ?>
					<span style="color: #dc2626;">⚠ Je hebt nog geen API toegang. Neem contact op met de Network Administrator.</span>
				    <?php endif; ?>
				</p>
				<?php if (is_super_admin()): ?>
				    <p style="margin: 10px 0 0 0;">
					<a href="<?php echo network_admin_url('admin.php?page=rbstravel-network-settings'); ?>" class="button button-primary">
					    🔧 Network API Beheer
					</a>
				    </p>
				<?php endif; ?>
			    </div>
			<?php else: ?>
			    <p class="description"><?php _e('Beheer meerdere API credential sets. Je kunt tussen verschillende API\'s wisselen zonder telkens opnieuw in te loggen.', 'rbs-travel'); ?></p>
			<?php endif; ?>
		    </th>
		</tr>
		
		<?php if (!is_multisite()): // Only show API management on single site ?>
		<tr>
		    <td colspan="2">
			<div id="rrp-api-credentials-container">
			    <?php foreach ($api_credentials as $set_id => $credential): ?>
				<div class="rrp-api-set" data-set-id="<?php echo esc_attr($set_id); ?>">
				    <div class="rrp-api-set-header">
					<span class="rrp-toggle-icon dashicons dashicons-arrow-down-alt2"></span>
					<strong class="rrp-set-name-display"><?php echo esc_html($credential['name']); ?></strong>
					<button type="button" class="button button-small rrp-remove-set" style="float: right; margin-top: -3px;">
					    <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
					</button>
				    </div>
				    <div class="rrp-api-set-content" style="display: none;">
					<table class="form-table">
					    <tr>
						<th style="width: 200px;">
						    <label><?php _e('Naam', 'rbs-travel'); ?></label>
						</th>
						<td>
						    <input type="text" class="regular-text rrp-set-name" name="rbstravel_settings[api_credentials][<?php echo esc_attr($set_id); ?>][name]" value="<?php echo esc_attr($credential['name']); ?>" placeholder="Bijv: Robas Main" />
						    <p class="description"><?php _e('Geef deze API credential set een herkenbare naam', 'rbs-travel'); ?></p>
						</td>
					    </tr>
					    <tr>
						<th>
						    <label><?php _e('API Gebruikersnaam', 'rbs-travel'); ?></label>
						</th>
						<td>
						    <input type="text" class="regular-text" name="rbstravel_settings[api_credentials][<?php echo esc_attr($set_id); ?>][username]" value="<?php echo esc_attr($credential['username']); ?>" autocomplete="off" />
						</td>
					    </tr>
					    <tr>
						<th>
						    <label><?php _e('API Wachtwoord', 'rbs-travel'); ?></label>
						</th>
						<td>
						    <input type="password" class="regular-text" name="rbstravel_settings[api_credentials][<?php echo esc_attr($set_id); ?>][password]" value="<?php echo esc_attr($credential['password']); ?>" autocomplete="off" />
						</td>
					    </tr>
					    <tr>
						<th>
						    <label><?php _e('API MicrositeID', 'rbs-travel'); ?></label>
						</th>
						<td>
						    <input type="text" class="regular-text" name="rbstravel_settings[api_credentials][<?php echo esc_attr($set_id); ?>][micrositeid]" value="<?php echo esc_attr($credential['micrositeid']); ?>" autocomplete="off" />
						</td>
					    </tr>
					    
					    <tr>
						<th colspan="2" style="padding-top: 20px;">
						    <h3 style="margin: 0;">🔗 Webhook Auto-Import</h3>
						</th>
					    </tr>
					    
					    <?php 
					    $webhook_config = isset($credential['webhook']) ? $credential['webhook'] : array();
					    $webhook_enabled = isset($webhook_config['enabled']) ? $webhook_config['enabled'] : '0';
					    $webhook_secret = isset($webhook_config['secret']) ? $webhook_config['secret'] : '';
					    $webhook_country = isset($webhook_config['default_country']) ? $webhook_config['default_country'] : '';
					    $webhook_status = isset($webhook_config['post_status']) ? $webhook_config['post_status'] : 'draft';
					    
					    // Generate webhook URL
					    $webhook_url = rest_url('rbs-travel/v1/webhook/' . $set_id);
					    ?>
					    
					    <tr>
						<th>
						    <label><?php _e('Auto-Import Inschakelen', 'rbs-travel'); ?></label>
						</th>
						<td>
						    <label>
							<input type="checkbox" name="rbstravel_settings[api_credentials][<?php echo esc_attr($set_id); ?>][webhook][enabled]" value="1" <?php checked($webhook_enabled, '1'); ?> />
							<?php _e('Automatisch reizen importeren via webhook', 'rbs-travel'); ?>
						    </label>
						    <p class="description"><?php _e('Wanneer ingeschakeld, worden nieuwe reizen automatisch geïmporteerd wanneer ze in Travel Compositor worden aangemaakt of gewijzigd.', 'rbs-travel'); ?></p>
						</td>
					    </tr>
					    
					    <tr>
						<th>
						    <label><?php _e('Webhook URL', 'rbs-travel'); ?></label>
						</th>
						<td>
						    <input type="text" class="regular-text" value="<?php echo esc_attr($webhook_url); ?>" readonly onclick="this.select();" style="background: #f0f0f0; font-family: monospace; font-size: 12px;" />
						    <p class="description"><?php _e('Gebruik deze URL in Travel Compositor webhook instellingen. Klik om te selecteren en kopiëren.', 'rbs-travel'); ?></p>
						</td>
					    </tr>
					    
					    <tr>
						<th>
						    <label><?php _e('Webhook Secret', 'rbs-travel'); ?></label>
						</th>
						<td>
						    <input type="text" class="regular-text" name="rbstravel_settings[api_credentials][<?php echo esc_attr($set_id); ?>][webhook][secret]" value="<?php echo esc_attr($webhook_secret); ?>" placeholder="Optioneel: geheime sleutel voor beveiliging" />
						    <p class="description"><?php _e('Optioneel: Voeg een geheime sleutel toe voor extra beveiliging. Gebruik dezelfde waarde in Travel Compositor (header: X-Webhook-Secret).', 'rbs-travel'); ?></p>
						</td>
					    </tr>
					    
					    <tr>
						<th>
						    <label><?php _e('Extra Land/Regio', 'rbs-travel'); ?></label>
						</th>
						<td>
						    <?php
						    // Get all location terms (countries)
						    $locations = get_terms(array(
							'taxonomy' => 'location',
							'hide_empty' => false,
							'orderby' => 'name'
						    ));
						    ?>
						    <select name="rbstravel_settings[api_credentials][<?php echo esc_attr($set_id); ?>][webhook][default_country]" class="regular-text">
							<option value=""><?php _e('-- Geen extra land --', 'rbs-travel'); ?></option>
							<?php if (!is_wp_error($locations) && !empty($locations)): ?>
							    <?php foreach ($locations as $location): ?>
								<option value="<?php echo esc_attr($location->slug); ?>" <?php selected($webhook_country, $location->slug); ?>>
								    <?php echo esc_html($location->name); ?>
								</option>
							    <?php endforeach; ?>
							<?php endif; ?>
						    </select>
						    <p class="description"><?php _e('<strong>Optioneel:</strong> Voeg een extra land/regio toe. Landen worden automatisch gedetecteerd uit de bestemmingen van de reis.', 'rbs-travel'); ?></p>
						</td>
					    </tr>
					    
					    <tr>
						<th>
						    <label><?php _e('Post Status', 'rbs-travel'); ?></label>
						</th>
						<td>
						    <select name="rbstravel_settings[api_credentials][<?php echo esc_attr($set_id); ?>][webhook][post_status]" class="regular-text">
							<option value="draft" <?php selected($webhook_status, 'draft'); ?>><?php _e('Concept (Draft)', 'rbs-travel'); ?></option>
							<option value="publish" <?php selected($webhook_status, 'publish'); ?>><?php _e('Gepubliceerd', 'rbs-travel'); ?></option>
						    </select>
						    <p class="description"><?php _e('Status van automatisch geïmporteerde reizen.', 'rbs-travel'); ?></p>
						</td>
					    </tr>
					</table>
				    </div>
				</div>
			    <?php endforeach; ?>
			</div>
			
			<p style="margin-top: 15px;">
			    <button type="button" id="rrp-add-api-set" class="button button-secondary">
				<span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
				<?php _e('Voeg API Credential Set Toe', 'rbs-travel'); ?>
			    </button>
			    <input type="hidden" id="rrp-set-counter" value="<?php echo count($api_credentials); ?>" />
			</p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row" style="width: 200px;">
			<label for="active_api_set"><?php _e('📌 Actieve API Set', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<select id="active_api_set" name="rbstravel_settings[active_api_set]" class="regular-text">
			    <?php foreach ($api_credentials as $set_id => $credential): ?>
				<option value="<?php echo esc_attr($set_id); ?>" <?php selected($active_api_set, $set_id); ?>>
				    <?php echo esc_html($credential['name']); ?>
				</option>
			    <?php endforeach; ?>
			</select>
			<p class="description"><?php _e('Selecteer welke API credential set momenteel gebruikt wordt voor import en synchronisatie.', 'rbs-travel'); ?></p>
		    </td>
		</tr>
		<?php endif; // End of !is_multisite() check ?>
		
		<tr>
		    <th scope="row" colspan="2">
			<hr>
		    </th>
		</tr>		
		
		<tr>
		    <th scope="row">
			<label for="tc_base_url"><?php _e('Travel Compositor Base URL', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="tc_base_url" type="url" class="text-field" name="rbstravel_settings[tc_base_url]" value="<?php echo RBS_TRAVEL_Settings::GetSetting('tc_base_url', 'https://online.travelcompositor.com'); ?>" placeholder="https://rondreis-planner.nl" autocomplete="off" style="width: 400px;" />
			<p class="description"><?php _e('Whitelabel URL voor cruise links (ZONDER trailing slash). Bijv: <code>https://rondreis-planner.nl</code><br>Cruise links worden: <code>[Deze URL]/nl/cruises/[cruise-id]/itinerary?booking=true</code>', 'rbs-travel'); ?></p>
		    </td>
		</tr>		

		
		<tr>
		    <th scope="row" colspan="2">
			<hr>
		    </th>
		</tr>		

		<tr>
		    <th scope="row">
			<label for="mapbox_token"><?php _e('Mapbox Token', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="mapbox_token" type="text" class="text-field" name="rbstravel_settings[mapbox_token]" value="<?php echo RBS_TRAVEL_Settings::GetSetting('mapbox_token', null);?>" />
		    </td>
		</tr>

		<tr>
		    <th scope="row" colspan="2">
			<hr>
		    </th>
		</tr>	        

		<tr>
		    <th scope="row">
			<label for="contact_form"><?php _e('Contact Form', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="contact_form" type="text" class="text-field" name="rbstravel_settings[contact_form]" value="<?php echo esc_attr(RBS_TRAVEL_Settings::GetSetting('contact_form', null));?>" />
			<p class="description"><?php _e('Enter shortcode', 'rbs-travel'); ?></p>
		    </td>
		</tr>

		<tr>
		    <th scope="row" colspan="2">
			<hr>
			<h2 style="margin-top: 20px;"><?php _e('🎨 Brand Kleuren', 'rbs-travel'); ?></h2>
			<p class="description" style="margin-bottom: 20px;"><?php _e('Pas de kleuren aan zodat de reisweergave bij je huisstijl past.', 'rbs-travel'); ?></p>
		    </th>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="primary_color"><?php _e('Hoofdkleur (Primary)', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="primary_color" type="text" class="color-picker" name="rbstravel_settings[primary_color]" value="<?php echo esc_attr(RBS_TRAVEL_Settings::GetSetting('primary_color', '#6366f1')); ?>" />
			<p class="description"><?php _e('Gebruikt voor knoppen, links en accenten. Standaard: #6366f1 (blauw)', 'rbs-travel'); ?></p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="secondary_color"><?php _e('Accentkleur (Secondary)', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="secondary_color" type="text" class="color-picker" name="rbstravel_settings[secondary_color]" value="<?php echo esc_attr(RBS_TRAVEL_Settings::GetSetting('secondary_color', '#10b981')); ?>" />
			<p class="description"><?php _e('Gebruikt voor secundaire elementen. Standaard: #10b981 (groen)', 'rbs-travel'); ?></p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="heading_color"><?php _e('Koppen Kleur', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="heading_color" type="text" class="color-picker" name="rbstravel_settings[heading_color]" value="<?php echo esc_attr(RBS_TRAVEL_Settings::GetSetting('heading_color', '#1f2937')); ?>" />
			<p class="description"><?php _e('Kleur voor titels en koppen. Standaard: #1f2937 (donkergrijs)', 'rbs-travel'); ?></p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="text_color"><?php _e('Tekst Kleur', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="text_color" type="text" class="color-picker" name="rbstravel_settings[text_color]" value="<?php echo esc_attr(RBS_TRAVEL_Settings::GetSetting('text_color', '#4b5563')); ?>" />
			<p class="description"><?php _e('Kleur voor normale tekst. Standaard: #4b5563 (grijs)', 'rbs-travel'); ?></p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row" colspan="2">
			<hr>
			<h2 style="margin-top: 20px;"><?php _e('🔘 Button Links', 'rbs-travel'); ?></h2>
			<p class="description" style="margin-bottom: 20px;"><?php _e('Configureer de links voor de actieknoppen op de reispagina. Gebruik <code>{travel_url}</code> als placeholder voor de reis URL.', 'rbs-travel'); ?></p>
		    </th>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="btn_book_url"><?php _e('📗 "Boek Nu" Link', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="btn_book_url" type="text" class="regular-text" name="rbstravel_settings[btn_book_url]" value="<?php echo esc_attr(RBS_TRAVEL_Settings::GetSetting('btn_book_url', '{travel_url}')); ?>" placeholder="{travel_url}" style="width: 400px;" />
			<p class="description"><?php _e('Link voor de "Boek Nu" knop. Standaard: <code>{travel_url}</code> (de reis URL uit Travel Compositor)', 'rbs-travel'); ?></p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="btn_info_url"><?php _e('📘 "Info Aanvragen" Link', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="btn_info_url" type="text" class="regular-text" name="rbstravel_settings[btn_info_url]" value="<?php echo esc_attr(RBS_TRAVEL_Settings::GetSetting('btn_info_url', '/contact')); ?>" placeholder="/contact" style="width: 400px;" />
			<p class="description"><?php _e('Link voor de "Info Aanvragen" knop. Bijv: <code>/contact</code> of <code>/offerte-aanvragen</code>', 'rbs-travel'); ?></p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="btn_customize_url"><?php _e('📙 "Reis Aanpassen" Link', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="btn_customize_url" type="text" class="regular-text" name="rbstravel_settings[btn_customize_url]" value="<?php echo esc_attr(RBS_TRAVEL_Settings::GetSetting('btn_customize_url', '{travel_url}')); ?>" placeholder="{travel_url}" style="width: 400px;" />
			<p class="description"><?php _e('Link voor de "Reis Aanpassen" knop. Standaard: <code>{travel_url}</code>', 'rbs-travel'); ?></p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row" colspan="2">
			<hr>
			<h2 style="margin-top: 20px;"><?php _e('📝 Frontend Settings', 'rbs-travel'); ?></h2>
		    </th>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="default_metform_shortcode"><?php _e('✉️ Metform Formulier ID', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="default_metform_shortcode" type="text" class="regular-text" name="rbstravel_settings[default_metform_shortcode]" value="<?php echo esc_attr(RBS_TRAVEL_Settings::GetSetting('default_metform_shortcode', '')); ?>" placeholder="[metform form_id=&quot;123&quot;]" style="font-family: monospace; font-size: 14px;" />
			<p class="description">
			    <strong>📋 Hoe vind je het Form ID?</strong><br>
			    1. Ga naar <strong>MetForm → All Forms</strong><br>
			    2. Zoek je contactformulier<br>
			    3. Kopieer de shortcode (bijv. <code>[metform form_id="456"]</code>)<br>
			    4. Plak de hele shortcode hier<br><br>
			    <strong>💡 Tip:</strong> Dit formulier verschijnt in de sidebar van ELKE reispagina
			</p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="itinerary_layout"><?php _e('🧭 Itinerary Layout', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<select id="itinerary_layout" name="rbstravel_settings[itinerary_layout]" class="regular-text">
			    <?php $itinerary_layout = RBS_TRAVEL_Settings::GetSetting('itinerary_layout', 'cards'); ?>
			    <option value="cards" <?php selected($itinerary_layout, 'cards'); ?>>Cards (standaard)</option>
			    <option value="slider" <?php selected($itinerary_layout, 'slider'); ?>>Wide Slider (versie 2)</option>
			</select>
			<p class="description"><?php _e('Kies hoe het dag-tot-dag programma wordt weergegeven op de reis detailpagina.', 'rbs-travel'); ?></p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="route_button_side"><?php _e('🗺️ Route knop positie', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<select id="route_button_side" name="rbstravel_settings[route_button_side]" class="regular-text">
			    <?php $route_button_side = RBS_TRAVEL_Settings::GetSetting('route_button_side', 'right'); ?>
			    <option value="right" <?php selected($route_button_side, 'right'); ?>>Rechts</option>
			    <option value="left" <?php selected($route_button_side, 'left'); ?>>Links</option>
			</select>
			<p class="description"><?php _e('Toon een icon-only knop in de title bar om de reisroute te openen.', 'rbs-travel'); ?></p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row" colspan="2">
			<h3 style="margin: 0 0 10px 0;"><?php _e('📦 Sidebar Widget', 'rbs-travel'); ?></h3>
			<p class="description" style="margin-bottom: 15px;">
			    Configureer een custom widget die in de sidebar van elke reispagina wordt getoond.
			</p>
		    </th>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="sidebar_widget_title"><?php _e('Widget Titel', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<input id="sidebar_widget_title" type="text" class="regular-text" name="rbstravel_settings[sidebar_widget_title]" value="<?php echo esc_attr(RBS_TRAVEL_Settings::GetSetting('sidebar_widget_title', '')); ?>" placeholder="bijv. Hulp nodig?" />
			<p class="description">Titel bovenaan de widget (optioneel)</p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="sidebar_widget_content"><?php _e('Widget Inhoud', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<?php 
			$widget_content = RBS_TRAVEL_Settings::GetSetting('sidebar_widget_content', '');
			wp_editor($widget_content, 'sidebar_widget_content', array(
			    'textarea_name' => 'rbstravel_settings[sidebar_widget_content]',
			    'textarea_rows' => 8,
			    'media_buttons' => true,
			    'teeny' => false,
			    'quicktags' => true,
			    'tinymce' => array(
				'toolbar1' => 'bold,italic,underline,link,unlink,bullist,numlist,alignleft,aligncenter,alignright,wp_adv',
				'toolbar2' => 'formatselect,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo',
			    ),
			));
			?>
			<p class="description" style="margin-top: 10px;">
			    <strong>💡 Tips:</strong><br>
			    • Voeg tekst, afbeeldingen, of shortcodes toe<br>
			    • Gebruik <code>[metform form_id="123"]</code> voor een contactformulier<br>
			    • HTML is toegestaan voor custom styling
			</p>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row">
			<label for="sidebar_widget_enabled"><?php _e('Widget Tonen', 'rbs-travel'); ?></label>
		    </th>
		    <td>
			<label>
			    <input type="checkbox" id="sidebar_widget_enabled" name="rbstravel_settings[sidebar_widget_enabled]" value="1" <?php checked(RBS_TRAVEL_Settings::GetSetting('sidebar_widget_enabled', '1'), '1'); ?> />
			    <?php _e('Toon sidebar widget op reispagina\'s', 'rbs-travel'); ?>
			</label>
		    </td>
		</tr>
		
		<tr>
		    <th scope="row" colspan="2">
			<hr>
			<h2 style="margin-top: 20px;"><?php _e('🎯 Tour Plan Icons', 'rbs-travel'); ?></h2>
			<p class="description" style="margin-bottom: 20px;"><?php _e('Klik op een icon om het te selecteren voor elk type item in het dag-programma.', 'rbs-travel'); ?></p>
		    </th>
		</tr>
		
		<?php
		// Get available icons from plugin folder
		$icons_dir = RBS_TRAVEL_PLUGIN_PATH . 'assets/images/icons/';
		$icons_url = RBS_TRAVEL_PLUGIN_URL . 'assets/images/icons/';
		$available_icons = array();
		
		if (is_dir($icons_dir)) {
		    $files = scandir($icons_dir);
		    foreach ($files as $file) {
			if (pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
			    $available_icons[] = $file;
			}
		    }
		    sort($available_icons);
		}
		
		$icon_types = array(
		    'flight' => array('label' => '✈️ Vlucht', 'suggested' => 'flight booking.svg'),
		    'destination' => array('label' => '📍 Bestemming', 'suggested' => 'travel.svg'),
		    'hotel' => array('label' => '🏨 Hotel', 'suggested' => 'luggage.svg'),
		    'cruise' => array('label' => '🚢 Cruise', 'suggested' => 'travel.svg'),
		    'transfer' => array('label' => '🚐 Transfer', 'suggested' => 'travel agent.svg'),
		    'transport' => array('label' => '🚗 Transport', 'suggested' => 'travel agent.svg'),
		    'activity' => array('label' => '🎯 Activiteit', 'suggested' => 'tour guide.svg'),
		);
		
		foreach ($icon_types as $icon_key => $icon_data):
		    $selected_icon = RBS_TRAVEL_Settings::GetSetting('icon_' . $icon_key, '');
		?>
		<tr>
		    <th scope="row" style="vertical-align: top; padding-top: 15px;">
			<label><?php echo $icon_data['label']; ?></label>
		    </th>
		    <td>
			<input type="hidden" name="rbstravel_settings[icon_<?php echo $icon_key; ?>]" value="<?php echo esc_attr($selected_icon); ?>" class="rbs-icon-value" data-type="<?php echo $icon_key; ?>" />
			
			<div class="rbs-icon-grid" data-type="<?php echo $icon_key; ?>">
			    <!-- None option -->
			    <div class="rbs-icon-option <?php echo empty($selected_icon) ? 'selected' : ''; ?>" data-icon="" title="Geen icon">
				<span style="color: #999; font-size: 16px;">✕</span>
			    </div>
			    
			    <?php foreach ($available_icons as $icon_file): ?>
			    <div class="rbs-icon-option <?php echo ($selected_icon === $icon_file) ? 'selected' : ''; ?>" data-icon="<?php echo esc_attr($icon_file); ?>" title="<?php echo esc_attr(pathinfo($icon_file, PATHINFO_FILENAME)); ?>">
				<img src="<?php echo esc_url($icons_url . $icon_file); ?>" alt="<?php echo esc_attr($icon_file); ?>" />
			    </div>
			    <?php endforeach; ?>
			</div>
			
			<?php if ($selected_icon): ?>
			<p class="description" style="margin-top: 8px;">Geselecteerd: <strong><?php echo esc_html(pathinfo($selected_icon, PATHINFO_FILENAME)); ?></strong></p>
			<?php endif; ?>
		    </td>
		</tr>
		<?php endforeach; ?>
		
		<tr>
		    <th scope="row" colspan="2">
			<hr>
			<h2 style="margin-top: 20px;"><?php _e('🔧 Advanced Settings', 'rbs-travel'); ?></h2>
		    </th>
		</tr>	
		
		<tr>
		    <th scope="row">
			<?php _e('Debug', 'rbs-travel'); ?>
		    </th>
		    <td>
			<input id="enable_debug" type="checkbox" name="rbstravel_settings[debug_enabled]" value="yes" <?php echo RBS_TRAVEL_Settings::GetSetting('debug_enabled', false) === 'yes' ? 'checked' : ''; ?>/>
			<label for="enable_debug"><?php _e('Enable debugging', 'rbs-travel'); ?></label>
		    </td>
		</tr>		
		
		<tr>
		    <th scope="row">
			<?php _e('Logging', 'rbs-travel'); ?>
		    </th>
		    <td>
			<input id="enable_logging" type="checkbox" name="rbstravel_settings[logging_enabled]" value="yes" <?php echo RBS_TRAVEL_Settings::GetSetting('logging_enabled', false) === 'yes' ? 'checked' : ''; ?>/>
			<label for="enable_logging"><?php _e('Enable logging', 'rbs-travel'); ?></label>
		    </td>
		</tr>		
		
		<tr>
		    <th scope="row">
			<?php //_e('Logging', 'rbs-travel'); ?>
		    </th>
		    <td>
			<label for="logging_directory"><?php _e('Logging directory', 'rbs-travel'); ?></label>
			<input id="logging_directory" type="text" class="text-field" name="rbstravel_settings[logging_directory]" value="<?php echo RBS_TRAVEL_Settings::GetSetting('logging_directory', ''); ?>" autocomplete="off" />
		    </td>
		</tr>		
		
	    </tbody>
	</table>
    

	<p class="submit">
	    <button type="submit" name="rbstravel_save_settings" id="submit" class="button button-primary" value="1">
		<?php _e('Save Settings', 'rbs-travel');?>
	    </button>
	</p>	
	
	<?php wp_nonce_field(); ?>
    
    </form>
    
</div>

<!-- API Credentials Accordion Styles -->
<style>
.rrp-api-set {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    margin-bottom: 10px;
}

.rrp-api-set-header {
    padding: 12px 15px;
    cursor: pointer;
    background: #f8f9fa;
    border-bottom: 1px solid #ccd0d4;
    transition: background 0.2s;
}

.rrp-api-set-header:hover {
    background: #e9ecef;
}

.rrp-api-set-header.active {
    background: #e9ecef;
}

.rrp-toggle-icon {
    transition: transform 0.2s;
    margin-right: 8px;
    color: #666;
}

.rrp-toggle-icon.active {
    transform: rotate(180deg);
}

.rrp-api-set-content {
    padding: 15px;
    background: #fff;
}

.rrp-api-set-content .form-table {
    margin: 0;
}

.rrp-api-set-content .form-table th {
    padding-left: 0;
}

.rrp-remove-set {
    color: #b32d2e;
}

.rrp-remove-set:hover {
    color: #fff;
    background: #b32d2e;
    border-color: #b32d2e;
}

.rrp-set-name-display {
    color: #1d2327;
    font-size: 14px;
}

/* Icon Grid Styles */
.rbs-icon-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    max-width: 600px;
    padding: 10px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.rbs-icon-option {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 2px solid #ddd;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.rbs-icon-option:hover {
    border-color: #2271b1;
    background: #f0f6fc;
    transform: scale(1.05);
}

.rbs-icon-option.selected {
    border-color: #2271b1;
    background: #e7f3ff;
    box-shadow: 0 0 0 2px rgba(34, 113, 177, 0.3);
}

.rbs-icon-option img {
    max-width: 28px;
    max-height: 28px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Icon Grid Selection
    $('.rbs-icon-option').on('click', function() {
        var $this = $(this);
        var $grid = $this.closest('.rbs-icon-grid');
        var type = $grid.data('type');
        var iconFile = $this.data('icon') || '';
        
        // Update hidden input - find by name attribute
        var $input = $('input[name="rbstravel_settings[icon_' + type + ']"]');
        $input.val(iconFile);
        
        // Debug log
        console.log('Icon selected:', type, iconFile, $input.length);
        
        // Update visual selection
        $grid.find('.rbs-icon-option').removeClass('selected');
        $this.addClass('selected');
        
        // Update description
        var $td = $grid.closest('td');
        var $desc = $td.find('p.description');
        if (iconFile) {
            var iconName = iconFile.replace('.svg', '');
            if ($desc.length) {
                $desc.html('Geselecteerd: <strong>' + iconName + '</strong>');
            } else {
                $grid.after('<p class="description" style="margin-top: 8px;">Geselecteerd: <strong>' + iconName + '</strong></p>');
            }
        } else {
            $desc.remove();
        }
    });
});
</script>