<?php
require_once "dbconnect.php";
if (!isset($_SESSION)) {
    session_start();
}

try {
    $sql = "select * from category";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (Exception $e) {
    echo $e->getMessage();
}

if (isset($_GET['eid'])) {
    $productId = $_GET['eid'];
    try {
        $sql = "SELECT p.productId, p.productName,
                c.catalog_name, p.category,
                p.price, p.description,
                p.quantity, p.imagePath
                FROM product p, category c
                WHERE p.category = c.catId AND 
                p.productId = ?";

        $statement = $con->prepare($sql);
        $statement->execute([$productId]);
        $product = $statement->fetch();
        //      $_SESSION['product'] = $product;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else if (isset($_GET['did'])) {
    try {
        $productId = $_GET['did'];
        $sql = "delete from product where productId=?";
        $stmt = $con->prepare($sql); //prevent SQL injection attack using prepare
        $status = $stmt->execute([$productId]);
        if ($status) {
            $_SESSION['deleteSuccess'] = "product Id $productId has been deleted.";
            header("location:viewProduct.php");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} else if (isset($_POST['updateBtn'])) {
    $productName = $_POST['pname'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $qty = $_POST['qty'];
    $fileImage = $_FILES['file'];
 
    $filePath = "productImage/$fileImage[name]";
    $status = move_uploaded_file($fileImage['tmp_name'],$filePath);
    if ($status == true) 
    {
        try{
            $pid = $_POST['pid'];
            $sql = "update product set productName=?, category=?, price=?, quantity=?, description=?, imagePath=? where productId=?";
            $stmt = $con->prepare($sql);
            $status = $stmt->execute([$productName, $category, $price, $qty, $description, $filePath, $pid]);
            if ($status) 
            {
                $_SESSION['updateMessage'] = "Product with product id $pid is updated!";
                header("location:viewProduct.php");
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php
            require_once "navbarcopy.php";
            ?>

        </div>
        <div class="row">
            <div class="col-md-2">
                <button class="btn btn-primary">add new</button>

            </div>
            <div class="col-md-10 p-3">
                    <form action="editDelete.php" class="form card p-4" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="pid" value="<?php echo $product['productId']; ?>">
                        <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pname" class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="pname" id="pname"
                                    value="<?php if (isset($product)) {
                                                echo $product['productName'];
                                            }
                                            ?>">
                            </div>
                            <div class="mb-2">
                                <p class="alert alert-info"><?php echo "Previous selected category $product[catalog_name]"; ?></p>
                                <select name="category" id="category" class="form-select">
                                    <option value="0">Choose Category</option>
                                    <?php
                                    if (isset($categories)) {
                                        foreach ($categories as $category) {
                                            echo "<option value=$category[catId]>$category[catalog_name]</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" id="price" class="form-control" name="price"  
                                value="<?php if (isset($product)) {
                                                echo $product['price'];
                                            }
                                            ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="desc" class="form-label">Description</label>
                                <textarea name="description" id="desc" class="form-control" placeholder="Write description here..."></textarea>
                            </div>
                            <div class="mb-2">
                                <label for="qty" class="form-label">Quantity</label>
                                <input type="number" class="form-control" name="qty" id="qty"
                                 value="<?php if (isset($product)) {
                                                echo $product['quantity'];
                                            }
                                            ?>">
                            </div>
                            <div class="mb-2">
                                 <?php if (isset($product)) {
                                                echo "<img class='img-responsive' style=width:100px; height:100px; src=$product[imagePath]>";
                                            }
                                            ?>
                                <label for="img" class="form-label">Product Image</label>
                                <input type="file" class="form-control" name="file" id="img">
                            </div>
                            <div class="mb-2">
                                <button type="submit" class="btn btn-primary" name="updateBtn">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>

</html>