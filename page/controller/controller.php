<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/arenawisata/wp-config.php');

global $wpdb;
$prefix = $wpdb->prefix;

function getCategory(): array
{
    global $wpdb;
    $cat_table = $wpdb->prefix . 'galery_category';

    return $wpdb->get_results("SELECT * FROM $cat_table");
}

function get($queries): array
{
    global $wpdb;
    $result = $wpdb->get_results($queries);

    return $result;
}

if (isset($_GET) && $_GET['fun'] === 'category') {
    $catid = $_GET['cat'];

    $query = "SELECT `aw_galery`.`id`, `aw_galery`.`title`, `aw_galery`.`filelocation` FROM `aw_galery` INNER JOIN `aw_galery_category` WHERE `aw_galery`.`id_category` = `aw_galery_category`.`id` AND `aw_galery_category`.`id` = $catid ORDER BY `aw_galery`.`id` DESC";
    $result = get($query);

    echo json_encode($result);

    // SELECT * FROM aw_galery INNER JOIN aw_galery_category WHERE aw_galery.id_category = aw_galery_category.id AND aw_galery.id = 2;
}

if (isset($_GET) && $_GET['fun'] === 'date') {
    $date = $_GET['date'];

    $query = "SELECT * FROM `aw_galery` WHERE DATE_FORMAT(date, '%Y%m') = $date ORDER BY id DESC";
    $result = get($query);

    echo json_encode($result);

    // SELECT * FROM aw_galery INNER JOIN aw_galery_category WHERE aw_galery.id_category = aw_galery_category.id AND aw_galery.id = 2;
}

if (isset($_GET) && $_GET['fun'] === 'galery') {
    $id = (int)$_GET['id'];

    // $query = "SELECT * FROM " . $prefix . "galery WHERE `id`= " . $id;
    $query = "SELECT `aw_galery`.*, `aw_galery_category`.`category`  FROM `aw_galery` INNER JOIN `aw_galery_category` WHERE `aw_galery`.`id_category` = `aw_galery_category`.`id` AND `aw_galery`.`id` = $id ORDER BY `aw_galery`.`id` DESC";
    $result = get($query);

    echo json_encode($result);

    // SELECT * FROM aw_galery INNER JOIN aw_galery_category WHERE aw_galery.id_category = aw_galery_category.id AND aw_galery.id = 2;
}

if (isset($_GET) && $_GET['fun'] === 'updategalery') {
    $post = json_decode(file_get_contents('php://input'), true);

    $thisid = (int)$_GET['id'];
    $title = $post['title'];
    $desc = $post['description'];
    $category = $post['id_category'];

    // $query = "SELECT * FROM " . $prefix . "galery WHERE `id`= " . $id;
    // $query = "UPDATE `aw_galery` SET `title`='$title', `description`='$desc', `id_category`=$category WHERE `aw_galery`.`id`=$thisid";
    // $result = get($query);
    $result = $wpdb->update($prefix . "galery", $post, array('id' => $thisid));;

    echo json_encode($result);

    // SELECT * FROM aw_galery INNER JOIN aw_galery_category WHERE aw_galery.id_category = aw_galery_category.id AND aw_galery.id = 2;
}

if (isset($_GET) && $_GET['fun'] === 'form') {

    $query = '';
    global $current_user;

    function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    if ($_POST['category'] === "default" || $_FILES['imagegalery']['name'][0] === '') {
        echo json_encode(
            [
                'message' => 'Image or category not found',
                'Inserted Data' => 0,
                "status" => 400,
            ]
        );
        die;
    }

    if (isset($_FILES['imagegalery']) && ($_FILES["imagegalery"]["name"][0] !== '')) {
        for ($index = 0; $index < count($_FILES["imagegalery"]["name"]); $index++) {

            // $uploadfile     = $_FILES["imagegalery"]["tmp_name"][$index];
            // $filename       = $_FILES["imagegalery"]["name"][$index];
            // $saveto         = str_replace("\\", "/", "$folder" . $filename);
            // $filelocation   = preg_replace("~.*?(?=/wp-content)~", "", $saveto);
            $folder         = $directory . "\\images\\";
            $title          = preg_replace("/\.[^.]*$/", "", $_FILES["imagegalery"]["name"][$index]);
            $user           = get_userdata($current_user->ID)->display_name;
            $category       = $_POST['category'];

            // RESIZE IMAGE
            // GET INFO IMAGE
            $info = getimagesize($_FILES["imagegalery"]["tmp_name"][$index]);

            // IF JPG/JPEG OR PNG
            if ($info['mime'] === "image/jpeg") {
                $image = imagecreatefromjpeg($_FILES["imagegalery"]["tmp_name"][$index]);
            } elseif ($info['mime'] === "image/png") {
                $image = imagecreatefrompng($_FILES["imagegalery"]["tmp_name"][$index]);
            } else {
                continue;
            }

            if (isset($image)) {
                if ($info['mime'] === "image/jpeg") {
                    $outputfile = $title . generateRandomString(7) . ".jpg";
                } elseif ($info['mime'] === "image/png") {
                    $outputfile = $title . generateRandomString(7) . ".png";
                }
                $saveto         = str_replace("\\", "/", "$folder" . $outputfile);
                $filelocation   = preg_replace("~.*?(?=/wp-content)~", "", $saveto);

                list($width, $height) = $info;

                if ($width > $height) {
                    $newHeight = 900;
                    $newWidth = ($newHeight * $width) / $height;
                } else {
                    $newWidth = 900;
                    $newHeight = ($newWidth * $height) / $width;
                }

                $thumb = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Resize and safe image
                imagejpeg($thumb, $saveto);
            }

            $query         .= "('" . $title . "','" . $outputfile . "','" . $user . "','" . $filelocation . "'," . $category . "),";

            // move_uploaded_file($outputfile, $saveto);
        }

        $newQuery = rtrim($query, ',');

        $result = $wpdb->query("INSERT INTO aw_galery (`title`, `filename`, `uploadby`,`filelocation`,`id_category`) VALUE $newQuery");
    }

    // RESPON SUCCESS
    echo json_encode(
        [
            'message' => 'Data inserted successfully',
            'Inserted Data' => $result,
            "status" => 201,
        ]
    );
}

if (isset($_GET) && $_GET['fun'] === 'delete') {
    $images = json_decode(file_get_contents("php://input"), true);
    $imageid = explode(',', $images);

    $imagelocation = [];
    $affectedrow = 0;

    for ($index = 0; $index < count($imageid); $index++) {
        $result = get("SELECT `filename` from `aw_galery` WHERE `id`=$imageid[$index]");
        $deleteres = $wpdb->query("DELETE FROM `aw_galery` WHERE `id`=$imageid[$index]");

        if ($deleteres == 1) {
            $affectedrow += $deleteres;
        }

        $imagelocation[] = $result[0]->filename;
    };

    for ($index = 0; $index < count($imagelocation); $index++) {
        unlink("$directory\\images\\$imagelocation[$index]");
    }

    if ($affectedrow > 0) {
        echo json_encode(
            [
                'message' => 'Data Deleted successfully',
                'Affected Row' => $affectedrow,
                "status" => 200,
            ]
        );
    }
}

if (isset($_GET) && $_GET['fun'] !== '') {
    function getBy($field, $value)
    {
        return get("SELECT COUNT(*) jumlah FROM aw_galery WHERE `$field`=$value");
    }

    if ($_GET['fun'] === 'getall') {
        $result = get("SELECT COUNT(*) jumlah FROM aw_galery");

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            $result = get("SELECT * FROM aw_galery limit $page, 21");

            echo json_encode(["data" => $result]);
            die;
        }

        echo json_encode(["page" => $result[0]->jumlah]);
    } elseif ($_GET['fun'] === 'getbycategory') {
        $_idcat = $_GET['idcat'];
        $result = getBy('id_category', $_idcat);

        echo json_encode(['page' => $result[0]->jumlah]);
    }
}
