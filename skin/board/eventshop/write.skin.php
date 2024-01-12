<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

include_once("$board_skin_path/auction.lib.php");

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

if ($w == '') {
    $write[wr_1] = date("Y-m-d 00:00:00", G5_SERVER_TIME + 86400 * 2);
    //$write[wr_2] = date("Y-m-d H:i:s", strtotime($write[wr_1]) + 86400 * 7);
    //$write[wr_2] = date("Y-m-d 12:30:00", strtotime($write[wr_1]) + 86400 * 7);
    $write[wr_2] = date("Y-m-d 12:30:00", strtotime($write[wr_1]) + 86400 * 7);

    //$write[wr_3] = $board[bo_1]; // 참여 포인트 기본값
    $write[wr_4] = $board[bo_2]; // 입찰 최소 포인트 기본값
    $write[wr_5] = $board[bo_3]; // 입찰 최대 포인트 기본값
    $write[wr_6] = $board[bo_4]; // 하루 참여 횟수 기본값
    $write[wr_7] = "0";
    $write[wr_8] = "0";
    $write[ca_name] = "무료배송";

    $possible_point = 10000;
    if ($member[mb_point] < $possible_point) {
        alert("회원의 포인트가 {$possible_point}점 이상이어야 포인트경매 상품등록이 가능합니다.");
    }
} else if ($w == 'u') {
    if (!$is_admin) {
        $sql = " select count(*) as cnt from $tender_table where wr_id = '$wr_id' ";
        $row = sql_fetch($sql);
        if ($row[cnt] > 10) {
            alert("입찰이 10회 초과이면 게시글 수정이 불가합니다.");
        }
    }

    list($subj1, $subj2) = explode('|', $write[wr_subject]);

    $subj1 = get_text($subj1);
    $subj2 = get_text($subj2);
}

?>
<style type="text/css">
.write_head { padding:5px 0 5px 20px; height:30px; background-color:#F9F9F9; width:130px; font-weight:bold; color:#000; font-family:dotum; line-height:15px; }
.write_main { padding:5px 0 5px 10px; }
.write_size { color:#999999; font-size:11px; font-weight:normal; margin-left:10px; }
</style>

<script type="text/javascript">
// 글자수 제한
var char_min = parseInt(<?=$write_min?>); // 최소
var char_max = parseInt(<?=$write_max?>); // 최대
</script>

<!-- form name="fwrite" method="post" onsubmit="return fwrite_check(this);" enctype="multipart/form-data" style="margin:0px;" -->
<form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" style="width:<?php echo $width; ?>">

<input type=hidden name=null> 
<input type=hidden name=w        value="<?=$w?>">
<input type=hidden name=bo_table value="<?=$bo_table?>">
<input type=hidden name=wr_id    value="<?=$wr_id?>">
<input type=hidden name=sca      value="<?=$sca?>">
<input type=hidden name=sfl      value="<?=$sfl?>">
<input type=hidden name=stx      value="<?=$stx?>">
<input type=hidden name=spt      value="<?=$spt?>">
<input type=hidden name=sst      value="<?=$sst?>">
<input type=hidden name=sod      value="<?=$sod?>">
<input type=hidden name=page     value="<?=$page?>">
<input type="hidden" name="wr_subject" id="subject_hidden" value="<?=$write[wr_subject]?>" />
<input type="hidden" name="wr_7" value="<?=$write[wr_7]?>" />
<input type="hidden" name="wr_8" value="<?=$write[wr_8]?>" />
<input type="hidden" name="wr_9" value="<?=$write[wr_9]?>" />
<input type="hidden" name="wr_10" value="<?=$write[wr_10]?>" />

<table width="<?=$width?>" align=center cellpadding=0 cellspacing=0><tr><td>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<colgroup width=100>
<colgroup width=''>
<tr><td colspan=2 height=2 bgcolor="#0A7299"></td></tr>
<tr><td style='padding-left:20px' colspan=2 height=38 bgcolor="#FBFBFB"><strong><?=$title_msg?></strong></td></tr>
<tr><td colspan="2" style="background:url(<?=$board_skin_path?>/img/title_bg.gif) repeat-x; height:3px;"></td></tr>
<? if ($is_name) { ?>
<tr>
    <td class=write_head>이름</td>
    <td class=write_main><input class="frm_input required" maxlength=20 size=15 name=wr_name itemname="이름" required value="<?=$name?>"></td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($is_password) { ?>
<tr>
    <td class=write_head>패스워드</td>
    <td class=write_main><input class="frm_input required" type=password maxlength=20 size=15 name=wr_password itemname="패스워드" <?=$password_required?>></td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($is_email) { ?>
<tr>
    <td class=write_head>이메일</td>
    <td class=write_main><input class="frm_input required" maxlength=100 size=50 name=wr_email email itemname="이메일" value="<?=$email?>"></td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($is_homepage) { ?>
<tr>
    <td class=write_head>홈페이지</td>
    <td class=write_main><input class="frm_input required" size=50 name=wr_homepage itemname="홈페이지" value="<?=$homepage?>"></td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<? if ($is_notice || $is_html || $is_secret || $is_mail) { ?>
<tr>
    <td class=write_head>옵션</td>
    <td class=write_main><? if ($is_notice) { ?><input type=checkbox name=notice value="1" <?=$notice_checked?>>공지&nbsp;<? } ?>
        <? if ($is_html) { ?>
            <? if ($is_dhtml_editor) { ?>
            <input type=hidden value="html1" name="html">
            <? } else { ?>
            <input onclick="html_auto_br(this);" type=checkbox value="<?=$html_value?>" name="html" <?=$html_checked?>><span class=w_title>html</span>&nbsp;
            <? } ?>
        <? } ?>
        <? if ($is_secret) { ?>
            <? if ($is_admin || $is_secret==1) { ?>
            <input type=checkbox value="secret" name="secret" <?=$secret_checked?>><span class=w_title>비밀글</span>&nbsp;
            <? } else { ?>
            <input type=hidden value="secret" name="secret">
            <? } ?>
        <? } ?>
        <? if ($is_mail) { ?><input type=checkbox value="mail" name="mail" <?=$recv_email_checked?>>답변메일받기&nbsp;<? } ?></td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<tr>
    <td class=write_head style="height:60px;">브랜드</td>
    <td class=write_main>
        <input class="frm_input required" maxlength="20" name="subj1" id="subj1" itemname="업체명" required value='<?=$subj1?>'>
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
    <td class=write_head style="height:60px;">상품명</td>
    <td class=write_main>
        <input class="frm_input required" maxlength="100" style="width:100%;" name="subj2" id="subj2" itemname="상품명" required value='<?=$subj2?>'>
        <div class=write_size style="line-height:25px;">상품명을 입력하세요. 예) 아이패드 32G</div>
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<!--
<tr>
    <td class=write_head>제공업체</td>
    <td><input class="frm_input required" style="width:100%;" name=wr_subject id="wr_11" itemname="제공업체" required value="<?=$write[wr_11]?>"></td></tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
-->
<tr>
    <td class=write_head>경매 시작일시</td>
    <td class=write_main>
        <input type=text size=20 name=wr_1 id=wr_1 value="<?=$write[wr_1]?>" itemname="경매 시작일시" required readonly>
        <? if ($is_admin) { ?>
        <input type=button value="오늘자정" onclick="document.getElementById('wr_1').value='<?=date("Y-m-d 00:00:00", G5_SERVER_TIME+60*60*24)?>';">
        <input type=button value="지금" onclick="document.getElementById('wr_1').value='<?=date("Y-m-d H:i:s", G5_SERVER_TIME)?>';">
        <input type=button value="+1일뒤" onclick="document.getElementById('wr_1').value='<?=date("Y-m-d 00:00:00", G5_SERVER_TIME+86400*1)?>';">
        <input type=button value="+2일뒤" onclick="document.getElementById('wr_1').value='<?=date("Y-m-d 00:00:00", G5_SERVER_TIME+86400*2)?>';">
        <input type=button value="+3일뒤" onclick="document.getElementById('wr_1').value='<?=date("Y-m-d 00:00:00", G5_SERVER_TIME+86400*3)?>';">
        <input type=button value="+5일뒤" onclick="document.getElementById('wr_1').value='<?=date("Y-m-d 00:00:00", G5_SERVER_TIME+86400*5)?>';">
        <input type=button value="+7일뒤" onclick="document.getElementById('wr_1').value='<?=date("Y-m-d 00:00:00", G5_SERVER_TIME+86400*7)?>';">
        <input type=button value="+10일뒤" onclick="document.getElementById('wr_1').value='<?=date("Y-m-d 00:00:00", G5_SERVER_TIME+86400*10)?>';">
        <? } ?>
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
    <td class=write_head>경매 종료일시</td>
    <td class=write_main>
        <input type=text size=20 name=wr_2 id=wr_2 value="<?=$write[wr_2]?>" itemname="경매 종료일시" required readonly>
        <? if ($is_admin) { ?>
        <input type=button value="지금" onclick="end_date(0)">
        <input type=button value="1일" onclick="end_date(1)">
        <input type=button value="3일" onclick="end_date(3)">
        <input type=button value="4일" onclick="end_date(4)">
        <input type=button value="5일" onclick="end_date(5)">
        <input type=button value="6일" onclick="end_date(6)">
        <input type=button value="7일" onclick="end_date(7)">
        <input type=button value="8일" onclick="end_date(8)">
        <input type=button value="10일" onclick="end_date(10)">
        <input type=button value="14일" onclick="end_date(14)">
        <input type=button value="21일" onclick="end_date(21)">
        <input type=button value="30일" onclick="end_date(30)">
        <? } ?>
        <br />
        <input type=button value="10시" onclick="end_hour(10)">
        <input type=button value="12시" onclick="end_hour(12)">
        <input type=button value="13시" onclick="end_hour(13)">
        <input type=button value="14시" onclick="end_hour(14)">
        <input type=button value="16시" onclick="end_hour(16)">
        <input type=button value="18시" onclick="end_hour(18)">
        <input type=button value="20시" onclick="end_hour(20)">
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
    <td class=write_head>참여포인트</td>
    <td class=write_main>
        <input type=text size=15 name=wr_3 id=wr_3 value="<?=$write[wr_3]?>" itemname="가격" required numeric> 포인트
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
    <td class=write_head>제품수량제공</td>
    <td class=write_main>
        <input type=text size=15 name=wr_4 id=wr_4 value="<?=$write[wr_4]?>" itemname="최소 참여수" required numeric> ~
        <input type=text size=15 name=wr_5 id=wr_5 value="<?=$write[wr_5]?>" itemname="최대 최대" required numeric>
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
    <td class=write_head>참여제한</td>
    <td class=write_main>
        <input type=text size=15 name=wr_6 id=wr_6 value="<?=$write[wr_6]?>" itemname="참여 횟수 제한" required numeric readonly>
        <div class='write_size' style="line-height:20px;"><span class='emphasis'>한사람당 제품 주문할수있는 개수를 입력 해 주세요</span></div>
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
    <td class=write_head>배송비</td>
    <td class=write_main>
        <input type=text size=15 name=ca_name id=ca_name value="<?=$write[ca_name]?>" itemname="배송비 선/착불 여부" required readonly>
        <input type=button value="무료배송" onclick="document.getElementById('ca_name').value='무료배송';">
        <input type=button value="선불" onclick="document.getElementById('ca_name').value='선불';">
        <input type=button value="착불" onclick="document.getElementById('ca_name').value='착불';">
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<tr>
    <td class=write_head>상세설명</td>
    <td class=write_main style='padding:5px;'>
        <?php if($write_min || $write_max) { ?>
        <!-- 최소/최대 글자 수 사용 시 -->
        <p id="char_count_desc">이 게시판은 최소 <strong><?php echo $write_min; ?></strong>글자 이상, 최대 <strong><?php echo $write_max; ?></strong>글자 이하까지 글을 쓰실 수 있습니다.</p>
        <?php } ?>
        
        <?php echo $editor_html; // 에디터 사용시는 에디터로, 아니면 textarea 로 노출 ?>
        
        <?php if($write_min || $write_max) { ?>
        <!-- 최소/최대 글자 수 사용 시 -->
        <div id="char_count_wrap"><span id="char_count"></span>글자</div>
        <?php } ?>
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>

<!------------------------------------------------------------
<tr>
    <td class=write_head>업체 URL</td>
    <td class=write_main>
        <input type='text' class="frm_input" size='80' name='wr_link1' itemname='업체 URL' value='<?=$write["wr_link1"]?>'>
        <div class=write_size style="line-height:25px;">홍보하시려는 회사의 주소(URL)를 입력하세요.</div>
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>

<tr>
    <td class=write_head>상품 URL</td>
    <td class=write_main>
        <input type='text' class="frm_input" size='80' name='wr_link2' itemname='상품 URL' value='<?=$write["wr_link2"]?>'>
        <div class=write_size style="line-height:25px;">홍보하시려는 상품의 주소(URL)를 입력하세요.</div>
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
------------------------------------------------------------->

<? if ($is_file) { ?>
<tr>
    <td class=write_head>목록 이미지 <div class=write_size>size 98 x 98</div> </td>
    <td class=write_main>
        <input type='file' class="frm_input" name='bf_file[]' title='파일 용량 <?=$upload_max_filesize?> 이하만 업로드 가능'>        
        <span id=file1></span>
    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>

<tr>
    <td class=write_head>내용 이미지 <div class=write_size>size 228 x 228</div> </td>
    <td class=write_main>
        <input type='file' class="frm_input" name='bf_file[]' title='파일 용량 <?=$upload_max_filesize?> 이하만 업로드 가능'>        
        <span id=file2></span>

        <script type="text/javascript">
        var flen = 1;
        function add_file(delete_code)
        {
            var obj = document.getElementById("file"+flen++);

            if (delete_code)
                obj.innerHTML += delete_code;
        }

        <?=$file_script; //수정시에 필요한 스크립트?>
        </script>

    </td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>

<? } ?>


<? if ($is_guest) { ?>
<tr>
    <td class=write_head>자동등록방지</td>
    <td class=write_main><?php echo $captcha_html ?></td>
</tr>
<tr><td colspan=2 height=1 bgcolor=#e7e7e7></td></tr>
<? } ?>

<tr><td colspan=2 height=2 bgcolor="#0A7299"></td></tr>
</table>

<div class="btn_confirm" style="margin:20px 0;"> 
    <input type="submit" value="작성완료" id="btn_submit" accesskey="s" class="btn_submit">
    <a href="./board.php?bo_table=<?php echo $bo_table ?>" class="btn_cancel">취소</a>
</div>

</td></tr></table>
</form>

<script type="text/javascript">

    <?php if($write_min || $write_max) { ?>
    // 글자수 제한
    var char_min = parseInt(<?php echo $write_min; ?>); // 최소
    var char_max = parseInt(<?php echo $write_max; ?>); // 최대
    check_byte("wr_content", "char_count");

    $(function() {
        $("#wr_content").on("keyup", function() {
            check_byte("wr_content", "char_count");
        });
    });
    <?php } ?>
    
document.getElementById("subj1").focus();

function html_auto_br(obj) {
    if (obj.checked) {
        result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
        if (result)
            obj.value = "html2";
        else
            obj.value = "html1";
    } else {
        obj.value = "";
    }
}

function fwrite_submit(f) {

    <?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>
    
    var subject = f.subj1.value + "|" + f.subj2.value;
    
    document.getElementById("subject_hidden").value = subject;
    
    var content = "";
    $.ajax({
        url: g5_bbs_url+"/ajax.filter.php",
        type: "POST",
        data: {
            "subject": f.wr_subject.value,
            "content": f.wr_content.value
        },
        dataType: "json",
        async: false,
        cache: false,
        success: function(data, textStatus) {
            subject = data.subject;
            content = data.content;
        }
    });

    if (subject) {
        alert("제목에 금지단어('"+subject+"')가 포함되어있습니다");
        f.wr_subject.focus();
        return false;
    }

    if (content) {
        alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
        if (typeof(ed_wr_content) != "undefined")
            ed_wr_content.returnFalse();
        else
            f.wr_content.focus();
        return false;
    }

    if (document.getElementById("char_count")) {
        if (char_min > 0 || char_max > 0) {
            var cnt = parseInt(check_byte("wr_content", "char_count"));
            if (char_min > 0 && char_min > cnt) {
                alert("내용은 "+char_min+"글자 이상 쓰셔야 합니다.");
                return false;
            } else if (char_max > 0 && char_max < cnt) {
                alert("내용은 "+char_max+"글자 이하로 쓰셔야 합니다.");
                return false;
            }
        }
    }

    <?php echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함  ?>

    document.getElementById("btn_submit").disabled = "disabled";
    
    return true;
}

function end_date(day) {
    var wr_1 = document.getElementById("wr_1");
    var wr_2 = document.getElementById("wr_2");

    var tmp = wr_1.value.split(" ");
    var date = tmp[0].split("-");
    var time = tmp[1].split(":");

    var yyyy = date[0];
    var mm = --date[1];
    var dd = Number(date[2]) + Number(day);
    var hh = Number(date[3]) + Number(day);
    var ii = Number(date[4]) + Number(day);
    var ss = Number(date[5]) + Number(day);

    var hh = time[0];
    var ii = time[1];
    var ss = time[2];

    oDate = new Date(yyyy, mm, dd)

    yyyy = oDate.getFullYear();
    mm = oDate.getMonth() + 1;
    dd = oDate.getDate();

    if (String(mm).length == 1) mm = "0" + mm;
    if (String(dd).length == 1) dd = "0" + dd;

    //wr_2.value = yyyy + "-" + mm + "-" + dd + " " + hh + ":" + ii + ":" + ss;
    wr_2.value = yyyy + "-" + mm + "-" + dd + " " + hh + ":" + ii + ":" + ss;
}

function end_hour(hour) {
    var wr_2 = document.getElementById("wr_2");
    wr_2.value = wr_2.value.substring(0,10) + ' ' + hour + ':00:00';
}
</script>

