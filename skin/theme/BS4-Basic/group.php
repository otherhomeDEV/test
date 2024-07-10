<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!$is_admin) {
	if (G5_IS_MOBILE) {
		if($group['gr_device'] == 'pc')
			alert($group['gr_subject'].' 그룹은 PC에서만 접근할 수 있습니다.');
	} else {
		if($group['gr_device'] == 'mobile')
		    alert($group['gr_subject'].' 그룹은 모바일에서만 접근할 수 있습니다.');
	}
}

$g5['title'] = $group['gr_subject'];
include_once(G5_THEME_PATH.'/head.sub.php');

include_once(G5_THEME_PATH.'/_loader.php');

include_once(G5_THEME_PATH.'/head.php');

// layout 내 경로지정
$group_skin_path = G5_THEME_PATH.'/group';
$group_skin_url = G5_THEME_URL.'/group';
if(is_file($group_skin_path.'/'.$gr_id.'.php')) {
	include_once($group_skin_path.'/'.$gr_id.'.php');
	include_once(G5_THEME_PATH.'/tail.php');
	return;
}

// 칼럼
if($tset['pwide']) {
	$gr_row_cols = ($tset['scol']) ? 'row-cols-xl-3' : 'row-cols-lg-3 row-cols-xl-4';
} else {
	$gr_row_cols = ($tset['scol']) ? '' : 'row-cols-lg-3';
}

?>


<div class="mb-3 mb-sm-4 mt-n3 mt-sm-0">
    <?php echo na_widget('data-carousel', 'grt-'.$gr_id, 'xl=27%', 'auto=0'); //타이틀 ?>
</div>

<?php
$bo_device = (G5_IS_MOBILE) ? 'pc' : 'mobile';
$sql = " SELECT bo_table, bo_subject
            FROM {$g5['board_table']}
            WHERE gr_id = '{$gr_id}'
              AND bo_list_level <= '{$member['mb_level']}'
              AND bo_order >= 0
              AND bo_device <> '{$bo_device}' ";
if (!$is_admin) {
    $sql .= " AND bo_use_cert = '' ";
}
$sql .= " ORDER BY bo_order ";
$result = sql_query($sql);
$num_rows = sql_num_rows($result);
?>

<div class="row <?php echo ($num_rows % 2 == 0 && !is_mobile() ) ? 'row-cols-2' : 'row-cols-1'; ?> <?php echo $gr_row_cols ?> na-row"> <!-- && $num_rows > 2  추가 예정 -->
    <?php
    for ($i = 0; $row = sql_fetch_array($result); $i++) {
    ?>
        <div class="col na-col">
            <!-- 위젯 시작 { -->
            <h3 class="h3 f-lg en">
                <a href="<?php echo get_pretty_url($row['bo_table']); ?>">
                    <span class="pull-right more-plus"></span>
                    <?php echo get_text($row['bo_subject']) ?>
                </a>
            </h3>
            <hr class="hr" />
            <div class="mt-3 mb-4">
                <?php
                if ($gr_id === 'business') {
                    echo na_widget('wr-gallery-slider', 'gr-'.$row['bo_table'], 'bo_list='.$row['bo_table'].' cache=5');
                } else {
                    echo na_widget('wr-list', 'gr-'.$row['bo_table'], 'bo_list='.$row['bo_table'].' cache=5');
                }
                ?>
            </div>
        </div>
    <?php
    }
    ?>
</div>


<?php
include_once(G5_THEME_PATH.'/tail.php');