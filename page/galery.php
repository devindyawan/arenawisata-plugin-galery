<?php
global $wpdb;
$tablecat = $wpdb->prefix . "galery_category";
$tablegal = $wpdb->prefix . "galery";

function get($queries): array
{
    global $wpdb;
    $result = $wpdb->get_results($queries);

    return $result;
}

$resultCat = get("SELECT * FROM $tablecat");
$resultDate = get("SELECT DISTINCT YEAR(date) AS 'year', MONTH(date) AS 'month' FROM $tablegal");
$resultGal = get("SELECT id, title, filelocation FROM $tablegal ORDER BY id DESC LIMIT 1, 21");

$monthlist = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$date = [];

foreach ($resultDate as $item) {
    $month = (int)$item->month;
    $year = (int)$item->year;

    $date[] = [$year . ($month < 10 ? "0" . $month : $month), $monthlist[$month - 1] . " " . $year];
}

?>

<section class="galery">
    <div class="container">
        <div class="header">
            <div class="galerytitle">
                <p>Arena Wisata Galery</p>
            </div>
            <div class="leftstage">
                <div class="shortdate">
                    <label for="category">Category : </label>
                    <select name="category" id="category" onchange="getByCategory()">
                        <option value="">Category</option>
                        <?php
                        foreach ($resultCat as $item) {
                        ?>
                            <option value="<?= $item->id ?>"><?= $item->category ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="shortcat">
                    <label for="date">Date : </label>
                    <select name="date" id="date" onchange="getByDate()">
                        <option value="date">Date</option>
                        <?php
                        foreach ($date as $item) {
                        ?>
                            <option value="<?= $item[0] ?>"><?= $item[1] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="multiple">
                    <button onclick="handleSelect()">Multipe Select</button>
                </div>
                <div class="delbutton">

                </div>
            </div>
        </div>
        <div class="galerycontainer">
            <div class="galeryimage" id="galery">
                <?php
                foreach ($resultGal as $item) {
                ?>
                    <div class="imagelist">
                        <img id="image" src="<?= get_site_url() . $item->filelocation ?>" alt="<?= $item->title ?>" key="<?= $item->id ?>">
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="pagination" id="all">

        </div>
    </div>
</section>

<div class="galerymodal" id="galerymodal">
    <div class="container">
        <div class="modalheader">
            <div class="modaltitle">
                <div class="modalwraper">
                    <h2>Galery Photo Detail</h2>
                    <img src="<?= get_site_url() . '/wp-content/plugins/arenawisata-galery/page/asset/close.png' ?>" id="close" alt="Close">
                </div>
            </div>
        </div>
        <div class="modalbody">
            <div class="imageview">

            </div>
            <div class="imagedetail">
                <div class="detailwraper">
                    <div class="readonlydetail">
                        <h3 id="title">Dummy Title</h3>
                        <div class="detail">

                        </div>
                    </div>
                    <div class="formdetail">
                        <div class="formtitle">
                            <h3>Editable Fields</h3>
                        </div>
                        <div class="form">
                            <div class="formbox">
                                <div class="label">Title</div>
                                <div class="input">
                                    <input type="text" id="titleinput">
                                </div>
                            </div>
                            <div class="formbox">
                                <div class="label">Description</div>
                                <div class="input">
                                    <textarea type="text" id="description"></textarea>
                                </div>
                            </div>
                            <div class="formbox">
                                <div class="label">Category</div>
                                <div class="input">
                                    <select name="categoryinput" id="categoryinput">
                                        <option value="">Category</option>
                                        <?php
                                        foreach ($resultCat as $item) {
                                        ?>
                                            <option value="<?= $item->id ?>"><?= $item->category ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="formbox">
                                <div class="label"></div>
                                <div class="input">
                                    <div class="update">
                                        <button>UPDATE</button>
                                    </div>
                                </div>
                            </div>

                            <div class="buttonform">
                                <div id="notification">
                                    Data Berhasil Diupdate.
                                </div>
                                <div class="delete">
                                    <a>Delete Permanent</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>