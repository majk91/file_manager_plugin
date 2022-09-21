<?php
/*
 * Plugin Name: Створення структури файлів 
 * Plugin URI:  Empty
 * Description: Плагін для автоматизації створення структури файлові системи
 * Version: 1.0.0
 * Author: Mike Yuryshynets
 * Author URI: Empty
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Text Domain: Empty
 * Domain Path: /Empty
 *
 * Network: true
 */

add_action('admin_menu', 'file_manager_plugin_setup_menu');
add_shortcode('file_manager', 'file_manager_shrt');
 
function file_manager_plugin_setup_menu(){
    add_menu_page( 'Менеджер файлів', 'Менеджер файлів', 'manage_options', 'file_manager_plugin', 'file_manager_init' );
    wp_enqueue_script('file-manager', 'https://code.jquery.com/jquery-3.6.0.min.js');
    wp_localize_script('file-manager', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  // remove admin_script and add unique javascript file.
}
 
function file_manager_shrt($atts) {
    global $wpdb;
    // пример: add_action ('init', 'my_init_function');
    $table_name = $wpdb->prefix . "diploms_archive";
    $default = array(
        'id' => 0,
        'file' => "''",
        'year' => "''",
        'autor' => "''",
        'category' => "''"
    );
    $a = shortcode_atts($default, $atts);
    $filters = '';
    if ($atts) {
        $filters = " WHERE id in (".$a['id'].") OR file in (".$a['file'].") OR year in (".$a['year'].") OR autor=".$a['autor']." OR category=".$a['category'];
    }
    $myrows = $wpdb->get_results( "SELECT `id`,`year`,`category`,`autor`,`file` FROM 
    ".$table_name.$filters.";");
    $res = '';
    foreach ($myrows as $details){
        $res .= "<a href='".wp_get_upload_dir()['baseurl']."/diploms/".$details->file."' target='_blank'><img src='".plugin_dir_url( __FILE__ )."icon.svg' alt='icon' width='20px' />&nbsp;&nbsp;".$details->file."</a></br>";
    }
    return $res;
}

function file_manager_init(){
    global $wpdb;
    // пример: add_action ('init', 'my_init_function');
    $table_name = $wpdb->prefix . "diploms_archive";
    
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
       
       $sql = "CREATE TABLE " . $table_name . " (
       id mediumint(9) NOT NULL AUTO_INCREMENT,
       year year(4) NOT NULL,
       category varchar(255) NOT NULL,
       autor varchar(255) NOT NULL,
       file varchar(255) NOT NULL,
       UNIQUE KEY id (id)
     );";
 
     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
       dbDelta($sql);
    }
    test_handle_post();
?>


 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
 <link rel="stylesheet" href="https://bulma.io/vendor/fontawesome-free-5.15.2-web/css/all.min.css">
 <style>
     select{
        background: #fff!important;
     }

    #filter-btn {
        background-color: #00d1b2;
        border-color: transparent;
        color: #fff;
        margin-left: 50px;
        height: 40px;
        width: 150px;
        cursor: pointer;
        border-radius: 4px;
     }
     #filter-btn:hover {
        background-color: #00c4a7;
     }
    #reset-btn {
        background-color: #14322d;
        border-color: transparent;
        color: #fff;
        margin-left: 30px;
        height: 40px;
        width: 150px;
        cursor: pointer;
        border-radius: 4px;
    }
     .fa-upload:before {
        content: "\f093";
    }
    .row {
        display: flex;
        align-items: flex-end;
        justify-content: flex-start;
    }
    .col {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
    }
    .row .control:first-child {
        margin-right: 20px;
    }
    .form-filter {
        margin-bottom: 30px;
        display: flex;
        align-content: flex-start;
        justify-content: flex-start;
    }
    .margin-bottom {
        margin-bottom: 1.5rem;
    }
    .autor-field {
        width: 400px;
    }
    .file.is-primary {
        margin-left: 50px;
    }
 </style>    
<h1 class="title">Менеджер файлів</h1>
<h2 class="title">Filter records</h2>

<form method="post" id="filter-wrapper" class="form-filter">
    <div class="col">    
        <div class="control">
            <div class="select">
                <select id="filterByYear" name="filterByYear">
                    <option value="2005">2005</option>
                    <option value="2006">2006</option>
                    <option value="2007">2007</option>
                    <option value="2008">2008</option>
                    <option value="2009">2009</option>
                    <option value="2010">2010</option>
                    <option value="2011">2011</option>
                    <option value="2012">2012</option>
                    <option value="2013">2013</option>
                    <option value="2014">2014</option>
                    <option value="2015">2015</option>
                    <option value="2016">2016</option>
                    <option value="2017">2017</option>
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                    <option value="2022" selected>2022</option>
                </select>
            </div>
            <div class="select">
                <select name="category" id="filterByCategory">
                    <option value="bachelor" selected>Бакалавр</option>
                    <option value="master">Магістр</option>
                    <option value="postgraduate">Аспірант</option>
                </select>
            </div>
            <div class="select">
                <select id="filterByAutor" name="filterByAutor">
                    <option selected value="">Chouse Autor</option>
                    <?global $wpdb;
                    $table_name = "wp_diploms_archive";
                    $myrows = $wpdb->get_results( "SELECT DISTINCT `autor` FROM ".$table_name."");
                    foreach ($myrows as $autor){
                       if(!empty($autor->autor)) echo '<option value="'. $autor->autor.'">'.$autor->autor.'</option>';
                    }?>
                </select>
            </div>
            <div class="col">
                <label class="label">File name search</label> 
                <div class="control autor-field">
                    <input type="text" id="filterByName" name="filterByName">
                </div>
            </div>
        </div>
    </div>
    <button id="filter-btn" onclick="btnClick(event)">Do Filter</button>
    <button id="reset-btn" onclick="btnReset(event)">Reset</button>
</form>
<script>
    function filterById(id) {
        var e = document.getElementById(id);
        return e.value;   
    };


    function btnClick(e) {
        e.preventDefault();
        var frm = jQuery('#filter-wrapper');
        var year = filterById("filterByYear");
        var autor = filterById("filterByAutor");
        var fileName = filterById("filterByName");
        var category = filterById("filterByCategory");
        jQuery.ajax({
            method: 'POST',
            type: 'POST',
            data: { 
                action: 'filter',
                data: {year:year, autor:autor, fileName:fileName, category:category},  
            },
            url: myAjax.ajaxurl,
            //dataType: "html",
            success: function (data) {
                console.log(data);
                let elem = document.querySelector('.table');
                elem.innerHTML = data;
                // console.log(data);
                }
        });
    }
    function btnReset(e) {
        e.preventDefault();
        jQuery.ajax({
            method: 'POST',
            type: 'POST',
            data: { 
                action: 'resetFilter',
            },
            url: myAjax.ajaxurl,
            dataType: "html",
            success: function (data) {
                // var d = JSON.parse(data);
                let elem = document.querySelector('.table');
                elem.innerHTML = data;
                // console.log(data);
                }
        });
    }
    jQuery(document).ready(function(){
        jQuery.ajax({
            method: 'POST',
            type: 'POST',
            data: { 
                action: 'resetFilter',
            },
            url: myAjax.ajaxurl,
            dataType: "html",
            success: function (data) {
                // var d = JSON.parse(data);
                let elem = document.querySelector('.table');
                elem.innerHTML = data;
                // console.log(data);
                }
        });
    });
</script>

<table class="table">
  <thead>
  <tr>
     <th>ID </th>
     <th>Year </th>
     <th>Category </th>
     <th>Autor </th>
     <th>File </th>
  </tr>
  </thead>
<tbody>

</tbody>
</table>
    <form  method="post" enctype="multipart/form-data">
        <h2 class="title">Add new records</h2>
        <div class="row margin-bottom">
            <div class="control">
                <div class="select">
                    <select name="year"> 
                        <option value="2005">2005</option>
                        <option value="2006">2006</option>
                        <option value="2007">2007</option>
                        <option value="2008">2008</option>
                        <option value="2009">2009</option>
                        <option value="2010">2010</option>
                        <option value="2011">2011</option>
                        <option value="2012">2012</option>
                        <option value="2013">2013</option>
                        <option value="2014">2014</option>
                        <option value="2015">2015</option>
                        <option value="2016">2016</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022" selected>2022</option>
                    </select>
                </div>
            </div>
            <div class="control">
                <div class="select">
                    <select name="category">
                        <option value="bachelor" selected>Бакалавр</option>
                        <option value="master">Магістр</option>
                        <option value="postgraduate">Аспірант</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="margin-bottom row">
            <div class="col">
                <label class="label">Autor</label>
                <div class="control autor-field">
                    <input class="input" type="text" placeholder="Autor name" name="autor">
                </div> 
            </div>
            <div class="file is-primary">
                <label class="file-label">
                    <input class="file-input" type="file" id='test_upload_pdf' name='test_upload_pdf'>
                    <span class="file-cta">
                    <span class="file-icon">
                        <i class="fas fa-upload"></i>
                    </span>
                    <span class="file-label">
                        Primary file…
                    </span>
                    </span>
                </label>
            </div>
        </div>
        <?php submit_button('Upload') ?>
    </form>
<?php
}
 
function test_handle_post(){
    // First check if the file appears on the _FILES array
    if(isset($_FILES['test_upload_pdf'])){
        $pdf = $_FILES['test_upload_pdf'];
 
        // Use the wordpress function to upload
        // test_upload_pdf corresponds to the position in the $_FILES array
        // 0 means the content is not associated with any other posts
        function wpse_custom_upload_dir( $dir_data ) {
            // $dir_data already you might want to use
            $custom_dir = 'diploms';
            return [
                'path' => $dir_data[ 'basedir' ] . '/' . $custom_dir,
                'url' => $dir_data[ 'url' ] . '/' . $custom_dir,
                'subdir' => '/' . $custom_dir,
                'basedir' => $dir_data[ 'error' ],
                'error' => $dir_data[ 'error' ],
            ];
        }

        add_filter( 'upload_dir', 'wpse_custom_upload_dir' );

        $uploaded=media_handle_upload('test_upload_pdf', 0);
        // Error checking using WP functions
        if(is_wp_error($uploaded)){
            echo "Error uploading file: " . $uploaded->get_error_message();
        }else{
            global $wpdb;
            $lastrowId=$wpdb->get_col( "SELECT ID FROM wp_posts where post_type='attachment' ORDER BY post_date DESC LIMIT 1" );

            $year = $_POST['year'];
            $category = $_POST['category']; 
            $autor = $_POST['autor'];
            $file = $_FILES['test_upload_pdf']['name']; 

            $sql = "INSERT INTO `wp_diploms_archive` (`year`,`category`,`autor`,`file`,`posts_id`) 
            values ('$year', '$category', '$autor', '$file', '$lastrowId[0]')";
            $wpdb->query($sql);
        }
    }
}

function filterDiploms(){
    global $wpdb;
    $data = $_POST['data'];
    $year = $data['year'];
    $autor = $data['autor'];
    $name = $data['fileName'];
    $category = $data['category'];
    $filter = json_decode( $data );

    $table_name = "wp_diploms_archive";
    $query = "SELECT `id`,`year`,`category`,`autor`,`file` FROM ".$table_name." WHERE `year`='$year'";
    if(!empty($autor)) $query = $query . " AND `autor`='$autor'";
    if(!empty($name)) $query = $query . " AND `file` LIKE '%$name%'";
    if(!empty($category)) $query = $query . " AND `category`='$category'";
    $myrows = $wpdb->get_results($query);

    echo '<table class="table">
    <thead>
        <tr>
            <th>ID </th>
            <th>Year </th>
            <th>Category </th>
            <th>Autor </th>
            <th>File </th>
            <th>Shortcode </th>
        </tr>
    </thead>
        <tbody>';
             
            foreach ($myrows as $details){
                echo'<tr class="item_row">
                        <td>'.$details->id.'</td>
                        <td>'.$details->year.'</td>
                        <td>'.$details->category.'</td>
                        <td>'.$details->autor.'</td>
                        <td>'.$details->file.'</td>
                        <td>[file_manager id='.$details->id.']</td>
                </tr>';
                }
       echo '</tbody>
    </table>';        
    die();
}

function getDiploms(){
    global $wpdb;
        $table_name = "wp_diploms_archive";
        $myrows = $wpdb->get_results( "SELECT `id`,`year`,`category`,`autor`,`file` FROM ".$table_name);
return $myrows;   
}

function paintTable(){
    $myrows = getDiploms();
    echo '<table class="table">
    <thead>
        <tr>
            <th>ID </th>
            <th>Year </th>
            <th>Category </th>
            <th>Autor </th>
            <th>File </th>
            <th>Shortcode </th>
        </tr>
    </thead>
        <tbody>';
             
            foreach ($myrows as $details){
                echo'<tr class="item_row">
                        <td>'.$details->id.'</td>
                        <td>'.$details->year.'</td>
                        <td>'.$details->category.'</td>
                        <td>'.$details->autor.'</td>
                        <td>'.$details->file.'</td>
                        <td><input readonly value="[file_manager id='.$details->id.']" /></td>
                </tr>';
                }
       echo '</tbody>
    </table>';        
    die();
}

add_action('wp_ajax_nopriv_filter','filterDiploms');
add_action('wp_ajax_filter','filterDiploms');

add_action('wp_ajax_nopriv_resetFilter','paintTable');
add_action('wp_ajax_resetFilter','paintTable');

/* For adding custom field to gallery popup */
function rt_image_attachment_fields_to_edit($form_fields, $post) {
    global $wpdb;
    $post_id = $post->ID;
    $archiv_ids=$wpdb->get_col( "SELECT id FROM wp_diploms_archive where posts_id=".$post_id);


    $form_fields["rt-short-code"] = array(
        "label" => __("Shortcode"),
        "input" => "html", // this is default if "input" is omitted
        "value" => "[file_manager id=".$archiv_ids[0]."]",//get_post_meta($post->ID, "_rt-image-link", true),
        //"helps" => __("To be used with special slider added via [rt_carousel] shortcode."),
    );
   return $form_fields;
}


// now attach our function to the hook
add_filter("attachment_fields_to_edit", "rt_image_attachment_fields_to_edit", null, 2);

?>