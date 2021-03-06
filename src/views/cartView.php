<?php include 'header.php'; ?>

<!-- START jQuery-->
<script>
    $(document).ready(function() {
        console.log("READY");
        // $('#show-text').hide();

        $('#pick-quantity').change(function() {
            console.log("clicked!");
            if ($(this).val() == -1) {
                $(this).replaceWith('<input type="textarea" class="form-control" />');
            }
        });
    });
</script>

    <div class="container-fluid">
        <div class="row cart-view">
            <div class="col-9">
                <div id="shopping-cart-label">Shopping Cart</div>
                <div class="row cart-table-header">
                    <div class="col-8">
                    </div>

                    <div class="col-3">
                        Price
                    </div>

                    <div class="col-1">
                        Quantity
                    </div>
                </div>

<?php
    require('mysqli_connect.php');

    session_start();

    $user_for_cart = $_SESSION['user_id'];

    $query = "SELECT cart_product_img_url, product_name, product_price_dollars, product_price_cents, product_is_prime FROM cart WHERE user_id = " .$_SESSION['user_id'];

    $run = mysqli_query($dbc, $query);

    $count = mysqli_num_rows($run);

    $numOfItems = 0;
    $totalAmt = 0;
    if($count > 0) {
        while ($row = mysqli_fetch_array($run, MYSQLI_ASSOC)) {
            // $totalDonationAmt += $row['product_price_dollars'] += ($row['product_price_cents']/100);
            // $dollars =
            $dollars = (int)$row['product_price_dollars'];
            $cents = (int)$row['product_price_cents']/100;

            $totalAmt += $dollars;
            $totalAmt += $cents;

            $numOfItems += 1;
            echo "
            <!-- RECYCLE STARTING POINT -->
            <div class='row cart-table'>
                <div class='col-8'> <!-- column for item details-->
                    <div class='row item-row'>
                        <div class='col-3 item-image'>
                            <img src='" .$row['cart_product_img_url']. "' />
                            <br />
                        </div>

                        <div class='col-9'>
                            <span class='item-desc'>" .$row['product_name']. "</span><br />

                            <!-- TODO: Add this if item is prime -->
                            <img src='images/prime.png' class='prime' />
                            <br />

                            <input type='checkbox' /><span id='checkbox-gift'>&nbsp;&nbsp;This is a gift</span>
                            <span class='hyperlink'> Learn more</span>
                            <br />

                            <!-- TODO: remove this from HTML and DELETE from database on click -->
                            <input type='submit' value='Delete' />
                        </div>
                    </div>
                </div>

                <div class='col-3'> <!-- Column for price -->
                    <div id='cart-price'>$" .$row['product_price_dollars']. "."  . $row['product_price_cents']. "</div>
                </div>

                <div class='col-1 quantity-form'> <!-- Column for quantity drop down -->
                    <form action=''>
                        <select id='pick-quantity'>
                            <option value='1'>1</option>
                            <option value='2'>2</option>
                            <option value='3'>3</option>
                            <option value='4'>4</option>
                            <option value='5'>5</option>
                            <option value='-1' id='quantity-more'>6+</option>
                        </select>
                    </form>
                </div>
            </div>
            <!-- RECYCLE ENDING POINT -->
            ";
        }
    }
    $donationAmount = round($totalAmt * .005, 2);

    // $d = mysqli_real_escape_string($dbc, $donationAmount);


    $query2 = "UPDATE users
    SET donations = donations + $donationAmount
    WHERE user_id = '$user_for_cart';";

    $run = mysqli_query($dbc, $query2);

    if ($run) {
        // echo "<p>Donation Updated Successfully!</p>";
    } else {
        echo "<p>Error. Couldn't add to cart!</p>";
        echo "<br />";
        //Print a debugging message
        echo "<p>".mysqli_error($dbc)."</p>";
        mysqli_close($dbc); //Close the db connection
        exit(); //Terminate the execution of the script
    }
    mysqli_close($dbc); //Close the db connection

?>


            </div>

            <div class="col-3">
                <div class="checkout-card">
                    <!-- TODO: Dynamically set price -->
                    <div id="subtotal-label">Subtotal (<?php
                            echo "$numOfItems";
                            if ($numOfItems == 1) {
                                echo " item";
                            } else {
                                echo " items";
                            }
                        ?>): <span id="subtotal-label-price"></span></div>
                    <input type="checkbox" /><span id="checkbox-gift">&nbsp;&nbsp;This order contains a gift</span>
                    <br /><br />

<script>
    // Script to dynamically set price in subtotal label
    var priceArr = document.querySelectorAll("#cart-price");

    var subtotal = 0;
    for (var i = 0; i < priceArr.length; i++) {
        var priceStr = priceArr[i].innerHTML
        var priceFloat = Number(priceStr.substring(1));
        subtotal += priceFloat;
    }
    subtotal = subtotal.toFixed(2); // 2 decimal places
    console.log(subtotal);

    document.getElementById("subtotal-label-price").innerHTML = "$" + subtotal;
</script>

                    <form action="transactionSuccessful.php" method="post">
                        <input type="submit" value="Proceed to checkout" />
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?>
