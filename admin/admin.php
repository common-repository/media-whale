<style type="text/css">
input#disconnect_site {
    background: #f44336;
    color: #fff;
    border: 0;
    width: 100%;
    max-width: 443px;
    margin: 0 auto;
    display: block;
    padding: 13px;
}
</style>
<script type="text/javascript">
const popupCenter = ({url, title, w, h}) => {
    // Fixes dual-screen position                             Most browsers      Firefox
    const dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
    const dualScreenTop = window.screenTop !==  undefined   ? window.screenTop  : window.screenY;

    const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    const systemZoom = width / window.screen.availWidth;
    const left = (width - w) / 2 / systemZoom + dualScreenLeft
    const top = (height - h) / 2 / systemZoom + dualScreenTop
    const newWindow = window.open(url, title, 
      `
      scrollbars=yes,
      width=${w / systemZoom}, 
      height=${h / systemZoom}, 
      top=${top}, 
      left=${left}
      `
    )

    if (window.focus) newWindow.focus();
}
</script>
<div style="width: 100%;">
	<div class="Wellcome_heading">
		<h1><?php _e('Welcome to Media Whale Admin page','mediawhale'); ?></h1>
		<p><?php _e('This page will help you to connect your WordPress site with Media Whale in few steps.','mediawhale'); ?></p>
	</div>
	<div class="connect_section">
		<div class="">
			<img src="<?php echo esc_url(plugin_dir_url( __FILE__ )); ?>../images/layer.png">
		</div>
		<!-- <div class="image_right">
			<img src="<?php //echo get_template_directory_uri(); ?>/images/logo02.png">
		</div> -->
	</div>
	<br>
	<div class="border">
		
	</div>

	<div>
		<?php if(empty(get_option('mediawhale_connection'))) { ?>
			<div class="Wellcome_heading">
				<h1><?php _e("Let's Get Started!","mediawhale"); ?></h1>
				<p><?php _e("To connect Media Whale with WordPress you need to connect your website with Media Whale.
				<br>You need to click on connect button below to authorize the connection.","mediawhale"); ?>
				</p>
			</div>
			<div>
				<div class="connect_box">
					<h2><?php _e("Welcome!","mediawhale"); ?></h2>
					<!-- <a id="btn" class="connect_button">Connect to Mediawhale</a> -->
					<input class="connect_button" type="button" value="Connect to Media Whale" onclick="popupCenter({url: 'https://app.mediawhale.com/login?mode=connect&url=<?php echo esc_url(site_url()); ?>&ver=<?php echo rand(); ?>', title: 'Connect Media Whale', w: 400, h: 500});">
				</div>
				<div class="having_trouble">
					<p><?php _e("Having trouble connecting? Head over to ","mediawhale"); ?><span><a>https://mediawhale.com .</a></span></p>
				</div>
			</div>
		<?php } else {  
			if(isset($_REQUEST['disconnect_site'])){
				update_option('mediawhale_connection','');
			}
			?>
			<div class="site-connected">
				<div class="site-connect-inr">
					<?php _e("Site is connected to our servers!","mediawhale"); ?>
				</div>
			</div>
			<form action="" method="get">
				<input type="hidden" name="page" value="mediawhale" />
				<input type="submit" name="disconnect_site" value="Disconnect" id="disconnect_site" />
			</form>

				<div class="article-usage">
					<span class="article-usage-title">Article Usage:</span><?php 
					$mediawhale_connection = get_option('mediawhale_connection');
					$mediawhale_connection = explode('_MDWHALE_',$mediawhale_connection);
					?>
					<?php mediawhale_article_usage($mediawhale_connection[1]); ?>
				</div>

				<div class="connected-account">
					
				<?php 
					$mediawhale_connection = get_option('mediawhale_connection');
					$mediawhale_connection = explode('_MDWHALE_',$mediawhale_connection);
				?>
				<?php mediawhale_connected_account($mediawhale_connection[1]); ?>
				</div>					

				</div>

		<?php } ?>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
	<?php if(isset($_REQUEST['disconnect_site'])){ ?>
	    $.ajax({
	        url: 'https://app.mediawhale.com/wp-admin/admin-ajax.php',
	        data: {
	            'action': 'remove_main_site_connection',
	            'site_url' : '<?php echo site_url(); ?>',
	        },
	        success:function(data) {
	        	window.location = "<?php get_admin_url() ?>?page=mediawhale";            
	        },
	        error: function(errorThrown){
	            console.log(errorThrown);
	        }
	    }); 		
	<?php } ?>
	$(".connect_button").click(function(){
		$(this).val("Connecting to our servers...");
		var intervalcheck = setInterval(function(){
		    $.ajax({
		        url: 'https://app.mediawhale.com/wp-admin/admin-ajax.php',
		        data: {
		            'action': 'check_site_connection_interval',
		            'site_url' : '<?php echo esc_url(site_url()); ?>',
		        },
		        success:function(data) {

		        	if(data){
		        		clearInterval(intervalcheck);
		        		$(".connect_button").val("Few more seconds please...");
					    $.ajax({
					        url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
					        type: 'post',
					        data: {
					            'action': 'mediawhale_update_site_status_on_interval',
					            'connection' : data,
					            'nonce' : '<?php echo wp_create_nonce('mediawhalesiteconnection'); ?>'
					        },
					        success:function(data) {
					            window.location.reload();

					        },
					        error: function(errorThrown){
					            console.log(errorThrown);
					        }
					    });  
				    }

		        },
		        error: function(errorThrown){
		            console.log(errorThrown);
		        }
		    }); 
		},2000);
	});
});
</script>