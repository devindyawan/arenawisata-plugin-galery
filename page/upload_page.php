<?php
global $wpdb;
$tablecat = $wpdb->prefix . "galery_category";

function get($queries): array
{
    global $wpdb;
    $result = $wpdb->get_results($queries);
    return $result;
}

$resultCat = get("SELECT * FROM $tablecat");

?>

<section class="home">
    <div class="container">
        <div class="title">
            <h2>Upload Multiple Image</h2>
        </div>
        <div class="formdata">
            <form action="" method="post" enctype="multipart/form-data" id="imagegaleryform">
                <div class="select">
                    <label for="imagegalery" id="selectfile">Select File</label>
                    <input type="file" name="imagegalery[]" id="imagegalery" onchange="previewImage()" multiple>
                    <select name="category" id="category">
                        <option value="default">Category</option>
                        <?php
                        foreach ($resultCat as $item) {
                        ?>
                            <option value="<?= $item->id ?>"><?= $item->category ?></option>
                        <?php } ?>
                    </select>
                </div>
                <input type="submit" name="uploadImage" value="Upload File">
            </form>
        </div>
        <div class="prevcontainer">
            <div class="preview" id="preview">

            </div>
        </div>
    </div>
</section>