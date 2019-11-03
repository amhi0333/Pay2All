<?php
    session_start();
    $config = parse_ini_file("config.ini");
    require_once('database_conn.php');
    if( !isset($_SESSION['logged_in']) ){
            header("Location: ".$config["HOST_URL"]."/"); 
    }
    
    // $conn and $conn_stat
    $session_user=isset($_SESSION['user'])?$_SESSION['user']:"";
    //user_info
    $sql_query = "select * from user where username='$session_user'";
    $result = $conn->query($sql_query);
    $user_arr = [] ;
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $user_arr['id'] = $row["id"]; 
            $user_arr['name'] = $row["name"]; 
            $user_arr['email'] = $row["email"]; 
            $user_arr['username'] = $row["username"]; 
            $user_arr['password'] = $row["password"]; 
            $user_arr['phone'] = $row["phone"];
            $user_arr['print_limit'] = $row["print_limit"]; 
            $user_arr['printed'] = $row["printed"]; 
            $user_arr['hide_column'] = $row["hide_column"]; 
        }
    } 
    $print_capability=true;
    if($user_arr['printed']>=$user_arr['print_limit']){
        $print_capability=false;
    }
    if( $conn_stat && count($_POST) > 0 && isset($_POST["modalsave"]) ) {
         //$name = isset($_POST["name"])?$_POST["name"]:"";
        //$address = isset($_POST["address"])?$_POST["address"]:"";
        //$payableAmount = isset($_POST["payableAmount"])?$_POST["payableAmount"]:"";
        $customMessage = isset($_POST["customMessage"])?$_POST["customMessage"]:"";
        $amount = isset($_POST["amount"])?$_POST["amount"]:"";
        $id = $_POST["modalsave"];
        $billing_date=$_POST["billing_date"];
        //$customerId = $_POST["customerId"];
        //$dueDate = $_POST["dueDate"];
        $sql_query = "UPDATE customer SET billing_date = '".$billing_date."',added_amount = '".$amount."', custom_message = '".$customMessage."' WHERE customer.id = ".$id;
        $result = $conn->query($sql_query);
    }
    if( $conn_stat && count($_POST) > 0 && isset($_POST["insert_customer"]) ) {
        $name_post = isset($_POST["name"])?$_POST["name"]:"";
        $add_arr=explode(',',$name_post);
        $name="";
        $address="";
        if(isset($add_arr[0])){
            $name=$add_arr[0];
        }
        if(isset($add_arr[1])){
            $address=$add_arr[1];
        }
        $session_user=isset($_SESSION['user'])?$_SESSION['user']:"";
        $payableAmount = isset($_POST["payableAmount"])?$_POST["payableAmount"]:"";
        $customMessage = isset($_POST["customMessage"])?$_POST["customMessage"]:"";
        $amount = isset($_POST["amount"])?$_POST["amount"]:"";
        $customerId = $_POST["value"];
        $dueDate = $_POST["dueDate"];
        $sql_exists_query="select * from customer where customer_id=$customerId and due_date='$dueDate'";
        $result = $conn->query($sql_exists_query);
        $found=false;
        if ($result->num_rows > 0) {
            $found=true;
        }
        if($found){//update
             $sql_query = "UPDATE customer SET name = '$name',address='$address',payable_amount='$payableAmount',due_date='$dueDate',billing_date = '".date("Y-m-d")."',added_amount = '".$amount."', custom_message = '".$customMessage."' WHERE customer.customer_id = ".$customerId;
        }else{//insert
             $sql_query = "INSERT INTO customer (id, customer_id, name, address, payable_amount, due_date,billing_date, added_amount, custom_message,user_name) VALUES (NULL, ".$customerId.", '".$name."', '".$address."', ".$payableAmount.", '".$dueDate."','".date("Y-m-d")."', ".$amount.", '".$customMessage."','$session_user')";
        }
//        $sql_query = "INSERT INTO customer (id, customer_id, name, address, payable_amount, due_date,billing_date, added_amount, custom_message,user_name) VALUES (NULL, ".$customerId.", '".$name."', '".$address."', ".$payableAmount.", '".$dueDate."','".date("Y-m-d")."', ".$amount.", '".$customMessage."','$session_user')";
        if( !mysqli_query($conn , $sql_query) ){
            // echo mysqli_error($conn);
            echo ($sql_query);
        }else{
            echo '{ "result" : "true" , "message" : "Bill has been paid "}';
        }
        exit;
    }
    if( $conn_stat && count($_POST) > 0 && isset($_POST["editusermodal"]) ) {
        if(!empty($_POST["editusermodal"])){
            $user_id=$_POST["editusermodal"];
            $set="id=$user_id";
            if(!empty($_POST['edit_user_name'])){
                $name=$_POST['edit_user_name'];
                $set.=",name='$name'";
            }
            if(!empty($_POST['edit_print_limit'])){
                $print_limit=$_POST['edit_print_limit'];
                $set.=",print_limit=$print_limit";
            }
            if(!empty($_POST['edit_password'])){
                $edit_password=$_POST['edit_password'];
                $set.=",password='$edit_password'";
            }
            if(!empty($_POST['edit_hide_column'])){
                $set.=",hide_column=1";
            }
            if(empty($_POST['edit_hide_column'])){
                $set.=",hide_column=0";
            }
            $sql_q="UPDATE user set $set WHERE id=$user_id";
            if( !mysqli_query($conn , $sql_q) ){
            // echo mysqli_error($conn);
                echo ($sql_q);
            }else{
                //echo '{ "result" : "true" , "message" : "User Updated"}';
            }
            header("Location: ".$config["HOST_URL"]."/dashboard.php");
        }else{
        }
    }
    if(isset($_POST['cmd']) && $_POST['cmd']=='print'){
        $print_user=$_POST['user'];
        //increase printed 
        $print_sql="update user set printed=printed+1 where username='$print_user'";
        $result = $conn->query($print_sql);
    }
    if(isset($_GET['cmd']) && $_GET['cmd']=='delete'){
        $user_id=$_GET['user'];
        $sql="DELETE FROM user WHERE id=$user_id";
        $result = $conn->query($sql);
        header("Location: ".$config["HOST_URL"]."/"); 
    }
    if(isset($_GET['logout'])){
        unset($_SESSION['logged_in']);
        header("Location: ".$config["HOST_URL"]."/"); 
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pay2All</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <!-- Bootstrap core CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.10/css/mdb.min.css" rel="stylesheet">
    
    <!-- JQuery -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.4/umd/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.10/js/mdb.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/b-1.6.1/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/b-1.6.1/datatables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">
    <script type="text/javascript" src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
  

    <title>Document</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <br>
                <br>
                <br>
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab"
                        aria-controls="pills-home" aria-selected="true">Payment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab"
                        aria-controls="pills-profile" aria-selected="false">Payment List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-user-tab" data-toggle="pill" href="#pills-user" role="tab"
                        aria-controls="pills-user" aria-selected="false">User Management</a>
                    </li>
                </ul>
                <div class="tab-content pt-2 pl-1" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <!-- Card -->
                        <div class="card">
                            <!-- Card content -->
                            <div class="card-body">
                                <!-- Title -->
                                <h4 class="card-title"><a>Electricity Payment</a></h4>
                                <div class="col-md-5">
                                    <form class="md-form needs-validation billPay" action="#" method="POST">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <input name="customerId" type="number" class="form-control" id="CustomerId" aria-describedby="basic-addon3" required>
                                                <label for="CustomerId">Consumer ID Number</label>
                                            </div>
                                            <button id="validateBtn" type="button" class="btn btn-primary">Validate</button>
                                            <div class="invalid-feedback">
                                                Please provide a valid ID Number.
                                            </div>
                                        </div>
                                        <div class="input-group mb-3 validityHidden" >
                                            <div class="input-group">
                                                <input name="name" type="text" class="form-control" id="name" aria-describedby="basic-addon3" readonly="readonly">
                                                <label for="name" class="disabled">Name</label>
                                            </div>
                                        </div>
                                        <div class="input-group mb-3 validityHidden">
                                            <div class="input-group">
                                                <input name="dueDate" type="text" class="form-control" id="dueDate" aria-describedby="basic-addon3" readonly="readonly">
                                                <label for="dueDate" class="disabled">Due Date</label>
                                            </div>
                                        </div>
                                        <div class="input-group mb-3 " style="display: none;">
                                            <div class="input-group">
                                                <input name="payableAmount" type="number" class="form-control" id="payableAmount" aria-describedby="basic-addon3" readonly="readonly">
                                                <label for="payableAmount" class="disabled">Payable Amount</label>
                                            </div>
                                        </div>
                                        <div class="input-group mb-3 validityHidden">
                                            <div class="input-group">
                                                <input name="customMessage" type="text" class="form-control" id="customMessage" aria-describedby="basic-addon3" >
                                                <label for="customMessage" class="">Custom Message</label>
                                            </div>
                                        </div>
                                        <div class="input-group mb-3 validityHidden payingAmount">
                                            <div class="input-group">
                                                <input name="amount" type="number" class="form-control" id="amount" aria-describedby="basic-addon3" >
                                                <label for="amount">Password</label>
                                            </div>
                                        </div>
                                        <input name="OperatorId" type="hidden" id="OperatorId" class="form-control" value=83>
                                </div>
                                <?php if(!$print_capability):?>
                                <div><p style="color: red;">Please contact with Administration for enabling Print option.</p></div>
                                <?php endif;?>
                                <!-- Button -->
                                <button type="submit" id="billPayBtn" class="btn btn-primary validityHidden" <?=(!$print_capability)?'disabled':''?>>Print</button>
                                <button type="submit" id="billSave" class="btn btn-primary validityHidden">Save</button>
                                </form>
                            </div>
                        </div>
                        <!-- Card -->
                    </div>
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <!-- Editable table -->
                        <div class="card">
                            <h3 class="card-header text-center font-weight-bold text-uppercase py-4">Payment List</h3>
                            <div class="card-body">
                            <div id="table" class="table-editable">
                                <table class="table table-bordered table-responsive-md table-striped text-center" id="paymentHistoryTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Id</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Address</th>
                                        <th class="text-center">Customer No</th>
                                        <th class="text-center">Due Date</th>
                                        <th class="text-center">Billing Date</th>
                                        <th class="text-center">Payable Amount</th>
                                        <th class="text-center">Added Amount</th>
                                        <th class="text-center">Custom Message</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                                </table>
                            </div>
                            </div>
                        </div>
                        <!-- Editable table -->
                    </div>
                    <div class="tab-pane fade" id="pills-user" role="tabpanel" aria-labelledby="pills-user-tab">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-header text-center font-weight-bold text-uppercase py-4">User Management</h3>
                                 <form class="md-form needs-validation billPay" action="#" method="GET">
                                        <div class="input-group mb-3">
                                            <button type="submit" name="logout" id="logout" class="btn btn-primary">Logout</button>
                                        </div>
                                </form>
                                <div id="user_table" class="table-editable">
                                <table class="table table-bordered table-responsive-md table-striped text-center" id="user_list_table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Id</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Username</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Print Limit</th>
                                        <th class="text-center">Printed</th>
                                        <th class="text-center">Hide Column</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                                </table>
                            </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div id="myModal" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header text-center">
                                <h4 class="modal-title w-100 font-weight-bold">Edit Payment</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form class="md-form" id="modalForm" action="#" method="POST">
                                    <div class="input-group mb-3 " >
                                        <div class="input-group">
                                            <input name="name" type="text" class="form-control" id="name" aria-describedby="basic-addon3" readonly="readonly">
                                            <label for="name" class="">Name</label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 ">
                                        <div class="input-group">
                                            <input name="address" type="text" class="form-control" id="address" aria-describedby="basic-addon3" readonly="readonly">
                                            <label for="address" class="">Address</label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 ">
                                        <div class="input-group">
                                            <input name="customerId" type="number" class="form-control" id="customerId" aria-describedby="basic-addon3" readonly="readonly">
                                            <label for="customerId" class="">Customer Id</label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 ">
                                        <div class="input-group">
                                            <input name="dueDate" type="text" class="form-control" id="dueDate" aria-describedby="basic-addon3" readonly="readonly">
                                            <label for="dueDate" class="">Due Date</label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 ">
                                        <div class="input-group">
                                            <input name="billing_date" type="text" class="form-control" id="billing_date" aria-describedby="basic-addon3">
                                            <label for="billing_date" class="">Billing Date</label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 ">
                                        <div class="input-group">
                                            <input name="payableAmount" type="number" class="form-control" id="payableAmount" aria-describedby="basic-addon3" readonly="readonly">
                                            <label for="payableAmount" class="">Payable Amount</label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 ">
                                        <div class="input-group">
                                            <input name="customMessage" type="text" class="form-control" id="customMessage" aria-describedby="basic-addon3" >
                                            <label for="customMessage" class="">Custom Message</label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="input-group">
                                            <input name="amount" type="number" class="form-control" id="amount" aria-describedby="basic-addon3">
                                            <label for="Amount">Amount</label>
                                        </div>
                                    </div>
                                    <input name="modalsave" type="hidden" class="form-control" id="modalsave" aria-describedby="basic-addon3">
                            </div>
                            <div class="modal-footer">
                                <button type="button" type="submit" name="modal_save" id="modal_save" class="btn btn-primary" >Save</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="userModal" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header text-center">
                                <h4 class="modal-title w-100 font-weight-bold">Edit User</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form class="md-form" id="modalUser" action="#" method="POST">
                                    <div class="input-group mb-3 " >
                                        <div class="input-group">
                                            <input name="edit_user_name" type="text" class="form-control" id="edit_user_name" aria-describedby="basic-addon3" >
                                            <label for="edit_user_name" class="">Name</label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 " >
                                        <div class="input-group">
                                            <input name="edit_email" type="text" class="form-control" id="edit_email" aria-describedby="basic-addon3">
                                            <label for="edit_email" class="">Email</label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 " >
                                        <div class="input-group">
                                            <input name="edit_password" type="text" class="form-control" id="edit_password" aria-describedby="basic-addon3">
                                            <label for="edit_password" class="">Password</label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 " >
                                        <div class="input-group">
                                            <input name="edit_print_limit" type="text" class="form-control" id="edit_print_limit" aria-describedby="basic-addon3" <?=($session_user!='admin')?'readonly="readonly"':'';?>>
                                            <label for="edit_print_limit" class="">Print Limit</label>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 " >
                                        <div class="input-group">
                                            <input name="edit_hide_column" type="checkbox" class="form-control" id="edit_hide_column" aria-describedby="basic-addon3" <?=($session_user!='admin')?'readonly="readonly"':'';?>>
                                            <label for="edit_hide_column" class="">Hide Column</label>
                                        </div>
                                    </div>
                                    
                                    
                                    <input name="editusermodal" type="hidden" class="form-control" id="editusermodal" aria-describedby="basic-addon3">
                            </div>
                            <div class="modal-footer">
                                <button type="button" type="submit" name="edituser_modal" id="edituser_modal" class="btn btn-primary" >Save</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                
        </div>
    </div>
	<div id="printSlip">
		<div>
			<!--<h4>------Pay2All------</h4>-->
			<h5>Bill Payment</h5>
                        <h6>Receiver:<?=$session_user?></h6>
			<h6><span id="setTime"></span></h6>
			<h6>Name:<span id="printName"></span></h6>
			<h6>Customer:<span id="printCustomerId"></span></h6>
			<h6>Due Date:<span id="printDueDate"></span></h6>
			<h6>Bill Date:<?=date("Y-m-d")?></h6>
			<h6>Total Amount:<span id="printAmount"></span></h6>
			<h6>TID:<span id="message"></span></h6>
			<h5>--Thank you for using--</h5>
			<h5>Electricity Bill Payment</h5>
		</div>
	</div>
</body>
<style>
    .pt-3-half {
        padding-top: 1.4rem;
    }
    .validityHidden{
        display: none;
        color : #495057;
    }
    #printSlip{
        display: none;
        width : 71mm;
        font-size: 20px;
        size: 100mm 150mm;
        font-weight: bold;
    }
	
	@media print {
		@page { size: 100mm 150mm}
		#printSlip{
			display:block;
                        margin-left:0 ;
		}
        #printSlip h1,h2,h3,h4,h5,h6{
            /*font-family: SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;*/
            display: block;
            font-size: 20px;
            color: #212529;
            font-weight: bold;
        }
		body> div.container>.row{
			display:none;
		}
		
	} /* this line is needed for fixing Chrome's bug */
	
</style>

<script>
    var consumerNo = $("#CustomerId").val();
    $(document).ready(function() {
        var print_capa='<?=$print_capability?>';
        // var data_url = '/Pay2All/paymentHistory.php?consumerId='+consumerNo
        var data_url = '<?php echo $config['HOST_URL']?>/paymentHistory.php';
        $('#paymentHistoryTable').DataTable( {
            "ajax": data_url,
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "address" },
                { "data": "customer_id" },
                { "data": "due_date" },
                { "data": "billing_date" },
                { "data": "payable_amount" },
                { "data": "added_amount" },
                { "data": "custom_message" },
                {   
                    "data": "id",
                    "render": function ( data, type, row ) {
                        if(print_capa=='1'){
                            print_button='<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="Print_copy(this)" id="print_row'+data+'">Print</button>';
                        }else{
                            print_button='<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="Print_copy(this)" id="print_row'+data+'" disabled>Print</button>';
                        }
                        if(type === 'display'){
                            return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editRow(this)" id="row'+data+'">Edit</button>'
                             +print_button;
                        }else if(type === 'sort'){
                            return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editRow(this)" id="row'+data+'">Edit</button>'
                             +print_button;
                        }else{
                            return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editRow(this)" id="row'+data+'">Edit</button>'
                                +print_button;
                        }
                        
                    }
                    
                }
            ]
        } );
        hide_column_check=<?=$user_arr['hide_column']?>;
        if(hide_column_check==1){
            payment_data_table=$('#paymentHistoryTable').DataTable();
            payment_data_table.columns( [ 6,7 ] ).visible( false, false );
            payment_data_table.columns.adjust().draw( false );
        }else{
            payment_data_table=$('#paymentHistoryTable').DataTable();
            payment_data_table.columns( [ 6,7 ] ).visible( true, true );
            payment_data_table.columns.adjust().draw( true );
        }
        //user_management
        var data_url = '<?php echo $config['HOST_URL']?>/userManagement.php';
        var session_user='<?=$session_user?>';
        $('#user_list_table').DataTable( {
            "ajax": data_url,
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "username" },
                { "data": "email" },
                { "data": "print_limit" },
                { "data": "printed" },
                { "data": "hide_column" },
                {   
                    "data": "id",
                    "render": function ( data, type, row ) {
                        if(session_user=='admin'){
                            if(row.username=="admin"){
                                if(type === 'display'){
                                return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editUser(this)" id="row'+data+'">Edit</button>';
                                }else if(type === 'sort'){
                                    return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editUser(this)" id="row'+data+'">Edit</button>';
                                }else{
                                    return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editUser(this)" id="row'+data+'">Edit</button>';
                                }
                            }else{
                                if(type === 'display'){
                                return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editUser(this)" id="row'+data+'">Edit</button>'
                                 +'<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="deleteUser(this)" id="print_row'+data+'">Delete</button>';
                                }else if(type === 'sort'){
                                    return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editUser(this)" id="row'+data+'">Edit</button>'
                                     +'<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="deleteUser(this)" id="print_row'+data+'">Delete</button>';
                                }else{
                                    return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editUser(this)" id="row'+data+'">Edit</button>'
                                        +'<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="deleteUser(this)" id="print_row'+data+'">Delete</button>';
                                }
                            }
                        }else{
                            if(type === 'display'){
                                return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editUser(this)" id="row'+data+'">Edit</button>';
                            }else if(type === 'sort'){
                                return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editUser(this)" id="row'+data+'">Edit</button>';
                            }else{
                                return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editUser(this)" id="row'+data+'">Edit</button>';
                            }
                        }
                    }
                    
                }
            ]
        } );
        
    } );
    function ajaxSubmit(data , url , type){
        $.ajax({
            type : type,
            url : url,
            data : data,
            success : function(response){
                response = JSON.parse(response);
                if(response.result == "true"){
                    console.log(response.result)
                    return true;
                    // to do print
                }else{
                    console.log(response.result)
                    return false;
                }
            },
            error: function(){
                return 0;
            }
        })
    }

    function editUser(value){
        $("#userModal").modal("toggle");
        var values = $(value).data(values);
        $("#modalUser #edit_user_name").val(values.values.name);
        $("#modalUser #edit_email").val(values.values.email);
        $("#modalUser #edit_print_limit").val(values.values.print_limit);
        $("#modalUser #editusermodal").val(values.values.id);
        hide_col=values.values.hide_column;
        if(hide_col==1){
            $("#modalUser #edit_hide_column").prop( "checked", true );
        }else{
            $("#modalUser #edit_hide_column").prop( "checked", false );
        }
        $("#modalUser .input-group>label").attr("class" , "active");
    }
    function deleteUser(value){
        var values = $(value).data(values);
        user_id=values.values.id;
        var delete_url = '<?php echo $config['HOST_URL']?>/dashboard.php?cmd=delete&user='+user_id;
        window.location=delete_url;
    }
    function editRow(value){
        $("#myModal").modal("toggle");
        var values = $(value).data(values);
        $("#modalForm #customerId").val(values.values.customer_id);
        $("#modalForm #name").val(values.values.name);
        $("#modalForm #address").val(values.values.address);
        $("#modalForm #dueDate").val(values.values.due_date);
        $("#modalForm #billing_date").val(values.values.billing_date);
        $("#modalForm #payableAmount").val(values.values.payable_amount);
        $("#modalForm #customMessage").val(values.values.custom_message);
        $("#modalForm #amount").val(values.values.added_amount);
        $("#modalForm #modalsave").val(values.values.id);
        $("#modalForm .input-group>label").attr("class" , "active");
    }
    function update_printed_value(){
        var url = "<?php echo $config['HOST_URL']?>/dashboard.php";
        $.ajax({
            type : 'POST',
            url : url,
            data : {cmd:"print",user:'<?=$session_user?>'},
            success : function(response){
               
            },
            error: function(){
                return 0;
            }
        });
    }
    function Print_copy(value){
        var data = $(value).data(values);
        var values=data.values;
        var d = new Date().toLocaleString();
        total_pay=parseInt(values.payable_amount)+parseInt(values.added_amount);
        $("#setTime").html(d);
        $("#printName").html(values.name);
        $("#printDueDate").html(values.due_date);
        $("#printCustomerId").html(values.customer_id);
        $("#printPayableAmount").html(values.payable_amount);
        $("#printAmount").html(total_pay);
        $("#message").html(values.custom_message);
        window.print();
//        update_printed_value();
        // printJS(
        //     {
        //         printable :'printSlip',
        //         type : 'html' ,
        //         targetStyles: [
        //             'width', 'font-size','size'
        //         ],
        //         maxWidth : '219.212598425'
        //     }
        // );
         window.onafterprint = function(){
             update_printed_value();
             window.location.reload(true);
         }
    }

    $("#validateBtn").click(function(event){
        consumerNo = $("#CustomerId").val();
        var provider = 83;
        var apiToken = "<?php echo $config["API_TOKEN"] ;?>";
        var forms = document.getElementsByClassName('needs-validation');
        $(".invalid-feedback").hide();
        $(".invalid-feedback").html("Provide a valid ID number");
        new Promise( (resolve , reject)=>{
            var validation = Array.prototype.filter.call(forms, function(form) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                    reject(new Error('Invalid'));
                }
                form.classList.add('was-validated');
            })
            resolve(validation);
        })
        .then(function(){
            $("#validateBtn").html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Validating ... ');
            $('.validityHidden').hide();
            $.get("https://www.pay2all.in/web-api/check-bill?apiToken="+ apiToken + "&number=" + consumerNo + "&provider_id=" + provider)
            .done(function (response) {
                if( response.status == 1 ){
                    $("#validateBtn").html('Validate');
                    $("#payableAmount").val(response.amount);
                    $("#amount").val(0);
                    $("#name").val(response.name);
                    $("#dueDate").val(response.duedate);
                    $('.validityHidden > .input-group > label').attr('class' , 'active');
                    $('.validityHidden').show();
                }
                else if( response.status == 2 ){
                    $("#validateBtn").html('Validate');
                    $(".invalid-feedback").html("No Data Found!");
                    $(".invalid-feedback").show();
                }
                else{
                    $("#validateBtn").html('Validate');
                    $(".invalid-feedback").html("Something went wrong!");
                    $(".invalid-feedback").show();
                }
            });
                   
        });
    })

    $("#modal_save").click(function(event){
        event.preventDefault();
        event.stopPropagation();
        document.getElementById("modalForm").submit();
    });
    $("#edituser_modal").click(function(event){
        event.preventDefault();
        event.stopPropagation();
        document.getElementById("modalUser").submit();
    });
    
    

    $('#billPayBtn').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var billPayFormData = $('.billPay').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        });
        var url = "<?php echo $config['HOST_URL']?>/dashboard.php";
        new Promise( (resolve , reject) => {
            billPayFormData['insert_customer']=1;
            var x = ajaxSubmit( billPayFormData , url , "POST" );
            setTimeout( resolve(x) , 2000)
        } ).then(function(data){
            var nameArr = billPayFormData.name.split(',');
            var d = new Date().toLocaleString();
            total_pay=parseInt(billPayFormData.payableAmount)+parseInt(billPayFormData.amount);
            $("#setTime").html(d);
            $("#printName").html(nameArr[0]);
            $("#printDueDate").html(billPayFormData.dueDate);
            $("#printCustomerId").html(billPayFormData.value);
            $("#printPayableAmount").html(billPayFormData.payableAmount);
            $("#printAmount").html(total_pay);
            $("#message").html(billPayFormData.customMessage);
            // printJS(
            //     {
            //         printable :'printSlip',
            //         type : 'html' ,
            //         targetStyles: [
            //             'width', 'font-size','size'
            //         ],
            //         maxWidth: '219.212598425'
            //     }
            // );
            window.print();
            window.onafterprint = function(){
                update_printed_value();
                window.location.reload(true);
            }
        });
        
    });
    $('#billSave').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var billPayFormData = $('.billPay').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        });
        var url = "<?php echo $config['HOST_URL']?>/dashboard.php";
        new Promise( (resolve , reject) => {
            billPayFormData['insert_customer']=1;
            var x = ajaxSubmit( billPayFormData , url , "POST" );
            setTimeout( resolve(x) , 2000)
        } ).then(function(data){
            location.reload();
        });
        
    });


</script>
</html>