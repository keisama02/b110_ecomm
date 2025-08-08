<?php
require_once "dbconnect.php";
if(!isset($_SESSION))
{
    session_start();
}

if(isset($_GET['eid'])) 
{
    $productId = $_GET['eid'];
    try{
    }catch(Exception $e){
        echo $e->getMessage();
    }
}
else if(isset($_GET['did']))
{
    try
    {
    $productId = $_GET['did'];
    $sql = "delete from product where productId=?";
    $stmt = $con->prepare($sql); //prevent SQL injection attack using prepare
    $status = $stmt->execute([$productId]);
    if($status)
    {
        $_SESSION['deleteSuccess'] = "product Id $productId has been deleted.";
        header("location:viewProduct.php");
    }
    }catch(Exception $e)
    {
        echo $e->getMessage();
    }
}

?>