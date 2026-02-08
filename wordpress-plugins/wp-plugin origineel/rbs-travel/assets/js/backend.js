"use strict";

// console.log('backend.js');

function handleClosePopupWindow() {
    const elements = document.querySelectorAll('.rbstravel-popup-window__close');
    elements.forEach(function(el) {
        el.addEventListener('click', function(ev) {
            let target = el.dataset.target;            
            document.getElementById('rbstravel-travel-ajax-response').style.display = 'none';
            document.getElementById(target).style.display = 'none';
        });  
    });
  
}

function handleCloseImportError() {
    let elements = document.querySelectorAll('.rbstravel-import-error-close');
    for(let i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', function(ev) {
            ev.target.parentElement.style.display = 'none';
        });
    }
}

function showInfo(data) {
    let el_window_content = document.getElementById('rbstravel-travel-info__content');
    el_window_content.textContent = JSON.stringify(data.result, null, 2);
    document.getElementById('rbstravel-travel-info').style.display = 'block';
    document.getElementById('rbstravel-travel-ajax-response').style.display = 'block';
    handleClosePopupWindow();
}

function showDetails(data) {
    let el_window_content = document.getElementById('rbstravel-travel-details__content');
    el_window_content.textContent = JSON.stringify(data.result, null, 2);
    document.getElementById('rbstravel-travel-details').style.display = 'block';
    document.getElementById('rbstravel-travel-ajax-response').style.display = 'block';
    handleClosePopupWindow();
}

function doneImportSingle(data) {
    //console.log('doneImportSingle', data);
    document.getElementById('rbstravel-import-busy-' + data.travel_id).style.display = 'none';
    if (data.error === true) {
        let el_error = document.getElementById('rbstravel-import-error-' + data.travel_id);
        el_error.querySelector('p').textContent = data.message;
        el_error.style.display = 'block';
    }
    location.reload();
}


function actionViewInfo(travel_id) {
    fetch(rbsTravel.ajax_url, {
	method: 'POST',
	credentials: 'same-origin',
	headers: {
	    'Content-Type': 'application/x-www-form-urlencoded',
	    'Cache-Control': 'no-cache'
	},
	body: new URLSearchParams({
	    action: 'rbstravel_view_info',
	    travel_id: travel_id

	})
    })    
    .then(resp => resp.json())
    .then(data => showInfo(data));
}


function actionViewDetails(travel_id) {
    fetch(rbsTravel.ajax_url, {
	method: 'POST',
	credentials: 'same-origin',
	headers: {
	    'Content-Type': 'application/x-www-form-urlencoded',
	    'Cache-Control': 'no-cache'
	},
	body: new URLSearchParams({
	    action: 'rbstravel_view_details',
	    travel_id: travel_id

	})
    })
    
    .then(resp => resp.json())
    .then(data => showDetails(data));
}

function actionImportSingle(travel_id) {
    document.getElementById('rbstravel-import-busy-' + travel_id).style.display = 'inline-block';
    fetch(rbsTravel.ajax_url, {
	method: 'POST',
	credentials: 'same-origin',
	headers: {
	    'Content-Type': 'application/x-www-form-urlencoded',
	    'Cache-Control': 'no-cache'
	},
	body: new URLSearchParams({
	    action: 'rbstravel_import_single',
	    travel_id: travel_id

	})
    })
    
    .then(resp => resp.json())
    .then(data => doneImportSingle(data));
}


// !! Preparation change for 'overwrite/cancel/new' popup, for when idea already exists:
// function actionImportSingle(travel_id) {
//     const busyIndicator = document.getElementById('rbstravel-import-busy-' + travel_id);
//     busyIndicator.style.display = 'inline-block';

//     fetch(rbsTravel.ajax_url, {
//         method: 'POST',
//         credentials: 'same-origin',
//         headers: {
//             'Content-Type': 'application/x-www-form-urlencoded',
//             'Cache-Control': 'no-cache'
//         },
//         body: new URLSearchParams({
//             action: 'rbstravel_import_single',
//             travel_id: travel_id
//         })
//     })
//     .then(resp => resp.json())
//     .then(data => {
//         if (data.post_exists) {
//             // Show popup with options
//             const userChoice = confirm(
//                 `A post with ID ${data.existing_post_id} already exists. Do you want to:
//                 \nCancel - Cancel the import
//                 \nOverwrite - Overwrite the existing post
//                 \nCreate New - Create a new post`
//             );

//             if (userChoice === true) {
//                 // User chooses to overwrite or create a new post
//                 handleUserChoice(travel_id, data.existing_post_id, 'overwrite_or_new');
//             } else {
//                 // User chooses to cancel
//                 busyIndicator.style.display = 'none';
//             }
//         } else {
//             // No post exists, continue normal
//             doneImportSingle(data);
//         }
//     });
// }

// function handleUserChoice(travel_id, post_id, action) {
//     fetch(rbsTravel.ajax_url, {
//         method: 'POST',
//         credentials: 'same-origin',
//         headers: {
//             'Content-Type': 'application/x-www-form-urlencoded',
//             'Cache-Control': 'no-cache'
//         },
//         body: new URLSearchParams({
//             action: 'rbstravel_handle_user_choice',
//             travel_id: travel_id,
//             post_id: post_id,
//             user_action: action // Action taken by the user
//         })
//     })
//     .then(resp => resp.json())
//     .then(data => {
//         // Continue with the chosen action
//         doneImportSingle(data);
//     });
// }
// !! END Change


function handleRowActionLinks() {
    let action_links = document.querySelectorAll('.rbstravel-row-action-link');
    
    for(let i = 0; i < action_links.length; i++) {
	action_links[i].addEventListener('click', function(ev) {
	    ev.preventDefault();
	    let travel_id = ev.target.dataset.travelId;
	    let action = ev.target.dataset.action;
	    
	    if (action === 'view_info') {
		    actionViewInfo(travel_id);
	    } else if (action === 'view_details') {
		    actionViewDetails(travel_id);
	    } else if (action === 'import') {
		    actionImportSingle(travel_id);
	    }
	    
	});
    }
}




function handleGenerateMap() {
    const generateMapButton = document.getElementById('generate-map');
    const generateImageButton = document.getElementById('generate-map-image');

    var ideaMap = null;
    if (generateMapButton) {
        generateMapButton.addEventListener('click', () => {
            console.log('generate map', ideaMapMarkers);


            ideaMap = new rbsMap('ideaMap', 'Idea Map', {debug : true, width: '100%'});      //, width: '700px'
            
            // ideaMap.setCenterLocation(51.7791246, 4.6689966);
            ideaMap.setCenterLocation(ideaMapMarkers[0]['lat'], ideaMapMarkers[0]['lng']);
            
            ideaMapMarkers.forEach((val => {
                var mapMarker = new rbsMapMarker(val.name, val.lat, val.lng);
                ideaMap.addMarker(mapMarker);
            }));
    
            // const mapMarker01 = new rbsMapMarker('marker01', 51.7791246, 4.6689966);
            // ideaMap.addMarker(mapMarker01);
    
            // const mapMarker02 = new rbsMapMarker('marker02', 51.8816079, 4.5393749);
            // ideaMap.addMarker(mapMarker02);        
    
            ideaMap.renderMap('ideaMap');          
            
            // testMap.renderImage('testMap');

        });
    }

    if (generateImageButton) {
        generateImageButton.addEventListener('click', () => {
            console.log('generate map image', ideaMap);

            const textWait = document.getElementById('generate-image--wait');
            textWait.style.display = 'inline';

            function onRenderImageComplete(canvas) {
                console.log('onRenderImageComplete', canvas);

                const textBase64 = document.getElementById('idea-map-image-base64');
                textBase64.value = canvas.toDataURL();
                textWait.style.display = 'none';
            }

            if (ideaMap !== null) {
                ideaMap.renderImage('ideaMap', 'ideaMapImage', {callback: {completed : onRenderImageComplete}});
            }


            // const testMap = new rbsMap('ideaMapImage', 'Idea Map Image', {debug : true, width: '100%'});      //, width: '700px'
            // testMap.setCenterLocation(51.7791246, 4.6689966);
    
            // const mapMarker01 = new rbsMapMarker('marker01', 51.7791246, 4.6689966);
            // testMap.addMarker(mapMarker01);
    
            // const mapMarker02 = new rbsMapMarker('marker02', 51.8816079, 4.5393749);
            // testMap.addMarker(mapMarker02);        
    
            // // testMap.renderMap('ideaMap');          
            
            

        });
    }    
    
}


function handleUpdateTravelMapImage() {
    console.log('handleUpdateTravelMapImage');
    // document.addEventListener('DOMContentLoaded', function () {
        // Select the post form
        const postForm = document.getElementById('post');
    
        // Add an event listener for the form submission
        if (postForm) {
            console.log('handleUpdateTravelMapImage -> postForm', postForm);
            postForm.addEventListener('submit', function (e) {
                console.log('Custom post type is being saved!');
                myCustomFunction(); // Call your custom function
            });
        }
    
        // Define your custom function
        function myCustomFunction() {
            // console.log('handleUpdateTravelMapImage -> myCustomFunction');
            // alert('Custom post type saved!');
            const testMap = new rbsMap('test-map-01', 'Test Map 01', {debug : true});
            testMap.setCenterLocation(51.7791246, 4.6689966);
    
            const mapMarker01 = new rbsMapMarker('marker01', 51.7791246, 4.6689966);
            testMap.addMarker(mapMarker01);
    
            const mapMarker02 = new rbsMapMarker('marker02', 51.8816079, 4.5393749);
            testMap.addMarker(mapMarker02);        
    
            testMap.renderMap('testMap');
    
            // document.addEventListener('DOMContentLoaded', () => {
                // testMap.renderImage('testMap');
            // });
            
        }
    // });
}





document.addEventListener("DOMContentLoaded", function() {
    //console.log('rbsTravel', rbsTravel);
    
    handleRowActionLinks();
    handleCloseImportError();
    handleClosePopupWindow();

    handleGenerateMap();
    handleUpdateTravelMapImage();
});