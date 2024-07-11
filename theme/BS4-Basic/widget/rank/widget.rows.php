<?php
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가

// 추출하기
$list = na_member_rows($wset);
$list_cnt = count($list);

// 리스트
for ($i=0; $i < $list_cnt; $i++) { 
	// 랭킹 아이콘
	$rank_icon = ($wset['rank']) ? '<span class="rank-icon en bg-'.$wset['rank'].'">'.($i + 1).'</span>' : '';;	
?>
	<li class="px-3 px-sm-0 py-1">
		<div class="d-flex">
			<?php if($wset['rank']) { ?>
				<div class="align-self-center pr-2">
					<span class="rank-icon en bg-<?php echo $wset['rank'] ?>"><?php echo ($i + 1) ?></span>
				</div>
			<?php } ?>
			<div class="align-self-center">
				<?php echo na_name_photo($list[$i]['mb_id'], $list[$i]['name']) ?>
			</div>
			<div class="align-self-center ml-auto">
				<?php echo number_format($list[$i]['cnt']) ?>
			</div>
		</div>
	</li>
<?php } ?>

<?php if(!$list_cnt) { ?>
	<li class="f-de text-muted text-center px-4 py-5">
		자료가 없습니다.
	</li>
<?php } ?>