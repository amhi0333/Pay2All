<?php
    session_start();
    $config = parse_ini_file("config.ini");
    require_once('database_conn.php');
    if( !isset($_SESSION['logged_in']) ){
            header("Location: ".$config["HOST_URL"]."/"); 
    }
    // $conn and $conn_stat
    $session_user=isset($_SESSION['user'])?$_SESSION['user']:"";
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
    else if( $conn_stat && count($_POST) > 0 && !isset($_POST["modalsave"]) ) {
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
        $sql_exists_query="select * from customer where customer_id=$customerId";
        $result = $conn->query($sql_exists_query);
//        $found=false;
//        if ($result->num_rows > 0) {
//            $found=true;
//        }
//        if($found){//update
//             $sql_query = "UPDATE customer SET name = '$name',address='$address',payable_amount='$payableAmount',due_date='$dueDate',billing_date = '".date("Y-m-d")."',added_amount = '".$amount."', custom_message = '".$customMessage."' WHERE customer.customer_id = ".$customerId;
//        }else{//insert
//             $sql_query = "INSERT INTO customer (id, customer_id, name, address, payable_amount, due_date,billing_date, added_amount, custom_message) VALUES (NULL, ".$customerId.", '".$name."', '".$address."', ".$payableAmount.", '".$dueDate."','".date("Y-m-d")."', ".$amount.", '".$customMessage."')";
//        }
        $sql_query = "INSERT INTO customer (id, customer_id, name, address, payable_amount, due_date,billing_date, added_amount, custom_message,user_name) VALUES (NULL, ".$customerId.", '".$name."', '".$address."', ".$payableAmount.", '".$dueDate."','".date("Y-m-d")."', ".$amount.", '".$customMessage."','$session_user')";
        if( !mysqli_query($conn , $sql_query) ){
            // echo mysqli_error($conn);
            echo ($sql_query);
        }else{
            echo '{ "result" : "true" , "message" : "Bill has been paid "}';
        }
        exit;
    }
    else{
        // echo print_r("Negtive",true);
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
                                        <div class="input-group mb-3 validityHidden">
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
                                                <label for="amount">Amount</label>
                                            </div>
                                        </div>
                                        <input name="OperatorId" type="hidden" id="OperatorId" class="form-control" value=83>
                                </div>
                                <!-- Button -->
                                <button type="submit" id="billPayBtn" class="btn btn-primary validityHidden">Print</button>
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
                                 <form class="md-form needs-validation billPay" action="#" method="GET">
                                        <div class="input-group mb-3">
                                            <button type="submit" name="logout" id="logout" class="btn btn-primary">Logout</button>
                                        </div>
                                </form>
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
                <div id="printSlip" class="col-md-4">
                    <div style="text-align:center">
                        <h4><pre>Pay2All</pre></h4>
                        <h5><pre>Bill Payment</pre></h5>
                        <h6 id="setTime"></h6>
                        <h6><pre>Name:<span id="printName"></span></pre></h6>
                        <h6><pre>Customer:<span id="printCustomerId"></span></pre></h6>
                        <h6><pre>Due Date:<span id="printDueDate"></span></pre></h6>
                        <h6><pre>Bill Date:<?=date("Y-m-d")?></pre></h6>
                        <h6><pre>Payable Amount:<span id="printPayableAmount"></span></pre></h6>
                        <h6><pre>Total Amount:<span id="printAmount"></span></pre></h6>
                        <h6><pre>Message:<span id="message"></span></pre></h6>
                        <h6><pre>Receiver:<?=$session_user?></pre></h6>
                        
                        <h5>-----Thank you for using Pay2All------</pre></h5>
                    </div>
                </div>
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
        width : 400px;
        font-size: 9px;
    }
</style>

<script>
    var consumerNo = $("#CustomerId").val();
    $(document).ready(function() {
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
                        console.log(JSON.stringify(row));
                        if(type === 'display'){
                            return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editRow(this)" id="row'+data+'">Edit</button>';
                        }else if(type === 'sort'){
                            return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editRow(this)" id="row'+data+'">Edit</button>';
                        }else{
                            return '<button class="btn btn-sm btn-primary" data-values=\''+JSON.stringify(row)+'\' onclick="editRow(this)" id="row'+data+'">Edit</button>';
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
    })
    
    

    $('#billPayBtn').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var billPayFormData = $('.billPay').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        });
        var url = "<?php echo $config['HOST_URL']?>/dashboard.php";
        new Promise( (resolve , reject) => {
            var x = ajaxSubmit( billPayFormData , url , "POST" );
            setTimeout( resolve(x) , 2000)
        } ).then(function(data){
            var nameArr = billPayFormData.name.split(',');
            var d = new Date();
            total_pay=parseInt(billPayFormData.payableAmount)+parseInt(billPayFormData.amount);
            $("#setTime").html(d);
            $("#printName").html(nameArr[0]);
            $("#printDueDate").html(billPayFormData.dueDate);
            $("#printCustomerId").html(billPayFormData.value);
            $("#printPayableAmount").html(billPayFormData.payableAmount);
            $("#printAmount").html(total_pay);
            $("#message").html(billPayFormData.customMessage);
            printJS(
                {
                    printable :'printSlip',
                    type : 'html' ,
                    targetStyles: [
                        'width', 'font-size'
                    ]
                }
            );
            window.onafterprint = function(){
                window.location.reload(true);
            }
        }) 
        
    })


</script>
</html>