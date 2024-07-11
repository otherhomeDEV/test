<?php
include_once("./_common.php");

if (!$bo_table && !$wr_id)
    die("bo_table 혹은 wr_id 가 없습니다.");

include_once("$board_skin_path/auction.lib.php");
include_once(G5_PATH.'/head.sub.php');

if (!$write)
    alert_only("bo_table 과 wr_id 를 확인하십시오.");

//if (!$point)
//    alert_only("point 를 입력해주세요.");

tender_send($wr_id, $point);

$url = G5_BBS_URL."/board.php?bo_table=".$bo_table."&wr_id=".$wr_id;
?>
<script>
alert("참여하였습니다.");
location.replace('<?php echo $url?>');
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>
