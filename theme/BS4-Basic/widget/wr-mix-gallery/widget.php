<?php
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$widget_url.'/widget.css">', 0);

// 내용줄
$wset['line'] = (isset($wset['line']) && $wset['line'] > 0) ? $wset['line'] : 3;
$wset['line_height'] = 23 * $wset['line'] + 2;
$img_height = $wset['line_height'] + 22;

// 하단간격(큰이미지)
$wset['bh'] = (isset($wset['bh']) && $wset['bh'] > 0) ? $wset['bh'] : 20;

// 랜덤아이디
$id = 'mix_gallery_'.na_rid();
?>
<style>
	#<?php echo $id;?> .lt-item-bg p { height:<?php echo $wset['line_height'];?>px; }
</style>
<div id="<?php echo $id;?>" class="row mix_gallery">
<?php
if($wset['cache']) {
	echo na_widget_cache($widget_path.'/widget.rows.php', $wset, $wcache);
} else {
	include($widget_path.'/widget.rows.php');
}
?>
</div>

<?php if($setup_href) { ?>
	<div class="btn-wset pt-0">
		<a href="<?php echo $setup_href;?>" class="btn-setup">
			<span class="f-sm text-muted"><i class="fa fa-cog"></i> 위젯설정</span>
		</a>
	</div>
<?php } ?>