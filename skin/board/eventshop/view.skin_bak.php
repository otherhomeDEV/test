<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
include_once("$board_skin_path/auction.lib.php");//경매정보

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css?ver='.G5_CSS_VER.'">', 0);//add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_javascript('<script src="'.G5_JS_URL.'/viewimageresize.js?ver='.G5_JS_VER.'"></script>');

/////////////경매정보////////////////////
// 경매정보 읽어오기
$info = get_info_auction($wr_id);

// 낙찰
if ($info[status] == "2") {$success_member = get_member($info[mb_id]);}

$end_time = strtotime($info[end_datetime])-G5_SERVER_TIME;

//if ($is_admin){
    // 명수
    $sql = "select count( distinct mb_id ) as cnt from $tender_table where wr_id = '$wr_id' ";
    $row = sql_fetch($sql);

    $tender_mb_id_count = number_format(intval($row[cnt]));

    // 최고|최저 입찰된 내역을 조회 (현재 1위)
    $super = auction_super_point($info);
    // 최고|최저 입찰된 내역을 조회 (현재 1위)

    $qry = sql_query(" select mb_id from $tender_table where td_tender_point = '$super[point]' and wr_id = '$wr_id' ");
    while ($row = sql_fetch_array($qry)){$super_mb_id[] = $row[mb_id];}
    //foreach($super_mb_id as $mb_id){$mb = get_member($mb_id);echo get_sideview($mb[mb_id], $mb[mb_nick], $mb[mb_email], $mb[mb_homepage]);}//원하는 곳에 사용
    //foreach($super_mb_id as $mb_id){$mb = get_member($mb_id);echo "(입찰자 : {$mb[mb_nick]})";}//원하는 곳에 사용
//}

//이미지 출력 //print_r2($view[file]); // 상품이미지 (중)
$image = urlencode($view[file][1][file]); 
//이미지 출력
if(preg_match("/\.(gif|jpg|png)$/i", $image)) {$img_1 = G5_DATA_URL.'/file/'.$bo_table.'/'.$image;} else {$img_1 = $board_skin_url.'/img/noimg_view.gif';}

$auction_status=""; $auction_status_color="";//고대영 추가 초기화
$tender_status=""; $tender_list=0;//고대영 추가 초기화

switch ($info[status]) {
        case 0: // 시작전
                //경매정보
                $auction_status = auction_status($info[status])." 입니다.";
                $auction_status_color = "#888";
                //경매정보
                //입찰정보
                $tender_status="[경매 ".auction_status($info[status])."] <strong>".date("Y년 m월 d일 H시 i분", strtotime($info[start_datetime]))."</strong> 에 시작됩니다.";
                //입찰정보
                break;
        case 1: // 진행중
                //경매정보
                $auction_status ="입찰가능";
                $auction_status_color = "#009520";
                //경매정보
                //입찰정보
                if($info[tender_count]){$tender_list=1;} else {$tender_list=0;};
                //입찰정보
                break;
        case 2: // 낙찰
                //경매정보
                $auction_status = "".auction_status($info[status])."";
                $auction_status_color = "blue";
                //경매정보
                //입찰정보
                if($info[mb_id]){$mb = get_member($info[mb_id]); $tender_status="경매 낙찰자 : <span style='color:green;font-size:1.30em;'>".$mb[mb_nick]."</span>&nbsp;&nbsp;&nbsp;".$info['auction_off_method']." 입찰 금액 : <span style='color:red;font-size:1.30em;'>".number_format($info[auction_off_point])."원</span>";}
                if($info[tender_count]){$tender_list=1;} else {$tender_list=0;};
                //입찰정보
                break;
        case 3: // 유찰
                //경매정보
                $auction_status = "".auction_status($info[status])."";
                $auction_status_color = "red";
                //경매정보
                //입찰정보
                $tender_status="".auction_status($info[status])."";
                if($info[tender_count]){$tender_list=1;} else {$tender_list=0;};
                //입찰정보
                break;
}
/////////////경매정보////////////////////
?>


<!-- 게시물 읽기 시작 { -->
<!-- <div id="bo_v_table"><?php echo $board['bo_subject']; ?></div> //고대영 처리 -->

<article id="bo_v" style="width:<?php echo $width; ?>">
    <header>
        <h1 id="bo_v_title">
            <?php
            if ($category_name) echo $view['ca_name'].' | '; // 분류 출력 끝
            //echo cut_str(get_text($view['wr_subject']), 70); // 글제목 출력
            ////경매정보
            echo get_text($info['company']).' | ';; // 글제목 출력
            echo get_text($info['product']); // 글제목 출력
            ////경매정보
            ?>
        </h1>
    </header>
    

    <section id="bo_v_info">
        <h2>페이지 정보</h2>
        작성자 <strong><?php echo $view['name'] ?><?php if ($is_ip_view) { echo "&nbsp;($ip)"; } ?></strong>
        <span class="sound_only">작성일</span><strong><?php echo date("y-m-d H:i", strtotime($view['wr_datetime'])) ?></strong>
        조회<strong><?php echo number_format($view['wr_hit']) ?>회</strong>
        댓글<strong><?php echo number_format($view['wr_comment']) ?>건</strong>
    </section>

    <?php
    if ($view['file']['count']) {
        $cnt = 0;
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
                $cnt++;
        }
    }
     ?>

    <?php if($cnt=0) { //고대영처리-감추기 ?>
    <!-- 첨부파일 시작 { -->
    <section id="bo_v_file">
        <h2>첨부파일</h2>
        <ul>
        <?php
        // 가변 파일
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
         ?>
            <li>
                <a href="<?php echo $view['file'][$i]['href'];  ?>" class="view_file_download">
                    <img src="<?php echo $board_skin_url ?>/img/icon_file.gif" alt="첨부">
                    <strong><?php echo $view['file'][$i]['source'] ?></strong>
                    <?php echo $view['file'][$i]['content'] ?> (<?php echo $view['file'][$i]['size'] ?>)
                </a>
                <span class="bo_v_file_cnt"><?php echo $view['file'][$i]['download'] ?>회 다운로드</span>
                <span>DATE : <?php echo $view['file'][$i]['datetime'] ?></span>
            </li>
        <?php
            }
        }
         ?>
        </ul>
    </section>
    <!-- } 첨부파일 끝 -->
    <?php } ?>

    <?php if ($view['link']=0) { //고대영처리-감추기 ?>
     <!-- 관련링크 시작 { -->
    <section id="bo_v_link">
        <h2>관련링크</h2>
        <ul>
        <?php
        // 링크
        $cnt = 0;
        for ($i=1; $i<=count($view['link']); $i++) {
            if ($view['link'][$i]) {
                $cnt++;
                $link = cut_str($view['link'][$i], 70);
         ?>
            <li>
                <a href="<?php echo $view['link_href'][$i] ?>" target="_blank">
                    <img src="<?php echo $board_skin_url ?>/img/icon_link.gif" alt="관련링크">
                    <strong><?php echo $link ?></strong>
                </a>
                <span class="bo_v_link_cnt"><?php echo $view['link_hit'][$i] ?>회 연결</span>
            </li>
        <?php
            }
        }
         ?>
        </ul>
    </section>
    <!-- } 관련링크 끝 -->
    <?php } ?>

    <!-- 게시물 상단 버튼 시작 { -->
    <div id="bo_v_top">
        <?php
        ob_start();
         ?>
        <?php if ($prev_href || $next_href) { ?>
        <ul class="bo_v_nb">
            <?php if ($prev_href) { ?><li><a href="<?php echo $prev_href ?>" class="btn_b01">이전글</a></li><?php } ?>
            <?php if ($next_href) { ?><li><a href="<?php echo $next_href ?>" class="btn_b01">다음글</a></li><?php } ?>
        </ul>
        <?php } ?>

        <ul class="bo_v_com">
            <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>" class="btn_b01">수정</a></li><?php } ?>
            <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" class="btn_b01" onclick="del(this.href); return false;">삭제</a></li><?php } ?>
            <?php if ($copy_href) { ?><li><a href="<?php echo $copy_href ?>" class="btn_admin" onclick="board_move(this.href); return false;">복사</a></li><?php } ?>
            <?php if ($move_href) { ?><li><a href="<?php echo $move_href ?>" class="btn_admin" onclick="board_move(this.href); return false;">이동</a></li><?php } ?>
            <?php if ($search_href) { ?><li><a href="<?php echo $search_href ?>" class="btn_b01">검색</a></li><?php } ?>
            <li><a href="<?php echo $list_href ?>" class="btn_b01">목록</a></li>
            <?php if ($reply_href) { ?><li><a href="<?php echo $reply_href ?>" class="btn_b01">답변</a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">글쓰기</a></li><?php } ?>
        </ul>
        <?php
        $link_buttons = ob_get_contents();
        ob_end_flush();
         ?>
    </div>
    <!-- } 게시물 상단 버튼 끝 -->
<style>
.img-tag {
    width: 100%;
    max-width:400px;
    border: 0;
}
</style>
    <section id="bo_v_atc">
        <h2 id="bo_v_atc_title">본문</h2>
      <!-- ////////////경매정보////////////  -->
        <?php
        $border_td_height="2";//구분 td
        ?>
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
                <td><span style="font-weight:bold;"><?php echo($info['company']);?></span></td>
              </tr>
              <tr>
                <td height="30"><span style="font-weight:bold; height:30px;">상 품 명</span></td>
                <td><span style="font-weight:bold;"><?php echo($info['product']);?></span></td>
              </tr>
            </table></td>
          </tr>
          
          <tr><td colspan="2" style="border-top:1px solid #E0E0E0;height:<?php echo($border_td_height);?>px;"></td></tr>
          
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:10px;">
              <tr>
                <td height="30"width="75"><span style="font-weight:bold; height:28px;">경 매 일</span></td>
                <td><?php echo($info['start_datetime']);?> ~ <?php echo($info['end_datetime']);?></td>
              </tr>
              <tr>
                <td height="30"><span style="font-weight:bold; height:28px;">남은시간</span></td>
                <td><span id=end_timer></span></td>
              </tr>
              <tr>
                <td height="30"><span style="font-weight:bold; height:28px;">입찰단위</span></td>
                <td><span style="font-size:1.20em;color:red; font-weight:bold;"><?php echo(number_format($info['tender_unit']));?></span>&nbsp;포인트</td>
              </tr>
              <tr>
                <td height="30"><span style="font-weight:bold; height:28px;">입찰횟수</span></td>
                <td><?php if($is_admin){echo(number_format($tender_mb_id_count)." 명, ");}?>
                  <?php echo(number_format($info[tender_count]));?>회 참여 <font style="color:#999;">(조회<?php echo number_format($view['wr_hit']) ?>회)</font></td>
              </tr>
              <!-------------즉시 구매가----------------
              <tr>
                <td height="30"><span style="font-weight:bold; height:28px;">즉시구매가</span></td>
                <td><?php if($info['buy_now_price'] > 0){ ?>
                  <span style="font-size:1.20em;color:green;"><?php echo(number_format($info['buy_now_price']));?></span>&nbsp;원
                  <?php } else {echo("없음");}?></td>
              </tr>
              -------------즉시 구매가----------------->
            </table></td>
          </tr>
          <tr><td colspan="2" style="border-top:1px solid #E0E0E0;height:<?php echo($border_td_height);?>px;"></td></tr>
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:10px;">
              <tr>
                <td height="30"width="75"><span style="font-weight:bold;">배송방법</span></td>
                <td><?php echo($info['delivery_method']);?></td>
              </tr>
              <tr>
                <td height="30"><span style="font-weight:bold;">배송비용</span></td>
                <td><?php if(preg_match("/[0-9]/",$info['delivery_price'])){echo(number_format(intval($info['delivery_price']))."&nbsp;원");} else {echo($info['delivery_price']);}?></td>
              </tr>
              <tr>
                <td height="30"><span style="font-weight:bold; height:28px;">경매상태</span></td>
                <td><span style="color:<?php echo($auction_status_color);?>; font-weight:bold;"><?php echo($auction_status);?></span></td>
              </tr>
              <!-------------즉시 구매가----------------
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
					<?php if($super[point] && ($info[status]=='1')){ ?>
					<?php if($super[count]){?>
                    현재&nbsp;<?php echo($info['auction_off_method']);?>
                    <?php } else {echo("시작가");}?>
                    &nbsp;<span style="font-size:1.50em;color:red;font-weight:bold;"><?php echo(number_format($super[point]));?></span>&nbsp;포인트
                    
                    <?php if($super[count] && ($info[tender_method]=="공개" || $is_admin)){foreach($super_mb_id as $mb_id){$mb = get_member($mb_id);echo("<span style='color:black;font-size:0.70em;font-weight:normal;'>&nbsp;(최종입찰자: <span style='color:black;font-size:0.70em;font-weight:bold;'>".$mb['mb_name']."</span>)</span>");}}?>
                    <?php } ?>
				</td>
              </tr>
              <tr>
                <td align="center" style="color:black;background:#f6f6f6;">
                  <!-- 입찰 폼 -->
                  <form name="auction_tender" id="auction_tender" method="post" action="<?php echo $board_skin_url; ?>/tender.php" onkeydown="if(event.keyCode==13) return false;" onsubmit="return tender_send();" target="hiddenframe">
                    <input type="hidden" name="bo_table" value="<?=$bo_table?>" />
                    <input type="hidden" name="wr_id" value="<?=$wr_id?>" />
                    <input type="hidden" name="tender_unit" id="tender_unit" value="<?php echo($info['tender_unit']);?>" />
                    <!-- 입찰 상태 -->
                    <?php if($info[status] == '1'){ //입찰폼?>
                    <div style="display:inline;padding:5px;">
                      <input type="number" name="point" id="point" value="입찰 금액" class="tender-w-num" required="required" numeric="numeric" itemname="입찰 금액" step="<?php echo($info['tender_unit']);?>" min="<?php if($info['tender_lower']){echo($info['tender_lower']);} else {echo($info['tender_lower']);}?>" max="<?php if($info['tender_higher']){echo($info['tender_higher']);}?>" />
                    </div>
                    <div style="display:inline;padding:5px;"> <a href="#none" onclick="tender_send()" class="tender-btn t-btn"><i class="fa fa-gavel" aria-hidden="true"></i> 입찰하기</a></div>
                    <?php } //입찰폼?>
                    <!-- 입찰 내역 -->
                    <?php if($tender_list) {?>
                    <div style="display:inline;padding:5px;"> <a href="#nonw" onclick="tender_list()" class="tender-li t-btn "><i class="fa fa-list-ul" aria-hidden="true"></i> 입찰내역</a></div>
                    <?php }?>
                  </form>
                  <!-- 입찰 폼 --></td>
              </tr>
            </table></td>
          </tr>
        </table>          <!-- 입찰세부정부 --><!-- table2 -->        </td>
        <!-- 입찰세부정부 -->
        </tr><!-- table1 tr1 -->


        </table><!-- table1 -->
        </div><!-- div1 -->
        <!-- ////////////경매정보////////////  -->
        <div style="width:100%; height:40px; background:#CCC;"></div>
        <!-- 본문 내용 시작 { -->
        <div id="bo_v_con"><?php echo get_view_thumbnail($view['content']); ?></div>
        <?php //echo $view['rich_content']; // {이미지:0} 과 같은 코드를 사용할 경우 ?>
        <!-- } 본문 내용 끝 -->

        <?php if ($is_signature) { ?><p><?php echo $signature ?></p><?php } ?>

        <!-- 스크랩 추천 비추천 시작 { -->
        <?php if ($scrap_href || $good_href || $nogood_href) { ?>
        <div id="bo_v_act">
            <?php if ($scrap_href) { ?><a href="<?php echo $scrap_href;  ?>" target="_blank" class="btn_b01" onclick="win_scrap(this.href); return false;">스크랩</a><?php } ?>
            <?php if ($good_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo $good_href.'&amp;'.$qstr ?>" id="good_button" class="btn_b01">추천 <strong><?php echo number_format($view['wr_good']) ?></strong></a>
                <b id="bo_v_act_good"></b>
            </span>
            <?php } ?>
            <?php if ($nogood_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo $nogood_href.'&amp;'.$qstr ?>" id="nogood_button" class="btn_b01">비추천  <strong><?php echo number_format($view['wr_nogood']) ?></strong></a>
                <b id="bo_v_act_nogood"></b>
            </span>
            <?php } ?>
        </div>
        <?php } else {
            if($board['bo_use_good'] || $board['bo_use_nogood']) {
        ?>
        <div id="bo_v_act">
            <?php if($board['bo_use_good']) { ?><span>추천 <strong><?php echo number_format($view['wr_good']) ?></strong></span><?php } ?>
            <?php if($board['bo_use_nogood']) { ?><span>비추천 <strong><?php echo number_format($view['wr_nogood']) ?></strong></span><?php } ?>
        </div>
        <?php
            }
        }
        ?>
        <!-- } 스크랩 추천 비추천 끝 -->
    </section>

    <?php
    include_once(G5_SNS_PATH."/view.sns.skin.php");
    ?>

    <?php
    // 코멘트 입출력
    include_once(G5_BBS_PATH.'/view_comment.php');
     ?>

    <!-- 링크 버튼 시작 { -->
    <div id="bo_v_bot">
        <?php echo $link_buttons ?>
    </div>
    <!-- } 링크 버튼 끝 -->

</article>
<!-- } 게시판 읽기 끝 -->

<script>
<?php if ($board['bo_download_point'] < 0) { ?>
$(function() {
    $("a.view_file_download").click(function() {
        if(!g5_is_member) {
            alert("다운로드 권한이 없습니다.\n회원이시라면 로그인 후 이용해 보십시오.");
            return false;
        }

        var msg = "파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board['bo_download_point']) ?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?";

        if(confirm(msg)) {
            var href = $(this).attr("href")+"&js=on";
            $(this).attr("href", href);

            return true;
        } else {
            return false;
        }
    });
});
<?php } ?>

function board_move(href)
{
    window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
}
</script>

<script>
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

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

    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();
});

function excute_good(href, $el, $tx)
{
    $.post(
        href,
        { js: "on" },
        function(data) {
            if(data.error) {
                alert(data.error);
                return false;
            }

            if(data.count) {
                $el.find("strong").text(number_format(String(data.count)));
                if($tx.attr("id").search("nogood") > -1) {
                    $tx.text("이 글을 비추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                } else {
                    $tx.text("이 글을 추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                }
            }
        }, "json"
    );
}
</script>
<!-- } 게시글 읽기 끝 -->

<!-- ///////////경매정보//////////////// -->
<script>
function tender_send() {
    var p = parseInt(document.getElementById("point").value);
    var u = parseInt(document.getElementById("tender_unit").value);

    if (!p) {
        alert("입찰 금액을 입력해주세요.");
        return;
    }

    if ((p % u) != 0) {
        alert("입찰 금액은 "+ u +" 단위로 입력해주세요.");
        return;
    }

    if (confirm("정말 입찰하시겠습니까?")) {
        document.auction_tender.submit();
    }
}

function tender_list() {
    tender_list_win = window.open("<?php echo $board_skin_url?>/tender_list.php?bo_table=<?php echo $bo_table?>&wr_id=<?php echo $wr_id?>","tender_list","width=500, height=800, scrollbars=1");
    tender_list_win.focus();
}

<?php if ($info[status] == 1 && $end_time > 0) {?>

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

    timer.style.color = "#1955ff";
    timer.style.fontWeight = "bold";
    timer.innerHTML = str;

    end_time--;

    if (end_time < 0) clearInterval(tid);
}

run_timer();

tid = setInterval('run_timer()', 1000); 
<?php } ?>

</script>

<iframe width=0 height=0 name='hiddenframe' style='display:none;'></iframe>
<!-- ///////////경매정보//////////////// -->
