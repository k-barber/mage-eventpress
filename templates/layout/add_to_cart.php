<?php
	/*
* @Author 		engr.sumonazma@gmail.com
* Copyright: 	mage-people.com
*/
	if (!defined('ABSPATH')) {
		die;
	} // Cannot access pages directly.
	$event_id = $event_id ?? 0;
	$backend_order = MP_Global_Function::data_sanitize($_POST['backend_order']);
	$link_wc_product = MP_Global_Function::get_post_info($event_id, 'link_wc_product');;
?>
	<div class="col_12 mpwem_form_submit_area mT_xs">
		<div class="justifyBetween _alignCenter">
			<h5 class="_mpBtn"><?php esc_html_e('Total Price : ', 'mage-eventpress'); ?><span class="mpwem_total _textTheme"><?php echo wc_price(0); ?></span></h5>
			<?php if ($backend_order>0) { ?>
				<button type="submit" class="_themeButton">
					<?php esc_html_e('Book Now ', 'mage-eventpress'); ?>
				</button>
			<?php } else { ?>
				<button type="submit" class="_themeButton" name="add-to-cart" value="<?php echo esc_attr($link_wc_product); ?>">
					<?php esc_html_e('Register Event ', 'mage-eventpress'); ?>
				</button>
			<?php } ?>
		</div>
	</div>
<?php