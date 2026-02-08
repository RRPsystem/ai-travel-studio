<?php
if (!defined('ABSPATH')) {
    exit;
}
global $travel_meta_fields;

?>

<?php get_header(); ?>
  
<div style="border: 2px solid #000000" class="OVERVIEW-SCREEN">
<div class="overview-wrapper">

      <div class="content-left">
        <div class="rbs-travel-overview">
          <div class="earth-americas">
            <img class="primary" src="https://c.animaapp.com/ZbVsYiqQ/img/primary.png" />
          </div>
          <div class="text-wrapper">Bestemmingen</div>
          <div class="checkitem">
            <div class="rectangle"></div>
            <div class="div">Afrika</div>
          </div>
          <div class="checkitem-2">
            <div class="rectangle-2"></div>
            <div class="div">Europa</div>
          </div>
          <div class="checkitem-2">
            <div class="rectangle"></div>
            <div class="div">Nederland</div>
          </div>
          <div class="checkitem-2">
            <div class="rectangle"></div>
            <div class="afrika">Amerika</div>
          </div>
          <div class="rbs-tof-min-price">
            <div class="div">Minimale prijs</div>
            <img class="rbs-tofmps-slider" src="https://c.animaapp.com/ZbVsYiqQ/img/rbs-tofmps-slider.svg" />
            <div class="rbs-tofmps-value">500</div>
          </div>
          <button class="button">
            <div class="zoeken">ZOEKEN</div>
            <div class="magnifying-glass"></div>
          </button>
          <button class="button">
            <div class="zoeken">ZOEKEN</div>
            <div class="magnifying-glass"></div>
          </button>          
          <div class="check"></div>
        </div>
        <?php
        $template = 'frontend' . DIRECTORY_SEPARATOR . 'idealist-filter';
          $args = array(
              'add_wrapper' => false,                
          );
          //echo rbstravel_template_loader($template, $vars, null, $args);  
          ?>
      </div>

             
      



      <div class="content-right">
          <?php 
          $counter = 0;
          foreach ($vars['ideas'] as $idea) : ?>   
         <?php //echo '<pre>' . print_r($idea['idea']['travel_price_per_person'], true) . '</pre>';die('xx');?>    
           <?php //echo get_permalink($idea['post']->ID);die('xx');?>    
       
            <?php if($counter === 2) : ?>
              <!-- <img class="brand-mark" src="https://c.animaapp.com/ZbVsYiqQ/img/brand-mark.svg" /> -->
           
              <!-- <div style="opacity: 0.5;height: 400px;width: 100%;background: url('<?php //echo RBS_TRAVEL_PLUGIN_URL . 'assets/images/brandmark.jpg';?>'); background-size: cover;background-position: right -1px;background-repeat: no-repeat;"></div>
              <img class="img" src="<?php //echo RBS_TRAVEL_PLUGIN_URL . 'assets/images/tour_planning.svg';?>" /> -->


            <?php endif; ?>
         
            <div class="rbs-travel-overview-2">
            <div class="rbs-travel-ovr-left">
            <img class="image" src="<?php echo $idea['idea']['travel_image_url']; ?>">
            </div>
            <div class="rbs-travel-ovr">
              <div class="ovr-middle-title">
                <div class="ovr-middle-title-2"><?php echo $idea['post']->post_title;?></div>
                <img class="ovr-middle-icon" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-middle-icon.svg" />
              </div>
              <div class="ovr-middle-features">
                <div class="feature-item">
                  <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
                  <div class="text-wrapper-2">Zwembad</div>
                </div>
                <div class="feature-item">
                  <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
                  <div class="text-wrapper-2">Kindvriendelijk</div>
                </div>
                <div class="feature-item">
                  <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
                  <div class="text-wrapper-2">WIFI</div>
                </div>
                <div class="feature-item">
                  <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
                  <div class="text-wrapper-2">Goed bereikbaar</div>
                </div>
              </div>
              <p class="ovr-middle">
              <?php echo $idea['post']->post_content; ?>
              </p>
              <!-- <div class="ovr-middle-bottom">
                <div class="ovr-middle-bb">
                  <div class="ovr-mbb-text">BESCHIKBAARHEID</div>
                  <img class="ovr-mbb-icon" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-mbb-icon.svg" />
                </div>
              </div> -->
            </div>
            <div class="rbs-travel-ovr-right">
              <img class="ovr-right-map" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-right-map@2x.png" />
              <div class="ovr-right-bottom">
                <div class="ovr-right-cleft">
                  <div class="ovr-right-price-text">EURO</div>
                  <div class="ovr-right-price"><?php echo $idea['idea']['travel_price_per_person']['amount']; ?></div>
                </div>
                <div class="ovr-right-cright">
                  <div class="ovr-right-button">
                    <div class="ovr-right-button-2">INFO</div>
                    <a href="<?php echo get_permalink($idea['post']->ID);?>  "><span style="margin-right: 50px;color: #ffffff; font-size: 12px;">bekijken</span></a>   
                    <img class="ovr-right-icon" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-right-icon.svg" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php $counter ++;endforeach; ?>
         
          <?php 
          $template = 'frontend' . DIRECTORY_SEPARATOR . 'idealist-overview';
          $args = array(
              'add_wrapper' => false,                
          );
             // echo rbstravel_template_loader($template, $vars, null, $args);  
          ?>   
             
      </div>      




  </div>
</div>

<div style="border: 2px solid #000000" class="OVERVIEW-SCREEN">
      <div class="overview-wrapper">
        <div class="content-left">
          <div class="rbs-travel-overview">
            <div class="earth-americas">
              <img class="primary" src="https://c.animaapp.com/ZbVsYiqQ/img/primary.png" />
            </div>
            <div class="text-wrapper">Bestemmingen</div>
            <div class="checkitem">
              <div class="rectangle"></div>
              <div class="div">Afrika</div>
            </div>
            <div class="checkitem-2">
              <div class="rectangle-2"></div>
              <div class="div">Europa</div>
            </div>
            <div class="checkitem-2">
              <div class="rectangle"></div>
              <div class="div">Nederland</div>
            </div>
            <div class="checkitem-2">
              <div class="rectangle"></div>
              <div class="afrika">Amerika</div>
            </div>
            <div class="rbs-tof-min-price">
              <div class="div">Minimale prijs</div>
              <img class="rbs-tofmps-slider" src="https://c.animaapp.com/ZbVsYiqQ/img/rbs-tofmps-slider.svg" />
              <div class="rbs-tofmps-value">500</div>
            </div>
            <button class="button">
              <div class="zoeken">ZOEKEN</div>
              <div class="magnifying-glass"></div>
            </button>
            <div class="check"></div>
          </div>
        </div>
        <div class="content-right">
          <div class="rbs-travel-overview-2">
            <div class="rbs-travel-ovr-left">
              <img class="image" src="https://c.animaapp.com/ZbVsYiqQ/img/image@2x.png" />
            </div>
            <div class="rbs-travel-ovr">
              <div class="ovr-middle-title">
                <div class="ovr-middle-title-2">IJSLAND</div>
                <img class="ovr-middle-icon" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-middle-icon.svg" />
              </div>
              <div class="ovr-middle-features">
                <div class="feature-item">
                  <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
                  <div class="text-wrapper-2">Zwembad</div>
                </div>
                <div class="feature-item">
                  <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
                  <div class="text-wrapper-2">Kindvriendelijk</div>
                </div>
                <div class="feature-item">
                  <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
                  <div class="text-wrapper-2">WIFI</div>
                </div>
                <div class="feature-item">
                  <img class="img" src="https://c.animaapp.com/ZbVsYiqQ/img/primary-3.svg" />
                  <div class="text-wrapper-2">Goed bereikbaar</div>
                </div>
              </div>
              <p class="ovr-middle">
                Reykjavik is geen typische hoofdstad.&nbsp;&nbsp;Gelegen net ten zuiden van de poolcirkel is het de
                meest noordelijke&nbsp;&nbsp;hoofdstad ter wereld en wordt beschouwd als een van de
                schoonste,&nbsp;&nbsp;veiligste en groenste steden ter wereld.<br />Reykjavik&nbsp;&nbsp;ligt aan de
                rand van de Atlantische Oceaan - tussen twee fjorden - en&nbsp;&nbsp;wordt omringt door een adembenemend
                maanvulkanisch landschap.
              </p>
              <div class="ovr-middle-bottom">
                <div class="ovr-middle-bb">
                  <div class="ovr-mbb-text">BESCHIKBAARHEID</div>
                  <img class="ovr-mbb-icon" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-mbb-icon.svg" />
                </div>
              </div>
            </div>
            <div class="rbs-travel-ovr-right">
              <img class="ovr-right-map" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-right-map@2x.png" />
              <div class="ovr-right-bottom">
                <div class="ovr-right-cleft">
                  <div class="ovr-right-price-text">EURO</div>
                  <div class="ovr-right-price">3500</div>
                </div>
                <div class="ovr-right-cright">
                  <div class="ovr-right-button">
                    <div class="ovr-right-button-2">INFO</div>
                    <img class="ovr-right-icon" src="https://c.animaapp.com/ZbVsYiqQ/img/ovr-right-icon.svg" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <img class="brand-mark" src="https://c.animaapp.com/ZbVsYiqQ/img/brand-mark.svg" />
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
            <div class="dinfotext">
              <div class="text-wrapper-6">1-9-25</div>
              <img class="icon-smal" src="https://c.animaapp.com/By9XtBF2/img/icon-smal.svg" />
            </div>
            <div class="frame-3">
              <p class="text-wrapper-7">Reizen van Schiphol naar Johannesburg</p>
              <div class="frame-4">
                <img class="img-2" src="https://c.animaapp.com/By9XtBF2/img/flights.png" />
                <div class="frame-5">
                  <div class="text-wrapper-8">KLM AMS KL591</div>
                  <div class="element-amsterdam">10:35 Amsterdam Schiphol<br />13:45 Johannesburg</div>
                </div>
              </div>
            </div>
            <div class="frame-6">
              <div class="text-wrapper-7">Auto verhuur</div>
              <div class="frame-7">
                <img class="img-2" src="https://c.animaapp.com/By9XtBF2/img/cars.png" />
                <div class="frame-8">
                  <div class="text-wrapper-9">TOYOTA URBAN CRUISER</div>
                  <p class="text-wrapper-10">Info about car goes here...</p>
                </div>
              </div>
            </div>
            <div class="frame-9">
              <div class="text-wrapper-7">Over Reijkjavik</div>
              <div class="frame-wrapper">
                <div class="frame-10">
                  <div class="text-wrapper-9">Ontdek Reijkjavik!</div>
                  <p class="text-wrapper-10">Text about destination goes here</p>
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
            <div class="frame-12">
              <div class="text-wrapper-7">Geselecteerd hotel</div>
              <div class="frame-13">
                <img class="img-2" src="https://c.animaapp.com/By9XtBF2/img/hotels.png" />
                <div class="frame-8">
                  <div class="text-wrapper-9">KIWARA GUEST HOUSE</div>
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
  height: 3344px;
}

.wide .overlap {
  position: relative;
  width: 1152px;
  height: 3245px;
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
  width: 100%;
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































<div class="rbs_travel_wrapper">
    <div class="rbs_travel_container">
    <div class="rbs_travel_overview_left">

        <svg class="watermark rbs_travel_center" width="160" height="160" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M18.0312 60.3125L20.9688 65.4375C23.5625 69.9688 27.8125 73.3125 32.8438 74.75L50.9375 79.9062C56.3125 81.4375 60 86.3438 60 91.9375V104.406C60 107.844 61.9375 110.969 65 112.5C68.0625 114.031 70 117.156 70 120.594V132.781C70 137.656 74.6562 141.188 79.3438 139.844C84.375 138.406 88.2812 134.375 89.5625 129.281L90.4375 125.781C91.75 120.5 95.1875 115.969 99.9062 113.281L102.438 111.844C107.125 109.188 110 104.188 110 98.8125V96.2188C110 92.25 108.406 88.4375 105.594 85.625L104.375 84.4062C101.562 81.5938 97.75 80 93.7812 80H80.3125C76.8438 80 73.4062 79.0938 70.375 77.375L59.5938 71.2188C58.25 70.4375 57.2188 69.1875 56.7188 67.7188C55.7188 64.7188 57.0625 61.4688 59.9062 60.0625L61.75 59.125C63.8125 58.0938 66.2188 57.9062 68.4062 58.6562L75.6562 61.0625C78.2188 61.9062 81.0312 60.9375 82.5 58.7188C83.9688 56.5312 83.8125 53.625 82.125 51.5938L77.875 46.5C74.75 42.75 74.7812 37.2812 77.9688 33.5938L82.875 27.875C85.625 24.6562 86.0625 20.0625 83.9688 16.4062L83.2188 15.0938C82.125 15.0312 81.0625 15 79.9688 15C50.9687 15 26.375 34.0312 18.0312 60.3125ZM145 80C145 68.5 142 57.6875 136.75 48.2812L128.75 51.5C123.844 53.4688 121.312 58.9375 122.969 63.9375L128.25 79.7812C129.344 83.0312 132 85.5 135.312 86.3125L144.406 88.5938C144.781 85.7812 144.969 82.9062 144.969 80H145ZM0 80C0 58.7827 8.42855 38.4344 23.4315 23.4315C38.4344 8.42855 58.7827 0 80 0C101.217 0 121.566 8.42855 136.569 23.4315C151.571 38.4344 160 58.7827 160 80C160 101.217 151.571 121.566 136.569 136.569C121.566 151.571 101.217 160 80 160C58.7827 160 38.4344 151.571 23.4315 136.569C8.42855 121.566 0 101.217 0 80Z" fill="white" fill-opacity="0.2"/>
        </svg>
            <div class="rbs_travel_filterblock rbs_travel_center">
            <?php 
            $template = 'frontend' . DIRECTORY_SEPARATOR . 'idealist-filter';
            $args = array(
                'add_wrapper' => false,                
            );
            echo rbstravel_template_loader($template, $vars, null, $args);  
        
            ?>    
            
            <button class="rbs_travelbutton">
            <div class="zoeken">ZOEKEN</div>
            <img class="magnifying-glass" src="<?php echo RBS_TRAVEL_PLUGIN_URL . 'assets/images/magnifying-glass.svg';?>" />
            </button>
            </div>
     
    

    </div>
    <div class="rbs_travel_overview_right">
        
    </div>    


    </div>
</div>


<style>
    *,
    *::before,
    *::after 
    {
    box-sizing: border-box;
    }

    * {
    margin: 0,
    padding: 0,
    line-height: 1.5
    }

    :root {
        --main-bg-color: #8f8f8f;
        --primary-orange: #ff7700;
        --primary-green: #0b6e4f;
        --primary-blue: #2D3047;
    }


    body {
        background-color: var(--main-bg-color);
    }


    .rbs_travel_wrapper {
        background: red;
        width: 100%;

    }
    .rbs_travel_container {
        width: 1140px;
        background: #ffffff;
        margin: 0 auto;
        min-height: 500px;
    }

    .rbs_travel_overview_left {

        background-color: var(--primary-blue);
        display: flex;
        flex-direction: column;
       
        
        width: 388px;
        height: 2502px;
        padding: 22px 41px;
        flex-direction: column;
   
        gap: 40px;
        flex-shrink: 0;    
    }

    .rbs_travel_center {
        margin: 0 auto;
    }

    .rbs_travel_filterblock {
        
        width: 100%;

    }

    .rbs_travel_filterblock .rbs_travelbutton {
        background-color: var(--primary-orange);
        border-radius: 20px;
        display: flex;
        width: 100%;;
        padding: 12px 18px;
        justify-content: space-between;
        align-items: flex-start;
        align-self: stretch;
        border: 0px solid #ffffff;
    }

    .rbs_travel_filterblock .rbs_travelbutton .zoeken {

        color: #ffffff;

    }

    .rbs_travel_filterblock .rbs_travelbutton .magnifying-glass {
        position: relative;
        width: 16px;
        height: 16px;
    }

 
    
</style>



<div class="idealist_wrapper">

    <div class="column_left">
        <h2>Filter</h2>
        <?php 
        $template = 'frontend' . DIRECTORY_SEPARATOR . 'idealist-filter';
        $args = array(
            'add_wrapper' => false,                
        );
        echo rbstravel_template_loader($template, $vars, null, $args);  
       
        ?>      
    </div>
    <div class="column_right">
        <h2>Reizen</h2>
        <?php 
        $template = 'frontend' . DIRECTORY_SEPARATOR . 'idealist-overview';
        $args = array(
            'add_wrapper' => false,                
        );
            echo rbstravel_template_loader($template, $vars, null, $args);  
        ?>      
    </div>

</div>

<style>


    .idealist_wrapper {
        display: flex;
        flex-direction: row;
    }

    .column_left  {
       
        width: 30%;
    }
    .column_left h2 {
        padding-left: 10px;
    }



    .column_right {
        width: 70%;
    }

    @media only screen and (min-width: 601px) {

    }        

    @media only screen and (max-width: 600px) {
        body {
            color: red !important;
        }
    }    
</style>