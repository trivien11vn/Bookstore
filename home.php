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
   $product_id=$_POST['product_id'];

   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_cart_numbers) > 0){
      $message[] = 'already added to cart!';
   }else{
      mysqli_query($conn, "INSERT INTO `cart`(user_id,Product_ID, name, price, quantity, image) VALUES('$user_id','$product_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
      $message[] = 'product added to cart!';
   }

}


if(isset($_REQUEST['get'])){
   mysqli_query($conn, "INSERT INTO `discount_code` (Discount, Expiration_date, Name, ACC_ID) VALUES ('0.2', '2022-12-30', 'Giam 20% cho ban moi', '$user_id')") or die('query failed');
   $message[] = 'vourcher added to cart!';
}
if(isset($user_id)){
   $check = mysqli_query($conn, "SELECT * FROM `discount_code` WHERE name = 'Giam 20% cho ban moi' AND ACC_ID = '$user_id'") or die('query failed');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>HOME</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="home">

   <div class="content">
      <h3>Hand Picked Book to your door.</h3>
      <p>Our destiny is bringing the greatest experience about reading to you. Enjoy with us now</p>
      <a href="about.php" class="white-btn">discover more</a></br>
      <?php if(mysqli_num_rows($check)==0){ ?>
      <a style="margin:auto;margin-top:10px;width:320px;display:flex; justify-content:center; align-items: center;"href="home.php?get=<?php echo $user_id?>" class="infoobtn" id="new_customer"><span>Mã giảm giá 20% cho bạn mới.</span> <img style="width:10%;"src="uploaded_img/voucher_remove.png" alt=""></a>
      <?php } ?>
   </div>

</section>

<section class="products">

   <h1 class="title">latest products</h1>

   <div class="box-container">

      <?php  
         $select_products = mysqli_query($conn, "SELECT * FROM `product` where deleted = 0 LIMIT 6") or die('query failed');
         $count=0;
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
     <form action="" method="post" class="box">
      <img class="image" src="uploaded_img/<?php echo $fetch_products['Thumbnail']; ?>" alt="" onclick="location.href='details.php?product_id=<?php echo $fetch_products['Product_ID']; ?>';">
      <div class="name"><?php echo $fetch_products['Name']; ?></div>
      <div class="price"><?php 
         $price=$fetch_products['Price'];
         $discount_price=$fetch_products['Discount_price'];
         if($discount_price) {
            echo "<s style='text-decoration: line-through'>$".$price."</s>";
            echo " | $".$discount_price;
         }
         else {
            echo "$".$price;
         }
          ?></div>
      <input type="number" min="1" name="product_quantity" value="1" class="qty">
      <input type="hidden" name="product_name" value="<?php echo $fetch_products['Name']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_products['Price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_products['Thumbnail']; ?>">
      <input type="hidden" name="product_id" value="<?php echo $fetch_products['Product_ID']; ?>" >
      <input type="submit" value="add to cart" name="add_to_cart" class="btn">
     </form>
      <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
      ?>
   </div>

   <div class="load-more" style="margin-top: 2rem; text-align:center">
      <a href="books.php" class="option-btn">load more</a>
   </div>

</section>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="images/about2.jpg" alt="">
      </div>

      <div class="content">
         <h3>about us</h3>
         <p>Nhom Bai Tap Lon mon Lap Trinh Web - lop L04 - Giang vien: Nguyen Huu Hieu - hoc ki 2022_1
            * MSSV: 2014887 - 2015043 - 2012667 - 2012609
            * Dai Hoc Bach Khoa - DHQG TPHCM</p>
         <a href="about.php" class="btn">read more</a>
      </div>

   </div>

</section>



<?php include 'footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>