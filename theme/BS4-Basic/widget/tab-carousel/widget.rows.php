<?php
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가

// 자동실행
$wset['auto'] = (isset($wset['auto']) && (int)$wset['auto'] >= 1000) ? $wset['auto'] : 'false';

// 전환 효과
$wset['effect'] = (isset($wset['effect']) && $wset['effect']) ? $wset['effect'] : '';
switch($wset['effect']) {
	case 'slide' : $wset['effect'] = ' slide'; break;
	case 'fade'	 : $wset['effect'] = ' slide carousel-fade'; break;
	default		 : $wset['effect'] = ''; break;	
}

// 선택 탭 컬러
$wset['active'] = (isset($wset['active']) && $wset['active']) ? $wset['active'] : '#007bff';

$list = array();

$n = 0;

// 데모
if(IS_DEMO && (!isset($wset['d']) || !is_array($wset['d'])) && isset($wset['demo']) && $wset['demo']) {
	@include($widget_path.'/demo/'.$wset['demo'].'.php');
}

if(isset($wset['d']['tab']) && is_array($wset['d']['tab'])) {
	$data_cnt = count($wset['d']['tab']);
	for($i=0; $i < $data_cnt; $i++) {
		if(isset($wset['d']['tab'][$i]) && $wset['d']['tab'][$i] && isset($wset['d']['wname'][$i]) && $wset['d']['wname'][$i]) {
			$list[$n]['tab'] = $wset['d']['tab'][$i];
			$list[$n]['dot'] = $wset['d']['dot'][$i];
			$list[$n]['wname'] = $wset['d']['wname'][$i];
			$list[$n]['wid'] = $wset['d']['wid'][$i];
			$list[$n]['wset'] = $wset['d']['wset'][$i];
			$n++;
		}
	}
}

$list_cnt = $n;

// 랜덤
if(isset($wset['rand']) && $wset['rand'] && $list_cnt) 
	shuffle($list);

// 랜덤아이디
$id = 'tab_'.na_rid(); 

?>
<style>
#<?php echo $id ?> .carousel-indicators .active { color:<?php echo $wset['active'] ?> !important; }
</style>
<div id="<?php echo $id ?>" class="carousel<?php echo $wset['effect'] ?>" data-ride="carousel" data-interval="<?php echo $wset['auto'] ?>">
	<?php if($list_cnt) { ?>
		 <div class="d-sm-flex justify-content-start align-items-end mb-2 mx-3 mx-sm-0">
			<?php if(isset($wset['title']) && $wset['title']) { ?>
				<div class="f-lg pb-1 pr-4 text-nowrap" style="margin-bottom:0 !important;">
					<b><?php echo $wset['title'] ?></b>
				</div>
			<?php } ?>
			<div class="flex-grow-1 pb-1">
				<div class="d-flex justify-content-between align-items-end">
					<div class="text-wrap">
						 <!-- Indicators -->
						 <div class="carousel-indicators d-block position-relative mx-0">
						<?php for ($i=0; $i < $list_cnt; $i++) { ?>
							<a href="javascript:;" data-target="#<?php echo $id ?>" data-slide-to="<?php echo $i ?>" class="text-nowrap mr-3<?php echo (!$i) ? ' active' : '';?>">
								<b><?php echo $list[$i]['tab'] ?></b>
							</a>
						<?php } ?>
						</div>
					</div>
					<div class="text-nowrap">
						<!-- Controls -->
						<a href="#<?php echo $id ?>" data-slide="prev">
							<i class="fa fa-chevron-left text-black-50 mr-2" aria-hidden="true"></i>
							<span class="sr-only">Previous</span>
						</a>
						<a href="#<?php echo $id ?>" data-slide="next">
							<i class="fa fa-chevron-right text-black-50" aria-hidden="true"></i>
							<span class="sr-only">Next</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
	<div class="mr-n4">
		<div class="carousel-inner">
			<?php for ($i=0; $i < $list_cnt; $i++) { ?>
				<div class="carousel-item bg-white<?php echo (!$i) ? ' active' : '';?>">
					<div class="pr-4">
						<?php echo na_widget($list[$i]['wname'], $list[$i]['wid'], $list[$i]['wset']) ?>
					</div>
				</div>
			<?php } ?>
			<?php if(!$list_cnt) { ?>
				<div class="carousel-item pr-4 active">
					<div class="pr-4">
						<div class="alert alert-warning px-3 py-5 mb-0 text-center" role="alert">
							위젯설정에서 사용할 탭을 등록해 주세요.
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>