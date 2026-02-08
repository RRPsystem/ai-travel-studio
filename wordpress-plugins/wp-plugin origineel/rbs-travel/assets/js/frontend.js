// console.log('rbstravel-frontend.js');

/**
 * disabled script, due several issues
 * - add check for certain element(s) before executing script....
 * - ....
 */


document.addEventListener("DOMContentLoaded", (event) => {

  
// "use strict";

function FetchPostSelection(postdata) {
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
    console.log('renderTravelItems', data);


    const overviewItems = document.getElementById('rbstravel-overview-items');
    overviewItems.innerHTML = '';

    if (typeof data.html !== 'undefined') {
        if (data.html.length === 0) {
            overviewItems.innerHTML = '<p>TEMP: No results!!</p>';
        } else {
            data.html.forEach((value, index) => {
                overviewItems.innerHTML += value;
            });
        }

    } else {
        overviewItems.innerHTML = '<p>TEMP: Invalid request response</p>';
    }

    // const element = document.getElementById(id);
    if (overviewItems) {
        overviewItems.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        //console.error(`Element with ID "${id}" not found.`);
    }


    // $idealist_items = document.querySelectorAll('.idealist_overview_item');

    // //console.log($idealist_items);

    // // $ideaalist_over

    // $idealist_items.forEach(item => {
    //     // console.log(typeof(data[0]));
    //     console.log(typeof(item.dataset.postid));

    //     // console.log(item.dataset.postid);
    //     if (data.includes(parseInt(item.dataset.postid))) {
    //         item.classList.remove("rbs-travel-hide");
    //         item.classList.remove("rbs-travel-remove"); 

    //     } else {
    //         item.classList.add("rbs-travel-hide");

    //         setTimeout(function(){
    //             item.classList.add("rbs-travel-remove"); //removing parent element with its contains
    //         }, 2000)            


    //     }
   
    // });
    // // console.log(data.destinations);
  
}  

// let postdata = 'this is a test';
// let my_arr = [];
// my_arr['name'] = 'My name';
// my_arr['address'] = 'Address';
//console.log(JSON.stringify(my_arr));

//fetchFullCalendarInfo(JSON.stringify(my_arr));


const form2 = document.querySelector('form');
formtrigger = document.getElementById('formtrigger');
// console.log(formtrigger);
formtrigger.addEventListener('click', (event) => {
    console.log('clicked');
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
    FetchPostSelection(data);
});


});



document.addEventListener("DOMContentLoaded", (event) => {

    let rbsddayopenitems = document.querySelectorAll('.rbsd-day-open'); 
    // console.log(dayrows);
    console.log(rbsddayopenitems);
    
    rbsddayopenitems.forEach(item => item.addEventListener('click', rbsddayrowclicked));  
     
   
    function rbsddayrowclicked(event) {
        console.log('clicked');
        const target = event.target;
        const closestparent = target.closest(".rbsd-day-row");            
        closestparent.classList.toggle('openclose');   
    }

    function rbsddestimages(event) {
        const target = event.target;
        alert('target');
        // const closestparent = target.closest(".rbsd-day-row");            
        // closestparent.classList.toggle('openclose');   
    }    


      

   
      

});    

'use strict';

document.addEventListener("DOMContentLoaded", (event) => {

  const tabitems = document.querySelectorAll('.tablinks');

    class TabInstance {
       
        constructor(settings) {
       
            this.settings = settings;
            this.state = {
                currentactivetab: settings.primarytab,
                currentactivecontent: ""
            };            
            this.dom_tab_linkwrapper = document.getElementById(this.settings.id);                    
            this.dom_tab_linkitems = this.dom_tab_linkwrapper.querySelectorAll('.' + this.settings.classes.tablinkclasses.link);
            this.dom_tab_content_wrapper = document.getElementById(this.settings.classes.tabcontentclasses.wrapper + '_' + this.settings.idparts[1]);
            this.dom_tab_content_items = this.dom_tab_content_wrapper.querySelectorAll('.' + this.settings.classes.tabcontentclasses.item);           
            this.boundEventHandlerClick = this.eventHandlerClick.bind(this);
            this.dom_tab_linkitems.forEach(item => item.addEventListener('click', this.boundEventHandlerClick), {capture: true});     
       
        }       

        set_currentactivetab(classlist) {
            let theclass = '';
            let theclassprefix = this.settings.classes.tablinkclasses.identifierprefix;
            classlist.forEach(function(item) {

                if (item.includes(theclassprefix)) {
                    theclass = item;
                }
            });
            this.state.currentactivetab = theclass;            
        }
        
        eventHandlerClick(event) {
            // console.log('eventHandlerClick');
            event.stopPropagation();
            const target = event.target;
            const closestparent = target.closest(".rbs-tablink");
            // closestparent.style.color = "white";
            console.log(closestparent);
            // console.log(e.target.closest(".grandparent"));            
            this.set_currentactivetab(closestparent.classList);
            let theclass = this.state.currentactivetab;
            // console.log(this.state);
            // console.log(this.dom_tab_content_items);
            
            this.dom_tab_content_items.forEach(function(item) {

                // console.log(theclass);

                // console.log(item);
                
                if(item.classList.contains(theclass)) {  
                    console.log('activated');                  
                    item.classList.remove('rbs-inactivetabcontent'); 
                    item.classList.add('rbs-activetabcontent'); 

                } else {
                    console.log('deactivated');
                    item.classList.remove('rbs-activetabcontent'); 
                    item.classList.add('rbs-inactivetabcontent'); 
                }                
            });   
            
            this.refreshMap();
            this.currentactivetab = theclass;   
     
        }


        refreshMap() {
            var mapId = 'rbstravel-idea__fullmap';
            const elMap = document.getElementById(mapId);
            if (elMap) {
                
                elMap._map_instance.invalidateSize(true);

                elMap._map_instance.fitBounds(elMap._marker_group.getBounds());
                // elMap._map_instance.fitBounds(elMap.markerGroup.getBounds());
                
            }
        }
    }   

    // Class that holds a collection of tabs instances 
    class TabInstances {
        constructor(){
            this.tabinstances = []                  
        }
        // create a new tabinstance and save it in the collection
        addTabInstance(settings){
            let p = new TabInstance(settings)
            this.tabinstances.push(p)
            this.amount = this.tabinstances.length;           
        return p
        }
        getNumberOfTabInstances(){
            return this.amount;
        }
    }
  
    let pageTabInstances = new TabInstances()
    let classes = {
        tablinkclasses: {
            wrapper: 'rbs-tablink-wrapper', 
            link:  'rbs-tablink',  
            identifierprefix:  'tl', //indentify current item 
            active: 'rbs-activetablink'
          
        },
        tabcontentclasses: {
            wrapper: 'rbs-tabcontent-wrapper',
            item:  'rbs-tabcontentitem',
            active: 'rbs-inactivetabcontent',
            inactive: 'rbs-inactivetabcontent',
        }        
    }
    
    let tabs_in_dom = document.querySelectorAll('.rbs-tablink-wrapper');

    tabs_in_dom.forEach(function(item) {
       
        pageTabInstances.addTabInstance({
            id: item.id, 
            idparts: item.id.split("_"),
            primarytab: '1',
            classes: classes   
        }); 
    });    

    // console.log(pageTabInstances);    
    // console.log(pageTabInstances.getNumberOfTabInstances());
    });




/*
EXAMPLE HMLT TAB LINKS
<!-- !! underscore required for now!!!  -->
<div id="rbs-tablink-wrapper_1" class="rbs-tablink-wrapper">
    <span class="rbs-tablink tl1 rbs-activetablink">link 1</span>
    <span class="rbs-tablink tl2 rbs-inactivetablink">link 2</span>
</div>

<!-- !! underscore required for now!!!  -->
<div id="rbs-tablink-wrapper_2" class="rbs-tablink-wrapper">
    <span class="rbs-tablink tl3 rbs-activetablink">link 4</span>
    <span class="rbs-tablink tl4 rbs-inactivetablink">link 5</span>
</div>

<!-- !! underscore required for now!!!  -->
<div id="rbs-tabcontent-wrapper_1" class="rbs-tabcontent-wrapper">
    tab 1
    <div class="tl1 rbs-tabcontentitem rbs-activetabcontent">
    Content of tab 1
    </div>    
    <div class="tl2 rbs-tabcontentitem rbs-inactivetabcontent">
    Content of tab 2
    </div>    

</div>

<!-- !! underscore required for now!!!  -->
<div id="rbs-tabcontent-wrapper_2" class="rbs-tabcontent-wrapper">
    tab 2
    <div class="tl3 rbs-tabcontentitem rbs-activetabcontent">
    Content of tab 3
    </div>
    <div class="tl4 rbs-tabcontentitem rbs-inactivetabcontent">
    Content of tab 4
    </div>
</div>

*/
document.addEventListener("DOMContentLoaded", (event) => {
    class RbsModal {
        constructor(){
            this.instances = [];            
        }
        addToList(settings){
            let p = new RbsModalInstance(settings);
            this.instances.push(p);        
        }
        getNumberOfTabInstances() {
            return this.instances.length;
        }
    
    } 
    
    class RbsModalInstance {
        constructor(settings) {
            this.id = settings.id;
            this.overlay = document.getElementById('rbs-modal-overlay');
            this.domwrapperref = settings.domwrapperref;
            this.domref = this.domwrapperref.querySelector('.rbs-modal');
            this.images = [];
            this.element = this.domref.id;
            this.openbutton = this.domwrapperref.querySelector('.rbs-modal-open');
            this.closebutton = this.domwrapperref.querySelector('.rbs-modal-close');
            this.boundEventHandlerOpen = this.eventHandlerOpen.bind(this)
            this.boundEventHandlerClose = this.eventHandlerClose.bind(this)
            this.openbutton.addEventListener('click', this.boundEventHandlerOpen)  
            this.closebutton.addEventListener('click', this.boundEventHandlerClose) 
            this.openbuttons = this.domwrapperref.querySelectorAll('.rbs-modal-open');
            this.openbuttons.forEach(item => item.addEventListener('click', this.boundEventHandlerOpen)); 
    
            
            
        }
        
        eventHandlerOpen(event) {
            console.log(this);
            this.domref.classList.add('active');
            this.overlay.classList.add('active');        
            this.domref.style.border = "thick solid #0000FF"; 
        }   
        
        eventHandlerClose(event) {
            console.log(this);
            this.domref.classList.remove('active');
            this.overlay.classList.remove('active');
            this.domref.style.border = "thick solid green"; 
        }       
        getId() {
            return this.id;
        }  
    }
    
    let RbsModalList = new RbsModal();
    let RbsModalsInDom = document.querySelectorAll('.rbs-modal-wrapper');
    
    RbsModalsInDom.forEach(function(item) {
       
        RbsModalList.addToList({
            id: item.id, 
            domwrapperref: item,
        }); 
    }); 
    
    console.log(RbsModalList);
    
    
    
    
    
    
    
    
    
    
    class RbsSlider {
        constructor(){
            this.instances = [];            
        }
        addToList(settings){
            let p = new RbsSliderInstance(settings);
            this.instances.push(p);        
        }
        getNumberOfTabInstances() {
            return this.instances.length;
        }
    
    } 
    
    class RbsSliderInstance {
        constructor(settings) {        
            this.id = settings.id;
            this.domwrapperref = settings.domwrapperref;
            this.count = 0;
            this.inc = 0;
            this.margin = 0;
            this.itemDisplay = 'xx';
            this.slidercontainer = this.domwrapperref.querySelector('.rbs-slider-container');
            this.sliderwidth = this.domwrapperref.querySelector('.rbs-slider-width');  
            this.nextbutton = this.domwrapperref.querySelector('.rbs-slider-next');
            this.prevbutton = this.domwrapperref.querySelector('.rbs-slider-prev');      
            this.boundEventHandlerNext = this.eventHandlerNext.bind(this);
            this.boundEventHandlerPrev = this.eventHandlerPrev.bind(this);
            this.nextbutton.addEventListener('click', this.boundEventHandlerNext); 
            this.prevbutton.addEventListener('click', this.boundEventHandlerPrev);    
            if(screen.width > 999) {
                this.itemDisplay = this.slidercontainer.getAttribute("item-display-d"); 
                console.log(this.itemDisplay);     
                this.margin = this.itemDisplay * 5;
            }
        
            if(screen.width > 700 && screen.width < 990) {
                this.itemDisplay = this.slidercontainer.getAttribute("item-display-t");
                // console.log(itemDisplay);
                this.margin = this.itemDisplay * 6.8;
            }  
            
            if(screen.width > 280 && screen.width < 700) {
                this.itemDisplay = this.slidercontainer.getAttribute("item-display-m");
                // console.log(itemDisplay);
                this.margin = this.itemDisplay * 20;
            }    
            
            this.item = document.getElementsByClassName("item-" + name);
         
            this.itemLeft = this.item.length % this.itemDisplay;
           
            this.itemSlide =  Math.floor(this.item.length / this.itemDisplay) - 1;
            
            this.itemWidth = (screen.width / this.itemDisplay) - this.margin;  
            for(let i=0; i<this.item.length; i++) {
                //console.log(margin);
        
                this.item[i].style.width = this.itemWidth + "px";
            }                
            // this.domref = this.domwrapperref.querySelector('.rbs-modal');
            // this.images = [];
            // this.element = this.domref.id;
            // this.openbutton = this.domwrapperref.querySelector('.rbs-modal-open');
            // this.closebutton = this.domwrapperref.querySelector('.rbs-modal-close');
            // this.boundEventHandlerOpen = this.eventHandlerOpen.bind(this)
            // this.boundEventHandlerClose = this.eventHandlerClose.bind(this)
            // this.openbutton.addEventListener('click', this.boundEventHandlerOpen)  
            // this.closebutton.addEventListener('click', this.boundEventHandlerClose)                  
        }
        
        eventHandlerNext(event) {
            console.log(this.itemSlide);
            // console.log(this.slider);
            // console.log(this.nextbuttonid);
            if (this.inc !== this.itemSlide + this.itemLeft) {
                    if (this.inc === this.itemSlide) {
                        this.inc = this.inc + this.itemLeft;
                        this.count = this.count - (screen.width / this.itemDisplay) * this.itemLeft;
                    }
                    else {
                        this.inc ++;
                        this.count = this.count - screen.width; 
                    }
                }
                
                this.sliderwidth.style.left = this.count + "px";    
          }
          eventHandlerPrev(event) {
            
            // console.log(this.prevbuttonid);
            // console.log(this.slider);
            console.log(this.inc);
            if (this.inc !== 0) {
                    if (this.inc === this.itemLeft) {
        
                        this.inc = this.inc - this.itemLeft;
                        this.count = this.count + (screen.width / this.itemDisplay) * this.itemLeft;
                    }
                    else {
                        this.inc --;
                        this.count = this.count + screen.width; 
                    }
                }
                
                this.sliderwidth.style.left = this.count + "px";    
          }        
        getId() {
            return this.id;
        }  
    }
    
    let RbsSliderList = new RbsSlider();
    let RbsSlidersInDom = document.querySelectorAll('.rbs-slider-wrapper');
    
    RbsSlidersInDom.forEach(function(item) {
       
        RbsSliderList.addToList({
            id: item.id, 
            domwrapperref: item,
        }); 
    }); 
    
    console.log(RbsSliderList);
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
        
    

    
});    

