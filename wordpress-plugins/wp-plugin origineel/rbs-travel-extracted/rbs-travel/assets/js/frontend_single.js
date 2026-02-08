// console.log('rbstravel-frontend.js');

/**
 * disabled script, due several issues
 * - add check for certain element(s) before executing script....
 * - ....
 */


document.addEventListener("DOMContentLoaded", (event) => {
    return;
// "use strict";

function fetchFullCalendarInfo(postdata) {
    // console.log(postdata);
    // const obj = {name: "John", age: 30, city: "New York"};
    // const myJSON = JSON.stringify(obj);
        //dataString = ['data1','data2'] ; // array?
 
        var minpricejsonString = JSON.stringify(postdata['minprice']);
        var maxpricejsonString = JSON.stringify(postdata['maxprice']);
        var destinationsjsonString = JSON.stringify(postdata['destinations']);
        // var valBoxMax = document.getElementById(document.getElementById('valBoxMax')).innerText;
        // alert(valBoxMax);

        fetch('/wp-admin/admin-ajax.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Cache-Control': 'no-cache',
            },
            body: new URLSearchParams({
                action: 'rbstravel_get_travel_items',
                // testarr: postdata,
                // datastring: destinationsjsonString,
                //postdata: postdata,
                //data: {data: JSON.stringify(["rob"])}
                data: JSON.stringify( { "person": {name: "Johnnie", age: 30, city: "New York"}, "two": 2, "three": 3 }),
                ajaxdata: JSON.stringify({ "person": {name: "Johnnie", age: 30, city: "New York"}, "two": 2, "three": 3, "minprice": minpricejsonString, "maxprice": maxpricejsonString,"destinations": destinationsjsonString })
                

            })
        })
    .then(resp => resp.json())

    .then(data => renderTravelItems(data))
    

}  

function renderTravelItems(data) {
    console.log(data);
    $idealist_items = document.querySelectorAll('.idealist_overview_item');

    //console.log($idealist_items);

    $idealist_items.forEach(item => {
        // console.log(typeof(data[0]));
        console.log(typeof(item.dataset.postid));

        // console.log(item.dataset.postid);
        if (data.includes(parseInt(item.dataset.postid))) {
            item.classList.remove("rbs-travel-hide");
            item.classList.remove("rbs-travel-remove"); 

        } else {
            item.classList.add("rbs-travel-hide");

            setTimeout(function(){
                item.classList.add("rbs-travel-remove"); //removing parent element with its contains
            }, 2000)            


        }
   
    });
    // console.log(data.destinations);
  
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
    data = [];

    let destinations = [];
    document.querySelectorAll('[type="checkbox"]').forEach(item => {
        if (item.checked === true ) {
            destinations.push(item.value);

        }
    })
    var valBoxMin = document.getElementById('valBoxMin').innerText;
    var valBoxMax = document.getElementById('valBoxMax').innerText;
    data['minprice'] = valBoxMin;
    data['maxprice'] = valBoxMax;
    data['destinations'] = destinations;
    fetchFullCalendarInfo(data);
});


});




document.addEventListener("DOMContentLoaded", (event) => {
    return;

	class Slider {
        constructor(name, element) {
          this.sliderwrapper = element;
          this.name = name;
          this.count = 0;
          this.inc = 0;
          this.margin = 0;
          this.itemDisplay = '';
          this.slidercontainer = this.sliderwrapper.getElementsByClassName('slider-container')[0]; 
          this.items = this.slidercontainer.getElementsByClassName('item');
          this.sliderwidth = this.sliderwrapper.getElementsByClassName('slider-width')[0]; 
          this.nextbutton = this.sliderwrapper.getElementsByClassName('slider-button-next')[0];
          this.prevbutton = this.sliderwrapper.getElementsByClassName('slider-button-prev')[0];
          this.boundEventHandlerNext = this.eventHandlerNext.bind(this)
          this.boundEventHandlerPrev = this.eventHandlerPrev.bind(this)
          this.nextbutton.addEventListener('click', this.boundEventHandlerNext);  
          this.prevbutton.addEventListener('click', this.boundEventHandlerPrev);     
          this.id = name;
      
          if(this.slidercontainer.clientWidth > 999) {
              this.itemDisplay = this.slidercontainer.getAttribute("item-display-d");    
      
              this.margin = this.itemDisplay * 5;
          }
      
          if(this.slidercontainer.clientWidth > 700 && this.slidercontainer.clientWidth < 990) {
              this.itemDisplay = this.slidercontainer.getAttribute("item-display-t");
              this.margin = this.itemDisplay * 6.8;
          }  
          
          if(this.slidercontainer.clientWidth > 280 && this.slidercontainer.clientWidth < 700) {
              this.itemDisplay = this.slidercontainer.getAttribute("item-display-m");
              this.margin = this.itemDisplay * 20;
          }    
      
          this.itemLeft = this.items.length % this.itemDisplay;
          this.itemSlide =  Math.floor(this.items.length / this.itemDisplay) - 1;
          this.itemWidth = (this.slidercontainer.clientWidth / this.itemDisplay) - this.margin;  
          for(let i=0; i<this.items.length; i++) {
              this.items[i].style.width = this.itemWidth + "px";
          }    
      
        }
      
        eventHandlerNext() {
      
          if (this.inc !== this.itemSlide + this.itemLeft) {
                  if (this.inc === this.itemSlide) {
                      this.inc = this.inc + this.itemLeft;
                      this.count = this.count - (this.slidercontainer.clientWidth / this.itemDisplay) * this.itemLeft;
                  }
                  else {
                      this.inc ++;
                      this.count = this.count - this.slidercontainer.clientWidth; 
                  }
              }        
              this.sliderwidth.style.left = this.count + "px";    
        }
        eventHandlerPrev() {
          console.log(this)
          if (this.inc !== 0) {
                  if (this.inc === this.itemLeft) {
      
                      this.inc = this.inc - this.itemLeft;
                      this.count = this.count + (this.slidercontainer.clientWidth / this.itemDisplay) * this.itemLeft;
                  }
                  else {
                      this.inc --;
                      this.count = this.count + this.slidercontainer.clientWidth; 
                  }
              }        
              this.sliderwidth.style.left = this.count + "px";    
        }  
      }
      
      
      
      
      class Sliders {
        constructor(){
          this.sliders = []
        }
      
        newSlider(name, element){
          let p = new Slider(name, element)
          this.sliders.push(p)
          return p
        }
        getNumberOfSliders(){
            return this.sliders.length
        }
      }
      
      let pageSliders = new Sliders()
      
      // pageSliders.newSlider("1")
      // pageSliders.newSlider("2")
      sliderwrappers = document.getElementsByClassName('slider-wrapper');
      console.log(`yy${sliderwrappers.length}`)
      for (let i = 0; i < sliderwrappers.length; i++) {
          console.log(i)
        pageSliders.newSlider(i+1,sliderwrappers.item(i));
      }
      
      console.log(`xxx ${pageSliders.sliders}`);
      
      
  
          
  });
	
