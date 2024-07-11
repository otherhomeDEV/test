<?php
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가

na_script('slick');

//add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$widget_url.'/widget.css">', 0);

// 이미지 영역 크기 설정
$wset['thumb_w'] = (!isset($wset['thumb_w']) || !$wset['thumb_w']) ? 600 : (int)$wset['thumb_w'];
$wset['thumb_h'] = (!isset($wset['thumb_h']) || !$wset['thumb_h']) ? 350 : (int)$wset['thumb_h'];

// 간격
if(!isset($wset['margin']) || $wset['margin'] == "") {
	$wset['margin'] = (G5_IS_MOBILE) ? 16 : 12;
}

// 높이
$img_height = ($wset['thumb_w'] && $wset['thumb_h']) ? ($wset['thumb_h'] / $wset['thumb_w']) * 100 : '56.25';

// 이미지라운드
$round = (isset($wset['round']) && $wset['round']) ? ' na-r'.$wset['round'] : '';

// 회원정보
$is_profile = (isset($wset['profile']) && $wset['profile']) ? false : true;

$cut_txt = (isset($wset['wcut']) && (int)$wset['wcut']) ? $wset['wcut'] : 120;

$list = na_board_rows($wset);
$list_cnt = count($list);

// 랭킹
$rank = na_rank_start($wset['rows'], $wset['page']);

// 새글
$cap_new = (isset($wset['new']) && $wset['new']) ? $wset['new'] : 'red';

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

//배경색상
$color_array = array("red", "orangered", "green", "blue", "purple", "yellow", "navy");

// 분류 색상
$is_cc_rand = (isset($wset['cc_rand']) && $wset['cc_rand']) ? true : false;
$c_color = (isset($wset['c_color']) && $wset['c_color']) ? $wset['c_color'] : 'primary';

// 제목줄
$subject_line = (isset($wset['subject_line']) && $wset['subject_line'] > 0) ? $wset['subject_line'] : 1;
$subject_height = 28 * $subject_line + 2;

// 랜덤아이디
$id = 'slick_news_big_'.na_rid();

?>
<style>
.wr-news-big .post-title { height:<?php echo $subject_height;?>px; }
</style>
<!--Featured post Start-->
<div id="<?php echo $id;?>" class="wr-news-big mb-4">

    <div class="featured-slider<?php echo $round;?>">
        <div class="featured-slider-items">
			<?php

			// 랜덤
			if(isset($wset['rand']) && $wset['rand'] && $list_cnt)
				shuffle($list);

			for ($i=0; $i < $list_cnt; $i++) {

				// 아이콘 체크
				$wr_icon = '';
				if ($list[$i]['icon_secret']) {
					$is_lock = true;
					$wr_icon = '<span class="na-icon na-secret"></span>';
				}

				// 보드명, 분류명
				if($is_bo_name) {
					$ca_name = '';
					if(isset($list[$i]['bo_subject']) && $list[$i]['bo_subject']) {
						$ca_name = ($bo_name) ? cut_str($list[$i]['bo_subject'], $bo_name, '') : $list[$i]['bo_subject'];
					} else if($list[$i]['ca_name']) {
						$ca_name = ($bo_name) ? cut_str($list[$i]['ca_name'], $bo_name, '') : $list[$i]['ca_name'];
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
            <div class="slider-single">
				<div class="row no-gutters">
					<div class="col-lg-6 col-md-12 order-lg-1 order-2 my-auto">
						<div class="slider-caption">
							<?php if($ca_name){ ?>
							<div class="entry-meta meta-0 mb-3">
								<a href="category.html"><span class="post-in bg-<?php echo ($is_cc_rand)?$color_array[rand(0, 6)]:$c_color;?> txt-white f-sm"><?php echo $ca_name;?></span></a>
							</div>
							<?php } ?>
							<h2 class="post-title">
								<a href="<?php echo $list[$i]['href'] ?>"<?php echo $target ?>>
									<?php echo $wr_icon ?>
									<?php echo $list[$i]['subject'] ?>
								</a>
							</h2>
							<div class="entry-meta meta-1 f-sm txt-grey mt-2 mb-2">
								<span class="time-reading"><i class="fa fa-commenting-o"></i><?php echo $list[$i]['wr_comment'];?></span>
								<span class="hit-count"><i class="fa fa-eye"></i><?php echo $list[$i]['wr_hit'];?>회</span>
								<span class="post-on"><i class="fa fa-clock-o"></i><?php echo na_date($list[$i]['wr_datetime'], 'orangered', 'H:i', 'm.d', 'Y.m.d'); ?></span>
							</div>
							<p class="post-content mt-3 mb-3"><?php echo na_cut_text($list[$i]['wr_content'], $cut_txt) ?></p>
							<?php if($is_profile){ ?>
							<div class="entry-meta meta-2">
								<a class="float-left mr-2 author-img" href="<?php echo $list[$i]['href'] ?>"><?php echo get_member_profile_img($list[$i]['mb_id']); ?></a>
								<a href="<?php echo $list[$i]['href'] ?>"><span class="author-name"><?php echo $list[$i]['name'];?></span></a>
								<br>
								<span class="author-add txt-grey"><?php echo $list[$i]['mb_id'];?></span>
							</div>
							<?php } ?>
						</div>
					</div>
					<div class="slider-img col-lg-6 order-lg-2 order-1 col-md-12">
						<div class="img-hover-scale">
							<?php if($list[$i]['icon_new']) { ?>
							<span class="top-right-icon font-weight-bold bg-<?php echo $cap_new;?>">N</span>
							<?php } ?>
							<a href="<?php echo $list[$i]['href'] ?>">
								<img src="<?php echo $thumb ?>" alt="Image <?php echo $list[$i]['wr_id'] ?>" class="img-fluid">
							</a>
						</div>
					</div>
				</div>
            </div>
			<?php } ?>
			<?php if(!$list_cnt) { ?>
			<div class="w-100 f-de text-muted text-center px-2 py-5">
				글이 없습니다.
			</div>
			<?php } ?>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-12">
				<div class="arrow-cover"></div>
            </div>
        </div>
    </div>

</div>
<!--Featured post End-->

<script>
    // Slick slider
    $(function(){
        $('.featured-slider-items').slick({
            dots: <?php echo (isset($wset['dots']) && $wset['dots']) ? 'true' : 'false'; ?>,
            infinite: true,
            speed: <?php echo (isset($wset['speed']) && $wset['speed']) ? $wset['speed'] : 500; ?>,
            arrows: <?php echo (isset($wset['arrows']) && $wset['arrows']) ? 'false' : 'true'; ?>,
            slidesToShow: 1,
            autoplay: <?php echo (isset($wset['auto']) && $wset['auto']) ? 'false' : 'true'; ?>,
            loop: true,
            adaptiveHeight: true,
            fade: <?php echo (isset($wset['fade']) && $wset['fade']) ? 'false' : 'true'; ?>,
            cssEase: 'linear',
            prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-arrow-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fa fa-arrow-right"></i></button>',
            appendArrows: '.arrow-cover',
        });
    });
</script>

<?php if($setup_href) { ?>
	<div class="btn-wset">
		<a href="<?php echo $setup_href;?>" class="btn-setup">
			<span class="f-sm text-muted"><i class="fa fa-cog"></i> 위젯설정</span>
		</a>
	</div>
<?php } ?>