<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}


if(isset($_POST['add_to_cart'])){

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];
   $product_id = $_POST['product_id'];
   $product_stock = $_POST['product_stock']-1;

   if($product_quantity > $product_stock){
      $message[] = 'Add to cart failed, only have '.$product_stock.' books for "'.$product_name.'" in stock.';
   }
   else {
      $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

      if(mysqli_num_rows($check_cart_numbers) > 0){
         $message[] = 'already added to cart successfully!';
      }else{
         mysqli_query($conn, "INSERT INTO `cart`(user_id,Product_ID, name, price, quantity, image) VALUES('$user_id','$product_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
         $message[] = 'already added to cart successfully!';
      }
   }
}

function fetch_to_arr(&$mysql_obj, &$record_array, $num_fetch) {
   $i = 0;
   while($i < $num_fetch && $row_record = mysqli_fetch_assoc($mysql_obj)) {
      $record_array[] = $row_record;
      $i = $i + 1;
   }
   return count($record_array);
}

$mega_query1="SELECT `product`.`Product_ID` AS id, `product`.`Discount_price` AS discount_price, `product`.`Price` AS price, `category`.`Name` AS catego_name, `product`.`Thumbnail` AS thumbnail, `product`.`Name` AS book_name, `book`.`Quantity_in_store` AS quantity FROM `book` JOIN `category` ON `book`.`CATEG_ID` = `category`.`Category_ID` JOIN `product` ON `book`.`Product_ID` = `product`.`Product_ID` where deleted=0 ORDER BY price ASC";
$mega_query2="SELECT `product`.`Product_ID` AS id, `product`.`Discount_price` AS discount_price, `product`.`Price` AS price, `category`.`Name` AS catego_name, `product`.`Thumbnail` AS thumbnail, `product`.`Name` AS book_name, `book`.`Quantity_in_store` AS quantity FROM `book` JOIN `category` ON `book`.`CATEG_ID` = `category`.`Category_ID` JOIN `product` ON `book`.`Product_ID` = `product`.`Product_ID` where deleted=0 ORDER BY (price-discount_price) DESC";
$mega_product_info1 = mysqli_query($conn,$mega_query1) or die("Mega Query 1 Failed!!");
$mega_product_info2 = mysqli_query($conn,$mega_query2) or die("Mega Query 2 Failed!!");
$catego_list_query="SELECT Name FROM `category`";
$all_catego = mysqli_query($conn,$catego_list_query) or die("Category List Query Failed!!");

$mega_record_array = array();

if(!isset($_GET["sort_by"])){$_GET["sort_by"] = "pricelo";}
if(!isset($_GET["catego"])){$_GET["catego"] = "all_catego";}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <!--SEO tag-->
   <meta name="description" content="Visit our bookstore, shop latest and most popular books, magazines,...everything readable.
">
   <meta name="description" content="Any thing you want to read is here, books, magazines, or even magazin too...">
   <meta name="description" content="A truly place for a bookworm, indulge reading, bibliophile,.... everything related to books.">
   <meta name="description" content="Looking for a preriodical, journal, issues,... here is what for you.">
   <!--SEO tag-->
   <title>BOOKS</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>our shop</h3>
   <p> <a href="home.php">HOME</a> / BOOKS </p>
</div>

<section class="products">
   <div style="display:flex;justify-content:center;margin: 2rem 0;">
      <!--Before Modifed-->
      <h1 class="title">ALL PRODUCTS</h1>
      <!--Before Modifed-->
      <form action="books.php" method="GET">
         <select id="sort-by" name="sort_by">
            <!-- <option value="nosort">Non Sort</option> -->
            <option value="pricelo">Low Price</option>
            <option value="pricehi">High Price</option>
            <option value="discounthi">High Discount</option>
         </select>
         <select id="catego" name="catego">
            <option value="all_catego">All Category</option>
         <?php
            if(empty($all_catego) || !isset($all_catego)) {
               echo '<option value="no_catego">No category</option>';
            }
            else {
               while($fetched_catego = mysqli_fetch_assoc($all_catego)){
                  $catego_name = $fetched_catego['Name'];
                  echo "<option value= $catego_name>".$catego_name."</option>";
               }
            }
         ?>
         </select>
         <button id="sub-btn" type="submit"><i class="fas fa-filter"></i></button>
      </form>
      <style>
         #sort-by,#catego {
            height: 3.5rem;
            border-radius: 0.5rem;
            padding: 0.2rem;
            margin-top: 0.5rem;
            border: 1px black solid;
         }
         #sub-btn{
            border-radius: 0.5rem;
            width:5.5rem;
            height:3.5rem;
            /* background-color:limegreen; */
         }
         #sub-btn:hover{
            cursor:pointer;
            background-color:gray;
         }
         .title {
            margin-right:4rem;
         }
      </style>
      <?php
         echo '<script>document.getElementById("sort-by").value="'.$_GET["sort_by"].'";</script>';
         echo '<script>document.getElementById("catego").value="'.$_GET["catego"].'";</script>';
      ?>
   </div>
   <div class="box-container">

      <?php
         $mysqli_to_use = $mega_product_info1;
         if(strcmp($_GET["sort_by"],"discounthi")==0){
            $mysqli_to_use = $mega_product_info2;
         }

         $len_fetched = fetch_to_arr($mysqli_to_use,$mega_record_array,9999);
         $curr_start = 0;
         $curr_end = $len_fetched;
         $jump = 1;
         $catego_filter = $_GET["catego"];
         if(strcmp($_GET["sort_by"],"pricehi")==0){
            $curr_start = $len_fetched-1;
            $curr_end = -1;
            $jump = -1;
         }
         if($len_fetched > 0){
         //    while($fetch_products = mysqli_fetch_assoc($select_products)){
         for($i = $curr_start; $i != $curr_end; $i = $i+$jump) {
               // echo $mega_record_array[$i]["catego_name"]." Ơ vãi lol ".$catego_filter;
            if(strcmp($catego_filter,"all_catego")!=0 && strcmp($mega_record_array[$i]["catego_name"],$catego_filter)!=0)
               { continue; }
            $fetch_products = $mega_record_array[$i];
      ?>
     <form action="" method="POST" class="box">
      <!-- <img class="image" src="uploaded_img/<?php //echo $fetch_products['thumbnail']; ?>" alt=""> -->
      <img id="detailid" class="image" src="uploaded_img/<?php echo $fetch_products['thumbnail']; ?>" alt="product image" onclick="location.href='details.php?product_id=<?php echo $fetch_products['id']; ?>';">
      <p class="name" style="display: inline-block;
    white-space: nowrap;
    overflow-x: hidden !important;
    text-overflow: ellipsis; line-height:1rem; height:3rem;width:20rem;"><?php echo $fetch_products['book_name']; ?></p>
      <div class="price"><?php 
         $price=$fetch_products['price'];
         $discount_price=$fetch_products['discount_price'];
         if($discount_price) {
            echo "<s style='text-decoration: line-through'>$".$price."</s>";
            echo " | $".$discount_price;
         }
         else {
            echo "$".$price;
         }
          ?></div>
         <?php
            if($fetch_products['quantity'] < 10){echo '<div id="fewleft">few in stock!</div>';}
            else{echo '<div id="fewleft">In stock: '.$fetch_products['quantity'].'</div>';}
         ?>
      <style>#fewleft{width:16rem;margin:auto;padding:0.2rem;border-radius:0.5rem;font-size:2rem;color:white;background-color:olive;}</style>
      <input type="number" min="1" name="product_quantity" value="1" class="qty">
      <input type="hidden" name="product_name" value="<?php echo $fetch_products['book_name']; ?>">
      <input type="hidden" name="product_price" value="<?php 
         $price=$fetch_products['price'];
         $discount_price=$fetch_products['discount_price'];
         if($discount_price) {
            echo $discount_price;
         }
         else {
            echo $price;
         }
          ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_products['thumbnail']; ?>">
      <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>" >
      <input type="hidden" name="product_stock" value="<?php echo $fetch_products['quantity']; ?>" >
      <input type="submit" value="add to cart" name="add_to_cart" class="btn">
      <!-- <input type="button" value="Details" name="" class="btn" onclick="location.href='details.php?product_id=<?php echo $fetch_products['Product_ID']; ?>';"> -->
      
     </form>
      <?php
         }
      }else{
         echo '<p class="empty">No Products Found!!</p>';
      }
      ?>
   </div>

</section>




<?php
include 'footer.php';
?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>