<?php

require_once('database_conn.php');
// $conn and $conn_stat

if( $conn_stat && count($_GET) > 0 && isset($_GET['consumerId'] ) ){
    if( !empty($_GET['consumerId']) ){
        $consumerId = $_GET['consumerId'];
        return getPaymentHistoryList($consumerId , $conn);
    }else{
        return getPaymentHistoryListAll($conn);
    }
}else{
    return getPaymentHistoryListAll($conn);
}

function getPaymentHistoryList($customerId , $conn){
    $sql_query = "select * from customer where customer_id=". $customerId ;
    $result = $conn->query($sql_query);
    $result_arr = $payment = [] ;
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $payment['id'] = $row["id"]; 
            $payment['customer_id'] = $row["customer_id"]; 
            $payment['name'] = $row["name"]; 
            $payment['address'] = $row["address"]; 
            $payment['payable_amount'] = $row["payable_amount"]; 
            $payment['due_date'] = $row["billing_date"]; 
            $payment['billing_date'] = $row["billing_date"];
            $payment['added_amount'] = $row["added_amount"]; 
            $payment['custom_message'] = $row["custom_message"];
            $payment['user_name'] = $row["user_name"];
            $result_arr['data'][] = $payment; 
        }
        echo json_encode($result_arr);
    } else {
        echo json_encode($result_arr);
    }
}

function getPaymentHistoryListAll($conn){
    session_start();
    if(isset($_SESSION['user'])){
        $sql_query = "select * from customer where user_name='". $_SESSION['user']."'";
    }else{
        $sql_query = "select * from customer";
    }
    $result = $conn->query($sql_query);
    $result_arr = $payment = [] ;
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $payment['id'] = $row["id"]; 
            $payment['customer_id'] = $row["customer_id"]; 
            $payment['name'] = $row["name"]; 
            $payment['address'] = $row["address"]; 
            $payment['payable_amount'] = $row["payable_amount"]; 
            $payment['due_date'] = $row["due_date"]; 
            $payment['billing_date'] = $row["billing_date"];
            $payment['added_amount'] = $row["added_amount"]; 
            $payment['custom_message'] = $row["custom_message"];
            $payment['user_name'] = $row["user_name"];
            $result_arr['data'][] = $payment; 
        }
        echo json_encode($result_arr);
    } else {
        echo json_encode($result_arr);
    }
}