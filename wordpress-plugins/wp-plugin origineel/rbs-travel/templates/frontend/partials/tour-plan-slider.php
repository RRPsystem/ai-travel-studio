<?php
if (!defined('ABSPATH')) exit;

$all_items = array();

if (!empty($travel_meta_fields['travel_destinations'])) {
    foreach ($travel_meta_fields['travel_destinations'] as $destination) {
        if (!empty($destination['fromDay']) && $destination['fromDay'] > 0) {
            $all_items[] = array(
                'type' => 'destination',
                'fromDay' => $destination['fromDay'],
                'toDay' => $destination['toDay'],
                'data' => $destination
            );
        }
    }
}

if (!empty($travel_meta_fields['travel_hotels'])) {
    foreach ($travel_meta_fields['travel_hotels'] as $hotel) {
        $fromDay = isset($hotel['fromDay']) ? $hotel['fromDay'] : (isset($hotel['day']) ? $hotel['day'] : 0);
        $toDay = isset($hotel['toDay']) ? $hotel['toDay'] : ($fromDay + (isset($hotel['nights']) ? $hotel['nights'] : 0));

        if ($fromDay > 0) {
            $all_items[] = array(
                'type' => 'hotel',
                'fromDay' => $fromDay,
                'toDay' => $toDay,
                'data' => $hotel
            );
        }
    }
}

if (!empty($travel_meta_fields['travel_cruises'])) {
    foreach ($travel_meta_fields['travel_cruises'] as $cruise) {
        if (!empty($cruise['fromDay']) && $cruise['fromDay'] > 0) {
            $all_items[] = array(
                'type' => 'cruise',
                'fromDay' => $cruise['fromDay'],
                'toDay' => $cruise['toDay'],
                'data' => $cruise
            );
        }
    }
}

if (!empty($travel_meta_fields['travel_transports'])) {
    foreach ($travel_meta_fields['travel_transports'] as $transport) {
        $day = isset($transport['day']) ? intval($transport['day']) : 0;
        if ($day > 0) {
            $all_items[] = array(
                'type' => 'transport',
                'fromDay' => $day,
                'toDay' => $day,
                'data' => $transport
            );
        }
    }
}

if (!empty($travel_meta_fields['travel_transfers'])) {
    foreach ($travel_meta_fields['travel_transfers'] as $transfer) {
        $day = isset($transfer['day']) ? intval($transfer['day']) : 0;
        if ($day > 0) {
            $all_items[] = array(
                'type' => 'transfer',
                'fromDay' => $day,
                'toDay' => $day,
                'data' => $transfer
            );
        }
    }
}

usort($all_items, function($a, $b) {
    if ($a['fromDay'] !== $b['fromDay']) {
        return $a['fromDay'] - $b['fromDay'];
    }
    $type_priority = array('transport' => 1, 'transfer' => 2, 'destination' => 3, 'hotel' => 4, 'cruise' => 5);
    $a_priority = isset($type_priority[$a['type']]) ? $type_priority[$a['type']] : 99;
    $b_priority = isset($type_priority[$b['type']]) ? $type_priority[$b['type']] : 99;
    return $a_priority - $b_priority;
});

$grouped_days = array();
foreach ($all_items as $item) {
    $key = $item['fromDay'] . '-' . $item['toDay'];
    if (!isset($grouped_days[$key])) {
        $grouped_days[$key] = array(
            'fromDay' => $item['fromDay'],
            'toDay' => $item['toDay'],
            'items' => array()
        );
    }
    $grouped_days[$key]['items'][] = $item;
}

$grouped_days = array_values($grouped_days);

if (!function_exists('get_day_ordinal')) {
    function get_day_ordinal($number) {
        $suffixes = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        }
        return $number . $suffixes[$number % 10];
    }
}

?><style>
 .tour-plan-slider { margin-top: 0; }
 .tour-plan-slider h2 { font-size: 28px; font-weight: 700; color: var(--text, #374151); margin: 0 0 20px 0; }

 .rbs-itinerary-bg { background: #f3f4f6; padding: 30px 0; width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); overflow-x: hidden; }
 .rbs-itinerary-inner { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

 .rbs-itinerary-fullbleed { width: 100vw; max-width: 100vw; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); overflow-x: hidden; }
 .rbs-itinerary-fullbleed .rbs-itinerary-slider { border-radius: 0; border: 0; box-shadow: none; }
 .rbs-itinerary-slider { position: relative; border-radius: 18px; overflow: hidden; border: 1px solid rgba(17,24,39,0.08); background: #fff; box-shadow: 0 18px 45px rgba(16,24,40,0.10); }
 .rbs-itinerary-slide { display: none; }
 .rbs-itinerary-slide.active { display: block; }
 .rbs-itinerary-slide-inner { display: grid; grid-template-columns: 1.15fr 0.85fr; min-height: 420px; }
 .rbs-itinerary-left { position: relative; background: #111827; }
 .rbs-itinerary-right { padding: 28px 26px; background: #ffffff; }
 .rbs-itinerary-day { display: inline-flex; align-items: center; gap: 10px; font-weight: 800; color: #111827; font-size: 18px; margin-bottom: 14px; }
 .rbs-itinerary-day-pill { display: inline-flex; align-items: center; justify-content: center; height: 28px; padding: 0 12px; border-radius: 999px; background: rgba(0,0,0,0.06); font-size: 12px; font-weight: 700; }
 .rbs-itinerary-items { display: flex; flex-direction: column; gap: 14px; }
 .rbs-itinerary-item { border: 1px solid rgba(17,24,39,0.08); border-radius: 14px; padding: 14px 14px; background: #fff; }
 .rbs-itinerary-item-title { font-weight: 700; color: #111827; font-size: 15px; margin: 0 0 6px 0; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
 .rbs-itinerary-item-type { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 999px; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
 .rbs-itinerary-item-type img { width: 18px; height: 18px; object-fit: contain; }
 .rbs-itinerary-item-snippet { color: #4b5563; font-size: 13px; line-height: 1.5; margin: 0 0 10px 0; }
 .rbs-itinerary-item-actions { display: flex; gap: 10px; flex-wrap: wrap; }
 .rbs-itinerary-item-actions .btn-view-details { padding: 9px 14px; border-radius: 10px; }

 .rbs-itinerary-gallery { position: relative; width: 100%; height: 100%; }
 .rbs-itinerary-gallery-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 0.35s ease; }
 .rbs-itinerary-gallery-img.active { opacity: 1; }
 .rbs-itinerary-gallery-overlay { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.25) 40%, rgba(0,0,0,0.0) 70%); pointer-events: none; }
 .rbs-itinerary-gallery-controls { position: absolute; left: 18px; bottom: 18px; display: flex; gap: 10px; align-items: center; }
 .rbs-itinerary-gallery-btn { width: 42px; height: 42px; border-radius: 999px; border: 1px solid rgba(255,255,255,0.45); background: rgba(0,0,0,0.35); color: #fff; font-size: 22px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
 .rbs-itinerary-gallery-btn:hover { background: rgba(0,0,0,0.5); }
 .rbs-itinerary-gallery-count { color: #fff; font-weight: 700; font-size: 12px; background: rgba(0,0,0,0.35); border: 1px solid rgba(255,255,255,0.25); padding: 6px 10px; border-radius: 999px; }

 .rbs-itinerary-nav { position: absolute; top: 50%; transform: translateY(-50%); z-index: 5; width: 46px; height: 46px; border-radius: 999px; border: 1px solid rgba(17,24,39,0.12); background: rgba(255,255,255,0.95); box-shadow: 0 8px 20px rgba(16,24,40,0.15); cursor: pointer; font-size: 24px; display: inline-flex; align-items: center; justify-content: center; }
 .rbs-itinerary-nav:hover { background: #fff; }
 .rbs-itinerary-nav.prev { left: 16px; }
 .rbs-itinerary-nav.next { right: 16px; }

 .rbs-itinerary-steps-wrap { margin-top: 18px; }
 .rbs-itinerary-steps { display: flex; gap: 10px; align-items: center; overflow-x: auto; padding: 10px 12px; border-radius: 14px; background: rgba(255,255,255,0.85); border: 1px solid rgba(17,24,39,0.08); }
 .rbs-itinerary-step { border: 1px solid rgba(17,24,39,0.10); background: #ffffff; border-radius: 999px; padding: 8px 12px; cursor: pointer; font-weight: 700; font-size: 12px; color: #111827; white-space: nowrap; }
 .rbs-itinerary-step:hover { background: #f9fafb; }
 .rbs-itinerary-step.active { background: var(--primary, #4a6cf7); border-color: var(--primary, #4a6cf7); color: #ffffff; }

 @media (max-width: 980px) {
     .rbs-itinerary-slide-inner { grid-template-columns: 1fr; }
     .rbs-itinerary-left { min-height: 260px; }
     .rbs-itinerary-nav.prev { left: 12px; }
     .rbs-itinerary-nav.next { right: 12px; }
 }
</style>

<section class="tour-plan tour-plan-slider">
    <?php if (!empty($grouped_days)): ?>
        <div class="rbs-itinerary-bg">
            <div class="rbs-itinerary-inner">
                <h2>Dag-voor-dag Programma</h2>
            </div>

            <div class="rbs-itinerary-fullbleed">
            <div class="rbs-itinerary-slider" id="rbsItinerarySlider">
            <?php foreach ($grouped_days as $gIndex => $group): ?>
                <?php
                $day_label = 'Day ' . get_day_ordinal($group['fromDay']);
                if ($group['toDay'] > $group['fromDay']) {
                    $day_label .= ' - ' . get_day_ordinal($group['toDay']);
                }

                $gallery_images = array();
                foreach ($group['items'] as $item) {
                    if ($item['type'] === 'destination') {
                        $dest = $item['data'];
                        if (!empty($dest['imageUrls']) && is_array($dest['imageUrls'])) {
                            foreach ($dest['imageUrls'] as $u) {
                                if (!empty($u)) $gallery_images[] = $u;
                            }
                        }
                    } elseif ($item['type'] === 'hotel') {
                        $hotel = $item['data'];
                        $hotel_data = !empty($hotel['hotelData']) ? $hotel['hotelData'] : $hotel;
                        if (!empty($hotel_data['imageUrls']) && is_array($hotel_data['imageUrls'])) {
                            foreach ($hotel_data['imageUrls'] as $u) {
                                if (!empty($u)) $gallery_images[] = $u;
                            }
                        } elseif (!empty($hotel_data['images']) && is_array($hotel_data['images'])) {
                            foreach ($hotel_data['images'] as $img) {
                                $u = is_array($img) ? (!empty($img['url']) ? $img['url'] : '') : $img;
                                if (!empty($u)) $gallery_images[] = $u;
                            }
                        }
                    } elseif ($item['type'] === 'cruise') {
                        $cruise = $item['data'];
                        if (!empty($cruise['imageUrls']) && is_array($cruise['imageUrls'])) {
                            foreach ($cruise['imageUrls'] as $u) {
                                if (!empty($u)) $gallery_images[] = $u;
                            }
                        }
                    }
                }
                $gallery_images = array_values(array_unique($gallery_images));
                $gallery_images = array_slice($gallery_images, 0, 10);
                ?>

                <div class="rbs-itinerary-slide <?php echo $gIndex === 0 ? 'active' : ''; ?>" data-slide-index="<?php echo intval($gIndex); ?>">
                    <div class="rbs-itinerary-slide-inner">
                        <div class="rbs-itinerary-left">
                            <div class="rbs-itinerary-gallery" data-gallery-index="<?php echo intval($gIndex); ?>">
                                <?php if (!empty($gallery_images)): ?>
                                    <?php foreach ($gallery_images as $imgIndex => $imgUrl): ?>
                                        <img class="rbs-itinerary-gallery-img <?php echo $imgIndex === 0 ? 'active' : ''; ?>" data-img-index="<?php echo intval($imgIndex); ?>" src="<?php echo esc_url($imgUrl); ?>" alt="">
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <div class="rbs-itinerary-gallery-overlay"></div>
                                <?php if (count($gallery_images) > 1): ?>
                                    <div class="rbs-itinerary-gallery-controls">
                                        <button type="button" class="rbs-itinerary-gallery-btn" data-gallery-prev="<?php echo intval($gIndex); ?>">‹</button>
                                        <div class="rbs-itinerary-gallery-count" data-gallery-count="<?php echo intval($gIndex); ?>">1/<?php echo count($gallery_images); ?></div>
                                        <button type="button" class="rbs-itinerary-gallery-btn" data-gallery-next="<?php echo intval($gIndex); ?>">›</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="rbs-itinerary-right">
                            <div class="rbs-itinerary-day">
                                <span class="rbs-itinerary-day-pill"><?php echo esc_html($day_label); ?></span>
                            </div>

                            <div class="rbs-itinerary-items">
                                <?php foreach ($group['items'] as $item): ?>
                                    <?php if ($item['type'] === 'destination'): ?>
                                        <?php $dest = $item['data']; ?>
                                        <div class="rbs-itinerary-item">
                                            <div class="rbs-itinerary-item-title">
                                                <span class="rbs-itinerary-item-type">Bestemming</span>
                                                <?php echo esc_html($dest['name']); ?>
                                            </div>
                                            <?php if (!empty($dest['description'])): ?>
                                                <p class="rbs-itinerary-item-snippet"><?php echo wp_kses_post(substr($dest['description'], 0, 180)); ?>...</p>
                                            <?php endif; ?>
                                            <div class="rbs-itinerary-item-actions">
                                                <button class="btn-view-details" onclick="showDestinationDetail(this); return false;" data-dest='<?php echo htmlspecialchars(json_encode($dest), ENT_QUOTES, 'UTF-8'); ?>'>Bekijk Details →</button>
                                            </div>
                                        </div>

                                    <?php elseif ($item['type'] === 'hotel'): ?>
                                        <?php
                                        $hotel = $item['data'];
                                        $hotel_data = !empty($hotel['hotelData']) ? $hotel['hotelData'] : $hotel;
                                        ?>
                                        <div class="rbs-itinerary-item">
                                            <div class="rbs-itinerary-item-title">
                                                <span class="rbs-itinerary-item-type">Hotel</span>
                                                <?php echo esc_html($hotel_data['name'] ?? 'Hotel'); ?>
                                            </div>
                                            <?php if (!empty($hotel_data['description'])): ?>
                                                <p class="rbs-itinerary-item-snippet"><?php echo wp_kses_post(substr($hotel_data['description'], 0, 180)); ?>...</p>
                                            <?php endif; ?>
                                            <div class="rbs-itinerary-item-actions">
                                                <button class="btn-view-details" onclick="showHotelDetail(this); return false;" data-hotel='<?php echo htmlspecialchars(json_encode($hotel_data), ENT_QUOTES, 'UTF-8'); ?>'>Bekijk Hotel Details →</button>
                                            </div>
                                        </div>

                                    <?php elseif ($item['type'] === 'cruise'): ?>
                                        <?php $cruise = $item['data']; ?>
                                        <div class="rbs-itinerary-item">
                                            <div class="rbs-itinerary-item-title">
                                                <span class="rbs-itinerary-item-type">Cruise</span>
                                                <?php echo esc_html($cruise['name'] ?? 'Cruise'); ?>
                                            </div>
                                            <p class="rbs-itinerary-item-snippet">Geniet van een prachtige cruise.</p>
                                            <div class="rbs-itinerary-item-actions">
                                                <?php
                                                $cruise_safe = array(
                                                    'name' => isset($cruise['name']) ? $cruise['name'] : 'Cruise',
                                                    'images' => array(),
                                                    'destinations' => isset($cruise['destinations']) ? $cruise['destinations'] : array(),
                                                    'embarkDate' => isset($cruise['embarkDate']) ? $cruise['embarkDate'] : '',
                                                    'disembarkDate' => isset($cruise['disembarkDate']) ? $cruise['disembarkDate'] : '',
                                                    'nights' => isset($cruise['nights']) ? $cruise['nights'] : '',
                                                    'cabin' => isset($cruise['cabin']) ? $cruise['cabin'] : '',
                                                    'category' => isset($cruise['category']) ? $cruise['category'] : (isset($cruise['selectedCategory']) ? $cruise['selectedCategory'] : ''),
                                                    'id' => isset($cruise['id']) ? $cruise['id'] : '',
                                                    'itineraryUrl' => isset($cruise['itineraryUrl']) ? $cruise['itineraryUrl'] : '',
                                                    'embarkPort' => isset($cruise['embarkPort']) ? $cruise['embarkPort'] : (isset($cruise['originPort']) ? $cruise['originPort'] : ''),
                                                    'disembarkPort' => isset($cruise['disembarkPort']) ? $cruise['disembarkPort'] : '',
                                                    'cruiseLine' => isset($cruise['cruiseLine']) ? $cruise['cruiseLine'] : '',
                                                    'ship' => isset($cruise['ship']) ? $cruise['ship'] : (isset($cruise['shipName']) ? $cruise['shipName'] : ''),
                                                    'shipName' => isset($cruise['shipName']) ? $cruise['shipName'] : '',
                                                );
                                                if (!empty($cruise['images']) && is_array($cruise['images'])) {
                                                    foreach ($cruise['images'] as $img) {
                                                        if (is_string($img)) {
                                                            $cruise_safe['images'][] = $img;
                                                        } elseif (is_array($img) && !empty($img['url'])) {
                                                            $cruise_safe['images'][] = $img['url'];
                                                        }
                                                    }
                                                }
                                                if (!empty($cruise['imageUrls']) && is_array($cruise['imageUrls'])) {
                                                    foreach ($cruise['imageUrls'] as $img) {
                                                        if (is_string($img)) {
                                                            $cruise_safe['images'][] = $img;
                                                        }
                                                    }
                                                }
                                                ?>
                                                <button class="btn-view-details" onclick="showCruiseDetail(this); return false;" data-cruise='<?php echo htmlspecialchars(json_encode($cruise_safe), ENT_QUOTES, 'UTF-8'); ?>'>Bekijk Cruise Details →</button>
                                            </div>
                                        </div>

                                    <?php elseif ($item['type'] === 'transport'): ?>
                                        <?php
                                        $transport = $item['data'];
                                        $transport_type = isset($transport['type']) ? strtolower($transport['type']) : 'flight';
                                        $type_label = 'Vlucht';
                                        if ($transport_type === 'train') $type_label = 'Trein';
                                        elseif ($transport_type === 'bus') $type_label = 'Bus';
                                        elseif ($transport_type === 'ferry') $type_label = 'Ferry';

                                        $from = $transport['originDestinationCode'] ?? $transport['from'] ?? $transport['origin'] ?? '';
                                        $to = $transport['targetDestinationCode'] ?? $transport['to'] ?? $transport['destination'] ?? '';
                                        $company = trim(($transport['company'] ?? '') . ' ' . ($transport['transportNumber'] ?? ''));
                                        $depTime = $transport['departureTime'] ?? '';
                                        $arrTime = $transport['arrivalTime'] ?? '';
                                        $depDate = $transport['departureDate'] ?? '';
                                        ?>
                                        <div class="rbs-itinerary-item">
                                            <div class="rbs-itinerary-item-title">
                                                <span class="rbs-itinerary-item-type"><?php echo esc_html($type_label); ?></span>
                                                <?php echo esc_html($company ?: $type_label); ?>
                                            </div>
                                            <p class="rbs-itinerary-item-snippet">
                                                <?php if ($from || $to): ?><?php echo esc_html(trim($from . ' → ' . $to, ' →')); ?><?php endif; ?>
                                                <?php if ($depTime || $arrTime): ?> • <?php echo esc_html(trim($depTime . ' - ' . $arrTime, ' -')); ?><?php endif; ?>
                                                <?php if ($depDate): ?> • <?php echo esc_html(date('d M Y', strtotime($depDate))); ?><?php endif; ?>
                                            </p>
                                        </div>

                                    <?php elseif ($item['type'] === 'transfer'): ?>
                                        <?php
                                        $transfer = $item['data'];
                                        $from = $transfer['from'] ?? $transfer['origin'] ?? 'Vertrek';
                                        $to = $transfer['to'] ?? $transfer['destination'] ?? 'Aankomst';
                                        $tType = $transfer['type'] ?? '';
                                        $dur = $transfer['duration'] ?? '';
                                        ?>
                                        <div class="rbs-itinerary-item">
                                            <div class="rbs-itinerary-item-title">
                                                <span class="rbs-itinerary-item-type">Transfer</span>
                                                <?php echo esc_html(trim($from . ' → ' . $to)); ?>
                                            </div>
                                            <p class="rbs-itinerary-item-snippet">
                                                <?php if ($tType): ?><?php echo esc_html($tType); ?><?php endif; ?>
                                                <?php if ($dur): ?><?php echo $tType ? ' • ' : ''; ?><?php echo esc_html($dur); ?><?php endif; ?>
                                            </p>
                                        </div>

                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (count($grouped_days) > 1): ?>
                <button type="button" class="rbs-itinerary-nav prev" id="rbsItineraryPrev">‹</button>
                <button type="button" class="rbs-itinerary-nav next" id="rbsItineraryNext">›</button>
            <?php endif; ?>
        </div>
        </div>

        <?php if (count($grouped_days) > 1): ?>
            <div class="rbs-itinerary-inner rbs-itinerary-steps-wrap">
                <div class="rbs-itinerary-steps" id="rbsItinerarySteps" aria-label="Dagen navigatie">
                    <?php foreach ($grouped_days as $gIndex => $group): ?>
                        <?php
                        $range_label = 'Dag ' . intval($group['fromDay']);
                        if (intval($group['toDay']) > intval($group['fromDay'])) {
                            $range_label .= '-' . intval($group['toDay']);
                        }
                        ?>
                        <button type="button" class="rbs-itinerary-step <?php echo $gIndex === 0 ? 'active' : ''; ?>" data-step-index="<?php echo intval($gIndex); ?>">
                            <?php echo esc_html($range_label); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <script>
        (function() {
            var slider = document.getElementById('rbsItinerarySlider');
            if (!slider) return;

            var slides = slider.querySelectorAll('.rbs-itinerary-slide');
            var stepsWrap = document.getElementById('rbsItinerarySteps');
            var steps = stepsWrap ? stepsWrap.querySelectorAll('.rbs-itinerary-step') : [];
            var prevBtn = document.getElementById('rbsItineraryPrev');
            var nextBtn = document.getElementById('rbsItineraryNext');
            var index = 0;

            function show(i) {
                if (!slides.length) return;
                index = (i + slides.length) % slides.length;
                slides.forEach(function(s, si) { s.classList.toggle('active', si === index); });
                if (steps && steps.length) {
                    steps.forEach(function(d, di) { d.classList.toggle('active', di === index); });
                }
            }

            if (prevBtn) prevBtn.addEventListener('click', function() { show(index - 1); });
            if (nextBtn) nextBtn.addEventListener('click', function() { show(index + 1); });
            if (steps && steps.length) {
                steps.forEach(function(d) {
                    d.addEventListener('click', function() {
                        var di = parseInt(this.getAttribute('data-step-index') || '0', 10);
                        show(di);
                    });
                });
            }

            function setGallery(galleryIndex, imgIndex) {
                var g = slider.querySelector('.rbs-itinerary-gallery[data-gallery-index="' + galleryIndex + '"]');
                if (!g) return;
                var imgs = g.querySelectorAll('.rbs-itinerary-gallery-img');
                if (!imgs.length) return;
                var next = (imgIndex + imgs.length) % imgs.length;
                imgs.forEach(function(img, ii) { img.classList.toggle('active', ii === next); });
                var counter = g.querySelector('[data-gallery-count="' + galleryIndex + '"]');
                if (counter) counter.textContent = (next + 1) + '/' + imgs.length;
                g.setAttribute('data-active-index', String(next));
            }

            slider.querySelectorAll('[data-gallery-prev]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var gi = parseInt(this.getAttribute('data-gallery-prev'), 10);
                    var g = slider.querySelector('.rbs-itinerary-gallery[data-gallery-index="' + gi + '"]');
                    var cur = g ? parseInt(g.getAttribute('data-active-index') || '0', 10) : 0;
                    setGallery(gi, cur - 1);
                });
            });

            slider.querySelectorAll('[data-gallery-next]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var gi = parseInt(this.getAttribute('data-gallery-next'), 10);
                    var g = slider.querySelector('.rbs-itinerary-gallery[data-gallery-index="' + gi + '"]');
                    var cur = g ? parseInt(g.getAttribute('data-active-index') || '0', 10) : 0;
                    setGallery(gi, cur + 1);
                });
            });

            show(0);
        })();
        </script>
        </div>
    <?php endif; ?>
</section>
