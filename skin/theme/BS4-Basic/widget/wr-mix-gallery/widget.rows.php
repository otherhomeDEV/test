<?php
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가

// 큰이미지 영역 및 썸네일 크기 설정
$wset['thumb_w'] = (isset($wset['thumb_w']) && $wset['thumb_w'] != "") ? (int)$wset['thumb_w'] : 500;
$wset['thumb_h'] = (isset($wset['thumb_h']) && $wset['thumb_h'] != "") ? (int)$wset['thumb_h'] : 326;

if($wset['thumb_w'] && $wset['thumb_h']) {
	$img_height = ($wset['thumb_h'] / $wset['thumb_w']) * 100;
} else {
	$img_height = (isset($wset['thumb_d']) && $wset['thumb_d']) ? $wset['thumb_d'] : '56.25';
}


// 작은이미지 영역 및 썸네일 크기 설정
$wset['sthumb_w'] = (isset($wset['sthumb_w']) && $wset['sthumb_w'] != "") ? (int)$wset['sthumb_w'] : 120;
$wset['sthumb_h'] = (isset($wset['sthumb_h']) && $wset['sthumb_h'] != "") ? (int)$wset['sthumb_h'] : 90;

if($wset['sthumb_w'] && $wset['sthumb_h']) {
	$simg_height = ($wset['sthumb_h'] / $wset['sthumb_w']) * 100;
} else {
	$simg_height = (isset($wset['sthumb_d']) && $wset['sthumb_d']) ? $wset['sthumb_d'] : '56.25';
}

// 이미지라운드
$round = (isset($wset['round']) && $wset['round']) ? ' na-r'.$wset['round'] : '';

// 글내용 길이
$cut_txt = (isset($wset['wcut']) && (int)$wset['wcut']) ? $wset['wcut'] : 100;

// 추출하기
$wset['sideview'] = 1; // 이름 레이어

$wset['rows'] = isset($wset['rows']) ? $wset['rows'] : '4';
$wset['page'] = isset($wset['page']) ? $wset['page'] : '';
$wset['rank'] = isset($wset['rank']) ? $wset['rank'] : '';

// 리스트글
$post_cnt = $wset['rows'];

$img_post_cnt = (isset($wset['irows']) && $wset['irows'] > 0) ? $wset['irows'] : 1;

// 이미지글수
$img = array();
$img_arr = array();
$wset['image'] = 1; //이미지글만 추출
if($wset['rand']) { //랜덤
	$wset['rows'] = ($img_post_cnt > 3) ? $img_post_cnt : 3;
	$img = na_board_rows($wset);
	$img_cnt = count($img);
	if($img_cnt) {
		shuffle($img);
	}
	if($img_cnt > $img_post_cnt) {
		$img_cnt = $img_post_cnt;
	}
} else {
	$wset['rows'] = $img_post_cnt;
	$img = na_board_rows($wset);
	$img_cnt = count($img);
}

for($i=0; $i < $img_cnt; $i++) {
	$img_arr[$i] = $img[$i]['bo_table'].'-'.$img[$i]['wr_id'];
}

// 리스트글 - 중복글 제외
$tmp = array();
$wset['image'] = '';
$wset['rows'] = $post_cnt + $img_cnt;
$tmp = na_board_rows($wset);
$tmp_cnt = count($tmp);
$z = 0;
for($i=0; $i < $tmp_cnt; $i++) {

	$chk_wr = $tmp[$i]['bo_table'].'-'.$tmp[$i]['wr_id'];

	if($img_cnt && in_array($chk_wr, $img_arr)) continue;

	$list[$z] = $tmp[$i];

	if($z == $post_cnt) break;

	$z++;
}

unset($tmp);

$list_cnt = count($list);

// 랜덤
if(isset($wset['rand']) && $wset['rand'] && $list_cnt)
	shuffle($list);

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
?>
    <div class="col-md-6">
		<?php
		for ($i=0; $i < $img_cnt; $i++) {
			// 아이콘 체크
			$wr_icon = $wr_tack = $wr_cap = '';
			if ($img[$i]['icon_secret']) {
				$is_lock = true;
				$wr_icon = '<span class="na-icon na-secret"></span>';
			}

			if ($wset['rank']) {
				$wr_tack = '<span class="label-tack rank-icon en bg-'.$wset['rank'].'">'.$rank.'</span>';
				$rank++;
			}

			if($img[$i]['icon_new']) {
				$wr_cap = '<span class="label-cap en bg-'.$cap_new.'">New</span>';
			}

			// 보드명, 분류명
			if($is_bo_name) {
				$ca_name = '';
				if(isset($img[$i]['bo_subject']) && $img[$i]['bo_subject']) {
					$ca_name = ($bo_name) ? cut_str($img[$i]['bo_subject'], $bo_name, '') : $img[$i]['bo_subject'];
				} else if($img[$i]['ca_name']) {
					$ca_name = ($bo_name) ? cut_str($img[$i]['ca_name'], $bo_name, '') : $img[$i]['ca_name'];
				}

				if($ca_name) {
					$img[$i]['subject'] = $ca_name.' <span class="na-bar"></span> '.$img[$i]['subject'];
				}
			}

			// 링크 이동
			if($is_link && $img[$i]['wr_link1']) {
				$img[$i]['href'] = $img[$i]['link_href'][1];
			}

			// 이미지 추출
			$big_img = na_wr_img($img[$i]['bo_table'], $img[$i]);

			// 썸네일 생성
			$thumb = ($wset['thumb_w']) ? na_thumb($big_img, $wset['thumb_w'], $wset['thumb_h']) : $big_img;
		?>
        <div class="lt-item-bg">
			<div class="img-wrap bg-light<?php echo $round;?> mb-3" style="padding-bottom:<?php echo $img_height ?>%;">
				<div class="img-item">
					<a href="<?php echo $img[$i]['href'] ?>"<?php echo $target ?>>
						<?php echo $wr_tack ?>
						<?php echo $wr_cap ?>
						<?php if($thumb) { ?>
						<img src="<?php echo $thumb ?>" alt="Image <?php echo $img[$i]['wr_id'] ?>" class="big-img img-fluid">
						<?php } ?>
					</a>
				</div>
				<div class="img-caption font-weight-normal f-sm">
					<span><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo na_date($list[$i]['wr_datetime'], 'orangered', 'H:i', 'm.d', 'm.d'); ?></span>
					<span class="float-right"><?php echo na_name_photo($list[$i]['mb_id'], $list[$i]['name']) ?></span>
				</div>
			</div>
            <h6><a href="<?php echo $img[$i]['href'];?>"<?php echo $target;?>><?php echo $img[$i]['subject'];?></a></h6>
            <!--ul class="list-unstyled list-inline">
                <?php if($list[$i]['ca_name']){ ?>
				<li class="list-inline-item">
					<span class="sr-only">분류</span>
					<?php echo $list[$i]['ca_name'];?>
				</li>
				<?php } ?>
				<li class="list-inline-item">
					<span class="sr-only">등록자</span>
					<?php echo $list[$i]['name'];?>
				</li>
				<li class="list-inline-item">
					<span class="sr-only">등록일</span>
					<?php echo na_date($list[$i]['wr_datetime'], 'orangered', 'H:i', 'm.d', 'm.d'); ?>
				</li>
            </ul-->
            <p><?php echo na_cut_text($img[$i]['wr_content'], $cut_txt) ?></p>
        </div>

		<?php if($wset['bh'] && $img_cnt > 1) { //간격 ?>
			<div class="clearfix" style="height:<?php echo $wset['bh'];?>px;"></div>
		<?php } ?>

		<?php } ?>
    </div>
    <div class="col-md-6">
		<?php
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
			$thumb = ($wset['sthumb_w']) ? na_thumb($img, $wset['sthumb_w'], $wset['sthumb_h']) : $img;

		?>
        <div class="lt-item-sm d-flex">
            <div class="lt-img">
				<div class="img-wrap bg-light<?php echo $round;?>" style="padding-bottom:<?php echo $simg_height ?>%;">
					<a href="<?php echo $list[$i]['href'] ?>"<?php echo $target ?>>
						<?php echo $wr_tack ?>
						<?php echo $wr_cap ?>
						<?php if($thumb) { ?>
							<img src="<?php echo $thumb ?>" alt="Image <?php echo $list[$i]['wr_id'] ?>" class="img-render">
						<?php } ?>
					</a>
				</div>
            </div>
            <div class="img-content" style="">
                <p><a href="<?php echo $list[$i]['href'] ?>" <?php echo $target ?>><?php echo $wr_icon ?><?php echo $list[$i]['subject'] ?></a></p>
				<p class="ellipsis f-sm"><?php echo na_cut_text($list[$i]['wr_content'], 30) ?></p>
				<?php if($list[$i]['ca_name']){ ?><span><?php echo $list[$i]['ca_name'];?></span><?php } ?>
                <span><?php echo na_date($list[$i]['wr_datetime'], 'orangered', 'H:i', 'm.d', 'm.d'); ?></span>
            </div>
        </div>
        <?php } ?>
    </div>