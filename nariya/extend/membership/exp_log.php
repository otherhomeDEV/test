<?php
define('G5_IS_ADMIN', true);
include_once('./_common.php');

if ($is_admin != 'super')
    alert_close('최고관리자만 접근 가능합니다.');

$g5['title'] = '경험치 로그 정리';
include_once(G5_PATH.'/head.sub.php');

?>

<div id="menu_frm" class="new_win" style="background:#fff;">
    <h1><?php echo $g5['title']; ?></h1>

	<div class="local_desc01">
		준비 중...
	</div>

	<br>

</div>

<?php 
include_once(G5_PATH.'/tail.sub.php');