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





<?php get_footer(); ?>
