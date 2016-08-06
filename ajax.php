<?php
/**
 * Created by PhpStorm.
 * User: Nimo
 * Date: 03/08/2016
 * Time: 9:48 CH
 */
error_reporting(1);
//$conn = "";
try {
    $conn = new PDO("mysql:dbname=mvc-project;host=localhost", "root", "") or die("ahihi");
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $conn->exec("SET CHARACTER SET utf8");
} catch (PDOException $e) {
    die($e->getMessage());
}
//echo var_dump($_POST);
//foreach($conn->query('SELECT * from products LIMIT 0,12') as $row) {
//    echo print_r($row);
//}

if (isset($_POST['page']) && isset($_POST['items_per_page'])) {
//    echo 1;

//    sleep(2);
    $page = $_POST['page'];
    $items_per_page = $_POST['items_per_page'];
    load_more($page, $items_per_page, $conn);
}


if (isset($_POST['search'])) {

    $char = $_POST['search'];

    live_search($char, $conn);

}


if(isset($_POST['name'])){
    $name = $_POST['name'];
    load_contents_by_name($name,$conn);
}

function load_more($page, $items_per_page, $conn)
{
    $range = ($page - 1) * $items_per_page;

    try {
        $stmt = $conn->prepare("SELECT * FROM products LIMIT ?,? ");
        $stmt->bindValue(1, $range, PDO::PARAM_INT);
        $stmt->bindValue(2, $items_per_page, PDO::PARAM_INT);
        $stmt->execute();


    } catch (PDOException $e) {
        die(json_encode($e->getMessage()));
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = json_encode($result);
    echo $result;
}


function live_search($char,$conn)
{
    try {
        $sql = "SELECT * FROM products WHERE products.product_name LIKE :search ORDER BY products.product_name LIMIT 5 ";
        $query_string = $char . "%";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(
            ":search" => $query_string
        ));
    } catch (PDOException $e) {
        die(json_encode($e->getMessage()));
    }

    if ($stmt->rowCount() > 0) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = json_encode($result);
        echo $result;
    } else {
        echo json_encode(['not_found' => "Không tìm thấy kết quả"]);
    }
}


function load_contents_by_name($name,$conn){

    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE products.product_name = ?");
        $stmt->bindParam(1, $name, PDO::PARAM_STR);
        $stmt->execute();


    } catch (PDOException $e) {
        die(json_encode($e->getMessage()));
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = json_encode($result);
    echo $result;
}