<?php
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가

// 이미지 영역 및 썸네일 크기 설정
$wset['thumb_w'] = (isset($wset['thumb_w']) && $wset['thumb_w'] != "") ? (int)$wset['thumb_w'] : 400;
$wset['thumb_h'] = (isset($wset['thumb_h']) && $wset['thumb_h'] != "") ? (int)$wset['thumb_h'] : 225;

if($wset['thumb_w'] && $wset['thumb_h']) {
	$img_height = ($wset['thumb_h'] / $wset['thumb_w']) * 100;
} else {
	$img_height = (isset($wset['thumb_d']) && $wset['thumb_d']) ? $wset['thumb_d'] : '56.25';
}

// 이미지라운드
$round = (isset($wset['round']) && $wset['round']) ? ' na-r'.$wset['round'] : '';

// 추출하기
$wset['sideview'] = 1; // 이름 레이어

$wset['rows'] = isset($wset['rows']) ? $wset['rows'] : '';
$wset['page'] = isset($wset['page']) ? $wset['page'] : '';
$wset['rank'] = isset($wset['rank']) ? $wset['rank'] : '';

$list = na_board_rows($wset);
$list_cnt = count($list);

// 랭킹
$rank = na_rank_start($wset['rows'], $wset['page']);

// 새글
$cap_new = (isset($wset['new']) && $wset['new']) ? $wset['new'] : 'primary';

// 보드명, 분류명
$is_bo_name = (isset($wset['bo_name']) && $wset['bo_name']) ? true : false;
$bo_name = ($is_bo_name && (int)$wset['bo_name'] > 0) ? $wset['bo_name'] : 0;

// 글 이동
$is_link = false;
$wset['target'] = isset($wset['target']) ? $wset['target'] : '';
switch($wset['target']) {
	case '1' : $target = ' target="_blank"'; break;
	case '2' : $is_link = true; break;
	case '3' : $target = ' target="_blank"'; $is_link = true; break;
	default	 : $target = ''; break;
}

// 랜덤
if(isset($wset['rand']) && $wset['rand'] && $list_cnt)
	shuffle($list);

// 리스트
for ($i=0; $i < $list_cnt; $i++) {

	// 아이콘 체크
	$wr_icon = $wr_tack = $wr_cap = '';
	if ($list[$i]['icon_secret']) {
		$is_lock = true;
		$wr_icon = '<span class="na-icon na-secret"></span>';
	}

	if ($wset['rank']) {
		$wr_tack = '<span class="label-tack rank-icon en bg-'.$wset['rank'].'">'.$rank.'</span>';
		$rank++;
	}

	if($list[$i]['icon_new']) {
		$wr_cap = '<span class="label-cap en bg-'.$cap_new.'">New</span>';
	}

	// 보드명, 분류명
	if($is_bo_name) {
		$ca_name = '';
		if(isset($list[$i]['bo_subject']) && $list[$i]['bo_subject']) {
			$ca_name = ($bo_name) ? cut_str($list[$i]['bo_subject'], $bo_name, '') : $list[$i]['bo_subject'];
		} else if($list[$i]['ca_name']) {
			$ca_name = ($bo_name) ? cut_str($list[$i]['ca_name'], $bo_name, '') : $list[$i]['ca_name'];
		}

		if($ca_name) {
			$list[$i]['subject'] = $ca_name.' <span class="na-bar"></span> '.$list[$i]['subject'];
		}
	}

	// 링크 이동
	if($is_link && $list[$i]['wr_link1']) {
		$list[$i]['href'] = $list[$i]['link_href'][1];
	}

	// 이미지 추출
	$img = na_wr_img($list[$i]['bo_table'], $list[$i]);

	// 썸네일 생성
	$thumb = ($wset['thumb_w']) ? na_thumb($img, $wset['thumb_w'], $wset['thumb_h']) : $img;

?>
    <li class="item text-center">
		<div class="img-wrap bg-light<?php echo $round;?> mb-2" style="padding-bottom:<?php echo $img_height ?>%;">
			<div class="img-item">
				<a href="<?php echo $list[$i]['href'] ?>"<?php echo $target ?>>
					<?php echo $wr_tack ?>
					<?php echo $wr_cap ?>
					<?php if($thumb) { ?>
						<img src="<?php echo $thumb ?>" alt="Image <?php echo $list[$i]['wr_id'] ?>" class="img-render">
					<?php } ?>
				</a>
			</div>
		</div>

        <div class="na-item">
            <a href="<?php echo $list[$i]['href'] ?>" class="na-subject"<?php echo $target ?>>
                <?php echo $wr_icon ?>
                <?php echo $list[$i]['subject'] ?>
            </a>
        </div>
	</li>
<?php } ?>

<?php if(!$list_cnt) { ?>
     <li class="item">
        <div class="img-wrap bg-light">
            <div class="img-item">
                <div class="position-absolute text-white text-center mt-n3 w-100 f-de" style="top:50%; left:0;">
                글이 없습니다.
                </div>
            </div>
        </div>
    </li>
<?php } ?>