
// "use strict";

function fetchFullCalendarInfo(postdata) {

        var jsonString = JSON.stringify(postdata);

        fetch('/wp-admin/admin-ajax.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Cache-Control': 'no-cache',
            },
            body: new URLSearchParams({
                action: 'rbstravel_get_travel_items',
                testarr: postdata,
                datastring: jsonString,
                //postdata: postdata,
                //data: {data: JSON.stringify(["rob"])}
                data: JSON.stringify( { "person": {name: "Johnnie", age: 30, city: "New York"}, "two": 2, "three": 3 }),
                destinations: JSON.stringify({ "person": {name: "Johnnie", age: 30, city: "New York"}, "two": 2, "three": 3, "destinations": jsonString })
                

            })
        })
    .then(resp => resp.json())
    .then(data => renderTravelItems(data))
    

}  

function renderTravelItems(data) {
    console.log(data.destinations);
  
}  

let postdata = 'this is a test';
let my_arr = [];
my_arr['name'] = 'My name';
my_arr['address'] = 'Address';
//console.log(JSON.stringify(my_arr));

fetchFullCalendarInfo(JSON.stringify(my_arr));


const form2 = document.querySelector('form');
formtrigger = document.getElementById('formtrigger');
// console.log(formtrigger);
formtrigger.addEventListener('click', (event) => {
    event.preventDefault();


    let skills = [];
    document.querySelectorAll('[type="checkbox"]').forEach(item => {
        if (item.checked === true ) {
            skills.push(item.value);

        }
    })
    // console.log(`skills: ${skills}`);
    fetchFullCalendarInfo(skills);
});




