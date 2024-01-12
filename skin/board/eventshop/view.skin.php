<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once("$board_skin_path/auction.lib.php");
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
add_javascript('<script src="'.G5_JS_URL.'/viewimageresize.js"></script>');

$info = get_info_auction($wr_id);

// 경매전 -> 시작시간이 지났을 때 -> 경매진행중
if ($info[status] == "0" && $info[start_datetime] <= G5_TIME_YMDHIS) {
    sql_query(" update $write_table set wr_8 = '1' where wr_id = '$wr_id' ");
    $info[status] = "1";
}

// 경매진행중 -> 종료시간이 지났을 때 -> 경매종료
if ($info[status] == "1" && $info[end_datetime] <= G5_TIME_YMDHIS) {
    $result = auction_successful($wr_id);
    if ($result[wr_8] > 1) {
        $info[tender_count] = $result[wr_7];
        $info[status] = $result[wr_8];
        $info[td_id] = $result[wr_9];
        $info[mb_id] = $result[wr_10];
    }
}

// 낙찰
if ($info[status] == "2") {
    $success_member = get_member($info[mb_id]);
}

$end_time = strtotime($info[end_datetime])-G5_SERVER_TIME;

if ($is_admin) {
    // 명수
    $sql = "select count( distinct mb_id ) as cnt from $tender_table where wr_id = '$wr_id' ";
    $row = sql_fetch($sql);

    $tender_mb_id_count = number_format($row[cnt]);


    // 최저로 입찰된 내역을 조회 (현재 1위)
    $row = sql_fetch(" select td_tender_point as point, count(td_tender_point) as cnt from $tender_table where wr_id = '$wr_id' group by td_tender_point order by cnt, td_tender_point limit 1 ");
    $super = array("point"=>$row[point], "count"=>$row[cnt]);

    $qry = sql_query(" select mb_id from $tender_table where td_tender_point = '$row[point]' and wr_id = '$wr_id' ");
    while ($row = sql_fetch_array($qry))
    {
        $super_mb_id[] = $row[mb_id];
    }
}

$attach_list = '';
if ($view['link']) {
	// 링크
	for ($i=1; $i<=count($view['link']); $i++) {
		if ($view['link'][$i]) {
			$attach_list .= '<a class="list-group-item break-word" href="'.$view['link_href'][$i].'" target="_blank">';
			$attach_list .= '<span class="label label-warning pull-right view-cnt">'.number_format($view['link_hit'][$i]).'</span>';
			$attach_list .= '<i class="fa fa-link"></i> '.cut_str($view['link'][$i], 70).'</a>'.PHP_EOL;
		}
	}
}

// 가변 파일
$j = 0;
for ($i=0; $i<count($view['file']); $i++) {
	if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
		if ($board['bo_download_point'] < 0 && $j == 0) {
			$attach_list .= '<a class="list-group-item"><i class="fa fa-bell red"></i> 다운로드시 <b>'.number_format(abs($board['bo_download_point'])).'</b>'.AS_MP.' 차감 (최초 1회 / 재다운로드시 차감없음)</a>'.PHP_EOL;
		}
		$file_tooltip = '';
		if($view['file'][$i]['content']) {
			$file_tooltip = ' data-original-title="'.strip_tags($view['file'][$i]['content']).'" data-toggle="tooltip"';
		}
		$attach_list .= '<a class="list-group-item break-word view_file_download at-tip" href="'.$view['file'][$i]['href'].'"'.$file_tooltip.'>';
		$attach_list .= '<span class="label label-primary pull-right view-cnt">'.number_format($view['file'][$i]['download']).'</span>';
		$attach_list .= '<i class="fa fa-download"></i> '.$view['file'][$i]['source'].' ('.$view['file'][$i]['size'].') &nbsp;';
		$attach_list .= '<span class="en font-11 text-muted"><i class="fa fa-clock-o"></i> '.apms_datetime(strtotime($view['file'][$i]['datetime']), "Y.m.d").'</span></a>'.PHP_EOL;
		$j++;
	}
}

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css" media="screen">', 0);

?>
<?php if($boset['video']) { ?>
	<style>.view-wrap .apms-autowrap { max-width:<?php echo (G5_IS_MOBILE) ? '100%' : $boset['video'];?> !important;}</style>
<?php } ?>
<style>
.img-tag {
    width: 100%;
    max-width:400px;
    border: 0;
}


.auction_rule {
	margin: 10px 0px;
	border: 1px solid #ddd;
	text-align:left;
}
.auction_rule .subtitle {
    margin: 10px 0px;
	font-size:15px;
	font-weight:bold;
	padding-left: 20px;
    color: #a4a4a4;
	text-align:left;
}
.auction_rule .warning {
	list-style-type:none;
	text-align:left;
    line-height: 20px;
    margin: 10px 0px;
	box-sizing: border-box;
	font-family: "Poppins", "NanumBarunGothic", sans-serif;
}
.auction_rule .warning li {
	list-style-type:none;
	padding: 0 15px;
	text-align:left;
    line-height: 20px;
    margin:0px;
	box-sizing: border-box;
	font-family: "Poppins", "NanumBarunGothic", sans-serif;
}

</style>
<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<div class="view-wrap<?php echo (G5_IS_MOBILE) ? ' view-mobile font-14' : '';?>">



        <div style="padding:5px;border:1px solid #E0E0E0;"><!-- div1 -->
        <table border="0" cellpadding="0" cellspacing="0" style="width:100%;font-size:1.10em;"><!-- table1 -->
        <tr><!-- table1 tr1 -->
        <td align="center" valign="middle" style="width:48%;height:100%;background:#f6f6f6;">
        <div style="overflow:hidden;margin:0 auto; padding:0px;">
		<?php
            // 이미지 상단 출력
            $v_img_count = count($view['file']);
            if($v_img_count && $is_img_head) {
                echo '<div class="view-img">'.PHP_EOL;
                for ($i=0; $i<=count($view['file']); $i++) {
                    if ($view['file'][$i]['view']) {
                        echo get_view_thumbnail($view['file'][$i]['view']);
                    }
                }
                echo '</div>'.PHP_EOL;
            }
         ?>
         </div>
     </td>
        <td align="left" valign="top" style="width:52%;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td style="background:#f6f6f6;"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:10px;">
              <tr>
                <td width="65" height="30"><span style="font-weight:bold; height:30px;">제 조 사</span></td>
                <td><span style="font-weight:bold;"><?php echo $info[company];?></span></td>
              </tr>
              <tr>
                <td height="30"><span style="font-weight:bold; height:30px;">제 품 명</span></td>
                <td><span style="font-weight:bold;"><?php echo $info[product]; ?></span></td>
              </tr>
            </table></td>
          </tr>
          
          <tr><td colspan="2" style="border-top:1px solid #E0E0E0;height:<?php echo($border_td_height);?>px;"></td></tr>
          
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:10px;">
              <tr>
                <td height="30"width="75"><span style="font-weight:bold; height:28px;">시 간</span></td>
                <td><?php echo($info['start_datetime']);?> ~ <?php echo($info['end_datetime']);?></td>
              </tr>
              <tr>
                <td height="30"><span style="font-weight:bold; height:28px;">남은시간</span></td>
                <td><span id=end_timer></span></td>
              </tr>
              <tr>
                <td height="30"><span style="font-weight:bold; height:28px;">수량</span></td>
                <td><span style="font-size:1.20em;color:red; font-weight:bold;"><?php echo number_format($info[tender_lower])?> ~ <?php echo number_format($info[tender_higher])?></span></td>
              </tr>
              <tr>
                <td height="30"><span style="font-weight:bold; height:28px;">참여횟수</span></td>
                <td><?php echo(number_format($info[tender_count]));?>회 참여 <font style="color:#999;">(조회<?php echo number_format($view['wr_hit']) ?>회)</font>
                <font style="color:#09F;font-weight:bold;">총 수량 <font style="color:#ff0000;font-weight:bold;"><?php echo number_format($info[tender_lower])?></font> 이상 참여시 공동구매 성공</font></td>
              </tr>
            </table></td>
          </tr>
          <tr><td colspan="2" style="border-top:1px solid #E0E0E0;height:<?php echo($border_td_height);?>px;"></td></tr>
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:10px;">
              <tr>
                <td height="30"width="75"><span style="font-weight:bold;">배송</span></td>
                <td><?php echo $view[ca_name]?></td>
              </tr>
              <tr>
                <td height="30"><span style="font-weight:bold;">상태</span></td>
                <td>
				    <?php if ($info[status] == 3) { ?>
                    <span style="color:#888; font-weight:bold;">실패 햇습니다.</span>
                    <?php } else if ($info[status] == 2) { ?>
                    <span style="color:#950000; font-weight:bold;">종료 되었습니다.</span> <span style="color:red; font-weight:bold;">(참여하신분 관리자한테 쪽지주세요.)</span>
                    <?php } else if ($info[status] == 1) { ?>
                    <span style="color:#009520; font-weight:bold;">참여 가능</span>
                    <?php } else if ($info[status] == 0) { ?>
                    <span style="color:#888; font-weight:bold;">시작전 입니다.</span>
                    <?php } ?>
                </td>
              </tr>
              <!-------------즉시 구매가----------------
              <tr>
                <td height="30"><span style="font-weight:bold; height:28px;">경매상태</span></td>
                <td><span style="color:<?php echo($auction_status_color);?>; font-weight:bold;"><?php echo($auction_status);?></span></td>
              </tr>
              
              <tr>
                <td height="30"><span style="font-weight:bold; height:28px;">즉시구매가</span></td>
                <td><?php if($info['buy_now_price'] > 0){ ?>
                  <span style="font-size:1.20em;color:green;"><?php echo(number_format($info['buy_now_price']));?></span>&nbsp;원
                  <?php } else {echo("없음");}?></td>
              </tr>
              -------------즉시 구매가----------------->
            </table></td>
          </tr>
          <tr><td style="border-top:1px solid #E0E0E0;height:<?php echo($border_td_height);?>px;"></td></tr>
          <tr>
            <td><table style="width:100%; margin:0px;">
              <!-- table2 -->
              <tr>
                <td height="35" align="center" style="color:black; height:35px;">
                <span style="font-weight:bold; height:28px;">참여 포인트
                <span style="font-size:1.20em;color:red; font-weight:bold;"><?php echo $view[wr_3]?></span>&nbsp;포인트</span>
				</td>
              </tr>
              <tr>
                <td align="center" style="color:black;background:#f6f6f6;">
                  <!-- 입찰 폼 -->
        <form name="auction_tender" id="auction_tender" method="post" action="<?php echo $board_skin_url?>/tender.php" style="margin:18px 0 0 0; float:left;">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table?>">
            <input type="hidden" name="wr_id" value="<?php echo $wr_id?>">
            <input type="hidden" name="point" id="point" value="">
        </form>
<?php if ($info[status] == '0') { ?>                
<div style=" text-align:center; height:50px; line-height:50px;font-weight:bold;">
    공동구매는 <font style="color:#F00;"><?php echo date("Y년 m월 d일 H시 i분", strtotime($info[start_datetime]))?></font></u> 에 시작됩니다.
</div>   
<?php }?>     
        
<!-- 입찰 상태 -->
<?php if($info[status] == '1'){ //입찰폼?>
<div style="display:inline;padding:5px;"> <a href="#none" onclick="tender_send()" class="tender-btn t-btn"><i class="fa fa-gavel" aria-hidden="true"></i> 참여하기</a>
<?php } //입찰폼?>
<!-- 입찰 내역 -->
<?php if ($info[tender_count]) { ?>
<div style="display:inline;padding:5px;"> <a href="#nonw" onclick="tender_list()" class="tender-li t-btn "><i class="fa fa-list-ul" aria-hidden="true"></i> 참여내역</a></div>
<?php }?>
<!-- 입찰 폼 -->

<script>
jQuery.fn.center = function () { 
    this.css("position", "absolute"); 
    this.css("top", ( $(window).height() - this.height() ) / 2+$(window).scrollTop() + "px"); 
    this.css("left", ( $(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px"); 
    return this; 
}

$(function() {
    // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
    $( "#dialog:ui-dialog" ).dialog( "destroy" );

    $("#btn_tender_section").click(function() {
        $( "#dialog" ).dialog({
            width: 500,
            height: 500,
            modal: true
        });
    });
});
</script>
                  </td>
              </tr>
            </table></td>
          </tr>
        </table>          <!-- 입찰세부정부 --><!-- table2 -->        </td>
        <!-- 입찰세부정부 -->
        </tr><!-- table1 tr1 -->
        </table><!-- table1 -->
        </div><!-- div1 -->
        <!-- ////////////경매정보////////////  -->
        
        
        <!-- ////////////경매규칙////////////  -->
        <div class="auction_rule">
            <div class="subtitle">공동구매 규칙</div>
            <div class="warning">
                    <li>1. 한 번 참여한 포인트는 공동구매 실패 하지 않을경우 반환되지 않습니다. </li>
                    <li>2. 본 공동구매는 한 회원당 (<font style="color:#F00;font-weight:bold;"><?php echo $view[wr_6];?></font>)번 참여 하실 수 있습니다.</li>
                    <li>3. 본 공동구매는 최저 총 수량 (<font style="color:#ff0000;font-weight:bold;"><?php echo number_format($info[tender_lower])?></font>) 이상 참여시 공동구매 성공 합니다. 총 수량 (<font style="color:#F00;font-weight:bold;"><?php echo number_format($info[tender_lower])?></font>) 미만시 이번 공동구매는 실패됩니다.</font></li>
                    <li>4. 공동구매 실패시 참여하셨던 포인트는 자동 반환 됩니다.</li>
                    <li>5. 공동구매 성공시 관라지 한테 쪽지로 상세주소 및 연락처를 발송 해 주세요. <a href="javascript:win_memo('custumer');"><font style="color:#F00; font-size:14px; font-weight:bold;">[관리자쪽지 발송]</font></a></li>
            </div>
        </div>
        <!-- ////////////경매규칙////////////  -->

<script>
/**
 * 쪽지 창 , 제휴 문의 관리자
 **/
var win_memo = function(href) {
    
    if (href == "custumer") href =  g5_bbs_url + "/memo_form.php?me_recv_mb_id=admin";
	
    var new_win = window.open(href, 'win_memo', 'left=100,top=100,width=620,height=610,scrollbars=1');
    new_win.focus();
}
</script>
		<div class="div-separator"><span class="div-sep-icon"><i class="fa fa-chevron-down"></i></span></div>
        <div class="div-sep-line"></div>
        
		<div id="bo_v_con"><?php echo get_view_thumbnail($view['content']); ?></div>

	<?php if ($good_href || $nogood_href) { ?>
		<div class="print-hide view-good-box">
			<?php if ($good_href) { ?>
				<span class="view-good">
					<a href="#" onclick="apms_good('<?php echo $bo_table;?>', '<?php echo $wr_id;?>', 'good', 'wr_good'); return false;">
						<b id="wr_good"><?php echo number_format($view['wr_good']) ?></b>
						<br>
						<i class="fa fa-thumbs-up"></i>
					</a>
				</span>
			<?php } ?>
			<?php if ($nogood_href) { ?>
				<span class="view-nogood">
					<a href="#" onclick="apms_good('<?php echo $bo_table;?>', '<?php echo $wr_id;?>', 'nogood', 'wr_nogood'); return false;">
						<b id="wr_nogood"><?php echo number_format($view['wr_nogood']) ?></b>
						<br>
						<i class="fa fa-thumbs-down"></i>
					</a>
				</span>
			<?php } ?>
		</div>
		<p></p>
	<?php } ?>

	<?php if ($is_tag) { // 태그 ?>
		<p class="view-tag font-12"><i class="fa fa-tags"></i> <?php echo $tag_list;?></p>
	<?php } ?>

	<div class="print-hide view-icon">
		<div class="pull-right">
			<div class="form-group">
				<button onclick="apms_print();" class="btn btn-black btn-xs"><i class="fa fa-print"></i> <span class="hidden-xs">프린트</span></button>
				<?php if ($scrap_href) { ?>
					<a href="<?php echo $scrap_href;  ?>" target="_blank" class="btn btn-black btn-xs" onclick="win_scrap(this.href); return false;"><i class="fa fa-tags"></i> <span class="hidden-xs">스크랩</span></a>
				<?php } ?>
				<?php if ($is_shingo) { ?>
					<button onclick="apms_shingo('<?php echo $bo_table;?>', '<?php echo $wr_id;?>');" class="btn btn-black btn-xs"><i class="fa fa-bell"></i> <span class="hidden-xs">신고</span></button>
				<?php } ?>
				<?php if ($is_admin) { ?>
					<?php if ($view['is_lock']) { // 글이 잠긴상태이면 ?>
						<button onclick="apms_shingo('<?php echo $bo_table;?>', '<?php echo $wr_id;?>', 'unlock');" class="btn btn-black btn-xs"><i class="fa fa-unlock"></i> <span class="hidden-xs">해제</span></button>
					<?php } else { ?>
						<button onclick="apms_shingo('<?php echo $bo_table;?>', '<?php echo $wr_id;?>', 'lock');" class="btn btn-black btn-xs"><i class="fa fa-lock"></i> <span class="hidden-xs">잠금</span></button>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
		<div class="pull-left">
			<div class="form-group">
				<?php include_once(G5_SNS_PATH."/view.sns.skin.php"); // SNS ?>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<?php if($is_signature) echo apms_addon('sign-basic'); // 회원서명 ?>

	<h3 class="view-comment">Comments</h3>
	<?php include_once('./view_comment.php'); ?>

	<div class="clearfix"></div>

	<div class="print-hide view-btn text-right">
		<div class="btn-group">
			<?php if ($prev_href) { ?>
				<a href="<?php echo $prev_href ?>" class="btn btn-black btn-sm" title="이전글">
					<i class="fa fa-chevron-circle-left"></i><span class="hidden-xs"> 이전</span>
				</a>
			<?php } ?>
			<?php if ($next_href) { ?>
				<a href="<?php echo $next_href ?>" class="btn btn-black btn-sm" title="다음글">
					<i class="fa fa-chevron-circle-right"></i><span class="hidden-xs"> 다음</span>
				</a>
			<?php } ?>
			<?php if ($copy_href) { ?>
				<a href="<?php echo $copy_href ?>" class="btn btn-black btn-sm" onclick="board_move(this.href); return false;" title="복사">
					<i class="fa fa-clipboard"></i><span class="hidden-xs"> 복사</span>
				</a>
			<?php } ?>
			<?php if ($move_href) { ?>
				<a href="<?php echo $move_href ?>" class="btn btn-black btn-sm" onclick="board_move(this.href); return false;" title="이동">
					<i class="fa fa-share"></i><span class="hidden-xs"> 이동</span>
				</a>
			<?php } ?>
			<?php if ($delete_href) { ?>
				<a href="<?php echo $delete_href ?>" class="btn btn-black btn-sm" title="삭제" onclick="del(this.href); return false;">
					<i class="fa fa-times"></i><span class="hidden-xs"> 삭제</span>
				</a>
			<?php } ?>
			<?php if ($update_href) { ?>
				<a href="<?php echo $update_href ?>" class="btn btn-black btn-sm" title="수정">
					<i class="fa fa-plus"></i><span class="hidden-xs"> 수정</span>
				</a>
			<?php } ?>
			<?php if ($search_href) { ?>
				<a href="<?php echo $search_href ?>" class="btn btn-black btn-sm">
					<i class="fa fa-search"></i><span class="hidden-xs"> 검색</span>
				</a>
			<?php } ?>
			<a href="<?php echo $list_href ?>" class="btn btn-black btn-sm">
				<i class="fa fa-bars"></i><span class="hidden-xs"> 목록</span>
			</a>
			<?php if ($reply_href) { ?>
				<a href="<?php echo $reply_href ?>" class="btn btn-black btn-sm">
					<i class="fa fa-comments"></i><span class="hidden-xs"> 답변</span>
				</a>
			<?php } ?>
			<?php if ($write_href) { ?>
				<a href="<?php echo $write_href ?>" class="btn btn-color btn-sm">
					<i class="fa fa-pencil"></i><span class="hidden-xs"> 글쓰기</span>
				</a>
			<?php } ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<script language="JavaScript">
function file_download(link, file) {
    <?php if ($board[bo_download_point] < 0) { ?>if (confirm("'"+file+"' 파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board[bo_download_point])?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?"))<?}?>
    document.location.href=link;
}

function tender_send() {
    var p = document.getElementById("point").value;
/*
    if (!p) {
        alert("포인트를 입력해주세요.");
        return;
    }
*/

    if (confirm("정말 참여하시겠습니까?")) {
        document.auction_tender.submit();
    }
}

function tender_list() {
    tender_list_win = window.open("<?php echo $board_skin_url?>/tender_list.php?bo_table=<?php echo $bo_table?>&wr_id=<?php echo $wr_id?>","tender_list","width=500, height=800, scrollbars=1");
    tender_list_win.focus();
}
</script>

<?php if ($info[status] == 1 && $end_time > 0) {?>

<script language="JavaScript">

var end_time = <?php echo $end_time?>;

function run_timer() {
    var timer = document.getElementById("end_timer");

    dd = Math.floor(end_time/(60*60*24));
    hh = Math.floor((end_time%(60*60*24))/(60*60));
    mm = Math.floor(((end_time%(60*60*24))%(60*60))/60);
    ii = Math.floor((((end_time%(60*60*24))%(60*60))%60));

    var str = "";

    if (dd > 0) str += dd + "일 ";
    if (hh > 0) str += hh + "시간 ";
    if (mm > 0) str += mm + "분 ";
    str += ii + "초 ";

    timer.style.color = "red";
    timer.style.fontWeight = "bold";
    timer.innerHTML = str;

    end_time--;

    if (end_time < 0) clearInterval(tid);
}

run_timer();

tid = setInterval('run_timer()', 1000); 

</script>

<?php } ?>

<script>
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

/*
    // 추천, 비추천
    $("#good_button, #nogood_button").click(function() {
        var $tx;
        if(this.id == "good_button")
            $tx = $("#bo_v_act_good");
        else
            $tx = $("#bo_v_act_nogood");

        excute_good(this.href, $(this), $tx);
        return false;
    });
*/

    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();
});
</script>
