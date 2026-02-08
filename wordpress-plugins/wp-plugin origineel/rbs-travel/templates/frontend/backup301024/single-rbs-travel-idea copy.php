<?php
if (!defined('ABSPATH')) {
    exit;
}
global $travel_meta_fields;
$travel_meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields(); 
$vars = array();
$vars['travel_meta_fields'] = $travel_meta_fields;

?>






<?php get_header(); ?>


<div class="content-wrapper">
      <div class="rbs-travel-container">
        <div class="content-left">       
          <div class="DTITLE"><p class="text-wrapper">Travel idea title goes here....</p></div>
          <div class="rbsd-tabmenu">
            <div class="rbsd-tab">
              <img class="img" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-tab-item.svg" />
              <p class="rbsd-tabtext">
                <span class="span">&nbsp;</span>
                <span class="text-wrapper-2">Information</span>

              </p>
            </div>
            <div class="div">
              <img class="img" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-tab-item-1.svg" />
              <p class="rbsd-tabtext">
                <span class="span">&nbsp;</span>
                <span class="text-wrapper-2">Tourplanning</span>
     >
              </p>
            </div>
            <div class="rbsd-tab">
              <img class="img" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-tab-item-2.svg" />
              <div class="text-wrapper-3">Location share</div>
            </div>
            <div class="rbsd-tab">
              <img class="img" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-tab-item-3.svg" />
              <p class="p">
                <span class="text-wrapper-4">&nbsp;</span>
                <span class="text-wrapper-2">Shot Gallery</span>
                <span class="span">&nbsp;&nbsp;</span>
  
              </p>
            </div>
          </div>
          <div class="rbsd-day-row">
            <div class="rbsd-day-date">
              <div class="text-wrapper-5">1-9-25</div>
              <img class="rbsd-day-open" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-day-open.svg" />
            </div>
            <div class="rbsd-day-contentrow">
              <p class="rbsd-day-crtitle">Reizen van Schiphol naar Johannesburg</p>
              <div class="rbsd-day-crbottom">
                <img class="rbsd-day-crbottom-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-day-crbottom-img.png" />
                <div class="rbsd-day-crbottom-3">
                  <div class="text-wrapper-6">KLM AMS KL591</div>
                  <div class="element-amsterdam">10:35 Amsterdam Schiphol<br />13:45 Johannesburg</div>
                </div>
              </div>
            </div>
            <div class="rbsd-day-contentrow-2">
              <div class="rbsd-day-crtitle">Auto verhuur</div>
              <div class="rbsd-day-crbottom-4">
                <img class="rbsd-day-crbottom-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-day-crbottom-img-1.png" />
                <div class="rbsd-day-crbottom-5">
                  <div class="text-wrapper-7">TOYOTA URBAN CRUISER</div>
                  <p class="text-wrapper-8">Info about car goes here...</p>
                </div>
              </div>
            </div>
            <div class="rbsd-day-contentrow-3">
              <div class="rbsd-day-crtitle">Over Reijkjavik</div>
              <div class="frame">
                <div class="frame-2">
                  <div class="text-wrapper-7">Ontdek Reijkjavik!</div>
                  <p class="text-wrapper-8">Text about destination goes here</p>
                  <div class="rbsd-dest-images-row">
                    <img class="img-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-dest-image@2x.png" />
                    <img class="rbsd-dest-image" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-dest-image-1@2x.png" />
                  </div>
                  <div class="rbsd-dest-images-row">
                    <img class="img-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-dest-image-2@2x.png" />
                    <img class="rbsd-dest-image" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-dest-image-3@2x.png" />
                  </div>
                </div>
              </div>
            </div>
            <div class="rbsd-day-contentrow-4">
              <div class="rbsd-day-crtitle">Geselecteerd hotel</div>
              <div class="rbsd-day-crbottom-6">
                <img class="rbsd-day-crbottom-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-day-crbottom-img-2.png" />
                <div class="rbsd-day-crbottom-5">
                  <div class="text-wrapper-7">KIWARA GUEST HOUSE</div>
                  <div class="text-wrapper-8">Text goes here</div>
                </div>
              </div>
              <div class="frame-3">
                <img class="img-2" src="https://c.animaapp.com/IqJ2Y8e7/img/hotel4@2x.png" />
                <img class="img-2" src="https://c.animaapp.com/IqJ2Y8e7/img/hotel2@2x.png" />
              </div>
              <div class="frame-3">
                <img class="img-2" src="https://c.animaapp.com/IqJ2Y8e7/img/hotel1@2x.png" />
                <img class="img-2" src="https://c.animaapp.com/IqJ2Y8e7/img/hotel3@2x.png" />
              </div>
            </div>
          </div>
          <div class="dinfotext"><div class="text-wrapper-9">2-9-25</div></div>
          <div class="dinfotext"><div class="text-wrapper-9">4-9-25</div></div>
          <div class="dinfotext"><div class="text-wrapper-9">5-9-25</div></div>
        </div>
        <div class="content-right">
          <div class="rectangle">
          <section class="rbs-travel-idea__section">
          <?php
              $template = 'frontend' . DIRECTORY_SEPARATOR . 'single-idea' . DIRECTORY_SEPARATOR .  'fullmap';
              echo rbstravel_template_loader($template, $vars, null, array('add_wrapper' => false));  
          ?>
          </section>
          </div>
          <div class="rbsd-idea-info">
            <div class="div-2">
              <img class="rbsd-idea-info-icon" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-idea-info-icon.svg" />
              <div class="rbsd-tour-type">
                <div class="rbsd-tt-top">From</div>
                <div class="rbsd-tt-bottom">EURO 1135</div>
              </div>
            </div>
            <div class="div-2">
              <img class="rbsd-idea-info-icon-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-idea-info-icon-1.svg" />
              <div class="rbsd-tour-type-2">
                <div class="rbsd-tt-top">Duration</div>
                <div class="text-wrapper-3">5 days</div>
              </div>
            </div>
            <div class="div-2">
              <img class="rbsd-idea-info-icon-2" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-idea-info-icon-2.svg" />
              <div class="rbsd-tour-type-2">
                <div class="rbsd-tt-top">Tour Type</div>
                <div class="text-wrapper-3">Adventure</div>
              </div>
            </div>
          </div>
          <div class="div-3">
            <div class="rbsd-book-title">Boeken</div>
            <div class="checkitem">
              <img class="img-3" src="https://c.animaapp.com/IqJ2Y8e7/img/calendar-days.svg" />
              <div class="text-wrapper-10">Periode</div>
            </div>
            <div class="checkitem">
              <img class="img-3" src="https://c.animaapp.com/IqJ2Y8e7/img/user.svg" />
              <div class="text-wrapper-10">Aantal personen</div>
            </div>
            <div class="rbsd-book-button">
              <div class="rbsd-button-text">Verder</div>
              <img class="img" src="https://c.animaapp.com/IqJ2Y8e7/img/rbsd-button-icon.svg" />
            </div>
            <div class="check"></div>
          </div>
        </div>
      </div>
    </div>







<div class="wide">
      <div class="overlap-wrapper">
        <div class="overlap">
          <div class="frame">
            <div class="overlap-group">
              <div class="dtourinfo">
                <div class="dbook">
                  <img class="primary" src="https://c.animaapp.com/By9XtBF2/img/primary-3.svg" />
                  <div class="div">
                    <div class="text-wrapper">From</div>
                    <div class="text-wrapper-2">EURO 1135</div>
                  </div>
                </div>
                <div class="dbook">
                  <img class="img" src="https://c.animaapp.com/By9XtBF2/img/clock-rotate-left-3.svg" />
                  <div class="frame-2">
                    <div class="text-wrapper">Duration</div>
                    <div class="text-wrapper-3">5 days</div>
                  </div>
                </div>
                <div class="dbook">
                  <img class="img" src="https://c.animaapp.com/By9XtBF2/img/earth-americas-4.svg" />
                  <div class="frame-2">
                    <div class="text-wrapper">Tour Type</div>
                    <div class="text-wrapper-3">Adventure</div>
                  </div>
                </div>
              </div>
              <img class="primary-2" src="https://c.animaapp.com/By9XtBF2/img/primary-4.svg" />
            </div>
          </div>
          <div class="dcontentleft">
            <div class="DTITLE"><p class="p">Travel idea title goes here....</p></div>
            <div class="dtabmenu">
              <div class="dtabsmall">
                <img class="circle-info" src="https://c.animaapp.com/By9XtBF2/img/circle-info-6.svg" />
                <p class="information">
                  <span class="span">&nbsp;</span>
                  <span class="text-wrapper-4">Information</span>
                  <span class="span"
                    >
                  </span>
                </p>
              </div>
              <div class="dtabsmall-2">
                <img class="circle-info" src="https://c.animaapp.com/By9XtBF2/img/circle-info-7.svg" />
                <p class="information">
                  <span class="span">&nbsp;</span>
                  <span class="text-wrapper-4">Tourplanning</span>
                  <span class="span"
                    >
                  </span>
                </p>
              </div>
              <div class="dtabsmall">
                <img class="circle-info" src="https://c.animaapp.com/By9XtBF2/img/circle-info-8.svg" />
                <div class="text-wrapper-3">Location share</div>
              </div>
              <div class="dtabsmall">
                <img class="circle-info" src="https://c.animaapp.com/By9XtBF2/img/circle-info-9.svg" />
                <p class="information-2">
                  <span class="text-wrapper-5">&nbsp;</span>
                  <span class="text-wrapper-4">Shot Gallery</span>
                  <span class="span">&nbsp;&nbsp;</span>
                  <span class="text-wrapper-5"
                    >
                  </span>
                </p>
              </div>
            </div>
            <?php foreach($travel_meta_fields['travel_destinations'] as $destination): ?>
              
            <?php //echo '<pre>' . print_r($travel_meta_fields, true) . '</pre>'; ?>

            <?php echo $destination['fromDay']; ?>
         
                <div class="dinfotext">
                  <div class="text-wrapper-6"><?php printf(__('Van dag: %s - Tot dag: %s', 'rbs-travel'), $destination['fromDay'], $destination['toDay']); ?></div>
                  <img class="icon-smal" src="https://c.animaapp.com/By9XtBF2/img/icon-smal.svg" />
                </div>

                <?php foreach($travel_meta_fields['travel_transports'] as $travel_transport): ?>
                    <?php if ($destination['fromDay'] === $travel_transport['day']): ?>  
                        <div class="frame-3">
                          <p class="text-wrapper-7">Reizen van Schiphol naar Johannesburg</p>
                          <div class="frame-4">
                            <img class="img-2" src="https://c.animaapp.com/By9XtBF2/img/flights.png" />
                            <div class="frame-5">
                              <div class="text-wrapper-8">KLM AMS KL591</div>
                              <div class="element-amsterdam">
                              [day] => 1
                    [transportType] => FLIGHT
                    [originDestinationCode] => AMS
                    [originCode] => AMS
                    [targetDestinationCode] => REK
                    [targetCode] => KEF
                    [company] => Icelandair
                    [transportNumber] => FI501
                    [duration] => 3h 15m
                    [departureDate] => 2024-05-14
                    [arrivalDate] => 2024-05-14
                    [departureTime] => 14:10:00
                    [arrivalTime] => 15:25:00
                              </div>
                              
                            </div>
                          </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>    

                <?php foreach($travel_meta_fields['travel_cars'] as $travel_car): ?>
                    <?php if ($destination['fromDay'] === $travel_car['pickupDay']): ?>  
                        <div class="frame-6">
                          <div class="text-wrapper-7">Auto verhuur</div>
                          <div class="frame-7">
                            <img class="img-2" src="https://c.animaapp.com/By9XtBF2/img/cars.png" />
                            <div class="frame-8">
                              <div class="text-wrapper-9"><?php echo $travel_car['product'];?></div>
                              <p class="text-wrapper-10">
                              [product] => Toyota Auris 4dr
                    [imageUrl] => https://service.sunnycars.com/img/cars/small/Toyota_Auris_4dr.jpg
                    [pickupDay] => 1
                    [pickupDate] => 2024-05-14
                    [pickupLocation] => Keflavik International Airport
                    [pickupTime] => 16:30:00
                    [dropoffDay] => 6
                    [dropoffDate] => 2024-05-19
                    [dropoffLocation] => Keflavik International Airport
                    [dropoffTime] => 08:30:00

                              </p>
                            </div>
                          </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>     

                <div class="frame-9">
                  <div class="text-wrapper-7">Over: <?php echo $destination['name'];?></div>
                  <div class="frame-wrapper">
                    <div class="frame-10">
                      <div class="text-wrapper-9">Ontdek <?php echo $destination['name'];?> </div>
                      <p class="text-wrapper-10"><?php echo $destination['description'];?></p>
                      <div class="frame-11">
                        <img class="img-3" src="https://c.animaapp.com/By9XtBF2/img/image4@2x.png" />
                        <img class="img-3" src="https://c.animaapp.com/By9XtBF2/img/image5@2x.png" />
                      </div>
                      <div class="frame-11">
                        <img class="img-3" src="https://c.animaapp.com/By9XtBF2/img/ijsland2@2x.png" />
                        <img class="img-3" src="https://c.animaapp.com/By9XtBF2/img/image2@2x.png" />
                      </div>
                    </div>
                  </div>
                </div>

                <?php foreach($travel_meta_fields['travel_hotels'] as $travel_hotel): ?>
                  <?php if ($destination['fromDay'] === $travel_hotel['day']): ?>  
                      <div class="frame-12">
                        <div class="text-wrapper-7">Geselecteerd hotel</div>                
                        <div class="frame-13">
                          <img class="img-2" src="https://c.animaapp.com/By9XtBF2/img/hotels.png" />
                          <div class="frame-8">
                            <div class="text-wrapper-9"><?php echo $travel_hotel['hotelData']['name'];?></div>
                            <div class="text-wrapper-10">Text goes here</div>
                          </div>
                        </div>
                        <div class="frame-14">
                          <img class="img-3" src="https://c.animaapp.com/By9XtBF2/img/hotel4@2x.png" />
                          <img class="img-3" src="https://c.animaapp.com/By9XtBF2/img/hotel2@2x.png" />
                        </div>
                        <div class="frame-14">
                          <img class="img-3" src="https://c.animaapp.com/By9XtBF2/img/hotel1@2x.png" />
                          <img class="img-3" src="https://c.animaapp.com/By9XtBF2/img/hotel3@2x.png" />
                        </div>
                      </div>
                <?php endif; ?>
                <?php endforeach; ?>    
            <?php endforeach; ?>            

            <div class="div-wrapper"><div class="text-wrapper-11">2-9-25</div></div>
            <div class="div-wrapper"><div class="text-wrapper-11">3-9-25</div></div>
            <div class="div-wrapper"><div class="text-wrapper-11">4-9-25</div></div>
            <div class="div-wrapper"><div class="text-wrapper-11">5-9-25</div></div>
          </div>
          <img class="filter" src="https://c.animaapp.com/By9XtBF2/img/filter.svg" />
        </div>
      </div>
    </div>

  


    <style>
:root {
  --xxl-font-family: "Inter", Helvetica;
  --xxl-font-weight: 400;
  --xxl-font-size: 32px;
  --xxl-letter-spacing: 0px;
  --xxl-line-height: normal;
  --xxl-font-style: normal;
  --normal-font-family: "Inter", Helvetica;
  --normal-font-weight: 400;
  --normal-font-size: 12px;
  --normal-letter-spacing: 0px;
  --normal-line-height: normal;
  --normal-font-style: normal;
  --XL-font-family: "Inter", Helvetica;
  --XL-font-weight: 400;
  --XL-font-size: 20px;
  --XL-letter-spacing: 0px;
  --XL-line-height: normal;
  --XL-font-style: normal;
  --headlines-headline-font-family: "Inter", Helvetica;
  --headlines-headline-font-weight: 400;
  --headlines-headline-font-size: 32px;
  --headlines-headline-letter-spacing: 0px;
  --headlines-headline-line-height: normal;
  --headlines-headline-font-style: normal;
  --headlines-headline-l-font-family: "Inter", Helvetica;
  --headlines-headline-l-font-weight: 400;
  --headlines-headline-l-font-size: 64px;
  --headlines-headline-l-letter-spacing: 0px;
  --headlines-headline-l-line-height: normal;
  --headlines-headline-l-font-style: normal;
  --headlines-headline-XL-font-family: "Inter", Helvetica;
  --headlines-headline-XL-font-weight: 400;
  --headlines-headline-XL-font-size: 96px;
  --headlines-headline-XL-letter-spacing: 0px;
  --headlines-headline-XL-line-height: normal;
  --headlines-headline-XL-font-style: normal;
  --default-default-strong-font-family: "Inter", Helvetica;
  --default-default-strong-font-weight: 700;
  --default-default-strong-font-size: 16px;
  --default-default-strong-letter-spacing: 0px;
  --default-default-strong-line-height: normal;
  --default-default-strong-font-style: normal;
  --default-default-font-family: "Inter", Helvetica;
  --default-default-font-weight: 400;
  --default-default-font-size: 16px;
  --default-default-letter-spacing: 0px;
  --default-default-line-height: normal;
  --default-default-font-style: normal;
  --brand-colors-primary-20: rgba(255, 119, 0, 1);
  --brand-colors-primary-50: rgba(254, 90, 41, 1);
  --brand-colors-secondary-80: rgba(45, 48, 71, 1);
  --brand-colors-neutral-200: rgba(35, 35, 35, 1);
  --brand-colors-neutral-220: rgba(183, 183, 183, 1);
  --brand-colors-neutral-240: rgba(237, 239, 239, 1);
  --brand-colors-neutral-260: rgba(255, 255, 255, 1);
  --brand-colors-primary-30: rgba(11, 110, 79, 1);
  --size-xxs: 8px;
  --size-xs: 16px;
  --size-normal: 32px;
  --size-medium: 64px;
  --size-large: 96px;
}      
.wide {
  background-color: #8f8f8f;
  display: flex;
  flex-direction: row;
  justify-content: center;
  width: 100%;
}

.wide .overlap-wrapper {
  background-color: #8f8f8f;
  width: 1390px;
 
}

.wide .overlap {
  position: relative;
  width: 1152px;

  top: 99px;
  left: 125px;
}

.wide .frame {
  position: absolute;
  width: 1140px;
  height: 635px;
  top: 0;
  left: 0;
  background-color: #ffffff;
}

.wide .overlap-group {
  position: relative;
  height: 635px;
  background-image: url(https://c.animaapp.com/By9XtBF2/img/davide-cantelli-r0q06hjtgoc-unsplash-1.png);
  background-size: cover;
  background-position: 50% 50%;
}

.wide .dtourinfo {
  display: flex;
  flex-direction: column;
  width: 388px;
  height: 400px;
  align-items: flex-start;
  justify-content: flex-end;
  gap: 40px;
  position: absolute;
  top: 0;
  left: 752px;
  background: linear-gradient(
    180deg,
    rgba(45, 48, 71, 0.4) 0%,
    rgb(45, 48, 71) 100%
  );
}

.wide .dbook {
  display: flex;
  height: 68px;
  align-items: center;
  gap: 10px;
  padding: 10px 90px;
  position: relative;
  align-self: stretch;
  /* width: 100%; */
  background-color: #ffffff;
}

.wide .primary {
  position: relative;
  width: 12px;
  height: 10.5px;
}

.wide .div {
  flex-direction: column;
  width: 96px;
  align-items: center;
  justify-content: center;
  padding: 8px 21px;
  margin-top: -6.00px;
  margin-bottom: -6.00px;
  display: flex;
  gap: 10px;
  position: relative;
  background-color: #ffffff;
}

.wide .text-wrapper {
  position: relative;
  width: fit-content;
  margin-top: -1.00px;
  font-family: var(--normal-font-family);
  font-weight: var(--normal-font-weight);
  color: var(--brand-colors-primary-30);
  font-size: var(--normal-font-size);
  letter-spacing: var(--normal-letter-spacing);
  line-height: var(--normal-line-height);
  font-style: var(--normal-font-style);
}

.wide .text-wrapper-2 {
  position: relative;
  width: fit-content;
  margin-left: -15.50px;
  margin-right: -15.50px;
  font-family: var(--default-default-strong-font-family);
  font-weight: var(--default-default-strong-font-weight);
  color: var(--brand-colors-primary-30);
  font-size: var(--default-default-strong-font-size);
  letter-spacing: var(--default-default-strong-letter-spacing);
  line-height: var(--default-default-strong-line-height);
  white-space: nowrap;
  font-style: var(--default-default-strong-font-style);
}

.wide .img {
  position: relative;
  width: 12px;
  height: 12px;
}

.wide .frame-2 {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 8px 21px;
  position: relative;
  flex: 0 0 auto;
  margin-top: -6.00px;
  margin-bottom: -6.00px;
  background-color: #ffffff;
}

.wide .text-wrapper-3 {
  position: relative;
  width: fit-content;
  font-family: var(--default-default-strong-font-family);
  font-weight: var(--default-default-strong-font-weight);
  color: var(--brand-colors-primary-30);
  font-size: var(--default-default-strong-font-size);
  letter-spacing: var(--default-default-strong-letter-spacing);
  line-height: var(--default-default-strong-line-height);
  white-space: nowrap;
  font-style: var(--default-default-strong-font-style);
}

.wide .primary-2 {
  position: absolute;
  width: 952px;
  height: 635px;
  top: 0;
  left: 94px;
}

.wide .dcontentleft {
  display: flex;
  flex-direction: column;
  width: 751px;
  height: 2600px;
  align-items: flex-start;
  gap: 31px;
  padding: 10px;
  position: absolute;
  top: 635px;
  left: 0;
  background-color: #ffffff;
  overflow: hidden;
}

.wide .DTITLE {
  display: flex;
  width: 476px;
  align-items: flex-start;
  gap: 10px;
  padding: 10px 0px;
  position: relative;
  flex: 0 0 auto;
  background-color: #ffffff;
  overflow: hidden;
}

.wide .p {
  position: relative;
  width: 578px;
  margin-top: -1.00px;
  font-family: var(--headlines-headline-font-family);
  font-weight: var(--headlines-headline-font-weight);
  color: var(--brand-colors-primary-20);
  font-size: var(--headlines-headline-font-size);
  letter-spacing: var(--headlines-headline-letter-spacing);
  line-height: var(--headlines-headline-line-height);
  font-style: var(--headlines-headline-font-style);
}

.wide .dtabmenu {
  display: flex;
  width: 867px;
  align-items: flex-start;
  gap: 10px;
  padding: 2px 0px;
  position: relative;
  flex: 0 0 auto;
  margin-right: -136.00px;
  background-color: #ffffff;
}

.wide .dtabsmall {
  background-color: #ffffff;
  display: inline-flex;
  height: 38px;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 8px 11px;
  position: relative;
  flex: 0 0 auto;
}

.wide .circle-info {
  position: relative;
  width: 16px;
  height: 16px;
}

.wide .information {
  font-weight: var(--default-default-strong-font-weight);
  color: transparent;
  font-size: var(--default-default-strong-font-size);
  white-space: nowrap;
  position: relative;
  width: fit-content;
  font-family: var(--default-default-strong-font-family);
  letter-spacing: var(--default-default-strong-letter-spacing);
  line-height: var(--default-default-strong-line-height);
  font-style: var(--default-default-strong-font-style);
}

.wide .span {
  color: #000000;
  font-family: var(--default-default-strong-font-family);
  font-style: var(--default-default-strong-font-style);
  font-weight: var(--default-default-strong-font-weight);
  letter-spacing: var(--default-default-strong-letter-spacing);
  line-height: var(--default-default-strong-line-height);
  font-size: var(--default-default-strong-font-size);
}

.wide .text-wrapper-4 {
  color: #0b6e4f;
  font-family: var(--default-default-strong-font-family);
  font-style: var(--default-default-strong-font-style);
  font-weight: var(--default-default-strong-font-weight);
  letter-spacing: var(--default-default-strong-letter-spacing);
  line-height: var(--default-default-strong-line-height);
  font-size: var(--default-default-strong-font-size);
}

.wide .dtabsmall-2 {
  border-radius: 50px;
  overflow: hidden;
  border: 1px solid;
  border-color: #0b6e4f;
  display: inline-flex;
  height: 38px;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 8px 11px;
  position: relative;
  flex: 0 0 auto;
}

.wide .information-2 {
  font-weight: 400;
  color: transparent;
  font-size: 12px;
  position: relative;
  width: fit-content;
  font-family: "Inter", Helvetica;
  letter-spacing: 0;
  line-height: normal;
}

.wide .text-wrapper-5 {
  color: #000000;
  font-family: var(--normal-font-family);
  font-style: var(--normal-font-style);
  font-weight: var(--normal-font-weight);
  letter-spacing: var(--normal-letter-spacing);
  line-height: var(--normal-line-height);
  font-size: var(--normal-font-size);
}

.wide .dinfotext {
  display: flex;
  width: 724px;
  align-items: flex-start;
  gap: 10px;
  padding: 10px;
  position: relative;
  flex: 0 0 auto;
  background-color: var(--brand-colors-primary-20);
}

.wide .text-wrapper-6 {
  flex: 1;
  position: relative;
  margin-top: -1.00px;
  font-family: var(--headlines-headline-font-family);
  font-weight: var(--headlines-headline-font-weight);
  color: #ffffff;
  font-size: var(--headlines-headline-font-size);
  letter-spacing: var(--headlines-headline-letter-spacing);
  line-height: var(--headlines-headline-line-height);
  font-style: var(--headlines-headline-font-style);
}

.wide .icon-smal {
  position: relative;
  width: 58px;
  height: 45px;
}

.wide .frame-3 {
  display: flex;
  flex-direction: column;
  width: 724px;
  height: 256px;
  align-items: flex-start;
  gap: 10px;
  padding: 20px;
  position: relative;
  background-color: #ffffff;
  box-shadow: 0px 4px 4px #00000040;
}

.wide .text-wrapper-7 {
  position: relative;
  width: fit-content;
  margin-top: -1.00px;
  font-family: var(--default-default-strong-font-family);
  font-weight: var(--default-default-strong-font-weight);
  color: var(--brand-colors-primary-20);
  font-size: var(--default-default-strong-font-size);
  letter-spacing: var(--default-default-strong-letter-spacing);
  line-height: var(--default-default-strong-line-height);
  white-space: nowrap;
  font-style: var(--default-default-strong-font-style);
}

.wide .frame-4 {
  display: flex;
  height: 187px;
  align-items: flex-start;
  gap: 40px;
  padding: 10px;
  position: relative;
  align-self: stretch;
  width: 100%;
  background-color: #ffffff;
  overflow: hidden;
}

.wide .img-2 {
  position: relative;
  width: 144px;
  height: 144px;
  object-fit: cover;
}

.wide .frame-5 {
  width: 503px;
  padding: 10px;
  margin-right: -10.00px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 10px;
  position: relative;
  align-self: stretch;
  background-color: #ffffff;
}

.wide .text-wrapper-8 {
  position: relative;
  width: 401px;
  margin-top: -1.00px;
  font-family: var(--default-default-strong-font-family);
  font-weight: var(--default-default-strong-font-weight);
  color: #ff7700;
  font-size: var(--default-default-strong-font-size);
  letter-spacing: var(--default-default-strong-letter-spacing);
  line-height: var(--default-default-strong-line-height);
  font-style: var(--default-default-strong-font-style);
}

.wide .element-amsterdam {
  position: relative;
  align-self: stretch;
  font-family: var(--normal-font-family);
  font-weight: var(--normal-font-weight);
  color: #ff7700;
  font-size: var(--normal-font-size);
  letter-spacing: var(--normal-letter-spacing);
  line-height: var(--normal-line-height);
  font-style: var(--normal-font-style);
}

.wide .frame-6 {
  display: flex;
  flex-direction: column;
  width: 724px;
  height: 248px;
  align-items: flex-start;
  gap: 10px;
  padding: 20px;
  position: relative;
  background-color: #ffffff;
  box-shadow: 0px 4px 4px #00000040;
}

.wide .frame-7 {
  height: 179px;
  gap: 40px;
  display: flex;
  align-items: flex-start;
  padding: 10px;
  position: relative;
  align-self: stretch;
  width: 100%;
  background-color: #ffffff;
}

.wide .frame-8 {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 10px;
  padding: 10px;
  position: relative;
  flex: 1;
  align-self: stretch;
  flex-grow: 1;
  background-color: #ffffff;
}

.wide .text-wrapper-9 {
  position: relative;
  width: 401px;
  margin-top: -1.00px;
  font-family: var(--default-default-strong-font-family);
  font-weight: var(--default-default-strong-font-weight);
  color: #000000;
  font-size: var(--default-default-strong-font-size);
  letter-spacing: var(--default-default-strong-letter-spacing);
  line-height: var(--default-default-strong-line-height);
  font-style: var(--default-default-strong-font-style);
}

.wide .text-wrapper-10 {
  position: relative;
  align-self: stretch;
  font-family: var(--normal-font-family);
  font-weight: var(--normal-font-weight);
  color: #000000;
  font-size: var(--normal-font-size);
  letter-spacing: var(--normal-letter-spacing);
  line-height: var(--normal-line-height);
  font-style: var(--normal-font-style);
}

.wide .frame-9 {
  display: flex;
  flex-direction: column;
  width: 724px;
  height: 565px;
  align-items: flex-start;
  gap: 10px;
  padding: 20px;
  position: relative;
  background-color: #ffffff;
  overflow: hidden;
  box-shadow: 0px 4px 4px #00000040;
}

.wide .frame-wrapper {
  height: 615px;
  gap: 10px;
  margin-bottom: -20.00px;
  display: flex;
  align-items: flex-start;
  padding: 10px;
  position: relative;
  align-self: stretch;
  width: 100%;
  background-color: #ffffff;
}

.wide .frame-10 {
  padding: 0px 10px;
  flex: 1;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 10px;
  position: relative;
  align-self: stretch;
  background-color: #ffffff;
}

.wide .frame-11 {
  align-items: flex-start;
  align-self: stretch;
  width: 100%;
  flex: 0 0 auto;
  display: flex;
  gap: 10px;
  position: relative;
  background-color: #ffffff;
}

.wide .img-3 {
  position: relative;
  width: 315px;
  height: 192px;
  object-fit: cover;
}

.wide .frame-12 {
  display: flex;
  flex-direction: column;
  width: 724px;
  height: 677px;
  align-items: flex-start;
  gap: 10px;
  padding: 20px;
  position: relative;
  background-color: #ffffff;
  box-shadow: 0px 4px 4px #00000040;
}

.wide .frame-13 {
  gap: 40px;
  flex: 0 0 auto;
  display: flex;
  align-items: flex-start;
  padding: 10px;
  position: relative;
  align-self: stretch;
  width: 100%;
  background-color: #ffffff;
}

.wide .frame-14 {
  width: 644px;
  align-items: flex-start;
  flex: 0 0 auto;
  display: flex;
  gap: 10px;
  position: relative;
  background-color: #ffffff;
}

.wide .div-wrapper {
  flex-direction: column;
  display: flex;
  width: 724px;
  align-items: flex-start;
  gap: 10px;
  padding: 10px;
  position: relative;
  flex: 0 0 auto;
  background-color: var(--brand-colors-primary-20);
}

.wide .text-wrapper-11 {
  width: fit-content;
  position: relative;
  margin-top: -1.00px;
  font-family: var(--headlines-headline-font-family);
  font-weight: var(--headlines-headline-font-weight);
  color: #ffffff;
  font-size: var(--headlines-headline-font-size);
  letter-spacing: var(--headlines-headline-letter-spacing);
  line-height: var(--headlines-headline-line-height);
  font-style: var(--headlines-headline-font-style);
}

.wide .filter {
  position: absolute;
  width: 412px;
  height: 2844px;
  top: 401px;
  left: 740px;
}




    </style>













<?php //$filtered_posts = rbstravel_get_ideas($args = array(), $json_encode = false, $filter_terms); die('zzzz') ?>
<?php //echo '<pre>' . print_r($travel_meta_fields, true) . '</pre>';?> 






<?php
$travel_meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields(); 
$vars = array();
$vars['travel_meta_fields'] = $travel_meta_fields;
?>

<section class="rbs-travel-idea__section">
        <?php
            $template = 'frontend' . DIRECTORY_SEPARATOR . 'single-idea' . DIRECTORY_SEPARATOR .  'fullmap';
            echo rbstravel_template_loader($template, $vars, null, array('add_wrapper' => false));  
        ?>
</section>



<?php
$template = 'frontend' . DIRECTORY_SEPARATOR . 'single-idea' . DIRECTORY_SEPARATOR .  'hero';
$args = array(
	'add_wrapper' => false,                
);
echo rbstravel_template_loader($template, $vars, null, $args);     

$template = 'frontend' . DIRECTORY_SEPARATOR . 'single-idea' . DIRECTORY_SEPARATOR .  'tabs';
$args = array(
	'add_wrapper' => false,                
);
echo rbstravel_template_loader($template, $vars, null, $args);  
?>

<?php 
    
?>


<div id="single-rbs-travel-idea-<?php echo $post->ID;?>" class="single-rbs-travel-idea">
    <header class="rbs-travel-idea__header">
	<h1><?php echo get_the_title(); ?></h1>
    </header>



    <section class="rbs-travel-idea__thumbnail">
	<img src="<?php echo get_the_post_thumbnail_url(null, 'full'); ?>" title="<?php echo get_the_title(); ?>" alt="<?php echo get_the_title(); ?>" />
    </section>    
    
    <section class="rbs-travel-idea__index">
	<ul>
	    <li><a href="#travel-description"><?php _e('Description', 'rbs-travel'); ?></a></li>
	    <li><a href="#travel-remarks"><?php _e('Remarks', 'rbs-travel'); ?></a></li>
	    <li><a href="#travel-prices"><?php _e('Prices', 'rbs-travel'); ?></a></li>
	    <li><a href="#travel-destinations"><?php _e('Destinations', 'rbs-travel'); ?></a></li>
	    <li><a href="#travel-hotels"><?php _e('Hotels', 'rbs-travel'); ?></a></li>
	    <li><a href="#travel-transfers"><?php _e('Transfers', 'rbs-travel'); ?></a></li>
	</ul>
    </section>
    
    <section class="rbs-travel-idea__content">
	<h2 id="travel-description" class="rbs-travel-idea__subtitle"><?php _e('Description', 'rbs-travel'); ?></h2>
	<?php echo get_the_content(); ?>
    </section>
        
    <section class="rbs-travel-idea__content">
	<h2 id="travel-remarks" class="rbs-travel-idea__subtitle"><?php _e('Remarks', 'rbs-travel'); ?></h2>
	<?php echo $travel_meta_fields['travel_remarks']; ?>
    </section>
    
    <section class="rbs-travel-idea__content">
	<h2 id="travel-prices" class="rbs-travel-idea__subtitle"><?php _e('Prices', 'rbs-travel'); ?></h2>
	<p><?php printf(__('Total price: %s'), $travel_meta_fields['travel_total_price']['amount'] . ' ' . $travel_meta_fields['travel_total_price']['currency']); ?></p>
	<p><?php printf(__('Price per person: %s'), $travel_meta_fields['travel_price_per_person']['amount'] . ' ' . $travel_meta_fields['travel_price_per_person']['currency']); ?></p>
    </section>         

    <section class="rbs-travel-idea__content">
	<h2 id="travel-destinations" class="rbs-travel-idea__subtitle"><?php _e('Destinations', 'rbs-travel'); ?></h2>
	<?php rbstravel_template_part('destinations'); ?>
    </section>    
    
    <section class="rbs-travel-idea__content">
	<h2 id="travel-hotels" class="rbs-travel-idea__subtitle"><?php _e('Hotels', 'rbs-travel'); ?></h2>
	<?php rbstravel_template_part('hotels'); ?>
    </section>     
    
    <section class="rbs-travel-idea__content">
	<h2 id="travel-transfers" class="rbs-travel-idea__subtitle"><?php _e('Transfers', 'rbs-travel'); ?></h2>
	<?php rbstravel_template_part('transfers'); ?>
    </section>    
    
    
    <hr>
    
    <section class="rbs-travel-idea__meta">
	<h2><?php _e('Travel Meta Fields', 'rbs-travel');?></h2>
	<table>
	    <?php foreach($travel_meta_fields as $meta_key => $meta_value): ?>
	    <tr>
		<th><?php echo $meta_key; ?></th>
		<td><?php echo is_array($meta_value) ? '<pre>' . print_r($meta_value, true) . '</pre>' : $meta_value; ?></td>
	    </tr>
	    <?php endforeach; ?>
	</table>
    </section>    
</div>
<?php get_footer(); ?>
