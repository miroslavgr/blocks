<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
 
function primex_import_func()
{
    //iterate the file
    $file_name = WP_CONTENT_DIR."/themes/storefront/primex_b2b_prices";
    $row = 0;
    
    global $wpdb;
    
    $args = array(
        'status' => array( 'draft', 'pending', 'private', 'publish' ),
        'type' => array_merge( array_keys( wc_get_product_types() ) ),
        'parent' => null,
        'sku' => '',
        'category' => array(),
        'tag' => array(),
        'limit' =>400,
        'offset' => null,
        'page' => 1,
        'include' => array(),
        'exclude' => array(),
        'orderby' => 'date',
        'order' => 'DESC',
        'return' => 'objects',
        'paginate' => false,
        'shipping_class' => array());
    
    $variables = wc_get_products( $args );
    
    if (($handle = fopen($file_name.".csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 100000, ",")) !== FALSE) {
            
            $num = count($data);
         
              //echo "<p> $num fields in line $row: <br /></p>\n";
              
            $row++;
            
            if($row == 1)
            {
               
                continue;
            }
            
            for ($c=0; $c < $num; $c++) {
        
               $string_row = $data[$c];
               
               $array_row = explode(";",$string_row);
               
               $sku = 0;
               $name = 0;
               $distributor_tyre_id = 0;
               $stock = 0;
               $price_bgn = 0;
               $price_euro = 0;
               $brand = 0;
               $tyre = 0;
               $season = 0;
               $vendor_width = 0;
               $vendor_height = 0;
               $vendor_diameter = 0;
               $vehicle = 0;
               $image_url = 0;
               $netto_kg = 0;
               
               for($i = 0; $i < count($array_row); $i ++)
               {
               
                       
                   switch ($i) {
                       case 0:
                           $sku = $array_row[$i];
                           break;
                       case 1:
                           $name = $array_row[$i];
                           break;
                       case 2:
                           $distributor_tyre_id = $array_row[$i];
                           break;
                       case 3:
                           $stock = $array_row[$i];
                           break;
                       case 4:
                           $price_bgn = $array_row[$i];
                           break;
                       case 5:
                           $price_euro = $array_row[$i];
                           break;
                       case 6:
                           $vendor_marka = $array_row[$i];
                           break;
                       case 7:
                           $vendor_sharka = $array_row[$i];
                           break;
                       case 8:
                           $season = $array_row[$i];
                           break;
                       case 9:
                           $vendor_width = $array_row[$i];
                           break;
                       case 10:
                           $vendor_height = $array_row[$i];
                           break;
                       case 11:
                           $vendor_diameter = $array_row[$i];
                           break;   
                       case 12:
                           $vehicle = $array_row[$i];
                           break;
                       case 13:
                           $image_url = $array_row[$i];
                           break;
                       case 14:
                           $vendor_load_index = $array_row[$i];
                           break;
                       case 22:
                           $netto_kg = $array_row[$i];
                           break;              
                  }
               }
               // after the for loop with the row from primex, find the product in Woocommerce
               //$wc_tyre = find_tyre_in_wc($all_wc_products, $marka, $sharka, $width, $height, $diameter, $vehicle);
               
              
               foreach($variables as $variable)
               {
                   
                   if (strpos($variable->get_name(), $vendor_sharka) !== false)
                   {
                       
                   
                   $variations =$variable->get_children();
                   foreach ($variations as $value) {
                       
                       $single_variation=new WC_Product_Variation($value);
              
                       // this vendor puts BFGoodrich together
                       if($vendor_marka == "BFGoodrich")
                       {
                           $vendor_marka = "BF Goodrich";
                       }
                       
                      
                       // Find wc_sharka from variable
                       $terms = get_the_terms( $variable->get_id(), 'product_cat' );
                       foreach($terms as $term)
                       {
                           if($term->parent)
                           {
                               $wc_sharka = $term->name;
                           }
                       }
                       
                       $variation_attributes = $single_variation->get_attributes();
            
                      
                       // extract Marka from name by using the sharka category name
                       $wc_name = $single_variation->get_name();
                       $wc_marka_arr = explode($wc_sharka, $wc_name);
                       $wc_marka = trim($wc_marka_arr[0]);
                       $wc_width = $variation_attributes["pa_width"];
                       $wc_load_index = $variation_attributes["pa_load_index"];
                       
                       $wc_profile = $variation_attributes["pa_profile"];
                       $wc_diameter = $variation_attributes["pa_diameter"];
                       
                       $vendor_profile = $vendor_height;
                     
                       $vendor_load_index1 = 0;
                       $vendor_load_index2 = 0;
                       //vendor can have 113/111 format load index
                       $vendor_load_index_arr = explode("/",$vendor_load_index);
                       if(count($vendor_load_index_arr) == 2)
                       {
                           $vendor_load_index1 = $vendor_load_index_arr[0];
                           $vendor_load_index2 = $vendor_load_index_arr[1];
                       }
                       else
                       {
                           $vendor_load_index1 = $vendor_load_index;
                       }
                       
                    
                       if($wc_marka == $vendor_marka && 
                          $wc_sharka == $vendor_sharka &&
                          $wc_width == $vendor_width &&
                           $wc_profile == $vendor_profile &&
                          $wc_diameter == $vendor_diameter &&
                           ($vendor_load_index1 == $wc_load_index || $vendor_load_index2 == $wc_load_index))
                       {
                           //echo $wc_marka." ".$wc_sharka." ".$wc_width." ".$wc_profile." ".$wc_diameter."<br>";
                          
                           
                           //var_dump($single_variation);
                           //echo $distributor_tyre_id." ".$vendor_marka." ".$vendor_sharka." ".$vendor_width." ".$vendor_profile." ".$vendor_diameter."<br>";
                           
                           //We have got a match, update the woocommerce product
                           //$single_variation upgrade this.
                           
                           //echo $vehicle. " ".;
                        
                         
                       }
                       
                   }
               }
               }
               
               /*if($wc_tyre)
               {
                   // put new cost of good, put new regular and sale prices, put stock
               }*/
               
               
               
            }
            
        }
            
            
        
        fclose($handle);
        
    }
}

function primex_import_init()
{
    add_shortcode('primex_import', 'primex_import_func');
}

add_action('init', 'primex_import_init');

 
 //Main example 
function dump_wc_func($atts = [], $content = null, $tag = '')
{
  //28285
  global $wpdb;  

return;
  $args = array( 
        'status' => array( 'draft', 'pending', 'private', 'publish' ),  
        'type' => array_merge( array_keys( wc_get_product_types() ) ),  
        'parent' => null,  
        'sku' => '',  
        'category' => array(),  
        'tag' => array(),  
        'limit' =>50,  
        'offset' => null,  
        'page' => 9,  
        'include' => array(),  
        'exclude' => array(),  
        'orderby' => 'date',  
        'order' => 'DESC',  
        'return' => 'objects',  
        'paginate' => false,  
        'shipping_class' => array());
 
  $variables = wc_get_products( $args );
  
  

  /*
   $attribute_taxonomies = wc_get_attribute_taxonomies();
  
  foreach($variables as $variable)
    {
            $variations1=$variable->get_children();
        foreach ($variations1 as $value) {
            
            $single_variation=new WC_Product_Variation($value);
            

            
             foreach ( $attribute_taxonomies as $tax ) {
                if ( $name = wc_attribute_taxonomy_name( $tax->attribute_name ) ) {
                    $product_terms = wp_get_object_terms( $single_variation->get_id(),  $name );
                      var_dump($product_terms);
                }
             }
        
           
              foreach($single_variation->attributes as $attribute_name => $attribute_value)
             {
      	    	$term = get_term_by( 'name', $attribute_value, $attribute_name );
				
			//	var_dump($term);
				
				//	$sql = $wpdb->prepare( "INSERT INTO  ".$wpdb->prefix."term_relationships (object_id, term_taxonomy_id,term_order ) VALUES ( %d, %d, %d )", $variation_id, $term->term_id, 1 );
				//	$wpdb->query($sql);

             }
           // return;
        }
    }*/
  
   // echo count($variables);
  
  foreach($variables as $variable)
  {
      
      $variations =$variable->get_children();
        foreach ($variations as $value) {
            
            $single_variation=new WC_Product_Variation($value);
             
                    
              foreach($single_variation->attributes as $attribute_name => $attribute_value)
              {
      
          		$term = get_term_by( 'slug', $attribute_value, $attribute_name );
    			
    		//	echo $attribute_name. " ".$attribute_value. " ";
    			
    				
    					$sql = $wpdb->prepare( "INSERT INTO  ".$wpdb->prefix."term_relationships (object_id, term_taxonomy_id,term_order ) VALUES ( %d, %d, %d )", $single_variation->get_id(), $term->term_id, 1 );
    			
    		//	echo $sql. " <br>";
    				$wpdb->query($sql);

            }
            //echo $single_variation->get_id();
        }
  }
    return;
  /*
  $variation_id = 28285;
  $buff = "";
  $product = wc_get_product($variation_id); // post id
  
  foreach($product->attributes as $attribute_name => $attribute_value)
  {
      
      
      		$term = get_term_by( 'name', $attribute_value, $attribute_name );
				
				var_dump($term);
				
					$sql = $wpdb->prepare( "INSERT INTO  ".$wpdb->prefix."term_relationships (object_id, term_taxonomy_id,term_order ) VALUES ( %d, %d, %d )", $variation_id, $term->term_id, 1 );
					$wpdb->query($sql);

  }*/
  
    return "";
}
 
function dump_wc_mg()
{
    //add_shortcode('dump_wc', 'dump_wc_func');
}
 
add_action('init', 'dump_wc_mg');
 
 
 
 
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

add_action("wp_ajax_my_user_vote", "my_user_vote");
add_action("wp_ajax_nopriv_my_user_vote", "my_user_vote");

function my_user_vote() {

global $wpdb;

   $CarMakeID = $_REQUEST['CarMakeID'];
   
     $sql = "SELECT  * FROM CarModels as cm 
     WHERE CarMakeId = $CarMakeID";
     
    $result = $wpdb->get_results ($sql);

    $final_arr = array();
    $i = 0;
    foreach ($result as $obj)
    {
        $final_arr[$i]['CarModelID'] = $obj->CarModelID;
        $final_arr[$i]['CarModel'] = $obj->CarModel;
        $i += 1;
    }
   /*Get all car models where $CarMakeID */
   
   $json = json_encode($final_arr);
   echo $json;

   die();

}


add_action("wp_ajax_car_modification", "car_modification");
add_action("wp_ajax_nopriv_car_modification", "car_modification");

function car_modification() {

global $wpdb;

   $CarModelID = $_REQUEST['CarModelID'];
   $CarYear = $_REQUEST['CarYear'];
   
     $sql = "SELECT  * FROM CarModifications as cm 
     WHERE CarModelID = $CarModelID 
     AND YearFrom <= $CarYear
     AND YearTo >= $CarYear";
     
    $result = $wpdb->get_results ($sql);

    $final_arr = array();
    $i = 0;
    foreach ($result as $obj)
    {
        $final_arr[$i]['CarModificationID'] = $obj->CarModificationID;
        $final_arr[$i]['Modification'] = $obj->CarEngine." "."( ".$obj->YearFrom." - ".$obj->YearTo." ) ".$obj->PS." hp / ".$obj->KW." kw ".$obj->CarFuel;
        $i += 1;
    }
   /*Get all car models where $CarMakeID */
   
   $json = json_encode($final_arr);
   echo $json;

   die();

}

add_action("wp_ajax_car_tyres", "car_tyres");
add_action("wp_ajax_nopriv_car_tyres", "car_tyres");

function car_tyres() {

global $wpdb;

   $CarModificationID = $_REQUEST['CarModificationID'];
   
     $sql = "SELECT  * FROM CarTyres as cm 
     WHERE CarModificationID = $CarModificationID ";
     
    $result = $wpdb->get_results ($sql);

    $final_arr = array();
    $i = 0;
    foreach ($result as $obj)
    {
        $final_arr[$i]['TyreSize'] = $obj->TyreSize;
        $i += 1;
    }
   /*Get all car models where $CarMakeID */
   
   $json = json_encode($final_arr);
   echo $json;

   die();

}



//shortcode home page
function mg_tyre_filters_function($atts = [], $content = null, $tag = '')
{
 
   $buffer = '
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
   <style>
</style>';
   $buffer.= '
   
   <div class="row">
        <div class="home-search-engine col-lg-12 col-sm-12">
          <div class="row">
            <form action="" class="carsearch-form col-lg-4 col-md-6 col-sm-6">
                <h2>Търси гуми по автомобил</h2>

                <fieldset>
                    <label for="" class="d-block">Изберете Сезон</label>

                    <div class="radio-group">
                        <input type="radio" name="tire_season" id="car-season-summer" value="Summer">
                        <label for="car-season-summer" class="season-summer">Летни</label>
                        <input type="radio" name="tire_season" id="car-season-winter" value="Winter">
                        <label for="car-season-winter" class="season-winter">Зимни</label>
                        <input type="radio" name="tire_season" id="car-season-all-season" value="All season">
                        <label for="car-season-all-season" class="season-all-season">Всесезонни</label>
                    </div>
                </fieldset>

                <div class="clearfix"></div>

                <fieldset>
                    <label for="car-make">Марка</label>
                    <select name="carMake" id="car-make">
                        <option value="" selected="selected">Изберете марка</option>
                                                <option value="1">Alfa romeo</option>
                                                <option value="2">Alpina</option>
                                                <option value="3">Aston Martin</option>
                                                <option value="4">Audi</option>
                                                <option value="5">Bentley</option>
                                                <option value="6">Bmw</option>
                                                <option value="7">Bmw M</option>
                                                <option value="8">Bugatti</option>
                                                <option value="9">Cadillac</option>
                                                <option value="10">Chevrolet</option>
                                                <option value="11">Chrysler</option>
                                                <option value="12">Citroen</option>
                                                <option value="13">Dacia</option>
                                                <option value="14">Daewoo</option>
                                                <option value="15">Daihatsu</option>
                                                <option value="16">Dodge</option>
                                                <option value="17">Ferrari</option>
                                                <option value="18">Fiat</option>
                                                <option value="19">Ford</option>
                                                <option value="20">Honda</option>
                                                <option value="21">Hyundai</option>
                                                <option value="22">Infiniti</option>
                                                <option value="23">Isuzu</option>
                                                <option value="24">Iveco</option>
                                                <option value="25">Jaguar</option>
                                                <option value="26">Jeep</option>
                                                <option value="27">Kia</option>
                                                <option value="28">Lada</option>
                                                <option value="29">Lamborghini</option>
                                                <option value="30">Lancia</option>
                                                <option value="31">Land Rover</option>
                                                <option value="32">Lexus</option>
                                                <option value="33">Man</option>
                                                <option value="34">Maserati</option>
                                                <option value="35">Maybach</option>
                                                <option value="36">Mazda</option>
                                                <option value="37">McLaren</option>
                                                <option value="38">Mercedes-Benz</option>
                                                <option value="39">Mg</option>
                                                <option value="40">Mini</option>
                                                <option value="41">Mitsubishi</option>
                                                <option value="42">Nissan</option>
                                                <option value="43">Opel</option>
                                                <option value="44">Peugeot</option>
                                                <option value="45">Pontiac</option>
                                                <option value="46">Porsche</option>
                                                <option value="47">Proton</option>
                                                <option value="48">Renault</option>
                                                <option value="49">Rolls-Royce</option>
                                                <option value="50">Rover</option>
                                                <option value="51">Saab</option>
                                                <option value="52">Seat</option>
                                                <option value="53">Skoda</option>
                                                <option value="54">Smart</option>
                                                <option value="55">Ssangyong</option>
                                                <option value="56">Subaru</option>
                                                <option value="57">Suzuki</option>
                                                <option value="58">Tesla</option>
                                                <option value="59">Toyota</option>
                                                <option value="60">Volkswagen</option>
                                                <option value="61">Volvo</option>
                                        </select><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                </fieldset>
                <br>

                <fieldset>
                    <label for="car-model">Модел</label>
                    <select name="carModel" id="car-model" >
                        <option value="" selected="selected">Изберете модел</option>
                    </select><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                </fieldset>
                <br>

                <fieldset>
                    <label for="car-year">Година</label>
                    <select name="carYear" id="car-year">
                        <option value="" selected="selected">Изберете година</option>
                    </select><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                </fieldset>
                <br>

                <fieldset>
                    <label for="car-modification">Двигател</label>
                    <select name="carModification" id="car-modification">
                        <option value="" selected="selected">Изберете двигател</option>
                    </select><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                </fieldset>
                <br>

                <fieldset>
                    <label for="tire-sizes">Размер Гуми</label>
                    <select name="tireSize" id="tire-sizes">
                        <option value="" selected="selected">Изберете размер</option>
                    </select><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                </fieldset>
                <br>

                <fieldset>
                    <label for="" class="d-block">Допълнителни (незадължителни)</label>

                    <div class="options-group">
                        <input type="checkbox" name="tire_options[]" id="car-option-reinforced" value="reinforced">
                        <label for="car-option-reinforced"><span class="icon">XL</span><!-- Подсилена стена --></label>
                        <input type="checkbox" name="tire_options[]" id="car-option-protected" value="rim_protected">
                        <label for="car-option-protected"><span class="icon">FR</span><!-- Защита на джантата --></label>
                        <input type="checkbox" name="tire_options[]" id="car-option-runflat" value="runflat">
                        <label for="car-option-runflat"><span class="icon">RF</span><!-- Runflat --></label>
                    </div>
                </fieldset>
                <br>

                <button class="btn" disabled="disabled">Търсене</button>
            </form>

            <form action="" class="tiresearch-form col-lg-8 col-md-6 col-sm-6">
                <h2>Търси гуми по размер</h2>

                <fieldset>
                    <label for="" class="d-block">Изберете Сезон</label>

                    <div class="row">
                      <div class="col-lg-6 col-sm-12 no-padding-left">
                          <div class="radio-group">
                              <!-- <input type="radio" name="seasonType" id="season-all" value="" />
                              <label for="season-all">Всички</label> -->
                              <input type="radio" name="tire_season" id="tire-season-summer" value="Summer">
                              <label for="tire-season-summer" class="season-summer">Летни</label>
                              <input type="radio" name="tire_season" id="tire-season-winter" value="Winter">
                              <label for="tire-season-winter" class="season-winter">Зимни</label>
                              <input type="radio" name="tire_season" id="tire-season-all-season" value="All season">
                              <label for="tire-season-all-season" class="season-all-season">Всесезонни</label>
                          </div>
                      </div>
                      <div class="d-inline-block col-lg-6 no-padding-left">
                          <ul class="list">
                              <li><strong>Летни</strong> - за температури над 7℃.</li>
                              <li><strong>Зимни</strong> - за дълбок сняг.</li>
                              <li><strong>Всесезонни</strong> - за мека градска зима.</li>
                          </ul>
                      </div>
                    </div>
                </fieldset>

                <div class="clearfix"></div>

                <fieldset>
                    <label for="" class="d-block">Изберете тип</label>

                    <div class="row">
                      <div class="col-lg-6 col-sm-12 no-padding-left">
                        <div class="radio-group">
                            <!-- <input type="radio" name="vehicleType" id="vehicle-all" value="" />
                            <label for="vehicle-all" class="vehicle-all">Всички</label> -->
                            <input type="radio" name="vehicle_type" id="vehicle-passenger" value="Passenger">
                            <label for="vehicle-passenger" class="vehicle-passenger">Леки коли</label>
                            <input type="radio" name="vehicle_type" id="vehicle-4x4" value="4x4">
                            <label for="vehicle-4x4" class="vehicle-4x4">4x4</label>
                            <input type="radio" name="vehicle_type" id="vehicle-van" value="Van">
                            <label for="vehicle-van" class="vehicle-van">Лекотоварни</label>
                        </div>
                      </div>

                      <div class="d-inline-block col-lg-6 no-padding-left">
                          <ul class="list">
                              <li><strong>Леки коли</strong></li>
                              <li><strong>4x4</strong> - SUV и 4x4</li>
                              <li><strong>Лекотоварни</strong> - Бус и Ван.</li>
                          </ul>
                      </div>
                    </div>
                </fieldset>


                <div class="clearfix"></div>

                <div class="row tire-icon-holder">
                  <fieldset class="d-inline-block col-lg-2 no-padding-left">
                    <label for="tire-width">Широчина</label>
                    <select name="tire_width" id="tire-width" >
                      <option value="" selected="selected">205</option>
                      <option value="0">Всички</option>
                                              <option value="7">7</option>
                                              <option value="10">10</option>
                                              <option value="28">28</option>
                                              <option value="30">30</option>
                                              <option value="31">31</option>
                                              <option value="32">32</option>
                                              <option value="33">33</option>
                                              <option value="35">35</option>
                                              <option value="37">37</option>
                                              <option value="39">39</option>
                                              <option value="115">115</option>
                                              <option value="125">125</option>
                                              <option value="135">135</option>
                                              <option value="145">145</option>
                                              <option value="155">155</option>
                                              <option value="165">165</option>
                                              <option value="175">175</option>
                                              <option value="185">185</option>
                                              <option value="195">195</option>
                                              <option value="205">205</option>
                                              <option value="215">215</option>
                                              <option value="225">225</option>
                                              <option value="235">235</option>
                                              <option value="245">245</option>
                                              <option value="255">255</option>
                                              <option value="265">265</option>
                                              <option value="275">275</option>
                                              <option value="285">285</option>
                                              <option value="295">295</option>
                                              <option value="305">305</option>
                                              <option value="315">315</option>
                                              <option value="325">325</option>
                                              <option value="335">335</option>
                                              <option value="345">345</option>
                                              <option value="355">355</option>
                                              <option value="5.00">5.00</option>
                                              <option value="6.00">6.00</option>
                                              <option value="6.50">6.50</option>
                                              <option value="7.50">7.50</option>
                                              <option value="8.50">8.50</option>
                                              <option value="9.50">9.50</option>
                                              <option value="10.50">10.50</option>
                                              <option value="11.50">11.50</option>
                                              <option value="12.50">12.50</option>
                                              <option value="13.50">13.50</option>
                                      </select><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                  </fieldset>

                  <fieldset class="d-inline-block col-lg-2 no-padding-left">
                      <label for="tire-profile">Отношение</label>
                      <select name="tire_profile" id="tire-profile" >
                          <option value="" selected="selected">55</option>
                          <option value="0">Всички</option>
                                                  <option value="9.50">9.50</option>
                                                  <option value="10.50">10.50</option>
                                                  <option value="11.50">11.50</option>
                                                  <option value="12.50">12.50</option>
                                                  <option value="13.50">13.50</option>
                                                  <option value="25">25</option>
                                                  <option value="30">30</option>
                                                  <option value="35">35</option>
                                                  <option value="40">40</option>
                                                  <option value="45">45</option>
                                                  <option value="50">50</option>
                                                  <option value="55">55</option>
                                                  <option value="60">60</option>
                                                  <option value="65">65</option>
                                                  <option value="70">70</option>
                                                  <option value="75">75</option>
                                                  <option value="80">80</option>
                                                  <option value="82">82</option>
                                                  <option value="85">85</option>
                                                  <option value="90">90</option>
                                                  <option value="95">95</option>
                                                  <option value="10.00">10.00</option>
                                          </select><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                  </fieldset>

                  <fieldset class="d-inline-block col-lg-2 no-padding-left">
                      <label for="rim-size">Диаметър</label>
                      <select name="rim_size" id="rim-size" >
                          <option value="" selected="selected">16</option>
                          <option value="0">Всички</option>
                                                  <option value="10">10</option>
                                                  <option value="12">12</option>
                                                  <option value="13">13</option>
                                                  <option value="14">14</option>
                                                  <option value="15">15</option>
                                                  <option value="16">16</option>
                                                  <option value="17">17</option>
                                                  <option value="18">18</option>
                                                  <option value="19">19</option>
                                                  <option value="20">20</option>
                                                  <option value="21">21</option>
                                                  <option value="22">22</option>
                                                  <option value="23">23</option>
                                                  <option value="24">24</option>
                                                  <option value="28">28</option>
                                          </select><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                  </fieldset>

                  <div class="d-inline-block col-lg-6 no-padding-left">
                      <label for="">&nbsp;</label>
                      <ul class="list">
                          <li><strong>Широчина</strong>- широчина на гумата.</li>
                          <li><strong>Отношение</strong>- височина на борда на гумата в проценти от широчината.</li>
                          <li><strong>Диаметър</strong>- диаметър на джантата.</li>
                      </ul>
                  </div>

                  <div class="tire-icon margin-bottom"></div>
                </div>


                <div class="clearfix"></div>

                <div class="row">
                  <fieldset class="d-inline-block col-lg-3 no-padding-left">
                      <label for="speed-index">Скоростен индекс</label>
                      <select name="tire_speed_index" id="speed-index" >
                          <option value="" selected="selected">Всички</option>
                                                  <option value="H">H (до 210км/ч)</option>
                                                  <option value="M">M (до 130км/ч)</option>
                                                  <option value="N">N (до 140км/ч)</option>
                                                  <option value="P">P (до 150км/ч)</option>
                                                  <option value="Q">Q (до 160км/ч)</option>
                                                  <option value="R">R (до 170км/ч)</option>
                                                  <option value="S">S (до 180км/ч)</option>
                                                  <option value="T">T (до 190км/ч)</option>
                                                  <option value="V">V (до 240км/ч)</option>
                                                  <option value="W">W (до 270км/ч)</option>
                                                  <option value="Y">Y (до 300км/ч)</option>
                                                  <option value="Z">Z ()</option>
                                          </select><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                  </fieldset>

                  <fieldset class="d-inline-block col-lg-3 no-padding-left">
                      <label for="load-index">Тегловен индекс</label>
                      <select name="tire_load_index" id="load-index" >
                          <option value="" selected="selected">Всички</option>
                                                  <option value="65">65</option>
                                                  <option value="68">68</option>
                                                  <option value="69">69</option>
                                                  <option value="70">70</option>
                                                  <option value="71">71</option>
                                                  <option value="72">72</option>
                                                  <option value="73">73</option>
                                                  <option value="74">74</option>
                                                  <option value="75">75</option>
                                                  <option value="76">76</option>
                                                  <option value="77">77</option>
                                                  <option value="78">78</option>
                                                  <option value="79">79</option>
                                                  <option value="80">80</option>
                                                  <option value="81">81</option>
                                                  <option value="82">82</option>
                                                  <option value="83">83</option>
                                                  <option value="84">84</option>
                                                  <option value="85">85</option>
                                                  <option value="86">86</option>
                                                  <option value="87">87</option>
                                                  <option value="88">88</option>
                                                  <option value="89">89</option>
                                                  <option value="90">90</option>
                                                  <option value="91">91</option>
                                                  <option value="92">92</option>
                                                  <option value="93">93</option>
                                                  <option value="94">94</option>
                                                  <option value="95">95</option>
                                                  <option value="96">96</option>
                                                  <option value="97">97</option>
                                                  <option value="98">98</option>
                                                  <option value="99">99</option>
                                                  <option value="100">100</option>
                                                  <option value="101">101</option>
                                                  <option value="102">102</option>
                                                  <option value="103">103</option>
                                                  <option value="104">104</option>
                                                  <option value="105">105</option>
                                                  <option value="106">106</option>
                                                  <option value="107">107</option>
                                                  <option value="108">108</option>
                                                  <option value="109">109</option>
                                                  <option value="110">110</option>
                                                  <option value="111">111</option>
                                                  <option value="112">112</option>
                                                  <option value="113">113</option>
                                                  <option value="114">114</option>
                                                  <option value="115">115</option>
                                                  <option value="116">116</option>
                                                  <option value="117">117</option>
                                                  <option value="118">118</option>
                                                  <option value="119">119</option>
                                                  <option value="120">120</option>
                                                  <option value="121">121</option>
                                                  <option value="122">122</option>
                                                  <option value="123">123</option>
                                                  <option value="124">124</option>
                                                  <option value="125">125</option>
                                                  <option value="126">126</option>
                                                  <option value="127">127</option>
                                                  <option value="128">128</option>
                                                  <option value="129">129</option>
                                          </select><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                  </fieldset>

                  <div class="d-inline-block col-lg-6 no-padding-left">
                      <label for="">&nbsp;</label>
                      <ul class="list">
                          <li><strong>Скоростен Индекс</strong> - максимална скорост, с която може да се движи безопасно гумата.</li>
                          <li><strong>Тегловен Индекс</strong> - максимално тегло, което може да носи безопасно гумата.</li>
                      </ul>
                  </div>
                </div>
                
                <div class="clearfix"></div>

                <fieldset>
                    <label for="" class="d-block">Допълнителни (незадължителни)</label>
                    <div class="row">
                      <div class="col-lg-6 col-sm-12 no-padding-left">
                          <div class="options-group">
                              <input type="checkbox" name="tire_options[]" id="tire-option-reinforced" value="reinforced">
                              <label for="tire-option-reinforced"><span class="icon">XL</span><!-- Подсилена стена --></label>
                              <input type="checkbox" name="tire_options[]" id="tire-option-protected" value="rim_protected">
                              <label for="tire-option-protected"><span class="icon">FR</span><!-- Защита на джантата --></label>
                              <input type="checkbox" name="tire_options[]" id="tire-option-runflat" value="runflat">
                              <label for="tire-option-runflat"><span class="icon">RF</span><!-- Runflat --></label>
                          </div>
                      </div>

                      <div class="d-inline-block col-lg-6 no-padding-left">
                          <ul class="list">
                              <li><strong>XL</strong> - стената на гумата е подсилна, за да понася по-големи тежести.</li>
                              <li><strong>FR</strong> - специално създадено парче от гумата предпазва джантата от одраскване.</li>
                              <li><strong>RF</strong> - Runflat. Гуми, позволяващи движение след спукване.</li>
                          </ul>
                      </div>
                    </div>
                </fieldset>


                <button class="btn primary">Търсене</button>
            </form>
          </fdiv>
        </div>
  </div>
';
 
 
 
    // return output
    return $buffer;
}
 
function wporg_shortcodes_init()
{
    add_shortcode('mg_tyre_filters', 'mg_tyre_filters_function');
}
 
add_action('init', 'wporg_shortcodes_init');



// Add Variation Settings
add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );

// Save Variation Settings
add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );

// Add New Variation Settings
add_filter( 'woocommerce_available_variation', 'load_variation_settings_fields' );

/**
 * Add custom fields for variations
 *
*/
function load_variation_settings_fields( $variations ) {
	
	// duplicate the line for each field
    $variations['distributor'] = get_post_meta( $variations[ 'variation_id' ], 'distributor', true );

    $variations['distributor_tire_id'] = get_post_meta( $variations[ 'variation_id' ], 'distributor_tire_id', true );

    $variations['rim_size'] = get_post_meta( $variations[ 'variation_id' ], 'rim_size', true );
    
    $variations['tire_loading'] = get_post_meta( $variations[ 'variation_id' ], 'tire_loading', true );
    
    $variations['tire_load_index'] = get_post_meta( $variations[ 'variation_id' ], 'tire_load_index', true );
    
    $variations['tire_manufacturer'] = get_post_meta( $variations[ 'variation_id' ], 'tire_manufacturer', true );
    
    $variations['tire_options'] = get_post_meta( $variations[ 'variation_id' ], 'tire_options', true );
    
    $variations['tire_profile'] = get_post_meta( $variations[ 'variation_id' ], 'tire_profile', true );
    
     $variations['tire_season'] = get_post_meta( $variations[ 'variation_id' ], 'tire_season', true );
     
         $variations['tire_speed_index'] = get_post_meta( $variations[ 'variation_id' ], 'tire_speed_index', true );
    
    $variations['tire_variant'] = get_post_meta( $variations[ 'variation_id' ], 'tire_variant', true );
    
     $variations['tire_width'] = get_post_meta( $variations[ 'variation_id' ], 'tire_width', true );
     
     $variations['vehicle_type'] = get_post_meta( $variations[ 'variation_id' ], 'vehicle_type', true );
     
    $variations['tire_fuel_rating'] = get_post_meta( $variations[ 'variation_id' ], 'tire_fuel_rating', true );
    
     $variations['tire_wet_grip_rating'] = get_post_meta( $variations[ 'variation_id' ], 'tire_wet_grip_rating', true );
     
     $variations['tire_noise_emissions_value'] = get_post_meta( $variations[ 'variation_id' ], 'tire_noise_emissions_value', true );
	
	
	return $variations;

}
/**
 * Create new fields for variations
 *
*/
function variation_settings_fields( $loop, $variation_data, $variation ) {
    
    
    
    // Text Field
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'distributor[' . $variation->ID . ']', 
			'label'       => __( 'distributor', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'distributor', true )
		)
	);
	
	    // Text Field
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'distributor_tire_id[' . $variation->ID . ']', 
			'label'       => __( 'distributor_tire_id', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'distributor_tire_id', true )
		)
	);
	
		    // Text Field
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'rim_size[' . $variation->ID . ']', 
			'label'       => __( 'rim_size', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'rim_size', true )
		)
	);
	
		    // Text Field
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_loading[' . $variation->ID . ']', 
			'label'       => __( 'tire_loading', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_loading', true )
		)
	);
	
			    // Text Field
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_load_index[' . $variation->ID . ']', 
			'label'       => __( 'tire_load_index', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_load_index', true )
		)
	);
	
				    // Text Field
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_manufacturer[' . $variation->ID . ']', 
			'label'       => __( 'tire_manufacturer', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_manufacturer', true )
		)
	);
	
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_options[' . $variation->ID . ']', 
			'label'       => __( 'tire_options', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_options', true )
		)
	);
	
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_profile[' . $variation->ID . ']', 
			'label'       => __( 'tire_profile', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_profile', true )
		)
	);
	
	
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_season[' . $variation->ID . ']', 
			'label'       => __( 'tire_season', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_season', true )
		)
	);
	
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_speed_index[' . $variation->ID . ']', 
			'label'       => __( 'tire_speed_index', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_speed_index', true )
		)
	);
	
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_variant[' . $variation->ID . ']', 
			'label'       => __( 'tire_variant', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_variant', true )
		)
	);
	
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_width[' . $variation->ID . ']', 
			'label'       => __( 'tire_width', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_width', true )
		)
	);
	
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_options[' . $variation->ID . ']', 
			'label'       => __( 'tire_options', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_options', true )
		)
	);
	
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'vehicle_type[' . $variation->ID . ']', 
			'label'       => __( 'vehicle_type', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'vehicle_type', true )
		)
	);
	
		woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_fuel_rating[' . $variation->ID . ']', 
			'label'       => __( 'tire_fuel_rating', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_fuel_rating', true )
		)
	);
	
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_wet_grip_rating[' . $variation->ID . ']', 
			'label'       => __( 'tire_wet_grip_rating', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_wet_grip_rating', true )
		)
	);
	
	woocommerce_wp_text_input( 
		array( 
			'id'          => 'tire_noise_emissions_value[' . $variation->ID . ']', 
			'label'       => __( 'tire_noise_emissions_value', 'woocommerce' ), 
			'desc_tip'    => 'true',
			'value'       => get_post_meta( $variation->ID, 'tire_noise_emissions_value', true )
		)
	);
	
	
}

/**
 * Save new fields for variations
 *
*/
function save_variation_settings_fields( $post_id ) {
	
	// Number Field
	$text_field = $_POST['distributor'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'distributor', esc_attr( $text_field ) );
	}

	$text_field = $_POST['distributor_tire_id'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'distributor_tire_id', esc_attr( $text_field ) );
	}
	
		$text_field = $_POST['rim_size'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'rim_size', esc_attr( $text_field ) );
	}
	
		$text_field = $_POST['tire_loading'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_loading', esc_attr( $text_field ) );
	}
	
		$text_field = $_POST['tire_load_index'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_load_index', esc_attr( $text_field ) );
	}
	
		$text_field = $_POST['tire_manufacturer'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_manufacturer', esc_attr( $text_field ) );
	}
	
		$text_field = $_POST['tire_options'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_options', esc_attr( $text_field ) );
	}
	
		$text_field = $_POST['tire_profile'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_profile', esc_attr( $text_field ) );
	}

    $text_field = $_POST['tire_season'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_season', esc_attr( $text_field ) );
	}
	
	
			$text_field = $_POST['tire_speed_index'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_speed_index', esc_attr( $text_field ) );
	}
	
		$text_field = $_POST['tire_variant'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_variant', esc_attr( $text_field ) );
	}

    $text_field = $_POST['tire_width'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_width', esc_attr( $text_field ) );
	}
	
	  $text_field = $_POST['vehicle_type'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'vehicle_type', esc_attr( $text_field ) );
	}
	
	
	
	
			$text_field = $_POST['tire_fuel_rating'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_fuel_rating', esc_attr( $text_field ) );
	}

    $text_field = $_POST['tire_wet_grip_rating'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_wet_grip_rating', esc_attr( $text_field ) );
	}
	
	  $text_field = $_POST['tire_noise_emissions_value'][ $post_id ];
	if( ! empty( $text_field ) ) {
		update_post_meta( $post_id, 'tire_noise_emissions_value', esc_attr( $text_field ) );
	}
	
}

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';

	if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
		require 'inc/nux/class-storefront-nux-starter-content.php';
	}
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
