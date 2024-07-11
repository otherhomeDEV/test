<?php
if (!defined('_GNUBOARD_')) {
	include_once('../../../../common.php');
	include_once(G5_LIB_PATH.'/apms.more.lib.php');

	// 창열기
	$wset['modal_js'] = ($wset['modal'] == "1") ? apms_script('modal') : '';
}

// 링크
$is_modal_js = $wset['modal_js'];
$is_link_target = ($wset['modal'] == "2") ? ' target="_blank"' : '';

// 추출하기
$list = apms_board_rows($wset);
$list_cnt = count($list);

// 아이콘
$icon = (isset($wset['icon']) && $wset['icon']) ? '<span class="lightgray">'.apms_fa($wset['icon']).'</span>' : '';
$is_ticon = (isset($wset['ticon']) && $wset['ticon']) ? true : false;

// 랭킹
$rank = apms_rank_offset($wset['rows'], $wset['page']);

// 날짜
$is_date = (isset($wset['date']) && $wset['date']) ? true : false;
$is_dtype = (isset($wset['dtype']) && $wset['dtype']) ? $wset['dtype'] : 'm.d';
$is_dtxt = (isset($wset['dtxt']) && $wset['dtxt']) ? true : false;

// 새글
$is_new = (isset($wset['new']) && $wset['new']) ? $wset['new'] : 'red';

// 댓글
$is_comment = (isset($wset['comment']) && $wset['comment']) ? true : false;

$speed = (isset($wset['speed']) && $wset['speed']) ? $wset['speed'] : '5000';


// 강조글
$bold = array();
$strong = explode(",", $wset['strong']);
$is_strong = count($strong);
for($i=0; $i < $is_strong; $i++) {

	list($n, $bc) = explode("|", $strong[$i]);

	if(!$n) continue;

	$n = $n - 1;
	$bold[$n]['num'] = true;
	$bold[$n]['color'] = ($bc) ? ' class="'.$bc.'"' : '';
}

?>

<ul id="ticker_<?php echo $widget_id;?>" class="miso-post-news-ticker">
<?php
// 리스트
for ($i=0; $i < $list_cnt; $i++) {

	//아이콘 체크
	$wr_icon = $icon;
	$is_lock = false;
	if ($list[$i]['secret'] || $list[$i]['is_lock']) {
		$is_lock = true;
		$wr_icon = '<span class="wr-icon wr-secret"></span>';
	} else if ($wset['rank']) {
		$wr_icon = '<span class="rank-icon en bg-'.$wset['rank'].'">'.$rank.'</span>';
		$rank++;
	} else if($list[$i]['new']) {
		$wr_icon = '<span class="wr-icon wr-new"></span>';
	} else if($is_ticon) {
		if ($list[$i]['icon_video']) {
			$wr_icon = '<span class="wr-icon wr-video"></span>';
		} else if ($list[$i]['icon_image']) {
			$wr_icon = '<span class="wr-icon wr-image"></span>';
		} else if ($list[$i]['wr_file']) {
			$wr_icon = '<span class="wr-icon wr-file"></span>';
		}
	}

	// 링크이동
	$target = '';
	if($is_link_target && $list[$i]['wr_link1']) {
		$target = $is_link_target;
		$list[$i]['href'] = $list[$i]['link_href'][1];
	}

	//강조글
	if($is_strong) {
		if($bold[$i]['num']) {
			$list[$i]['subject'] = '<b'.$bold[$i]['color'].'>'.$list[$i]['subject'].'</b>';
		}
	}

?>
	<li>
		<a href="<?php echo $list[$i]['href'];?>" class="ellipsis"<?php echo $is_modal_js;?><?php echo $target;?>>
			<span class="news_arrow"></span>
			<?php if ($is_date) { ?> <span class="date"><?php echo ($is_dtxt) ? apms_datetime($list[$i]['date'], $is_dtype) : date($is_dtype, $list[$i]['date']); ?></span> <?php } ?>
			<span class="title"><?php echo $list[$i]['subject'];?></span> <?php echo $wr_icon;?>
		</a>
	</li>
<?php } ?>
<?php if(!$list_cnt) { ?>
	<li class="post-none">글이 없습니다.</li>
<?php } ?>
</ul>
