
<div id="single-idea-hero">
    <div id="single-idea-hero-left"></div>
    <div id="single-idea-hero-right">
        <div><h1>Boating And Sailing Tour</h1></div>
        <p><span>from</span></p>
        <p><span>$599.00</span> <span>$429.00</span></p>
        <p>Son agreed others exeter period myself few yet nature. Mention mr manners opinion if garrets enabled. To an occasional dissimilar impossible sentiments.</p>

        <div id="single-idea-hero-right-info">
            <table id="info-table-1" class="info-table">

                <tr>
                    <td><strong>Reviews</strong></td>
                    <td>Stars 4/5</td>
                    <td>&nbsp;</td>
                    <td><strong>Vacation Style</strong></td>
                    <td>Icons</td>
                </tr>
                <tr>
                    <td>1 Review</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Style</td>
                    <td>Culture, Guided</td>
                </tr>     
                <tr>
                    <td class="td-underline" colspan=2>&nbsp;</td>  
                    <td>&nbsp;</td>       
                    <td class="td-underline" colspan=2>&nbsp;</td>                 
                </tr>                   
                       

            </table>

            <table id="info-table-2" class="info-table">

                <tr>
                    <td><strong>Activity Level</strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><strong>Group Size</strong></td>
                    <td><strong>Icon</strong></td>
                </tr>
                <tr>
                    <td>Extreme</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Small Group</td>
                    <td>6</td>
                </tr>     
                <tr>
                    <td class="td-underline" colspan=2>&nbsp;</td>  
                    <td>&nbsp;</td>       
                    <td class="td-underline" colspan=2>&nbsp;</td>                 
                </tr>                                          
            </table>

        </div>
        <div id=travel-cta>
        <button class="slider-button-next">Prijs opvragen</button>
        </div>

    </div>
    </div>
</div>    

<style>
#single-idea-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    /* grid-template-rows: 50vh; */

}    
#single-idea-hero-left {
    background-image: url('<?php echo $travel_meta_fields['travel_image_url'];?>');
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}

#single-idea-hero-right {
    padding: 50px;
    text-align: center;
}
#single-idea-hero-right-specs {
    background: green;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    grid-template-rows: 100px;

}

.info-table {
    width: 100%;
    margin-bottom: 50px;
}

.info-table td {
    width: 100px;

}

.td-underline {
    border-top:1px solid #ededed;
}



#travel-cta {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;

}

</style>