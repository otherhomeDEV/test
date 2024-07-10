<?php
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가

//add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$widget_url.'/widget.css" media="screen">', 0);

// 링크 열기
$wset['modal_js'] = ($wset['modal'] == "1") ? apms_script('modal') : '';

// 랜덤아이디
$widget_id = apms_id(); // Random ID

?>
<script src="<?php echo $widget_url; ?>/jquery.bxslider.min.js"></script>
<style>
.ticker-bannermanage {
	border:1px solid #ddd;background:#f8f8f8
}
</style>

<div style="padding:10px 0px 10px 20px !important;" class="ticker-bannermanage">
	<?php
	include($widget_path.'/widget.rows.php');
	?>
	<div class="clearfix"></div>
</div>

<?php if($setup_href) { ?>
	<div class="btn-wset text-center p10">
		<a href="<?php echo $setup_href;?>" class="win_memo">
			<span class="text-muted"><i class="fa fa-cog"></i> 위젯설정</span>
		</a>
	</div>
<?php } ?>

<script type="text/javascript">

jQuery(function($){
	$.fn.list_ticker = function(options){

		var defaults = {
			speed:5000,
			effect:'slide',
			run_once:false,
			random:false
		};

		var options = $.extend(defaults, options);

		return this.each(function(){

			var obj = $(this);
			var list = obj.children();
			var count = list.length - 1;

			list.not(':first').hide();

			var interval = setInterval(function(){

				list = obj.children();
				list.not(':first').hide();

				var first_li = list.eq(0)
				var second_li = options.random ? list.eq(Math.floor(Math.random()*list.length)) : list.eq(1)

				if(first_li.get(0) === second_li.get(0) && options.random){
					second_li = list.eq(Math.floor(Math.random()*list.length));
				}

				if(options.effect == 'slide'){
					first_li.slideUp();
					second_li.slideDown(function(){
						first_li.remove().appendTo(obj);

					});
				} else if(options.effect == 'fade'){
					first_li.fadeOut(function(){
						obj.css('height',second_li.height());
						second_li.fadeIn();
						first_li.remove().appendTo(obj);
					});
				}

				count--;

				if(count == 0 && options.run_once){
					clearInterval(interval);
				}

			}, options.speed)
		});
	};

	$('#ticker_<?php echo $widget_id;?>').list_ticker({
		speed:<?php echo $speed;?>,
		effect:'slide',
		random:false
	});

});
</script>