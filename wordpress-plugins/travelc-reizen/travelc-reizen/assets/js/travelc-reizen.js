"use strict";

/**
 * TravelC Reizen - Route Map (Leaflet/OpenStreetMap)
 * Renders a map with numbered markers and polyline route
 * Uses pre-geocoded coordinates from the API (no client-side geocoding needed)
 */

document.addEventListener('DOMContentLoaded', function() {
    if (typeof travelcMapDestinations === 'undefined' || !travelcMapDestinations || travelcMapDestinations.length < 2) {
        return;
    }

    var mapEl = document.getElementById('travelc-routemap');
    if (!mapEl) return;

    // Initialize map
    var firstDest = travelcMapDestinations[0];
    var map = L.map('travelc-routemap').setView([firstDest.lat, firstDest.lng], 6);

    // OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add numbered markers
    var markerGroup = L.featureGroup();
    var latlngs = [];

    travelcMapDestinations.forEach(function(dest, idx) {
        var icon = L.divIcon({
            html: '<div class="travelc-numbered-marker">' + (idx + 1) + '</div>',
            className: '',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        var marker = L.marker([dest.lat, dest.lng], { icon: icon });

        // Build popup
        var popup = '<div style="max-width:220px">';
        popup += '<h3 style="margin:0 0 4px;font-size:14px;font-weight:600">' + dest.name + '</h3>';
        if (dest.nights > 0) {
            popup += '<div style="font-size:12px;color:#666;margin-bottom:4px">' + dest.nights + ' nachten</div>';
        }
        if (dest.image) {
            popup += '<img src="' + dest.image + '" style="width:100%;max-height:100px;object-fit:cover;border-radius:6px;margin-bottom:4px" />';
        }
        if (dest.description) {
            popup += '<p style="font-size:12px;color:#555;margin:0">' + dest.description + '</p>';
        }
        popup += '</div>';

        marker.bindPopup(popup);
        marker.addTo(markerGroup);
        latlngs.push([dest.lat, dest.lng]);
    });

    markerGroup.addTo(map);

    // Add polyline route
    if (latlngs.length >= 2) {
        L.polyline(latlngs, { color: '#2A81CB', weight: 3, opacity: 0.7 }).addTo(map);
    }

    // Fit bounds to show all markers
    map.fitBounds(markerGroup.getBounds(), { padding: [30, 30] });
});

/**
 * TravelC Reizen - Quote Request Form
 * Sends offerte/info requests to Supabase Edge Function
 */
function tcSubmitQuote(requestType) {
    var name = document.getElementById('tcQuoteName');
    var email = document.getElementById('tcQuoteEmail');
    var phone = document.getElementById('tcQuotePhone');
    var date = document.getElementById('tcQuoteDate');
    var persons = document.getElementById('tcQuotePersons');
    var message = document.getElementById('tcQuoteMessage');
    var resultDiv = document.getElementById('tcQuoteResult');
    var btnQuote = document.getElementById('tcBtnQuote');
    var btnInfo = document.getElementById('tcBtnInfo');

    // Validate
    if (!name || !name.value.trim()) {
        tcShowResult(resultDiv, 'error', 'Vul je naam in.');
        return;
    }
    if (!email || !email.value.trim() || email.value.indexOf('@') === -1) {
        tcShowResult(resultDiv, 'error', 'Vul een geldig e-mailadres in.');
        return;
    }

    // Check config
    if (typeof travelcConfig === 'undefined' || !travelcConfig.brandId) {
        tcShowResult(resultDiv, 'error', 'Plugin configuratie ontbreekt. Neem contact op met de beheerder.');
        return;
    }

    // Disable buttons
    btnQuote.disabled = true;
    btnInfo.disabled = true;
    var origText = requestType === 'quote' ? btnQuote.textContent : btnInfo.textContent;
    if (requestType === 'quote') {
        btnQuote.textContent = 'Verzenden...';
    } else {
        btnInfo.textContent = 'Verzenden...';
    }

    // Get travel info from page
    var travelTitle = document.querySelector('.tc-hero-title, .tc-title, h1');
    var travelId = '';
    var tcIdEl = document.querySelector('[data-tc-id]');
    if (tcIdEl) travelId = tcIdEl.getAttribute('data-tc-id');

    var payload = {
        brand_id: travelcConfig.brandId,
        travel_id: travelId || '',
        travel_title: travelTitle ? travelTitle.textContent.trim() : '',
        travel_url: window.location.href,
        customer_name: name.value.trim(),
        customer_email: email.value.trim(),
        customer_phone: phone ? phone.value.trim() : '',
        departure_date: date ? date.value : '',
        number_of_persons: persons ? persons.value : '2',
        request_type: requestType,
        message: message ? message.value.trim() : '',
        source_url: window.location.href
    };

    fetch(travelcConfig.quoteEndpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            tcShowResult(resultDiv, 'success', data.message || 'Bedankt! We nemen zo snel mogelijk contact met je op.');
            // Reset form
            name.value = '';
            email.value = '';
            if (phone) phone.value = '';
            if (date) date.value = '';
            if (persons) persons.value = '2';
            if (message) message.value = '';
        } else {
            tcShowResult(resultDiv, 'error', data.error || 'Er is een fout opgetreden. Probeer het later opnieuw.');
        }
    })
    .catch(function(err) {
        console.error('Quote request error:', err);
        tcShowResult(resultDiv, 'error', 'Er is een fout opgetreden. Probeer het later opnieuw.');
    })
    .finally(function() {
        btnQuote.disabled = false;
        btnInfo.disabled = false;
        btnQuote.textContent = 'Offerte Aanvragen';
        btnInfo.textContent = 'Info Aanvragen';
    });
}

function tcShowResult(el, type, msg) {
    if (!el) return;
    el.style.display = 'block';
    el.style.background = type === 'success' ? '#d4edda' : '#f8d7da';
    el.style.color = type === 'success' ? '#155724' : '#721c24';
    el.style.border = '1px solid ' + (type === 'success' ? '#c3e6cb' : '#f5c6cb');
    el.textContent = msg;
    if (type === 'success') {
        setTimeout(function() { el.style.display = 'none'; }, 8000);
    }
}
