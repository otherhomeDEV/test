<?php
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가

/* 슬라이드 배너 이미지 위젯 - Owl Carousel */

na_script('owl');

//add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$widget_url.'/widget.css">', 0);


// 간격
if(!isset($wset['margin']) || $wset['margin'] == "") {
	$wset['margin'] = (G5_IS_MOBILE) ? 16 : 12;
}

// 높이
//$img_height = ($wset['thumb_w'] && $wset['thumb_h']) ? ($wset['thumb_h'] / $wset['thumb_w']) * 100 : '56.25';

// 랜덤아이디
$id = 'banner_'.na_rid();

?>
<ul id="<?php echo $id;?>" class="owl-carousel basic-banner">

    <?php
        if($wset['cache']) {
            echo na_widget_cache($widget_path.'/widget.rows.php', $wset, $wcache);
        } else {
            include($widget_path.'/widget.rows.php');
        }
    ?>

</ul>

<script>
$('#<?php echo $id;?>').owlCarousel({
	autoplay:<?php echo (isset($wset['auto']) && $wset['auto']) ? 'false' : 'true'; ?>,
	autoplayHoverPause:true,
	loop:true,
	item:<?php echo (isset($wset['xl']) && $wset['xl']) ? $wset['xl'] : 4; ?>,
	margin:<?php echo $wset['margin'] ?>,
	stagePadding: <?php echo (isset($wset['padding']) && $wset['padding'] != "") ? $wset['padding'] : 0; ?>,
	nav:<?php echo (isset($wset['nav']) && $wset['nav']) ? 'false' : 'true'; ?>,
	dots:false,
	navText:["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
	responsive:{
		0:{ items:<?php echo (isset($wset['xs']) && $wset['xs'] != "") ? $wset['xs'] : 2; ?> },
		575:{ items:<?php echo (isset($wset['sm']) && $wset['sm'] != "") ? $wset['sm'] : 3; ?> },
		767:{ items:<?php echo (isset($wset['md']) && $wset['md'] != "") ? $wset['md'] : 4; ?> },
		991:{ items:<?php echo (isset($wset['lg']) && $wset['lg'] != "") ? $wset['lg'] : 4; ?> },
		1199:{ items:<?php echo (isset($wset['xl']) && $wset['xl'] != "") ? $wset['xl'] : 4; ?> }
	}
});
</script>

<?php if($setup_href) { ?>
	<div class="btn-wset">
		<a href="<?php echo $setup_href;?>" class="btn-setup">
			<span class="f-sm text-muted"><i class="fa fa-cog"></i> 위젯설정</span>
		</a>
	</div>
<?php } ?>