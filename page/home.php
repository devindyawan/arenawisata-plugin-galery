<?php

global $wpdb;
$result = $wpdb->get_results("SELECT * FROM aw_galery");

global $current_user;

// $new = "D:/Dimas/web/arenawisata/wp-content/plugins/arenawisata-galery/page/images/silas-baisch-Wn4ulyzVoD4-unsplash.jpg";
// echo preg_replace("~.*?(?=/wp-content)~", "", $new)
// $new = __DIR__ . "/images";
// echo $new;
// echo preg_replace("~.*?(?=wp-content)~", "", __DIR__);



?>
<section class="container">
    <?php
    print_r(json_encode(get_userdata($current_user->ID)->display_name))
    ?>
</section>