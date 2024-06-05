<?php

class Initialtable extends wpdb
{
    public function __construct($file)
    {
        register_activation_hook($file,  [$this, 'init']);
    }

    function init()
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $queries = array($this->galeryTable($prefix), $this->categoryTable($prefix), $this->tableFK($prefix), $this->insertTableCategory($prefix));

        require_once(ABSPATH . "wp-admin/includes/upgrade.php");

        foreach ($queries as $value) {
            dbDelta($value);
        }
    }

    function galeryTable($prefix): string
    {
        $tablename = $prefix . 'galery';

        return "CREATE TABLE $tablename (
            `id` INT (4) AUTO_INCREMENT,
            `title` VARCHAR (255),
            `filename` VARCHAR (255),
            `uploadby` VARCHAR (255),
            `description` TEXT DEFAULT '',
            `date` DATE NOT NULL DEFAULT (CURRENT_DATE),
            `filelocation` VARCHAR (255),
            `id_category` INT (4),
            PRIMARY KEY (`id`)) ENGINE=InnoDB";
    }

    function categoryTable($prefix): string
    {
        $tablename = $prefix . 'galery_category';

        return "CREATE TABLE $tablename (
            `id` INT (4) AUTO_INCREMENT,
            `category` VARCHAR (255),
            PRIMARY KEY (`id`)) ENGINE=InnoDB";
    }

    function tableFK($prefix): string
    {
        return "ALTER TABLE " . $prefix . "galery" .
            " ADD FOREIGN KEY (`id_category`)
            REFERENCES `" . $prefix . "galery_category" . "`(`id`)
            ON DELETE RESTRICT ON UPDATE RESTRICT";
    }

    function insertTableCategory($prefix): string
    {
        $result = get_categories(array("hide_empty" => 0, 'parent' => @get_term_by('slug', 'paket-wisata', 'category')->term_id));

        $category = '';
        foreach ($result as $cat) {
            $category .= "(NULL,'" . $cat->name . "'),";
        }

        $newCat = rtrim($category, ',');

        return "INSERT INTO " . $prefix . "galery_category" . " (id, category) VALUES $newCat";
    }
}


// function AWG_initialtable()
// {
//     global $wpdb;

//     $AWG_tablename_galery = $wpdb->prefix . 'galery';
//     $AWG_tablename_category = $wpdb->prefix . 'galery_category';

//     $AWG_galery_query = "CREATE TABLE $AWG_tablename_galery (
//         `id` int (4) AUTO_INCREMENT,
//         `filename` varchar (255),
//         `id_category` int (4),
//         PRIMARY KEY (`id`)
//         ) ENGINE=InnoDB";

//     $AWG_category_query = "CREATE TABLE $AWG_tablename_category(
//         `id` int (4) AUTO_INCREMENT,
//         `category` varchar (255),
//         PRIMARY KEY (`id`)
//         ) ENGINE=InnoDB";

//     $AWG_foreignkey_query = "ALTER TABLE $AWG_tablename_galery
//         ADD FOREIGN KEY (`id_category`)
//         REFERENCES `$AWG_tablename_category`(`id`)
//         ON DELETE RESTRICT ON UPDATE RESTRICT";


//     // ASSIGN to Table Categories
//     $paket_wisata = get_categories(array("hide_empty" => 0, 'parent' => @get_term_by('slug', 'paket-wisata', 'category')->term_id));

//     $category = '';
//     foreach ($paket_wisata as $cat) {
//         $category .= "(NULL,'" . $cat->name . "'),";
//     }

//     $newCat = rtrim($category, ',');
//     $AWG_catval_query = "INSERT INTO $AWG_tablename_category (id, category) VALUES $newCat";


//     require_once(ABSPATH . "wp-admin/includes/upgrade.php");
//     dbDelta($AWG_galery_query);
//     dbDelta($AWG_category_query);
//     dbDelta($AWG_foreignkey_query);
//     dbDelta($AWG_catval_query);
// }
