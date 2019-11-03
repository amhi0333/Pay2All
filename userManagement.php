<?php

require_once('database_conn.php');
// $conn and $conn_stat

if( $conn_stat && count($_GET) > 0 && isset($_GET['username'] ) ){
    if( !empty($_GET['username']) ){
        $username = $_GET['username'];
        return getUserList($username , $conn);
    }else{
        return getUserListAll($conn);
    }
}else{
    return getUserListAll($conn);
}

function getUserList($username , $conn){
    $sql_query = "select * from user where username='$username'";
    $result = $conn->query($sql_query);
    $result_arr = $user = [] ;
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $user['id'] = $row["id"]; 
            $user['name'] = $row["name"]; 
            $user['email'] = $row["email"]; 
            $user['username'] = $row["username"]; 
            $user['password'] = $row["password"]; 
            $user['phone'] = $row["phone"];
            $user['print_limit'] = $row["print_limit"]; 
            $user['printed'] = $row["printed"]; 
            $user['hide_column'] = $row["hide_column"]; 
            $result_arr['data'][] = $user; 
        }
        echo json_encode($result_arr);
    } else {
        echo json_encode($result_arr);
    }
}

function getUserListAll($conn){
    session_start();
    if(isset($_SESSION['user']) && $_SESSION['user']=="admin" ){
        $sql_query = "select * from user";
    }else{
        $sql_query = "select * from user where username='". $_SESSION['user']."'";
    }
    $result = $conn->query($sql_query);
    $result_arr = $user = [] ;
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $user['id'] = $row["id"]; 
            $user['name'] = $row["name"]; 
            $user['email'] = $row["email"]; 
            $user['username'] = $row["username"]; 
            $user['password'] = $row["password"]; 
            $user['phone'] = $row["phone"];
            $user['print_limit'] = $row["print_limit"];
            $user['printed'] = $row["printed"]; 
            $user['hide_column'] = $row["hide_column"]; 
            $result_arr['data'][] = $user; 
        }
        echo json_encode($result_arr);
    } else {
        echo json_encode($result_arr);
    }
}