<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

// license : gpl
// GPL 프로그램은 어떤 목적으로, 어떤 형태로든 사용할 수 있지만 사용하거나 변경된 프로그램을 배포하는 경우 무조건 동일한 라이선스 즉, GPL로 공개해야 한다.

// wr_1 : 경매시작일시
// wr_2 : 경매종료일시
// wr_3 : 참여 포인트
// wr_4 : 최소 참여수
// wr_5 : 최대 참여수
// wr_6 : 참여 횟수 
// wr_7 : 입찰 횟수
// wr_8 : 경매상태 (0: 경매전, 1:진행중, 2:낙찰, 3:유찰)
// wr_9 : 낙찰 포인트
// wr_10 : 낙찰회원아이디

$tender_table = $g5['write_prefix']."_".$bo_table;

if (!$board[bo_1]) {
    $sql = " update $g5[board_table] set ";
    //$sql.= "  bo_1_subj = '단가' ";
    $sql.= " ,bo_2_subj = '최소 참여수' ";
    $sql.= " ,bo_3_subj = '최대 참여수' ";
    $sql.= " ,bo_4_subj = '참여 횟수 기본값' ";
    $sql.= " where bo_table = '$bo_table' ";
    sql_query($sql, false);

    $sql = " update $g5[board_table] set ";
    //$sql.= "  bo_1 = '500' ";
    $sql.= " ,bo_2 = '5' ";
    $sql.= " ,bo_3 = '10000' ";
    $sql.= " ,bo_4 = '1' ";
    $sql.= " where bo_table = '$bo_table' ";
    sql_query($sql, false);
}


$sql = " create table $tender_table ( ";
$sql.= " `td_id` INT NOT NULL AUTO_INCREMENT ,";
$sql.= " `wr_id` INT NOT NULL ,";
$sql.= " `mb_id` VARCHAR( 30 ) NOT NULL ,";
$sql.= " `mb_name` VARCHAR( 255 ) NOT NULL ,";
$sql.= " `mb_nick` VARCHAR( 255 ) NOT NULL ,";
$sql.= " `mb_email` VARCHAR( 255 ) NOT NULL ,";
$sql.= " `mb_homepage` VARCHAR( 255 ) NOT NULL ,";
$sql.= " `td_inter_point` INT NOT NULL ,";
$sql.= " `td_tender_point` INT NOT NULL ,";
$sql.= " `td_status` CHAR( 1 ) NOT NULL ,";
$sql.= " `td_last` DATETIME NOT NULL ,";
$sql.= " `td_datetime` DATETIME NOT NULL ,";
$sql.= " PRIMARY KEY ( `td_id` ) ,";
$sql.= " INDEX ( `wr_id` ) ";
$sql.= " ); ";
sql_query($sql, false);


// 경매 상태 출력
function auction_status($status) {
    switch ($status) {
        case "0": $status = "시작전"; break;
        case "1": $status = "진행중"; break;
        case "2": $status = "낙찰"; break;
        case "3": $status = "유찰"; break;
    }
    return $status;
}

/*
// 타임스탬프 형식으로 넘어와야 한다.
// 시작시간, 종료시간
function gap_time($begin_time, $end_time) {
    $gap = $end_time - $begin_time;
    $time[days]    = (int)($gap / 86400);
    $time[hours]   = (int)(($gap - ($time[days] * 86400)) / 3600);
    $time[minutes] = (int)(($gap - ($time[days] * 86400 + $time[hours] * 3600)) / 60);
    $time[seconds] = (int)($gap - ($time[days] * 86400 + $time[hours] * 3600 + $time[minutes] * 60));
    return $time;
}
*/

// 경매정보(추가필드)를 가져온다.
function get_info_auction($wr_id, $row=null) {
    global $write, $write_table;
    global $g5;
    global $is_admin;

    if (!$row && !$write) {
        $row = sql_fetch(" select wr_subject, wr_1, wr_2, wr_3, wr_4, wr_5, wr_6, wr_7, wr_8, wr_9, wr_10, wr_11, wr_12, wr_13, wr_14, wr_15, wr_16, wr_17, wr_18, wr_19, wr_20 from $write_table where wr_id = '$wr_id' ");
    } elseif (!$row && $write) {
        $row = $write;
    }

    $pd = explode("|", $row[wr_subject]);

    unset($res);
    $res[company] = trim($pd[0]);
    $res[product] = trim($pd[1]);
    $res[start_datetime] = $row[wr_1];
    $res[end_datetime] = $row[wr_2];
    $res[inter_point] = $row[wr_3];
    $res[tender_lower] = $row[wr_4];
    $res[tender_higher] = $row[wr_5];
    $res[day_limit] = $row[wr_6];
    $res[tender_count] = $row[wr_7];
    $res[status] = $row[wr_8];
    $res[td_id] = $row[wr_9];
    $res[mb_id] = $row[wr_10];

/*
    // 경매가 종료되지 않았다면
    if (G5_TIME_YMDHIS < $res[end_datetime]) {
        // 날짜가 지날수록 초기 참여포인트에 20% 더해진다.
        //$date = gap_time(strtotime($res[start_datetime]), $g4[server_time]);
        $date = gap_time(strtotime(substr($res[start_datetime],0,10)), G5_SERVER_TIME);
        // 보통 자정부터 시작하므로 일수의 갭이 2일 일경우로 제한한다.
        //if ($is_admin) { echo $date[days]; }
        if ($date[days] > 0) {
            $res[inter_point] = (int)($res[inter_point] + (($res[inter_point] * $date[days]) * 0.1));
            //$res[day_limit] = (int)($res[day_limit] - (($res[day_limit] * $date[days]) * 0.1));
            //if ($res[day_limit] < 1) $res[day_limit] = 1;
        }

        // 입찰이 많아보이기 위해 입찰 횟수의 초기값을 변경
        // if ($res[status] > 0) $res[tender_count] += 9999;
    }
*/
    return $res;
}

// 경매 입찰 공통 검사
function tender_common_check($wr_id) {
    global $g5, $board, $member, $tender_table, $write_table, $write, $bo_table;
    
    $url = G5_BBS_URL."/board.php?bo_table=".$bo_table;

    if (!$member[mb_id])
        alert_only("로그인 해주세요.", "{$url}");

    if ($board[bo_5] > 0 && ((G5_SERVER_TIME - strtotime($member[mb_datetime])) < ($board[bo_5]*86400)))
        alert_only("회원가입 후 $board[bo_5] 일이 지나야 참여 가능합니다.", "{$url}");

    $auction = get_info_auction($wr_id);

    if (G5_TIME_YMDHIS < $auction[start_datetime])
        alert_only("공동구매 시작 전입니다.", "{$url}");

    if (G5_TIME_YMDHIS > $auction[end_datetime]) 
        alert_only("공동구매가 종료되었습니다.", "{$url}");

    return $auction;
}

// 경매 건별 입찰 진행
function tender_send($wr_id, $point) {
    global $g5, $board, $member, $tender_table, $write_table, $write, $bo_table;
    
    $url = G5_BBS_URL."/board.php?bo_table=".$bo_table."&wr_id=".$wr_id;

    $auction = tender_common_check($wr_id);

    //$row2 = sql_fetch(" select count(mb_id) as cnt from $tender_table where td_datetime like '".G5_TIME_YMD."%' and mb_id = '$member[mb_id]' and wr_id = '$wr_id' "); //하루에 한회원당 참여건주 제한
	$row2 = sql_fetch(" select count(mb_id) as cnt from $tender_table where mb_id = '$member[mb_id]' and wr_id = '$wr_id' "); //한회원당 참여건주 제한
    $tender_count = $row2[cnt];

    if ($tender_count >= $auction[day_limit])
        alert_only("이 젬품은 한 회원당 {$auction[day_limit]}번만 참여 가능합니다.", "{$url}");

    //if ($point < $auction[tender_lower] || $point > $auction[tender_higher])
    //    alert_only("입찰 번호는 ".number_format($auction[tender_lower])."~".number_format($auction[tender_higher])." 사이로 설정해주세요.", "{$url}");

    $total_point = (int)$auction[inter_point];

    if ($member[mb_point] - $total_point < 0)
        alert_only("보유중인 포인트(".number_format($member[mb_point]).") 가 참여 포인트(".number_format($total_point).") 보다 부족합니다.", "{$url}");

    $row = sql_fetch(" select count(*) as cnt from $tender_table where wr_id = '$wr_id' and mb_id = '$member[mb_id]' and td_tender_point = '$point' ");
   // if ($row[cnt])
   //     alert_only("이미 같은 번호로 입찰 하셨습니다.", "{$url}");

    tender_common_update($wr_id, $point, $auction);
}

// 경매 구간별 입찰 진행
function tender_send_section($wr_id, $point_min, $point_max) {
    global $g5, $board, $member, $tender_table, $write_table, $write, $bo_table;
    
    $url = G5_BBS_URL."/board.php?bo_table=".$bo_table."&wr_id=".$wr_id;

    $auction = tender_common_check($wr_id);

    if ($point_min < $auction[tender_lower] || $point_min > $auction[tender_higher]) {
        alert_only("시작 입찰 번호($point_min)는 ".number_format($auction[tender_lower])."~".number_format($auction[tender_higher])." 사이로 설정해주세요.", "{$url}");
    }

    if ($point_max < $auction[tender_lower] || $point_max > $auction[tender_higher]) {
        alert_only("종료 입찰 번호($point_max)는 ".number_format($auction[tender_lower])."~".number_format($auction[tender_higher])." 사이로 설정해주세요.", "{$url}");
    }

    // 종료입찰번호 - 시작입찰번호 + 1 = 구간의 횟수
    $section_count = $point_max - $point_min + 1;

    $total_point = (int)$auction[inter_point] * $section_count;

    if ($member[mb_point] - $total_point < 0) {
        alert_only("보유중인 포인트(".number_format($member[mb_point]).") 가 참여 포인트(".number_format($total_point).") 보다 부족합니다.\\n\\n입찰을 진행하지 않습니다.", "{$url}");
    }

    $row2 = sql_fetch(" select count(mb_id) as cnt from $tender_table where td_datetime like '".G5_TIME_YMD."%' and mb_id = '$member[mb_id]' and wr_id = '$wr_id' ");
    $tender_count = $row2[cnt] + $section_count;

/*    if ($auction[day_limit]) {
        alert_only("이 젬품은 한 회원당 {$auction[day_limit]}번만 참여 가능합니다.", "{$url}");
    }
*/
    //for ($i=$point_min; $i<=$point_max; $i++) {
    for ($i=$point_max; $i>=$point_min; $i--) {
        $point = $i;
        $row = sql_fetch(" select count(*) as cnt from $tender_table where wr_id = '$wr_id' and mb_id = '$member[mb_id]' and td_tender_point = '$point' ");
        // 같은 번호로 입찰한 내역이 없다면
        if ($row[cnt] == 0) {
            tender_common_update($wr_id, $point, $auction);
        }
    }
}


// 입찰 DB 처리
function tender_common_update($wr_id, $point, $auction) {
    global $g5, $board, $member, $tender_table, $write_table, $write, $bo_table;

    $sql = "insert into {$tender_table} set ";
    $sql.= " wr_id = '$wr_id' ";
    $sql.= ",mb_id = '$member[mb_id]' ";
    $sql.= ",mb_name = '$member[mb_name]' ";
    $sql.= ",mb_nick = '$member[mb_nick]' ";
    $sql.= ",mb_email = '$member[mb_email]' ";
    $sql.= ",mb_homepage = '$member[mb_homepage]' ";
    $sql.= ",td_inter_point = '$auction[inter_point]' ";
    $sql.= ",td_tender_point = '$point' ";
    $sql.= ",td_status = '1' ";
    $sql.= ",td_last = '".G5_TIME_YMDHIS."' ";
    $sql.= ",td_datetime = '".G5_TIME_YMDHIS."' ";
    sql_query($sql);

    // 입찰 횟수
    $row = sql_fetch(" select count(*) as cnt from {$tender_table} where wr_id = '$wr_id' ");
    $tender_count = (int)$row[cnt];

    sql_query(" update $write_table set wr_7 = {$tender_count} where wr_id = '$wr_id' ");

    if ($auction[inter_point]) {
        insert_point($member[mb_id], $auction[inter_point]*-1, "{$wr_id} 공동구매 참여 {$point}", $bo_table, $wr_id, "공동구매 참여 : ".G5_TIME_YMDHIS."&nbsp;".get_microtime());
        // 협찬자에게 포인트 적립
        $tmp_point = (int)($auction[inter_point]);
       // $tmp_point = $tmp_point > 50 ? 50 : $tmp_point;
       // insert_point($write[mb_id], $tmp_point, "{$wr_id} 협찬 감사 Beta3", $bo_table, $wr_id, "협찬 : ".G5_TIME_YMDHIS."&nbsp;".get_microtime());
    }
}

// 경매의 낙찰 여부 검사 및 업데이트
function auction_successful($wr_id) {
    global $g5, $write_table, $tender_table, $auction, $write, $bo_table;

    // 나중에 등록한 게시물의 종료시간이 더 짧으면 모두 종료되는 오류 수정
    //if (!$auction)
    $auction = get_info_auction($wr_id);

    // 경매상태 조회 - 이미 종료되었으면 return
    if ($auction[status] > 1) return false;

    // 경매가 시작전이면 return
    if ($auction[start_datetime] > G5_TIME_YMDHIS) return false;

    // 경매날짜를 비교하여 진행중일경우 return
    if ($auction[start_datetime] < G5_TIME_YMDHIS && $auction[end_datetime] > G5_TIME_YMDHIS) return false;

    // 최저로 입찰된 내역을 조회
    $row = sql_fetch(" select td_tender_point as point, count(td_tender_point) as cnt from $tender_table where wr_id = '$wr_id' group by td_tender_point order by cnt, td_tender_point limit 1 ");




    // 중복되었거나 입찰내역이 없을 경우 유찰
    if ($row[cnt] > $wr_4 || !$row)     {
        sql_query(" update $write_table set wr_8 = '3' where wr_id = '$wr_id' ");
		
		    if ($r_8 = '3') {
				$tmp_point = (int)($auction[inter_point]);
			    $tmp_point = $wr_3 ? $wr_3 : $tmp_point;
			    insert_point($write[mb_id], $tmp_point, "공동구매{$wr_id} 실패로 포인트 반환", $bo_table, $wr_id, "공동구매 : ".G5_TIME_YMDHIS."&nbsp;".get_microtime());
			}

        $res = sql_fetch(" select wr_7, wr_8, wr_9, wr_10 from $write_table where wr_id = '$wr_id' ");
        return $res;
    } else {
        // 낙찰된 입찰정보 가져오기
        $row = sql_fetch(" select * from $tender_table where td_tender_point = '$row[point]' and wr_id = '$wr_id' ");
        sql_query(" update $write_table set wr_8 = '2', wr_9 = '$row[td_tender_point]', wr_10 = '$row[mb_id]' where wr_id = '$wr_id' ");
        $res = sql_fetch(" select wr_7, wr_8, wr_9, wr_10 from $write_table where wr_id = '$wr_id' ");

        return $res;
    }
}

function alert_only($msg='', $url='') {
	//global $g5;

	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">";
	echo "<script>";
	echo "alert('$msg');";
	if($url) {
	    echo "location.replace('$url');";
	}
	echo "</script>";
    exit;
}
?>
