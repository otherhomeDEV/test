<?php
include_once("./_common.php");

if (!$bo_table && !$wr_id)
    die("bo_table 혹은 wr_id 가 없습니다.");

include_once("$board_skin_path/auction.lib.php");
include_once(G5_PATH.'/head.sub.php');

if (!$write) {
    alert_only("bo_table 과 wr_id 를 확인하십시오.");
}

$point_min = (int)$_POST['point_min'];
$point_max = (int)$_POST['point_max'];

if ($point_min > $point_max) {
    alert_only("구간 번호 오류입니다.");
}

$result = tender_send_section($wr_id, $point_min, $point_max);

$url = G5_BBS_URL."/board.php?bo_table=".$bo_table."&wr_id=".$wr_id;
?>
<script type='text/javascript'>
<?php if ($result) { ?>
    alert("<?=$result?>");
<?php } else { ?>
    alert("구간 입찰을 완료 하였습니다.\n\n입찰내역을 확인하세요.");
    location.replace('<?php echo $url?>');
<?php } ?>
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>
