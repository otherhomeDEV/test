<?php
include_once("./_common.php");

include_once("$board_skin_path/auction.lib.php");

$sql = " select wr_subject from $write_table where wr_id = '$wr_id' ";
$row = sql_fetch($sql);

$g5['title'] = get_text($row[wr_subject])." 경매입찰내역";

include_once(G5_PATH.'/head.sub.php');


// 경매진행중 -> 종료시간이 지났을 때 -> 경매종료
if ($write[wr_8] == "1" && $write[wr_2] <= G5_TIME_YMDHIS) {
    $result = auction_successful($wr_id);
    if ($result[wr_8] > 1) {
        $write[wr_7] = $result[wr_7];
        $write[wr_8] = $result[wr_8];
        $write[wr_9] = $result[wr_9];
        $write[wr_10] = $result[wr_10];
    }
}

if ($write[wr_8] >= 2) {
    $order_info = "포인트 오름차순 정렬";
    $orderby = "td_tender_point";
} else {
    $order_info = "입찰일시 내림차순 정렬";
    $orderby = "td_datetime desc";
}

$row = sql_fetch(" select count(*) as cnt from $tender_table where wr_id = '$wr_id' ");
$total_count = $total = $row[cnt];

$rows = $config[cf_page_rows];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산

// 페이지가 없으면 첫 페이지 (1 페이지)
if ($page == "") {
    $page = 1;
}
$from_record = ($page - 1) * $rows; // 시작 열을 구함
?>
<div style="height:50px; clear:both;">
    <div style="float:left; margin:20px 20px 0 10px;">
        <b style="color:#0000A0;">참여내역</b>
    </div>

    <div style="float:right; margin:20px 10px 10px 0;">
    총 참여수 : <?=number_format($total)?> 
    </div>
</div>

<style>
#tender_list_tb {margin:0 auto; width:450px; border-collapse:collapse;}
#tender_list_tb th{background-color:#e7e7e7;}
#tender_list_tb td {padding:7px 0; text-align:center; border-bottom:1px solid #ccc;}
#tender_list_tb td.align_right{text-align:right;}
.teneder-cls-btn input {
    text-align: center;
    background: #7b7b7b;
    height: 40px;
    width: 100px;
    border: 1px solid #7b7b7b;
    padding: 5px 10px;
    color: #fff;
}
</style>

<table id="tender_list_tb">
    <tr>
        <th style="width:50px; height:30px;"> 번호 </th>
        <th style="width:100px;"> 회원 </th>
        <th> 구매일시 </th>
        <!--<th style="width:100px;"> 입찰금액 </th>-->
    </tr>
<?php
if (!$total) {
    echo "<tr><td colspan=4> 입찰내역이 없습니다. </td></tr>";
}

$qry = sql_query(" select * from $tender_table where wr_id = '$wr_id' order by $orderby limit $from_record, $rows");
//$num = mysql_num_rows($qry);
$k = 0;
while ($row = sql_fetch_array($qry)) {
    $num = $total_count - ($page - 1) * $config[cf_page_rows] - $k;

    $is_view = ($is_admin || $write[wr_8] != 1 || $row[mb_id] == $member[mb_id]);
    //if ($save_mb_id != $row[mb_id]) $is_view = true;

    if ($is_view) 
        $tender_point = number_format($row[wr_3]);
    else
        $tender_point = '*****';

    if ($row[td_tender_point] == $write[wr_9]) $bgcolor = "#FAB074"; else $bgcolor = "#ffffff";

    $save_mb_id = $row[mb_id];

    $k++;
?>
    <tr bgcolor="<?=$bgcolor?>">
        <td> <?=$num?> </td>
        <td>
        <?php
        if ($is_view) {
            echo get_sideview($row[mb_id], $row[mb_nick], $row[mb_email], $row[mb_homepage]);
            $date = date("Y-m-d H:i:s", strtotime($row[td_datetime]));
        } else {
            echo '*****';
            $date = date("Y-m-d", strtotime($row[td_datetime]));
        }
        ?> 
        </td>
        <td> <?=$date?> </td>
        <!--<td class="align_right"> <?=$tender_point?> 포인트 &nbsp; </td>-->
    </tr>
<?php } ?>

</table>

<?php
$qstr = "bo_table=$bo_table&wr_id=$wr_id";
?>

<div align=center>
<?php echo get_paging($config[cf_write_pages], $page, $total_page, "$_SERVER[PHP_SELF]?$qstr&page=");?>
</div>

<div class="teneder-cls-btn" style="text-align:center; margin-top:30px;">
    <input type=button value="닫     기" onclick="self.close()">
</div>


<?php
include_once(G5_PATH.'/tail.sub.php');
?>
