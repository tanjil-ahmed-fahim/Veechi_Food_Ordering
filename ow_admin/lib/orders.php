<?php
	if(!isset($_SESSION['admin_email']) || !isset($_SESSION['admin_password']) || !isset($_SESSION['admin_id']) || $_SESSION['admin_password']=='' || $_SESSION['admin_email']=='' || $_SESSION['admin_id']=='')
	{
		header("Location: index.php");
	}
?>
	
	<div class="w3-container" style="margin-top:80px" id="orders">
		<h1 class="w3-jumbo w3-new-text-color" style="font-family: 'Comic Sans MS', cursive, sans-serif;"><b>Orders</b></h1>
		<hr style="width:50px;border:5px solid black;" class="w3-round">
		<p> This option is used for <font color="red">update order status</font> of <?php echo $website_title; ?>.</p>
		
		
		<!-- Order history will show here -->
		<div class="w3-container" style="padding:0px;margin:0px 0px 0px 0px;width:100%;max-width:700px;">
			<div class="w3-right w3-text-right" style="width:100%;max-width:724px;">
					
				
				
				
				
				
				<button class="w3-button w3-brown w3-round w3-small w3-right w3-hide-small" style="padding:3px 5px;margin-left:5px;" onclick="view_food_order(0)"><i class="fa fa-search"></i> Search</button>
				<button class="w3-button w3-brown w3-round w3-small w3-right w3-hide-large w3-hide-medium" style="padding:3px 5px;margin-left:5px;" onclick="view_food_order(0)"><i class="fa fa-search w3-bold"></i></button>
				
				<div class="w3-right" style="width:80%;max-width:180px;margin-left:5px;">
					<input type="text" id="food_order_search_value" oninput="get_food_order_suggestion()"  onfocus="get_food_order_suggestion()" class=" w3-round w3-small " placeholder=" Search by Customer" style="width:100%;">
					<ul id="food_order_suggestion" class="w3-container w3-white w3-round w3-border-black w3-border-right w3-border-left w3-border-bottom" style="display:none;margin:2px 0px 0px 0px;padding:0px;position:absolute;width:100%;max-width:252px;list-style-type:none;height:auto;max-height:150px;overflow:auto;">
						
					</ul>
				</div>
				
				
				<a onclick="document.getElementById('all_order').style.display='none';document.getElementById('in_queue').style.display='none';document.getElementById('processing').style.display='none';document.getElementById('delivered').style.display='none';document.getElementById('cancelled').style.display='block';" class="w3-right w3-round w3-red w3-button  w3-small  w3-padding-small" style="margin-right:5px;cursor:pointer;margin-bottom:10px;">Cancelled</a>	
				
				<a onclick="document.getElementById('all_order').style.display='none';document.getElementById('in_queue').style.display='none';document.getElementById('processing').style.display='none';document.getElementById('delivered').style.display='block';document.getElementById('cancelled').style.display='none';" class="w3-right w3-round  w3-green w3-button  w3-small w3-padding-small" style="margin-right:5px;cursor:pointer;margin-bottom:10px;">Delivered</a>
				
				<a onclick="document.getElementById('all_order').style.display='none';document.getElementById('in_queue').style.display='none';document.getElementById('processing').style.display='block';document.getElementById('delivered').style.display='none';document.getElementById('cancelled').style.display='none';" style="margin-right:5px;cursor:pointer;margin-bottom:10px;" class="w3-right w3-round w3-teal w3-button  w3-small w3-padding-small">Processing</a>
				
				<a onclick="document.getElementById('all_order').style.display='none';document.getElementById('in_queue').style.display='block';document.getElementById('processing').style.display='none';document.getElementById('delivered').style.display='none';document.getElementById('cancelled').style.display='none';" class="w3-right w3-round w3-blue w3-button w3-padding-small w3-small" style="margin-right:5px;cursor:pointer;margin-bottom:10px;">In Queue</a>
				
				
				
			</div>
		</div>
		
		<div id="all_order" class="w3-container w3-light-gray w3-leftbar w3-rightbar w3-bottombar w3-topbar" style="height:400px;overflow:auto;width:100%;max-width:700px;padding: 16px 6px 0px 6px;">
		
		<?php 
			try
			{

				$stmt = $conn->prepare("select * from order_info order by order_id desc ");
				$stmt->execute();
				$list = $stmt->fetchAll();
				$sl=0;
				foreach($list as $row)
				{
					$sl++;
					$coupon_code=$row['coupon_code'];
					$order_id=$row['order_id'];
					$customer_id=$row['customer_id'];
					$d_per=0;
					//Getting coupon code percentage 
					$stmt2 = $conn->prepare("select * from offer_coupon where offer_coupon_code='$coupon_code' order by offer_id asc ");
					$stmt2->execute();
					$list2 = $stmt2->fetchAll();
					foreach($list2 as $row2)
						$d_per=$row2['offer_in_percentage'];
					
					//Getting Sum of cart product
					$stmt3 = $conn->prepare("select sum(price*quantity) from cart_info where order_id='$order_id' order by cart_id asc");
					$stmt3->execute();
					$list3 = $stmt3->fetchAll();
					
					$total=$list3[0]['sum(price*quantity)'];
					
					$total=($total-(($total/100.0)*$d_per));
					
					
					$stmt4 = $conn->prepare("select * from customer where customer_id='$customer_id' ");
					$stmt4->execute();
					$list4 = $stmt4->fetchAll();
		?>
					<!-- A single order -->
					<div class="w3-medium w3-topbar w3-bottombar w3-container w3-border w3-padding-small w3-white w3-round w3-border-black" style="margin: 0px 0px 12px 0px;">
						<div class="w3-row">
							<div class="w3-bold w3-col" style="width:25%;">
								<p class="" style="margin:0px 0px 0px 4px;padding:0px;">Order #<?php echo $sl; ?></p>
							</div>
							<div class="w3-bold w3-col w3-left-align" style="width:35%;">
								<p class="" style="margin:0px 0px 0px 4px;padding:0px;">Total: &pound;<?php echo  number_format($total, 2, '.', ''); ?></p>
							</div>
							<div class="w3-bold w3-col w3-left-align w3-tiny" style="width:25%;">
								<?php
									if($row['status']=="Delivered")
									{
								?>
										<p class="w3-text-green" style="margin:2px 0px 0px 4px;padding:0px;">Delivered</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="In Queue")
									{
								?>
										<p class="w3-text-blue" style="margin:2px 0px 0px 4px;padding:0px;">In Queue</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="Processing")
									{
								?>
										<p class="w3-text-teal" style="margin:2px 0px 0px 4px;padding:0px;">Processing</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="Cancelled")
									{
								?>
										<p class="w3-text-red" style="margin:2px 0px 0px 4px;padding:0px;">Cancelled</p>
								<?php
									}
								?>
							</div>
							<div class="w3-col " style="width:15%;">
								<a id="all_btn_<?php echo $row['order_id']; ?>" onclick="document.getElementById('all_btn_<?php echo $row['order_id']; ?>').style.display='none';document.getElementById('all_btn_hide_<?php echo $row['order_id']; ?>').style.display='block';document.getElementById('all_details_<?php echo $row['order_id']; ?>').style.display='block';" class="w3-button w3-green w3-round w3-tiny" style="padding:2px 4px;width:40px;margin-right:8px;">Details</a>
								<a id="all_btn_hide_<?php echo $row['order_id']; ?>" onclick="document.getElementById('all_btn_<?php echo $row['order_id']; ?>').style.display='block';document.getElementById('all_btn_hide_<?php echo $row['order_id']; ?>').style.display='none';document.getElementById('all_details_<?php echo $row['order_id']; ?>').style.display='none';" class="w3-button w3-red w3-round w3-tiny" style="padding:2px 4px;display:none;width:40px;margin-right:8px;">Hide</a>
							</div>
						</div>
						<!-- Order Details -->
						<div id="all_details_<?php echo $row['order_id']; ?>" class="w3-pale-red w3-round w3-container w3-margin-top w3-border w3-padding-small" style="display:none;">
							<!-- Order date & time -->
							<div class="w3-row">
								<div class="w3-col w3-left-align w3-tiny" style="width:50%;">
									Time: <?php echo $row['time']; ?>
								</div>
								<div class="w3-col w3-right-align w3-tiny" style="width:50%;">
									Date: <?php echo $row['date']; ?>
								</div>
							</div>
							<!-- Personal Info -->
							<div class="w3-row w3-white w3-topbar w3-bottombar" style="margin-bottom:4px;padding:0px;">
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;margin-top:4px;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Name: <font class="w3-text-black w3-bold"><?php echo $list4[0]['first_name'].' '.$list4[0]['last_name']; ?></font></p>
								</div>
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Email: <font class="w3-text-blue w3-bold"><?php echo $list4[0]['email']; ?></font></p>
								</div>
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;margin-bottom:4px;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Mobile: <font class="w3-text-black w3-bold"><?php echo $list4[0]['mobile']; ?></font></p>
								</div>
							</div>
							
							
							<div class="w3-container w3-border w3-white w3-topbar w3-bottombar" style="padding:4px 4px 4px 4px;margin:5px 0px;padding-top:4px;">
								<?php
									$stmt4 = $conn->prepare("select * from cart_info where order_id='$order_id' order by cart_id asc ");
									$stmt4->execute();
									$list4 = $stmt4->fetchAll();
									foreach($list4 as $row4)
									{
										$stmt5 = $conn->prepare("select * from food where food_id='$row4[food_id]' order by food_id asc ");
										$stmt5->execute();
										$list5 = $stmt5->fetchAll();
								?>
											<!-- A single item in order -->
											<div class="w3-row w3-border w3-light-gray" style="margin-bottom:4px;padding:3px;">
												<div class="w3-col w3-left-align " style="width:20%;margin-top:4px;">
													<p style="padding:0px;margin:0px 0px 0px 10px;"><?php echo $row4['quantity']; ?> x</p>
												</div>
												<div class="w3-col w3-left-align " style="width:55%;">
													<p style="padding:0px;margin:0px 0px 0px 5px;"><?php echo $list5[0]['food_name']; ?></p>
												</div>
												<div class="w3-col w3-right-align " style="width:25%;">
													<p style="padding:0px;margin:0px 10px 0px 0px;"><?php echo $row4['price']; ?></p>
												</div>
											</div>
								<?php
									}
								?>
								<!-- Order information related to this order -->
								<div class="w3-row w3-white w3-topbar w3-bold" style="margin-bottom:4px;padding:0px;">
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:49%;margin-top:4px;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Subtotal: <?php echo number_format($list3[0]['sum(price*quantity)'], 2, '.', ''); ?></p>
									</div>
									<div class="w3-col" style="width:2%;margin-top:4px;">
									&nbsp
									</div>
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:49%;margin-top:4px;">
										<p style="padding:0px;margin:0px 0px 0px 10px;"> Discount: <?php echo number_format((($total/100.0)*$d_per), 2, '.', ''); ?></p>
									</div>
								</div>
								<?php 
									if($d_per!=0)
									{
								?>
									<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
										<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:100%;">
											<p style="padding:0px;margin:0px 0px 0px 10px;">Coupon Code: <font class="w3-text-teal"><?php echo $row['coupon_code'].' ('.$d_per.'%) '; ?></font><font class="w3-text-red w3-tiny">[shop over &pound;<?php echo $list2[0]['offer_conditional_amount']; ?>]</font></p>
										</div>
									</div>
								<?php
									}
								?>
								<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:100%;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Paid By: <font class="w3-text-blue"><?php echo $row['paid_through']; ?></font></p>
									</div>
								</div>
								<?php 
									if($row['advice']!="")
									{
								?>
										<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
											<div class="w3-col w3-light-gray w3-border w3-left-align w3-tiny" style="width:100%;">
												<p style="padding:0px;margin:0px 0px 0px 10px;">Suggestion: <font class=""><?php echo $row['advice']; ?></font></p>
											</div>
										</div>
								<?php
									}
								?>
								<div class="w3-row w3-white" style="padding:0px;">
									<div class="w3-col w3-light-gray w3-border w3-left-align w3-small" style="width:100%;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Delivery Address: <font class="">
										<?php 
											if($row['address']=="")
											{
												$stmt6 = $conn->prepare("select * from customer where customer_id='$customer_id' order by customer_id asc ");
												$stmt6->execute();
												$list6 = $stmt6->fetchAll();
												echo $list6[0]['address'];
											}
											else
												echo $row['address'];
										?>
										</font></p>
									</div>
								</div>
								
							</div>
						</div>
					</div>
		<?php
				}
			}
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
			if($sl==0)
			{
		?>
				<p class="w3-medium w3-center w3-bold w3-text-red" style="margin-top:150px;">Oops!!! No Order's Available</p>
		<?php
			}
		?>
		
		
		</div>
	
	
	
		<div id="in_queue" class="w3-container w3-light-gray w3-leftbar w3-rightbar w3-bottombar w3-topbar" style="height:400px;overflow:auto;width:100%;max-width:700px;padding: 16px 6px 0px 6px;display:none;">
		
		<?php 
			try
			{

				$stmt = $conn->prepare("select * from order_info where status='In Queue' order by order_id desc ");
				$stmt->execute();
				$list = $stmt->fetchAll();
				$sl=0;
				foreach($list as $row)
				{
					$sl++;
					$coupon_code=$row['coupon_code'];
					$order_id=$row['order_id'];
					$customer_id=$row['customer_id'];
					$d_per=0;
					//Getting coupon code percentage 
					$stmt2 = $conn->prepare("select * from offer_coupon where offer_coupon_code='$coupon_code' order by offer_id asc ");
					$stmt2->execute();
					$list2 = $stmt2->fetchAll();
					foreach($list2 as $row2)
						$d_per=$row2['offer_in_percentage'];
					
					//Getting Sum of cart product
					$stmt3 = $conn->prepare("select sum(price*quantity) from cart_info where order_id='$order_id' order by cart_id asc");
					$stmt3->execute();
					$list3 = $stmt3->fetchAll();
					
					$total=$list3[0]['sum(price*quantity)'];
					
					$total=($total-(($total/100.0)*$d_per));
					
					
					$stmt4 = $conn->prepare("select * from customer where customer_id='$customer_id' ");
					$stmt4->execute();
					$list4 = $stmt4->fetchAll();
		?>
					<!-- A single order -->
					<div class="w3-medium w3-topbar w3-bottombar w3-container w3-border w3-padding-small w3-white w3-round w3-border-black" style="margin: 0px 0px 12px 0px;">
						<div class="w3-row">
							<div class="w3-bold w3-col" style="width:25%;">
								<p class="" style="margin:0px 0px 0px 4px;padding:0px;">Order #<?php echo $sl; ?></p>
							</div>
							<div class="w3-bold w3-col w3-left-align" style="width:35%;">
								<p class="" style="margin:0px 0px 0px 4px;padding:0px;">Total: &pound;<?php echo  number_format($total, 2, '.', ''); ?></p>
							</div>
							<div class="w3-bold w3-col w3-left-align w3-tiny" style="width:25%;">
								<?php
									if($row['status']=="Delivered")
									{
								?>
										<p class="w3-text-green" style="margin:2px 0px 0px 4px;padding:0px;">Delivered</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="In Queue")
									{
								?>
										<p class="w3-text-blue" style="margin:2px 0px 0px 4px;padding:0px;">In Queue</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="Processing")
									{
								?>
										<p class="w3-text-teal" style="margin:2px 0px 0px 4px;padding:0px;">Processing</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="Cancelled")
									{
								?>
										<p class="w3-text-red" style="margin:2px 0px 0px 4px;padding:0px;">Cancelled</p>
								<?php
									}
								?>
							</div>
							<div class="w3-col " style="width:15%;">
								<a id="in_queue_btn_<?php echo $row['order_id']; ?>" onclick="document.getElementById('in_queue_btn_<?php echo $row['order_id']; ?>').style.display='none';document.getElementById('in_queue_btn_hide_<?php echo $row['order_id']; ?>').style.display='block';document.getElementById('in_queue_details_<?php echo $row['order_id']; ?>').style.display='block';" class="w3-button w3-green w3-round w3-tiny" style="padding:2px 4px;width:40px;margin-right:8px;">Details</a>
								<a id="in_queue_btn_hide_<?php echo $row['order_id']; ?>" onclick="document.getElementById('in_queue_btn_<?php echo $row['order_id']; ?>').style.display='block';document.getElementById('in_queue_btn_hide_<?php echo $row['order_id']; ?>').style.display='none';document.getElementById('in_queue_details_<?php echo $row['order_id']; ?>').style.display='none';" class="w3-button w3-red w3-round w3-tiny" style="padding:2px 4px;display:none;width:40px;margin-right:8px;">Hide</a>
							</div>
						</div>
						<!-- Order Details -->
						<div id="in_queue_details_<?php echo $row['order_id']; ?>" class="w3-pale-red w3-round w3-container w3-margin-top w3-border w3-padding-small" style="display:none;">
							<!-- Order date & time -->
							<div class="w3-row">
								<div class="w3-col w3-left-align w3-tiny" style="width:50%;">
									Time: <?php echo $row['time']; ?>
								</div>
								<div class="w3-col w3-right-align w3-tiny" style="width:50%;">
									Date: <?php echo $row['date']; ?>
								</div>
							</div>
							<!-- Personal Info -->
							<div class="w3-row w3-white w3-topbar w3-bottombar" style="margin-bottom:4px;padding:0px;">
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;margin-top:4px;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Name: <font class="w3-text-black w3-bold"><?php echo $list4[0]['first_name'].' '.$list4[0]['last_name']; ?></font></p>
								</div>
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Email: <font class="w3-text-blue w3-bold"><?php echo $list4[0]['email']; ?></font></p>
								</div>
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;margin-bottom:4px;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Mobile: <font class="w3-text-black w3-bold"><?php echo $list4[0]['mobile']; ?></font></p>
								</div>
							</div>
							
							
							<div class="w3-container w3-border w3-white w3-topbar w3-bottombar" style="padding:4px 4px 4px 4px;margin:5px 0px;padding-top:4px;">
								<?php
									$stmt4 = $conn->prepare("select * from cart_info where order_id='$order_id' order by cart_id asc ");
									$stmt4->execute();
									$list4 = $stmt4->fetchAll();
									foreach($list4 as $row4)
									{
										$stmt5 = $conn->prepare("select * from food where food_id='$row4[food_id]' order by food_id asc ");
										$stmt5->execute();
										$list5 = $stmt5->fetchAll();
								?>
											<!-- A single item in order -->
											<div class="w3-row w3-border w3-light-gray" style="margin-bottom:4px;padding:3px;">
												<div class="w3-col w3-left-align " style="width:20%;margin-top:4px;">
													<p style="padding:0px;margin:0px 0px 0px 10px;"><?php echo $row4['quantity']; ?> x</p>
												</div>
												<div class="w3-col w3-left-align " style="width:55%;">
													<p style="padding:0px;margin:0px 0px 0px 5px;"><?php echo $list5[0]['food_name']; ?></p>
												</div>
												<div class="w3-col w3-right-align " style="width:25%;">
													<p style="padding:0px;margin:0px 10px 0px 0px;"><?php echo $row4['price']; ?></p>
												</div>
											</div>
								<?php
									}
								?>
								<!-- Order information related to this order -->
								<div class="w3-row w3-white w3-topbar w3-bold" style="margin-bottom:4px;padding:0px;">
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:49%;margin-top:4px;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Subtotal: <?php echo number_format($list3[0]['sum(price*quantity)'], 2, '.', ''); ?></p>
									</div>
									<div class="w3-col" style="width:2%;margin-top:4px;">
									&nbsp
									</div>
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:49%;margin-top:4px;">
										<p style="padding:0px;margin:0px 0px 0px 10px;"> Discount: <?php echo number_format((($total/100.0)*$d_per), 2, '.', ''); ?></p>
									</div>
								</div>
								<?php 
									if($d_per!=0)
									{
								?>
									<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
										<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:100%;">
											<p style="padding:0px;margin:0px 0px 0px 10px;">Coupon Code: <font class="w3-text-teal"><?php echo $row['coupon_code'].' ('.$d_per.'%) '; ?></font><font class="w3-text-red w3-tiny">[shop over &pound;<?php echo $list2[0]['offer_conditional_amount']; ?>]</font></p>
										</div>
									</div>
								<?php
									}
								?>
								<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:100%;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Paid By: <font class="w3-text-blue"><?php echo $row['paid_through']; ?></font></p>
									</div>
								</div>
								<?php 
									if($row['advice']!="")
									{
								?>
										<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
											<div class="w3-col w3-light-gray w3-border w3-left-align w3-tiny" style="width:100%;">
												<p style="padding:0px;margin:0px 0px 0px 10px;">Suggestion: <font class=""><?php echo $row['advice']; ?></font></p>
											</div>
										</div>
								<?php
									}
								?>
								<div class="w3-row w3-white" style="padding:0px;">
									<div class="w3-col w3-light-gray w3-border w3-left-align w3-small" style="width:100%;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Delivery Address: <font class="">
										<?php 
											if($row['address']=="")
											{
												$stmt6 = $conn->prepare("select * from customer where customer_id='$customer_id' order by customer_id asc ");
												$stmt6->execute();
												$list6 = $stmt6->fetchAll();
												echo $list6[0]['address'];
											}
											else
												echo $row['address'];
										?>
										</font></p>
									</div>
								</div>
								
							</div>
						</div>
					</div>
		<?php
				}
			}
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
			if($sl==0)
			{
		?>
				<p class="w3-medium w3-center w3-bold w3-text-red" style="margin-top:150px;">Oops!!! No Order's Available</p>
		<?php
			}
		?>
		</div>
		
		<div id="processing" class="w3-container w3-light-gray w3-leftbar w3-rightbar w3-bottombar w3-topbar" style="height:400px;overflow:auto;width:100%;max-width:700px;padding: 16px 6px 0px 6px;display:none;">
		
		<?php 
			try
			{

				$stmt = $conn->prepare("select * from order_info where status='Processing' order by order_id desc ");
				$stmt->execute();
				$list = $stmt->fetchAll();
				$sl=0;
				foreach($list as $row)
				{
					$sl++;
					$coupon_code=$row['coupon_code'];
					$order_id=$row['order_id'];
					$customer_id=$row['customer_id'];
					$d_per=0;
					//Getting coupon code percentage 
					$stmt2 = $conn->prepare("select * from offer_coupon where offer_coupon_code='$coupon_code' order by offer_id asc ");
					$stmt2->execute();
					$list2 = $stmt2->fetchAll();
					foreach($list2 as $row2)
						$d_per=$row2['offer_in_percentage'];
					
					//Getting Sum of cart product
					$stmt3 = $conn->prepare("select sum(price*quantity) from cart_info where order_id='$order_id' order by cart_id asc");
					$stmt3->execute();
					$list3 = $stmt3->fetchAll();
					
					$total=$list3[0]['sum(price*quantity)'];
					
					$total=($total-(($total/100.0)*$d_per));
					
					
					$stmt4 = $conn->prepare("select * from customer where customer_id='$customer_id' ");
					$stmt4->execute();
					$list4 = $stmt4->fetchAll();
		?>
					<!-- A single order -->
					<div class="w3-medium w3-topbar w3-bottombar w3-container w3-border w3-padding-small w3-white w3-round w3-border-black" style="margin: 0px 0px 12px 0px;">
						<div class="w3-row">
							<div class="w3-bold w3-col" style="width:25%;">
								<p class="" style="margin:0px 0px 0px 4px;padding:0px;">Order #<?php echo $sl; ?></p>
							</div>
							<div class="w3-bold w3-col w3-left-align" style="width:35%;">
								<p class="" style="margin:0px 0px 0px 4px;padding:0px;">Total: &pound;<?php echo  number_format($total, 2, '.', ''); ?></p>
							</div>
							<div class="w3-bold w3-col w3-left-align w3-tiny" style="width:25%;">
								<?php
									if($row['status']=="Delivered")
									{
								?>
										<p class="w3-text-green" style="margin:2px 0px 0px 4px;padding:0px;">Delivered</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="In Queue")
									{
								?>
										<p class="w3-text-blue" style="margin:2px 0px 0px 4px;padding:0px;">In Queue</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="Processing")
									{
								?>
										<p class="w3-text-teal" style="margin:2px 0px 0px 4px;padding:0px;">Processing</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="Cancelled")
									{
								?>
										<p class="w3-text-red" style="margin:2px 0px 0px 4px;padding:0px;">Cancelled</p>
								<?php
									}
								?>
							</div>
							<div class="w3-col " style="width:15%;">
								<a id="processing_btn_<?php echo $row['order_id']; ?>" onclick="document.getElementById('processing_btn_<?php echo $row['order_id']; ?>').style.display='none';document.getElementById('processing_btn_hide_<?php echo $row['order_id']; ?>').style.display='block';document.getElementById('processing_details_<?php echo $row['order_id']; ?>').style.display='block';" class="w3-button w3-green w3-round w3-tiny" style="padding:2px 4px;width:40px;margin-right:8px;">Details</a>
								<a id="processing_btn_hide_<?php echo $row['order_id']; ?>" onclick="document.getElementById('processing_btn_<?php echo $row['order_id']; ?>').style.display='block';document.getElementById('processing_btn_hide_<?php echo $row['order_id']; ?>').style.display='none';document.getElementById('processing_details_<?php echo $row['order_id']; ?>').style.display='none';" class="w3-button w3-red w3-round w3-tiny" style="padding:2px 4px;display:none;width:40px;margin-right:8px;">Hide</a>
							</div>
						</div>
						<!-- Order Details -->
						<div id="processing_details_<?php echo $row['order_id']; ?>" class="w3-pale-red w3-round w3-container w3-margin-top w3-border w3-padding-small" style="display:none;">
							<!-- Order date & time -->
							<div class="w3-row">
								<div class="w3-col w3-left-align w3-tiny" style="width:50%;">
									Time: <?php echo $row['time']; ?>
								</div>
								<div class="w3-col w3-right-align w3-tiny" style="width:50%;">
									Date: <?php echo $row['date']; ?>
								</div>
							</div>
							<!-- Personal Info -->
							<div class="w3-row w3-white w3-topbar w3-bottombar" style="margin-bottom:4px;padding:0px;">
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;margin-top:4px;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Name: <font class="w3-text-black w3-bold"><?php echo $list4[0]['first_name'].' '.$list4[0]['last_name']; ?></font></p>
								</div>
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Email: <font class="w3-text-blue w3-bold"><?php echo $list4[0]['email']; ?></font></p>
								</div>
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;margin-bottom:4px;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Mobile: <font class="w3-text-black w3-bold"><?php echo $list4[0]['mobile']; ?></font></p>
								</div>
							</div>
							
							
							<div class="w3-container w3-border w3-white w3-topbar w3-bottombar" style="padding:4px 4px 4px 4px;margin:5px 0px;padding-top:4px;">
								<?php
									$stmt4 = $conn->prepare("select * from cart_info where order_id='$order_id' order by cart_id asc ");
									$stmt4->execute();
									$list4 = $stmt4->fetchAll();
									foreach($list4 as $row4)
									{
										$stmt5 = $conn->prepare("select * from food where food_id='$row4[food_id]' order by food_id asc ");
										$stmt5->execute();
										$list5 = $stmt5->fetchAll();
								?>
											<!-- A single item in order -->
											<div class="w3-row w3-border w3-light-gray" style="margin-bottom:4px;padding:3px;">
												<div class="w3-col w3-left-align " style="width:20%;margin-top:4px;">
													<p style="padding:0px;margin:0px 0px 0px 10px;"><?php echo $row4['quantity']; ?> x</p>
												</div>
												<div class="w3-col w3-left-align " style="width:55%;">
													<p style="padding:0px;margin:0px 0px 0px 5px;"><?php echo $list5[0]['food_name']; ?></p>
												</div>
												<div class="w3-col w3-right-align " style="width:25%;">
													<p style="padding:0px;margin:0px 10px 0px 0px;"><?php echo $row4['price']; ?></p>
												</div>
											</div>
								<?php
									}
								?>
								<!-- Order information related to this order -->
								<div class="w3-row w3-white w3-topbar w3-bold" style="margin-bottom:4px;padding:0px;">
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:49%;margin-top:4px;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Subtotal: <?php echo number_format($list3[0]['sum(price*quantity)'], 2, '.', ''); ?></p>
									</div>
									<div class="w3-col" style="width:2%;margin-top:4px;">
									&nbsp
									</div>
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:49%;margin-top:4px;">
										<p style="padding:0px;margin:0px 0px 0px 10px;"> Discount: <?php echo number_format((($total/100.0)*$d_per), 2, '.', ''); ?></p>
									</div>
								</div>
								<?php 
									if($d_per!=0)
									{
								?>
									<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
										<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:100%;">
											<p style="padding:0px;margin:0px 0px 0px 10px;">Coupon Code: <font class="w3-text-teal"><?php echo $row['coupon_code'].' ('.$d_per.'%) '; ?></font><font class="w3-text-red w3-tiny">[shop over &pound;<?php echo $list2[0]['offer_conditional_amount']; ?>]</font></p>
										</div>
									</div>
								<?php
									}
								?>
								<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:100%;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Paid By: <font class="w3-text-blue"><?php echo $row['paid_through']; ?></font></p>
									</div>
								</div>
								<?php 
									if($row['advice']!="")
									{
								?>
										<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
											<div class="w3-col w3-light-gray w3-border w3-left-align w3-tiny" style="width:100%;">
												<p style="padding:0px;margin:0px 0px 0px 10px;">Suggestion: <font class=""><?php echo $row['advice']; ?></font></p>
											</div>
										</div>
								<?php
									}
								?>
								<div class="w3-row w3-white" style="padding:0px;">
									<div class="w3-col w3-light-gray w3-border w3-left-align w3-small" style="width:100%;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Delivery Address: <font class="">
										<?php 
											if($row['address']=="")
											{
												$stmt6 = $conn->prepare("select * from customer where customer_id='$customer_id' order by customer_id asc ");
												$stmt6->execute();
												$list6 = $stmt6->fetchAll();
												echo $list6[0]['address'];
											}
											else
												echo $row['address'];
										?>
										</font></p>
									</div>
								</div>
								
							</div>
						</div>
					</div>
		<?php
				}
			}
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
			if($sl==0)
			{
		?>
				<p class="w3-medium w3-center w3-bold w3-text-red" style="margin-top:150px;">Oops!!! No Order's Available</p>
		<?php
			}
		?>
		
		</div>
	
	
	
		<div id="delivered" class="w3-container w3-light-gray w3-leftbar w3-rightbar w3-bottombar w3-topbar" style="height:400px;overflow:auto;width:100%;max-width:700px;padding: 16px 6px 0px 6px;display:none;">
		
		<?php 
			try
			{

				$stmt = $conn->prepare("select * from order_info where status='Delivered' order by order_id desc ");
				$stmt->execute();
				$list = $stmt->fetchAll();
				$sl=0;
				foreach($list as $row)
				{
					$sl++;
					$coupon_code=$row['coupon_code'];
					$order_id=$row['order_id'];
					$customer_id=$row['customer_id'];
					$d_per=0;
					//Getting coupon code percentage 
					$stmt2 = $conn->prepare("select * from offer_coupon where offer_coupon_code='$coupon_code' order by offer_id asc ");
					$stmt2->execute();
					$list2 = $stmt2->fetchAll();
					foreach($list2 as $row2)
						$d_per=$row2['offer_in_percentage'];
					
					//Getting Sum of cart product
					$stmt3 = $conn->prepare("select sum(price*quantity) from cart_info where order_id='$order_id' order by cart_id asc");
					$stmt3->execute();
					$list3 = $stmt3->fetchAll();
					
					$total=$list3[0]['sum(price*quantity)'];
					
					$total=($total-(($total/100.0)*$d_per));
					
					
					$stmt4 = $conn->prepare("select * from customer where customer_id='$customer_id' ");
					$stmt4->execute();
					$list4 = $stmt4->fetchAll();
		?>
					<!-- A single order -->
					<div class="w3-medium w3-topbar w3-bottombar w3-container w3-border w3-padding-small w3-white w3-round w3-border-black" style="margin: 0px 0px 12px 0px;">
						<div class="w3-row">
							<div class="w3-bold w3-col" style="width:25%;">
								<p class="" style="margin:0px 0px 0px 4px;padding:0px;">Order #<?php echo $sl; ?></p>
							</div>
							<div class="w3-bold w3-col w3-left-align" style="width:35%;">
								<p class="" style="margin:0px 0px 0px 4px;padding:0px;">Total: &pound;<?php echo  number_format($total, 2, '.', ''); ?></p>
							</div>
							<div class="w3-bold w3-col w3-left-align w3-tiny" style="width:25%;">
								<?php
									if($row['status']=="Delivered")
									{
								?>
										<p class="w3-text-green" style="margin:2px 0px 0px 4px;padding:0px;">Delivered</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="In Queue")
									{
								?>
										<p class="w3-text-blue" style="margin:2px 0px 0px 4px;padding:0px;">In Queue</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="Processing")
									{
								?>
										<p class="w3-text-teal" style="margin:2px 0px 0px 4px;padding:0px;">Processing</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="Cancelled")
									{
								?>
										<p class="w3-text-red" style="margin:2px 0px 0px 4px;padding:0px;">Cancelled</p>
								<?php
									}
								?>
							</div>
							<div class="w3-col " style="width:15%;">
								<a id="delivered_btn_<?php echo $row['order_id']; ?>" onclick="document.getElementById('delivered_btn_<?php echo $row['order_id']; ?>').style.display='none';document.getElementById('delivered_btn_hide_<?php echo $row['order_id']; ?>').style.display='block';document.getElementById('delivered_details_<?php echo $row['order_id']; ?>').style.display='block';" class="w3-button w3-green w3-round w3-tiny" style="padding:2px 4px;width:40px;margin-right:8px;">Details</a>
								<a id="delivered_btn_hide_<?php echo $row['order_id']; ?>" onclick="document.getElementById('delivered_btn_<?php echo $row['order_id']; ?>').style.display='block';document.getElementById('delivered_btn_hide_<?php echo $row['order_id']; ?>').style.display='none';document.getElementById('delivered_details_<?php echo $row['order_id']; ?>').style.display='none';" class="w3-button w3-red w3-round w3-tiny" style="padding:2px 4px;display:none;width:40px;margin-right:8px;">Hide</a>
							</div>
						</div>
						<!-- Order Details -->
						<div id="delivered_details_<?php echo $row['order_id']; ?>" class="w3-pale-red w3-round w3-container w3-margin-top w3-border w3-padding-small" style="display:none;">
							<!-- Order date & time -->
							<div class="w3-row">
								<div class="w3-col w3-left-align w3-tiny" style="width:50%;">
									Time: <?php echo $row['time']; ?>
								</div>
								<div class="w3-col w3-right-align w3-tiny" style="width:50%;">
									Date: <?php echo $row['date']; ?>
								</div>
							</div>
							<!-- Personal Info -->
							<div class="w3-row w3-white w3-topbar w3-bottombar" style="margin-bottom:4px;padding:0px;">
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;margin-top:4px;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Name: <font class="w3-text-black w3-bold"><?php echo $list4[0]['first_name'].' '.$list4[0]['last_name']; ?></font></p>
								</div>
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Email: <font class="w3-text-blue w3-bold"><?php echo $list4[0]['email']; ?></font></p>
								</div>
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;margin-bottom:4px;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Mobile: <font class="w3-text-black w3-bold"><?php echo $list4[0]['mobile']; ?></font></p>
								</div>
							</div>
							
							
							<div class="w3-container w3-border w3-white w3-topbar w3-bottombar" style="padding:4px 4px 4px 4px;margin:5px 0px;padding-top:4px;">
								<?php
									$stmt4 = $conn->prepare("select * from cart_info where order_id='$order_id' order by cart_id asc ");
									$stmt4->execute();
									$list4 = $stmt4->fetchAll();
									foreach($list4 as $row4)
									{
										$stmt5 = $conn->prepare("select * from food where food_id='$row4[food_id]' order by food_id asc ");
										$stmt5->execute();
										$list5 = $stmt5->fetchAll();
								?>
											<!-- A single item in order -->
											<div class="w3-row w3-border w3-light-gray" style="margin-bottom:4px;padding:3px;">
												<div class="w3-col w3-left-align " style="width:20%;margin-top:4px;">
													<p style="padding:0px;margin:0px 0px 0px 10px;"><?php echo $row4['quantity']; ?> x</p>
												</div>
												<div class="w3-col w3-left-align " style="width:55%;">
													<p style="padding:0px;margin:0px 0px 0px 5px;"><?php echo $list5[0]['food_name']; ?></p>
												</div>
												<div class="w3-col w3-right-align " style="width:25%;">
													<p style="padding:0px;margin:0px 10px 0px 0px;"><?php echo $row4['price']; ?></p>
												</div>
											</div>
								<?php
									}
								?>
								<!-- Order information related to this order -->
								<div class="w3-row w3-white w3-topbar w3-bold" style="margin-bottom:4px;padding:0px;">
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:49%;margin-top:4px;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Subtotal: <?php echo number_format($list3[0]['sum(price*quantity)'], 2, '.', ''); ?></p>
									</div>
									<div class="w3-col" style="width:2%;margin-top:4px;">
									&nbsp
									</div>
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:49%;margin-top:4px;">
										<p style="padding:0px;margin:0px 0px 0px 10px;"> Discount: <?php echo number_format((($total/100.0)*$d_per), 2, '.', ''); ?></p>
									</div>
								</div>
								<?php 
									if($d_per!=0)
									{
								?>
									<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
										<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:100%;">
											<p style="padding:0px;margin:0px 0px 0px 10px;">Coupon Code: <font class="w3-text-teal"><?php echo $row['coupon_code'].' ('.$d_per.'%) '; ?></font><font class="w3-text-red w3-tiny">[shop over &pound;<?php echo $list2[0]['offer_conditional_amount']; ?>]</font></p>
										</div>
									</div>
								<?php
									}
								?>
								<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:100%;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Paid By: <font class="w3-text-blue"><?php echo $row['paid_through']; ?></font></p>
									</div>
								</div>
								<?php 
									if($row['advice']!="")
									{
								?>
										<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
											<div class="w3-col w3-light-gray w3-border w3-left-align w3-tiny" style="width:100%;">
												<p style="padding:0px;margin:0px 0px 0px 10px;">Suggestion: <font class=""><?php echo $row['advice']; ?></font></p>
											</div>
										</div>
								<?php
									}
								?>
								<div class="w3-row w3-white" style="padding:0px;">
									<div class="w3-col w3-light-gray w3-border w3-left-align w3-small" style="width:100%;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Delivery Address: <font class="">
										<?php 
											if($row['address']=="")
											{
												$stmt6 = $conn->prepare("select * from customer where customer_id='$customer_id' order by customer_id asc ");
												$stmt6->execute();
												$list6 = $stmt6->fetchAll();
												echo $list6[0]['address'];
											}
											else
												echo $row['address'];
										?>
										</font></p>
									</div>
								</div>
								
							</div>
						</div>
					</div>
		<?php
				}
			}
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
			if($sl==0)
			{
		?>
				<p class="w3-medium w3-center w3-bold w3-text-red" style="margin-top:150px;">Oops!!! No Order's Available</p>
		<?php
			}
		?>
		</div>
	
		<div id="cancelled" class="w3-container w3-light-gray w3-leftbar w3-rightbar w3-bottombar w3-topbar" style="height:400px;overflow:auto;width:100%;max-width:700px;padding: 16px 6px 0px 6px;display:none;">
		
		<?php 
			try
			{

				$stmt = $conn->prepare("select * from order_info where status='Cancelled' order by order_id desc ");
				$stmt->execute();
				$list = $stmt->fetchAll();
				$sl=0;
				foreach($list as $row)
				{
					$sl++;
					$coupon_code=$row['coupon_code'];
					$order_id=$row['order_id'];
					$customer_id=$row['customer_id'];
					$d_per=0;
					//Getting coupon code percentage 
					$stmt2 = $conn->prepare("select * from offer_coupon where offer_coupon_code='$coupon_code' order by offer_id asc ");
					$stmt2->execute();
					$list2 = $stmt2->fetchAll();
					foreach($list2 as $row2)
						$d_per=$row2['offer_in_percentage'];
					
					//Getting Sum of cart product
					$stmt3 = $conn->prepare("select sum(price*quantity) from cart_info where order_id='$order_id' order by cart_id asc");
					$stmt3->execute();
					$list3 = $stmt3->fetchAll();
					
					$total=$list3[0]['sum(price*quantity)'];
					
					$total=($total-(($total/100.0)*$d_per));
					
					
					$stmt4 = $conn->prepare("select * from customer where customer_id='$customer_id' ");
					$stmt4->execute();
					$list4 = $stmt4->fetchAll();
		?>
					<!-- A single order -->
					<div class="w3-medium w3-topbar w3-bottombar w3-container w3-border w3-padding-small w3-white w3-round w3-border-black" style="margin: 0px 0px 12px 0px;">
						<div class="w3-row">
							<div class="w3-bold w3-col" style="width:25%;">
								<p class="" style="margin:0px 0px 0px 4px;padding:0px;">Order #<?php echo $sl; ?></p>
							</div>
							<div class="w3-bold w3-col w3-left-align" style="width:35%;">
								<p class="" style="margin:0px 0px 0px 4px;padding:0px;">Total: &pound;<?php echo  number_format($total, 2, '.', ''); ?></p>
							</div>
							<div class="w3-bold w3-col w3-left-align w3-tiny" style="width:25%;">
								<?php
									if($row['status']=="Delivered")
									{
								?>
										<p class="w3-text-green" style="margin:2px 0px 0px 4px;padding:0px;">Delivered</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="In Queue")
									{
								?>
										<p class="w3-text-blue" style="margin:2px 0px 0px 4px;padding:0px;">In Queue</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="Processing")
									{
								?>
										<p class="w3-text-teal" style="margin:2px 0px 0px 4px;padding:0px;">Processing</p>
								<?php
									}
								?>
								<?php
									if($row['status']=="Cancelled")
									{
								?>
										<p class="w3-text-red" style="margin:2px 0px 0px 4px;padding:0px;">Cancelled</p>
								<?php
									}
								?>
							</div>
							<div class="w3-col " style="width:15%;">
								<a id="cancelled_btn_<?php echo $row['order_id']; ?>" onclick="document.getElementById('cancelled_btn_<?php echo $row['order_id']; ?>').style.display='none';document.getElementById('cancelled_btn_hide_<?php echo $row['order_id']; ?>').style.display='block';document.getElementById('cancelled_details_<?php echo $row['order_id']; ?>').style.display='block';" class="w3-button w3-green w3-round w3-tiny" style="padding:2px 4px;width:40px;margin-right:8px;">Details</a>
								<a id="cancelled_btn_hide_<?php echo $row['order_id']; ?>" onclick="document.getElementById('cancelled_btn_<?php echo $row['order_id']; ?>').style.display='block';document.getElementById('cancelled_btn_hide_<?php echo $row['order_id']; ?>').style.display='none';document.getElementById('cancelled_details_<?php echo $row['order_id']; ?>').style.display='none';" class="w3-button w3-red w3-round w3-tiny" style="padding:2px 4px;display:none;width:40px;margin-right:8px;">Hide</a>
							</div>
						</div>
						<!-- Order Details -->
						<div id="cancelled_details_<?php echo $row['order_id']; ?>" class="w3-pale-red w3-round w3-container w3-margin-top w3-border w3-padding-small" style="display:none;">
							<!-- Order date & time -->
							<div class="w3-row">
								<div class="w3-col w3-left-align w3-tiny" style="width:50%;">
									Time: <?php echo $row['time']; ?>
								</div>
								<div class="w3-col w3-right-align w3-tiny" style="width:50%;">
									Date: <?php echo $row['date']; ?>
								</div>
							</div>
							<!-- Personal Info -->
							<div class="w3-row w3-white w3-topbar w3-bottombar" style="margin-bottom:4px;padding:0px;">
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;margin-top:4px;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Name: <font class="w3-text-black w3-bold"><?php echo $list4[0]['first_name'].' '.$list4[0]['last_name']; ?></font></p>
								</div>
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Email: <font class="w3-text-blue w3-bold"><?php echo $list4[0]['email']; ?></font></p>
								</div>
								<div class="w3-col w3-light-gray w3-border w3-left-align " style="width:100%;margin-bottom:4px;">
									<p style="padding:0px;margin:0px 0px 0px 10px;">Mobile: <font class="w3-text-black w3-bold"><?php echo $list4[0]['mobile']; ?></font></p>
								</div>
							</div>
							
							
							<div class="w3-container w3-border w3-white w3-topbar w3-bottombar" style="padding:4px 4px 4px 4px;margin:5px 0px;padding-top:4px;">
								<?php
									$stmt4 = $conn->prepare("select * from cart_info where order_id='$order_id' order by cart_id asc ");
									$stmt4->execute();
									$list4 = $stmt4->fetchAll();
									foreach($list4 as $row4)
									{
										$stmt5 = $conn->prepare("select * from food where food_id='$row4[food_id]' order by food_id asc ");
										$stmt5->execute();
										$list5 = $stmt5->fetchAll();
								?>
											<!-- A single item in order -->
											<div class="w3-row w3-border w3-light-gray" style="margin-bottom:4px;padding:3px;">
												<div class="w3-col w3-left-align " style="width:20%;margin-top:4px;">
													<p style="padding:0px;margin:0px 0px 0px 10px;"><?php echo $row4['quantity']; ?> x</p>
												</div>
												<div class="w3-col w3-left-align " style="width:55%;">
													<p style="padding:0px;margin:0px 0px 0px 5px;"><?php echo $list5[0]['food_name']; ?></p>
												</div>
												<div class="w3-col w3-right-align " style="width:25%;">
													<p style="padding:0px;margin:0px 10px 0px 0px;"><?php echo $row4['price']; ?></p>
												</div>
											</div>
								<?php
									}
								?>
								<!-- Order information related to this order -->
								<div class="w3-row w3-white w3-topbar w3-bold" style="margin-bottom:4px;padding:0px;">
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:49%;margin-top:4px;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Subtotal: <?php echo number_format($list3[0]['sum(price*quantity)'], 2, '.', ''); ?></p>
									</div>
									<div class="w3-col" style="width:2%;margin-top:4px;">
									&nbsp
									</div>
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:49%;margin-top:4px;">
										<p style="padding:0px;margin:0px 0px 0px 10px;"> Discount: <?php echo number_format((($total/100.0)*$d_per), 2, '.', ''); ?></p>
									</div>
								</div>
								<?php 
									if($d_per!=0)
									{
								?>
									<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
										<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:100%;">
											<p style="padding:0px;margin:0px 0px 0px 10px;">Coupon Code: <font class="w3-text-teal"><?php echo $row['coupon_code'].' ('.$d_per.'%) '; ?></font><font class="w3-text-red w3-tiny">[shop over &pound;<?php echo $list2[0]['offer_conditional_amount']; ?>]</font></p>
										</div>
									</div>
								<?php
									}
								?>
								<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
									<div class="w3-col w3-bold w3-light-gray w3-border w3-left-align " style="width:100%;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Paid By: <font class="w3-text-blue"><?php echo $row['paid_through']; ?></font></p>
									</div>
								</div>
								<?php 
									if($row['advice']!="")
									{
								?>
										<div class="w3-row w3-white" style="margin-bottom:4px;padding:0px;">
											<div class="w3-col w3-light-gray w3-border w3-left-align w3-tiny" style="width:100%;">
												<p style="padding:0px;margin:0px 0px 0px 10px;">Suggestion: <font class=""><?php echo $row['advice']; ?></font></p>
											</div>
										</div>
								<?php
									}
								?>
								<div class="w3-row w3-white" style="padding:0px;">
									<div class="w3-col w3-light-gray w3-border w3-left-align w3-small" style="width:100%;">
										<p style="padding:0px;margin:0px 0px 0px 10px;">Delivery Address: <font class="">
										<?php 
											if($row['address']=="")
											{
												$stmt6 = $conn->prepare("select * from customer where customer_id='$customer_id' order by customer_id asc ");
												$stmt6->execute();
												$list6 = $stmt6->fetchAll();
												echo $list6[0]['address'];
											}
											else
												echo $row['address'];
										?>
										</font></p>
									</div>
								</div>
								
							</div>
						</div>
					</div>
		<?php
				}
			}
			catch(PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
			if($sl==0)
			{
		?>
				<p class="w3-medium w3-center w3-bold w3-text-red" style="margin-top:150px;">Oops!!! No Order's Available</p>
		<?php
			}
		?>
		</div>
	</div>