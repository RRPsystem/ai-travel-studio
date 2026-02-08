<?php
if (!defined('ABSPATH')) exit;

global $travel_meta_fields;
$travel_meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields();
$vars = ['travel_meta_fields' => $travel_meta_fields];

rbstravel_get_header();
?>

<div id="rbstravel-content" class="rbstravel-wrapper">
    <div class="header-image-hero" style="background-image: url('<?php echo $travel_meta_fields['travel_destinations'][0]['imageUrls'][0]; ?>');">
        <div class="header-overlay">
            <h1><?php echo $travel_meta_fields['travel_destinations'][0]['name']; ?></h1>
            <h2><?php echo $vars['travel_meta_fields']['travel_large_title']; ?></h2>
        </div>
    </div>

    <div class="description-full">
        <div class="description-content">
            <?php echo the_content(); ?>
        </div>
    </div>

    <!-- DEBUG: Travel Data Overview -->
    <div class="debug-data-overview" style="background: #f0f0f0; padding: 20px; margin: 20px; border: 3px solid #ff6b6b; border-radius: 8px;">
        <h2 style="color: #ff6b6b; cursor: pointer;" onclick="document.getElementById('debug-details').style.display = document.getElementById('debug-details').style.display === 'none' ? 'block' : 'none';">
            🔍 DEBUG: Travel Data Overview (Click to expand)
        </h2>
        <div id="debug-details" style="display: block;">
            
            <div style="background: white; padding: 15px; margin: 10px 0; border-radius: 5px;">
                <h3 style="color: #2c3e50;">📊 Data Summary</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="background: #ecf0f1;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #bdc3c7;">Field</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #bdc3c7;">Count/Value</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #bdc3c7;">Status</th>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;"><strong>Destinations</strong></td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php echo isset($travel_meta_fields['travel_destinations']) ? count($travel_meta_fields['travel_destinations']) : 0; ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php echo !empty($travel_meta_fields['travel_destinations']) ? '✅' : '❌'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;"><strong>🚢 Cruises</strong></td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php echo isset($travel_meta_fields['travel_cruises']) ? count($travel_meta_fields['travel_cruises']) : 0; ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php echo !empty($travel_meta_fields['travel_cruises']) ? '✅' : '❌'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;"><strong>Hotels</strong></td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php echo isset($travel_meta_fields['travel_hotels']) ? count($travel_meta_fields['travel_hotels']) : 0; ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php echo !empty($travel_meta_fields['travel_hotels']) ? '✅' : '❌'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;"><strong>Flights</strong></td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php echo isset($travel_meta_fields['travel_transports']) ? count($travel_meta_fields['travel_transports']) : 0; ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php echo !empty($travel_meta_fields['travel_transports']) ? '✅' : '❌'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;"><strong>Cars</strong></td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php echo isset($travel_meta_fields['travel_cars']) ? count($travel_meta_fields['travel_cars']) : 0; ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php echo !empty($travel_meta_fields['travel_cars']) ? '✅' : '❌'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;"><strong>Images (Destinations)</strong></td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php 
                            $total_images = 0;
                            if (isset($travel_meta_fields['travel_destinations'])) {
                                foreach ($travel_meta_fields['travel_destinations'] as $dest) {
                                    if (isset($dest['imageUrls'])) {
                                        $total_images += count($dest['imageUrls']);
                                    }
                                }
                            }
                            echo $total_images;
                            ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #bdc3c7;">
                            <?php echo $total_images > 0 ? '✅' : '❌'; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <?php if (!empty($travel_meta_fields['travel_cruises'])): ?>
            <div style="background: #e3f2fd; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 5px solid #2196f3;">
                <h3 style="color: #1976d2;">🚢 Cruise Details</h3>
                <?php foreach ($travel_meta_fields['travel_cruises'] as $idx => $cruise): ?>
                    <div style="background: white; padding: 10px; margin: 10px 0; border-radius: 3px;">
                        <h4>Cruise #<?php echo $idx + 1; ?></h4>
                        <pre style="background: #f5f5f5; padding: 10px; overflow: auto; max-height: 300px;"><?php print_r($cruise); ?></pre>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($travel_meta_fields['travel_hotels'])): ?>
            <div style="background: #fff3e0; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 5px solid #ff9800;">
                <h3 style="color: #f57c00;">🏨 Hotel Details</h3>
                <?php foreach ($travel_meta_fields['travel_hotels'] as $idx => $hotel): ?>
                    <div style="background: white; padding: 10px; margin: 10px 0; border-radius: 3px;">
                        <h4>Hotel #<?php echo $idx + 1; ?></h4>
                        <pre style="background: #f5f5f5; padding: 10px; overflow: auto; max-height: 300px;"><?php print_r($hotel); ?></pre>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div style="background: #e8f5e9; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 5px solid #4caf50;">
                <h3 style="color: #388e3c;">📍 Destination Details</h3>
                <?php if (!empty($travel_meta_fields['travel_destinations'])): ?>
                    <?php foreach ($travel_meta_fields['travel_destinations'] as $idx => $dest): ?>
                        <div style="background: white; padding: 10px; margin: 10px 0; border-radius: 3px;">
                            <h4>Destination #<?php echo $idx + 1; ?>: <?php echo isset($dest['name']) ? $dest['name'] : 'Unknown'; ?></h4>
                            <p><strong>Days:</strong> <?php echo isset($dest['fromDay']) ? $dest['fromDay'] : '?'; ?> to <?php echo isset($dest['toDay']) ? $dest['toDay'] : '?'; ?></p>
                            <p><strong>Images:</strong> <?php echo isset($dest['imageUrls']) ? count($dest['imageUrls']) : 0; ?> available</p>
                            <pre style="background: #f5f5f5; padding: 10px; overflow: auto; max-height: 300px;"><?php print_r($dest); ?></pre>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div style="background: #fce4ec; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 5px solid #e91e63;">
                <h3 style="color: #c2185b;">🔧 All Available Meta Fields</h3>
                <pre style="background: white; padding: 10px; overflow: auto; max-height: 400px;"><?php print_r(array_keys($travel_meta_fields)); ?></pre>
            </div>

            <?php 
            // Check for raw travel details (stored when cruises are missing)
            $raw_details = get_post_meta(get_the_ID(), 'travel_details_raw', true);
            if ($raw_details): 
            ?>
            <div style="background: #ffebee; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 5px solid #f44336;">
                <h3 style="color: #c62828;">🚨 RAW API Response (Cruise data missing - stored for debugging)</h3>
                <p><strong>Available top-level keys:</strong></p>
                <pre style="background: white; padding: 10px; overflow: auto; max-height: 200px;"><?php print_r(array_keys($raw_details)); ?></pre>
                <p><strong>Full raw data:</strong></p>
                <pre style="background: white; padding: 10px; overflow: auto; max-height: 500px;"><?php print_r($raw_details); ?></pre>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="main-content">
        <div class="left-content">

            <!-- Itinerary Side by Side -->
            <div class="daily-itinerary-wrapper">
                <h2 class="daily-title">Daily Itinerary</h2>
                <div class="itinerary-grid">
                    <?php 
                    // Combine destinations and cruises into single itinerary
                    $itinerary_items = array();
                    
                    // Add destinations
                    foreach ($travel_meta_fields['travel_destinations'] as $destination) {
                        $itinerary_items[] = array(
                            'type' => 'destination',
                            'fromDay' => $destination['fromDay'],
                            'toDay' => $destination['toDay'],
                            'data' => $destination
                        );
                    }
                    
                    // Add cruises as main items
                    if (!empty($travel_meta_fields['travel_cruises'])) {
                        foreach ($travel_meta_fields['travel_cruises'] as $cruise) {
                            $itinerary_items[] = array(
                                'type' => 'cruise',
                                'fromDay' => isset($cruise['fromDay']) ? $cruise['fromDay'] : 0,
                                'toDay' => isset($cruise['toDay']) ? $cruise['toDay'] : 0,
                                'data' => $cruise
                            );
                        }
                    }
                    
                    // Sort by fromDay
                    usort($itinerary_items, function($a, $b) {
                        return $a['fromDay'] - $b['fromDay'];
                    });
                    
                    // Display itinerary
                    foreach ($itinerary_items as $item):
                        if ($item['type'] == 'destination'):
                            $destination = $item['data'];
                    ?>
                        <div class="itinerary-card-horizontal">
                            <?php if (!empty($destination['imageUrls'][1])): ?>
                                <div class="itinerary-image-small" style="background-image: url('<?php echo $destination['imageUrls'][1]; ?>');"></div>
                            <?php endif; ?>
                            <div class="itinerary-text">
                                <h3><?php printf('Day %s to %s: %s', $destination['fromDay'], $destination['toDay'], $destination['name']); ?></h3>
                                <p><?php echo strip_tags($destination['description']); ?></p>
                            </div>
                        </div>
                    <?php 
                        elseif ($item['type'] == 'cruise'):
                            $cruise = $item['data'];
                    ?>
                        <div class="itinerary-card-horizontal cruise-main">
                            <?php 
                            $cruise_image = '';
                            if (isset($cruise['cruiseData']['imageUrls'][0])) {
                                $cruise_image = $cruise['cruiseData']['imageUrls'][0];
                            } elseif (isset($cruise['imageUrl'])) {
                                $cruise_image = $cruise['imageUrl'];
                            }
                            if (!empty($cruise_image)): 
                            ?>
                                <div class="itinerary-image-small" style="background-image: url('<?php echo $cruise_image; ?>');"></div>
                            <?php endif; ?>
                            <div class="itinerary-text">
                                <h3>🚢 <?php 
                                    printf('Day %s to %s: ', $cruise['fromDay'], $cruise['toDay']);
                                    echo isset($cruise['cruiseData']['name']) ? $cruise['cruiseData']['name'] : 'Cruise';
                                ?></h3>
                                <p>
                                    <?php if (isset($cruise['cruiseData']['shipName'])): ?>
                                        <strong>Ship:</strong> <?php echo $cruise['cruiseData']['shipName']; ?><br>
                                    <?php endif; ?>
                                    <?php if (isset($cruise['embarkLocationName'])): ?>
                                        <strong>Departs from:</strong> <?php echo $cruise['embarkLocationName']; ?><br>
                                    <?php endif; ?>
                                    <?php if (isset($cruise['duration'])): ?>
                                        <strong>Duration:</strong> <?php echo $cruise['duration']; ?> nights<br>
                                    <?php endif; ?>
                                    <?php if (isset($cruise['cruiseData']['description'])): ?>
                                        <?php echo strip_tags($cruise['cruiseData']['description']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        
                        <?php 
                        // Show cruise ports as sub-items
                        if (isset($cruise['cruiseData']['ports']) && is_array($cruise['cruiseData']['ports'])):
                            foreach ($cruise['cruiseData']['ports'] as $port):
                        ?>
                            <div class="itinerary-card-horizontal cruise-port cruise-sub-item">
                                <?php if (!empty($port['imageUrl'])): ?>
                                    <div class="itinerary-image-small" style="background-image: url('<?php echo $port['imageUrl']; ?>');"></div>
                                <?php endif; ?>
                                <div class="itinerary-text">
                                    <h3>⚓ <?php echo $port['name']; ?></h3>
                                    <p>
                                        <?php if (isset($port['arrivalDate'])): ?>
                                            <strong>Arrival:</strong> <?php echo date('M d, Y H:i', strtotime($port['arrivalDate'])); ?><br>
                                        <?php endif; ?>
                                        <?php if (isset($port['departureDate'])): ?>
                                            <strong>Departure:</strong> <?php echo date('M d, Y H:i', strtotime($port['departureDate'])); ?>
                                        <?php endif; ?>
                                        <?php if (isset($port['description'])): ?>
                                            <br><?php echo strip_tags($port['description']); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>

        </div>

        <!-- Sidebar (Same as before) -->
        <div class="right-sidebar">
            <div class="sidebar-card">
                <div class="price-block">💶 <strong>From:</strong><br> EURO <?php echo $vars['travel_meta_fields']['travel_price_per_person']; ?></div>
                <div class="duration-block">⏱️ <strong>Duration:</strong><br> <?php echo $vars['travel_meta_fields']['travel_number_of_nights']; ?> days</div>
                <div class="type-block">🌍 <strong>Tour Type:</strong><br> Vliegreis</div>
            </div>

            <div class="sidebar-card booking">
                <h3>📩 Book This Trip</h3>
                <?php rbstravel_get_contact_form(); ?>
            </div>

            <div class="sidebar-card transport-info">
                <h3>✈️ Flights</h3>
                <?php foreach ($travel_meta_fields['travel_transports'] as $transport): ?>
                    <div class="info-block">
                        <strong><?php echo $transport['company'] . ' ' . $transport['transportNumber']; ?></strong><br>
                        <?php echo $transport['originDestinationCode']; ?> → <?php echo $transport['targetDestinationCode']; ?><br>
                        <?php echo $transport['departureTime']; ?> → <?php echo $transport['arrivalTime']; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($travel_meta_fields['travel_cars'])): ?>
                <div class="sidebar-card transport-info">
                    <h3>🚗 Car Rental</h3>
                    <?php foreach ($travel_meta_fields['travel_cars'] as $car): ?>
                        <div class="info-block">
                            <strong><?php echo $car['product']; ?></strong><br>
                            Pickup: <?php echo $car['pickupDate'] . ' ' . $car['pickupTime']; ?> (<?php echo $car['pickupLocation']; ?>)<br>
                            Dropoff: <?php echo $car['dropoffDate'] . ' ' . $car['dropoffTime']; ?> (<?php echo $car['dropoffLocation']; ?>)
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($travel_meta_fields['travel_cruises'])): ?>
                <div class="sidebar-card transport-info cruise-info">
                    <h3>🚢 Cruise</h3>
                    <?php foreach ($travel_meta_fields['travel_cruises'] as $cruise): ?>
                        <div class="info-block">
                            <strong><?php echo isset($cruise['cruiseData']['name']) ? $cruise['cruiseData']['name'] : 'Cruise'; ?></strong><br>
                            <?php if (isset($cruise['cruiseData']['shipName'])): ?>
                                Ship: <?php echo $cruise['cruiseData']['shipName']; ?><br>
                            <?php endif; ?>
                            <?php if (isset($cruise['embarkDate']) && isset($cruise['disembarkDate'])): ?>
                                <?php echo date('M d, Y', strtotime($cruise['embarkDate'])); ?> → <?php echo date('M d, Y', strtotime($cruise['disembarkDate'])); ?><br>
                            <?php endif; ?>
                            <?php if (isset($cruise['duration'])): ?>
                                Duration: <?php echo $cruise['duration']; ?> nights<br>
                            <?php endif; ?>
                            <?php if (isset($cruise['embarkLocationName'])): ?>
                                From: <?php echo $cruise['embarkLocationName']; ?><br>
                            <?php endif; ?>
                            <?php if (isset($cruise['disembarkLocationName'])): ?>
                                To: <?php echo $cruise['disembarkLocationName']; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .header-image-hero {
        height: 380px;
        background-size: cover;
        background-position: center;
        position: relative;
    }
    .header-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.45);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: white;
    }
    .header-overlay h1 {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }
    .header-overlay h2 {
        font-size: 1.5rem;
    }

    .description-full {
        background: #f9f9f9;
        padding: 3rem 2rem;
    }
    .description-content {
        max-width: 900px;
        margin: auto;
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }

    .main-content {
        display: flex;
        flex-wrap: wrap;
        margin: 2rem 0;
        gap: 2rem;
        padding: 0 2rem;
    }
    .left-content {
        flex: 1 1 70%;
    }
    .right-sidebar {
        flex: 1 1 25%;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .sidebar-card {
        background: #2d3142;
        color: #fff;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .sidebar-card h3 {
        margin-top: 0;
    }
    .sidebar-card .price-block,
    .sidebar-card .duration-block,
    .sidebar-card .type-block {
        margin-bottom: 1rem;
        font-size: 1rem;
        line-height: 1.5;
    }
    .sidebar-card .info-block {
        background: #445;
        border-left: 4px solid #55c;
        padding: 0.75rem;
        margin-bottom: 1rem;
        border-radius: 5px;
        font-size: 0.95rem;
    }

    /* New side-by-side layout */
    .itinerary-grid {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    .itinerary-card-horizontal {
        display: flex;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        transition: transform 0.2s ease;
    }
    .itinerary-card-horizontal:hover {
        transform: translateY(-4px);
    }
    .itinerary-image-small {
        width: 35%;
        min-height: 180px;
        background-size: cover;
        background-position: center;
    }
    .itinerary-text {
        padding: 1.5rem;
        flex: 1;
    }
    .itinerary-text h3 {
        font-size: 1.4rem;
        font-weight: bold;
        color: #0077cc;
        margin-bottom: 0.75rem;
    }
    .itinerary-text p {
        font-size: 1rem;
        line-height: 1.6;
        color: #555;
    }
    
    /* Cruise specific styles */
    .cruise-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .cruise-info h3 {
        color: white !important;
    }
    .cruise-info .info-block {
        background: rgba(255,255,255,0.15);
        border-left-color: white;
        color: white;
    }
    
    /* Cruise main item in itinerary */
    .itinerary-card-horizontal.cruise-main {
        border-left: 8px solid #667eea;
        background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.15);
    }
    .itinerary-card-horizontal.cruise-main .itinerary-text h3 {
        color: #667eea;
        font-size: 1.6rem;
    }
    
    /* Cruise port sub-items */
    .itinerary-card-horizontal.cruise-sub-item {
        margin-left: 3rem;
        border-left: 4px solid #a5b4fc;
        background: #f8f9ff;
    }
    .itinerary-card-horizontal.cruise-sub-item .itinerary-text h3 {
        color: #4f46e5;
        font-size: 1.2rem;
    }
</style>

<?php rbstravel_get_footer(); ?>
