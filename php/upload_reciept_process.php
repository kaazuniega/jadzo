<?php 
include 'server.php';
session_start();
date_default_timezone_set('Asia/Manila');
$date = date('m/d/Y h:i:s A');

$peso = "₱";
$id = $_GET['id'];
$amount_paid = $_POST['amount_paid'];
$full_name = strtoupper($_POST['full_name']);
$reserve_title = strtoupper($_POST['reserve_title']);
$price = ltrim($_POST['price'],"₱");
$contact_number = strtoupper($_POST['contact_number']);
$address = strtoupper($_POST['address']);
$car_model = strtoupper($_POST['car_model']);
$date_reserve = strtoupper($_POST['date_reserve']);
$ref_num = $_POST['ref_num'];

$get_img = $_FILES['reciept']['tmp_name'];
$receipt = addslashes(file_get_contents($get_img));

$new_amount = ($price - $amount_paid) <= 0 ? "0" : ($price - $amount_paid);
$new_amount_condition = ($price - $amount_paid) > 0 ? 1 : 0;

if ($new_amount_condition == 1) {
    $update_reserve = mysqli_query($conn, "UPDATE reserve set price = '$peso$new_amount' WHERE id = '$id'");

    if ($update_reserve) {
        $admin = mysqli_fetch_object(mysqli_query($conn, "SELECT * FROM admin_users WHERE id = '$_SESSION[id]'"));
        $admin_name = "{$admin->fname} {$admin->mname[0]}. {$admin->lname}";

        $insert_report = mysqli_query($conn, "INSERT INTO report VALUES (
            NULL,
            '$full_name',
            '$contact_number',
            '$address',
            '$car_model',
            '$reserve_title',
            '$peso$price',
            '$date_reserve',
            '$peso$amount_paid',
            '$date',
            '$receipt',
            '$admin_name',
            '$ref_num'
        )");

        if ( $insert_report ) {
            echo "
            <script>
                alert('Mark as initial payment.');
                window.location.href = '../pages/admin_reserves.php';
            </script>
            ";
        }
    }
}
else if ($new_amount_condition == 0) {
    $delete_reserve = mysqli_query($conn, "DELETE FROM reserve WHERE id = '$id'");

    if ($delete_reserve) {
        $admin = mysqli_fetch_object(mysqli_query($conn, "SELECT * FROM admin_users WHERE id = '$_SESSION[id]'"));
        $admin_name = "{$admin->fname} {$admin->mname[0]}. {$admin->lname}";

        $insert_report = mysqli_query($conn, "INSERT INTO report VALUES (
            NULL,
            '$full_name',
            '$contact_number',
            '$address',
            '$car_model',
            '$reserve_title',
            '$peso$price',
            '$date_reserve',
            '$peso$amount_paid',
            '$date',
            '$receipt',
            '$admin_name',
            'ref_num'
        )");

        if ( $insert_report ) {
            echo "
            <script>
                alert('Mark as Paid!');
                window.location.href = '../pages/admin_reserves.php';
            </script>
            ";
        }
    }
}
