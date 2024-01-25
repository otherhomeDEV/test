<?php
include_once('./_common.php');

if (!$board['bo_table']) {
   alert('존재하지 않는 게시판입니다.', G5_URL);
}


check_device($board['bo_device']);



if (isset($write['wr_is_comment']) && $write['wr_is_comment']) {
    goto_url(get_pretty_url($bo_table, $write['wr_parent'], '#c_'.$wr_id));
}

if (!$bo_table) {
    $msg = "bo_table 값이 넘어오지 않았습니다.\\n\\nboard.php?bo_table=code 와 같은 방식으로 넘겨 주세요.";
    alert($msg);
}

$g5['board_title'] = ((G5_IS_MOBILE && $board['bo_mobile_subject']) ? $board['bo_mobile_subject'] : $board['bo_subject']);

// wr_id 값이 있으면 글읽기
if ((isset($wr_id) && $wr_id) || (isset($wr_seo_title) && $wr_seo_title)) {
    // 글이 없을 경우 해당 게시판 목록으로 이동
    if (!$write['wr_id']) {
        $msg = '글이 존재하지 않습니다.\\n\\n글이 삭제되었거나 이동된 경우입니다.';
        alert($msg, get_pretty_url($bo_table));
    }

    // 그룹접근 사용
    if (isset($group['gr_use_access']) && $group['gr_use_access']) {
        if ($is_guest) {
            $msg = "비회원은 이 게시판에 접근할 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.";
            alert($msg, G5_BBS_URL.'/login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id, $qstr)));
        }

        // 그룹관리자 이상이라면 통과
        if ($is_admin == "super" || $is_admin == "group") {
            ;
        } else {
            // 그룹접근
            $sql = " select count(*) as cnt from {$g5['group_member_table']} where gr_id = '{$board['gr_id']}' and mb_id = '{$member['mb_id']}' ";
            $row = sql_fetch($sql);
            if (!$row['cnt']) {
                alert("접근 권한이 없으므로 글읽기가 불가합니다.\\n\\n궁금하신 사항은 관리자에게 문의 바랍니다.", G5_URL);
            }
        }
    }

    // 로그인된 회원의 권한이 설정된 읽기 권한보다 작다면
    if ($member['mb_level'] < $board['bo_read_level']) {
        if ($is_member)
            alert('글을 읽을 권한이 없습니다.', G5_URL);
        else
            alert('글을 읽을 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.', G5_BBS_URL.'/login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id, $qstr)));
    }

    // 본인확인을 사용한다면
    if ($config['cf_cert_use'] && !$is_admin) {
        // 인증된 회원만 가능
        if ($board['bo_use_cert'] != '' && $is_guest) {
            alert('이 게시판은 본인확인 하신 회원님만 글읽기가 가능합니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.', G5_BBS_URL.'/login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id, $qstr)));
        }

        if ($board['bo_use_cert'] == 'cert' && !$member['mb_certify']) {
            alert('이 게시판은 본인확인 하신 회원님만 글읽기가 가능합니다.\\n\\n회원정보 수정에서 본인확인을 해주시기 바랍니다.', G5_URL);
        }

        if ($board['bo_use_cert'] == 'adult' && !$member['mb_adult']) {
            alert('이 게시판은 본인확인으로 성인인증 된 회원님만 글읽기가 가능합니다.\\n\\n현재 성인인데 글읽기가 안된다면 회원정보 수정에서 본인확인을 다시 해주시기 바랍니다.', G5_URL);
        }

        if ($board['bo_use_cert'] == 'hp-cert' && $member['mb_certify'] != 'hp') {
            alert('이 게시판은 휴대폰 본인확인 하신 회원님만 글읽기가 가능합니다.\\n\\n회원정보 수정에서 휴대폰 본인확인을 해주시기 바랍니다.', G5_URL);
        }

        if ($board['bo_use_cert'] == 'hp-adult' && (!$member['mb_adult'] || $member['mb_certify'] != 'hp')) {
            alert('이 게시판은 휴대폰 본인확인으로 성인인증 된 회원님만 글읽기가 가능합니다.\\n\\n현재 성인인데 글읽기가 안된다면 회원정보 수정에서 휴대폰 본인확인을 다시 해주시기 바랍니다.', G5_URL);
        }
    }

    // 자신의 글이거나 관리자라면 통과
    if (($write['mb_id'] && $write['mb_id'] === $member['mb_id']) || $is_admin) {
        ;
    } else {
        // 비밀글이라면
        if (strstr($write['wr_option'], "secret"))
        {
            // 회원이 비밀글을 올리고 관리자가 답변글을 올렸을 경우
            // 회원이 관리자가 올린 답변글을 바로 볼 수 없던 오류를 수정
            $is_owner = false;
            if ($write['wr_reply'] && $member['mb_id'])
            {
                $sql = " select mb_id from {$write_table}
                            where wr_num = '{$write['wr_num']}'
                            and wr_reply = ''
                            and wr_is_comment = 0 ";
                $row = sql_fetch($sql);
                if ($row['mb_id'] === $member['mb_id'])
                    $is_owner = true;
            }

            $ss_name = 'ss_secret_'.$bo_table.'_'.$write['wr_num'];

            if (!$is_owner)
            {
                //$ss_name = "ss_secret_{$bo_table}_{$wr_id}";
                // 한번 읽은 게시물의 번호는 세션에 저장되어 있고 같은 게시물을 읽을 경우는 다시 비밀번호를 묻지 않습니다.
                // 이 게시물이 저장된 게시물이 아니면서 관리자가 아니라면
                //if ("$bo_table|$write['wr_num']" != get_session("ss_secret"))
                if (!get_session($ss_name))
                    goto_url(G5_BBS_URL.'/password.php?w=s&amp;bo_table='.$bo_table.'&amp;wr_id='.$wr_id.$qstr);
            }

            set_session($ss_name, TRUE);
        }
    }

    // 한번 읽은글은 브라우저를 닫기전까지는 카운트를 증가시키지 않음
    $ss_name = 'ss_view_'.$bo_table.'_'.$wr_id;
    if (!get_session($ss_name))
    {
        sql_query(" update {$write_table} set wr_hit = wr_hit + 1 where wr_id = '{$wr_id}' ");

        // 자신의 글이면 통과
        if ($write['mb_id'] && $write['mb_id'] === $member['mb_id']) {
            ;
        } else if ($is_guest && $board['bo_read_level'] == 1 && $write['wr_ip'] == $_SERVER['REMOTE_ADDR']) {
            // 비회원이면서 읽기레벨이 1이고 등록된 아이피가 같다면 자신의 글이므로 통과
            ;
        } else {
            // 글읽기 포인트가 설정되어 있다면
            if ($config['cf_use_point'] && $board['bo_read_point'] && $member['mb_point'] + $board['bo_read_point'] < 0)
                alert('보유하신 포인트('.number_format($member['mb_point']).')가 없거나 모자라서 글읽기('.number_format($board['bo_read_point']).')가 불가합니다.\\n\\n포인트를 모으신 후 다시 글읽기 해 주십시오.');

            insert_point($member['mb_id'], $board['bo_read_point'], ((G5_IS_MOBILE && $board['bo_mobile_subject']) ? $board['bo_mobile_subject'] : $board['bo_subject']).' '.$wr_id.' 글읽기', $bo_table, $wr_id, '읽기');
        }

        set_session($ss_name, TRUE);
    }

    $g5['title'] = strip_tags(conv_subject($write['wr_subject'], 255))." > ".$g5['board_title'];
} else {
    if ($member['mb_level'] < $board['bo_list_level']) {
        if ($member['mb_id'])
            alert('목록을 볼 권한이 없습니다.', G5_URL);
        else
            alert('목록을 볼 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.', G5_BBS_URL.'/login.php?'.$qstr.'&url='.urlencode(G5_BBS_URL.'/board.php?bo_table='.$bo_table.($qstr?'&amp;':'')));
    }

    // 본인확인을 사용한다면
    if ($config['cf_cert_use'] && !$is_admin) {
        // 인증된 회원만 가능
        if ($board['bo_use_cert'] != '' && $is_guest) {
            alert('이 게시판은 본인확인 하신 회원님만 글읽기가 가능합니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.', G5_BBS_URL.'/login.php?wr_id='.$wr_id.$qstr.'&amp;url='.urlencode(get_pretty_url($bo_table, $wr_id, $qstr)));
        }

        if ($board['bo_use_cert'] == 'cert' && !$member['mb_certify']) {
            alert('이 게시판은 본인확인 하신 회원님만 글읽기가 가능합니다.\\n\\n회원정보 수정에서 본인확인을 해주시기 바랍니다.', G5_URL);
        }

        if ($board['bo_use_cert'] == 'adult' && !$member['mb_adult']) {
            alert('이 게시판은 본인확인으로 성인인증 된 회원님만 글읽기가 가능합니다.\\n\\n현재 성인인데 글읽기가 안된다면 회원정보 수정에서 본인확인을 다시 해주시기 바랍니다.', G5_URL);
        }

        if ($board['bo_use_cert'] == 'hp-cert' && $member['mb_certify'] != 'hp') {
            alert('이 게시판은 휴대폰 본인확인 하신 회원님만 글읽기가 가능합니다.\\n\\n회원정보 수정에서 휴대폰 본인확인을 해주시기 바랍니다.', G5_URL);
        }

        if ($board['bo_use_cert'] == 'hp-adult' && (!$member['mb_adult'] || $member['mb_certify'] != 'hp')) {
            alert('이 게시판은 휴대폰 본인확인으로 성인인증 된 회원님만 글읽기가 가능합니다.\\n\\n현재 성인인데 글읽기가 안된다면 회원정보 수정에서 휴대폰 본인확인을 다시 해주시기 바랍니다.', G5_URL);
        }
    }

    if (!isset($page) || (isset($page) && $page == 0)) $page = 1;

    $g5['title'] = $g5['board_title'].' '.$page.' 페이지';
}

$is_auth = $is_admin ? true : false;

include_once(G5_PATH.'/head.sub.php');


$width = $board['bo_table_width'];
if ($width <= 100)
    $width .= '%';
else
    $width .='px';

// IP보이기 사용 여부
$ip = "";
$is_ip_view = $board['bo_use_ip_view'];
if ($is_admin) {
    $is_ip_view = true;
    if ($write && array_key_exists('wr_ip', $write)) {
        $ip = $write['wr_ip'];
    }
} else {
    // 관리자가 아니라면 IP 주소를 감춘후 보여줍니다.
    if (isset($write['wr_ip'])) {
        $ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", G5_IP_DISPLAY, $write['wr_ip']);
    }
}

// 분류 사용
$is_category = false;
$category_name = '';
if ($board['bo_use_category']) {
    $is_category = true;
    if (array_key_exists('ca_name', $write)) {
        $category_name = $write['ca_name']; // 분류명
    }
}

// 추천 사용
$is_good = false;
if ($board['bo_use_good'])
    $is_good = true;

// 비추천 사용
$is_nogood = false;
if ($board['bo_use_nogood'])
    $is_nogood = true;

$admin_href = "";
// 최고관리자 또는 그룹관리자라면
if ($member['mb_id'] && ($is_admin === 'super' || $group['gr_admin'] === $member['mb_id']))
    $admin_href = G5_ADMIN_URL.'/board_form.php?w=u&amp;bo_table='.$bo_table;

include_once(G5_BBS_PATH.'/board_head.php');

// 게시물 아이디가 있다면 게시물 보기를 INCLUDE
if (isset($wr_id) && $wr_id) {
    include_once(G5_BBS_PATH.'/view.php');
}

// 전체목록보이기 사용이 "예" 또는 wr_id 값이 없다면 목록을 보임
//if ($board['bo_use_list_view'] || empty($wr_id))

// 업체게시판 google map api 페이지 -> 리스트들 나열되는 부분

if ($board['bo_table'] === 'store' && empty($sca))  {
    ?>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&callback=initMap&v=weekly"
      defer
    ></script>

    <script>



  function initMap() {
  const myLatLng = { lat:  -34.92843, lng: 138.60002 };  

  
  // Create an info window to share between markers.
  const infoWindow = new google.maps.InfoWindow();

  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 11,
    center: myLatLng,
  });

  const marker =  new google.maps.Marker({
    position: myLatLng,
    map,
    title: "Victoria square",
  });

  // Add a click listener for each marker, and set up the info window.
  marker.addListener("click", () => {
      infoWindow.close();
      infoWindow.setContent(marker.getTitle());
      infoWindow.open(marker.getMap(), marker);
    });

}

window.initMap = initMap;

function updateSelection(category) {
        // 선택된 항목을 div에 표시
        document.getElementById('selectedText').innerText = category;
    }


function findingStores(category) {
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 11.5,
    center: { lat: -34.92843, lng: 138.60002 },
  });
  
  const tourStops = {
    음식점: [
      [{ lat: -35.02058, lng: 138.52303 }, "The Korean Vibe" , "513 Brighton Rd, Brighton SA 5048" ,"https://adelaideinside.com/data/editor/2401/8eb94b7344a02a8e5773374704e519ef_1704760025_1939.png"  , "https://www.google.com/maps/place/The+Korean+Vibe/@-35.0206534,138.5204877,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0db773c8423c9:0x1e1b35373ab4be78!8m2!3d-35.0206534!4d138.5230626!16s%2Fg%2F11l6xvf6y_?entry=ttu"],
      [{ lat: -35.00000, lng: 138.54000 }, "Seoul Sweetie","270 morphett St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702517477_6954.png" ,"https://www.google.com/maps/place/Seoul+Sweetie/@-34.929969,138.591593,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cf0e389e5b0f:0xd4eda11441ae0e06!8m2!3d-34.929969!4d138.5941626!16s%2Fg%2F11hs0v6k92?entry=ttu"],
      [{ lat: -34.92991, lng: 138.59422 }, "Busan Baby","272 Morphett St, Adelaide SA 5000", "https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702517007_7599.png","https://www.google.com/maps/place/Busan+Baby/@-34.9300081,138.5915895,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cf5364bcceaf:0x3c7483e4455355bb!8m2!3d-34.9300081!4d138.5941591!16s%2Fg%2F11j5p5rgks?entry=ttu"],
      [{ lat: -34.92781, lng: 138.59710 }, "GO-JJI ADELAIDE BBQ","15 Pitt St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702516629_6749.JPG","https://www.google.com/maps/place/GO-JJI+ADELAIDE+BBQ/@-34.9279319,138.5945094,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cf270971e6e5:0xcd69c98a6caececd!8m2!3d-34.9279319!4d138.597079!16s%2Fg%2F1th1v5hd?entry=ttu"],
      [{ lat: -34.92759, lng: 138.59352 }, "반반 치킨","145 Franklin St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702516245_1096.png","https://www.google.com/maps/place/Ban+Ban/@-34.9250954,138.5872989,15z/data=!3m1!5s0x6ab0cf241c00de1f:0x554051ac86bfa5a1!4m10!1m2!2m1!1sBan+Ban+Korean+Fried+Chicken+%26+Beer!3m6!1s0x6ab0cf25824cab4b:0x904fcc553e657506!8m2!3d-34.9277085!4d138.5935008!15sCiNCYW4gQmFuIEtvcmVhbiBGcmllZCBDaGlja2VuICYgQmVlciIpUicvZ2VvL3R5cGUvZXN0YWJsaXNobWVudF9wb2kvc2VydmVzX2JlZXJaJSIjYmFuIGJhbiBrb3JlYW4gZnJpZWQgY2hpY2tlbiAmIGJlZXKSARFrb3JlYW5fcmVzdGF1cmFudJoBI0NoWkRTVWhOTUc5blMwVkpRMEZuU1VONU0wbE1SbEZCRUFF4AEA!16s%2Fg%2F11g1lv0k70?entry=ttu"],
      [{ lat: -34.92620, lng: 138.59575 }, "+82 고기","12 Eliza St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702514956_4662.jpg","https://www.google.com/maps/place/Plus+82+GoGi+korean+BBQ+restaurant/@-34.9263749,138.5931236,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cfae3d9e3be9:0x3f64ac62413d210c!8m2!3d-34.9263749!4d138.5956932!16s%2Fg%2F11gycs0ryv?entry=ttu"],
      [{ lat: -34.92477, lng: 138.60088 }, "+82 포차","shop 3/25 Grenfell St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702514195_8114.png","https://www.google.com/maps/place/Plus+82+Pocha+Korean+restaurant/@-34.9248695,138.5983672,17z/data=!3m2!4b1!5s0x6ab0cec260c6d2f3:0x12cc3b77c91f27a4!4m6!3m5!1s0x6ab0ced7acdf4953:0x2e91638dadd3ea21!8m2!3d-34.9248695!4d138.6009368!16s%2Fg%2F11gbfbzbzc?entry=ttu"],
      [{ lat: -34.92991, lng: 138.59422 }, "주로흥할","15 Hindley St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702513514_0472.png","https://www.google.com/maps/place/%EC%A3%BC%EB%A1%9C%ED%9D%A5%ED%95%A0/@-34.9231758,138.5962158,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cf29d9a83ac5:0x2ea5ff307020a2d7!8m2!3d-34.9231758!4d138.5987854!16s%2Fg%2F11g6wctnr7?entry=ttu"],
      [{ lat: -34.93120, lng: 138.59589 }, "먹방","31 Field St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702512699_6875.png","https://www.google.com/maps/place/Mukbang+Korean+Cuisine+%26+Bar/@-34.9313857,138.5932605,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cfa38d309c6d:0x8d4e4910f6ce5b2d!8m2!3d-34.9313857!4d138.5958301!16s%2Fg%2F11ghfpcrzc?entry=ttu"],
      [{ lat: -34.93089, lng: 138.59491 }, "박봉숙 식당","152 Wright St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702512056_3372.png","https://www.google.com/maps/place/ParkBongSook+Restaurant/@-34.9316782,138.5920678,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cf5dfc6446e9:0xdce7f3bf2be99f00!8m2!3d-34.9316782!4d138.5946374!16s%2Fg%2F11vllhm7kh?entry=ttu"],
      [{ lat: -34.93423, lng: 138.60611 }, "고여사","449 Pulteney St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/9cca288eabfa3249e72ec6d731e7e672_1702511272_6008.png","https://www.google.com/maps/place/Koyeosa/@-34.9343302,138.6035863,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cf2af0f76e7d:0xf2653ae5e9e2c860!8m2!3d-34.9343302!4d138.6061559!16s%2Fg%2F11fjzk3b4x?entry=ttu"],
      // 추가적인 음식점 tourstops을 여기에 추가
    ],
    정비: [
      [{ lat: -34.90997, lng: 138.68176 }, "Green Crash Repairs","14 Hender Ave, Magill SA 5072","https://adelaideinside.com/data/editor/2312/91cd9edd5d55a5f2ee985364df6a3b81_1702602338_5552.png","https://www.google.com/maps/place/Green+Crash+Repairs/@-34.9101303,138.6792531,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cafed4e85943:0xa6c0ff1792cfecb1!8m2!3d-34.9101303!4d138.6818227!16s%2Fg%2F11cjkw7y77?entry=ttu"],
      // 추가적인 정비 tourstops을 여기에 추가
    ],
    미용: [
      [{ lat: -34.92605, lng: 138.60595 }, "Street hair","186a Pulteney St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1705886110_2174.png","https://www.google.com/maps/place/Street+hair/@-34.9262603,138.6060345,17z/data=!3m2!4b1!5s0x6ab0ced0f24b104d:0xe79fbf50cae13c81!4m6!3m5!1s0x6ab0ced174a4f861:0xbb86de4e35bd68f9!8m2!3d-34.9262603!4d138.6060345!16s%2Fg%2F11c1p5syt6?entry=ttu"],
      [{ lat: -34.92809, lng: 138.51131 }, "DD Lashes","50 Halsey Road Fulham SA, Australia","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1705885743_0693.png","https://www.google.com/maps/place/50+Halsey+Rd,+Fulham+SA+5024/@-34.9311045,138.511037,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0c4e40a481f17:0x4e4b7efa61957f73!8m2!3d-34.9311045!4d138.5136066!16s%2Fg%2F11c28p7plh?entry=ttu"],
      [{ lat: -34.88816, lng: 138.67468 }, "The Born Beauty","6/145 Montacute Rd, Newton SA 5074","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621787_3727.png","https://www.google.com/maps/place/TheBornBeauty+-+Korean+Beauty+Lounge/@-34.888324,138.6720825,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cb9cf00aa9af:0x2311c15d104781c3!8m2!3d-34.888324!4d138.6746521!16s%2Fg%2F11jnqmd7x9?entry=ttu"],
      [{ lat: -34.92036, lng: 138.64955 }, "Star Hair Salon","325 The Parade, Beulah Park SA 5067","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621530_4618.png","https://www.google.com/maps/place/Star+Hair+Salon/@-34.9204864,138.6495781,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cbe811fb10e9:0x17d96be00ba372d3!8m2!3d-34.9204864!4d138.6495781!16s%2Fg%2F1ptxtdd3n?entry=ttu"],
      [{ lat: -34.92217, lng: 138.60354 }, "Hair Beaute","Shop/14 Charles St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621296_0536.jpeg","https://www.google.com/maps/place/%ED%97%A4%EC%96%B4%EB%B3%B4%EB%96%BC/@-34.9222979,138.6010022,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cf51e7a4bafd:0xe1633bd05afe71cb!8m2!3d-34.9222979!4d138.6035718!16s%2Fg%2F11gl36c0jg?entry=ttu"],
      [{ lat: -34.92856, lng: 138.59450 }, "Oh Kim's Hair Salon","128A Grote St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621176_3691.png","https://www.google.com/maps/place/Oh+Kim's+Hair+Salon/@-34.928701,138.5944964,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cf26b1e97f35:0xffc6285d76281395!8m2!3d-34.928701!4d138.5944964!16s%2Fg%2F1pwfwrq7y?entry=ttu"],
      [{ lat: -34.92306, lng: 138.60575 }, "KOrean COlor Hairsalon","56 Pulteney St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621027_2335.png","https://www.google.com/maps/place/KOrean+COlour+Hairsalon/@-34.9231785,138.6032129,17z/data=!4m6!3m5!1s0x6ab0ced3ba17928d:0x4c03ce4467e70edf!8m2!3d-34.9231785!4d138.6057825!16s%2Fg%2F11c0xp5w68?entry=ttu"],
      [{ lat: -34.89021, lng: 138.65468 }, "Cozy Hair","Shop 6/474-476 Payneham Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702620820_4782.png","https://www.google.com/maps/place/Cozy+Hair.+%EC%BD%94%EC%A7%80%ED%97%A4%EC%96%B4.+Korean+Hairdressing/@-34.8904388,138.6546942,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cbd7ee439681:0xe53522a0a6589917!8m2!3d-34.8904388!4d138.6546942!16s%2Fg%2F1tfly00v?entry=ttu"],
      // 추가적인 미용 tourstops을 여기에 추가
    ],
    부동산: [
      [{ lat: -35.00159, lng: 138.59293 }, "Otherhome Sharehouse","3 Moore St, Pasadena SA 5042","https://adelaideinside.com/data/editor/2312/95453068bbdb3e279d6deab8d5d8e2d5_1702450536_3551.png","https://www.google.com/maps/place/Otherhome+Sharehouse/@-35.0016794,138.5903348,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0d15cea2f4b51:0x612674cafb5ddd47!8m2!3d-35.0016794!4d138.5929044!16s%2Fg%2F11v6zknx96?entry=ttu"],
      // 추가적인 부동산 tourstops을 여기에 추가
    ],
    정육: [
      [{ lat: -34.88034, lng: 138.68451 }, "yes butcher","2/6 Meredith st. Newton","https://adelaideinside.com/data/editor/2401/c74969568b0da1066b02b045ccf971bc_1704258634_0021.jpg","https://www.google.com/maps/place/yes+butcher+(%EC%98%88%EC%8A%A4+%EB%B6%80%EC%B2%98)/@-34.8805546,138.6819498,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0c7d190509119:0xd7d28301fa95f1b1!8m2!3d-34.8805546!4d138.6845194!16s%2Fg%2F11j32q2_mt?entry=ttu"],
      [{ lat: -34.89270, lng: 138.64912 }, "최가네 정육점","4/418 Payneham Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2312/95453068bbdb3e279d6deab8d5d8e2d5_1702445435_9297.jpg","https://www.google.com/maps/place/%EC%B5%9C%EA%B0%80%EB%84%A4+%EC%A0%95%EC%9C%A1%EC%A0%90/@-34.892827,138.6465779,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0ca301e5c2353:0x4b3d366ea77f6a2a!8m2!3d-34.892827!4d138.6491475!16s%2Fg%2F1pyqkz24s?entry=ttu"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    한인마트: [
      [{ lat: -34.90722, lng: 138.59519 }, "빌리지마트","T25/67 O'Connell St, North Adelaide SA 5006","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525732_1306.jpeg","https://www.google.com/maps/place/Village+Asian+Grocery+Kimchi+Store/@-34.9073932,138.5926369,17z/data=!3m2!4b1!5s0x6ab0c8d7659cdcb9:0xed8b71e3a0b5e145!4m6!3m5!1s0x6ab0c9102c627f97:0x22763b880cb1f8d7!8m2!3d-34.9073932!4d138.5952065!16s%2Fg%2F11k48lms8f?entry=ttu"],
      [{ lat: -34.92841, lng: 138.59668 }, "서울식품","66 Grote St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525593_3085.jpeg","https://www.google.com/maps/place/%EC%84%9C%EC%9A%B8%EC%8B%9D%ED%92%88+Seoul+Grocery/@-34.9286031,138.5941107,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cf2718fe87e7:0xd1a7c70e38df0214!8m2!3d-34.9286031!4d138.5966803!16s%2Fg%2F11f6k_56dq?entry=ttu"],
      [{ lat: -34.92886, lng: 138.59693 }, "코리아나마트","Market Plaza, Shop 2-5, 61/63 Grote St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525441_323.jpeg","https://www.google.com/maps/place/%EC%BD%94%EB%A6%AC%EC%95%84%EB%82%98%EB%A7%88%ED%8A%B8/@-34.928987,138.5968624,17z/data=!3m2!4b1!5s0x6ab0cf2724b5fab9:0x400e7788f935ac22!4m6!3m5!1s0x6ab0cf26d1cfe677:0xbf3e47ded0e7c6a2!8m2!3d-34.928987!4d138.5968624!16s%2Fg%2F11c3k6k_5x?entry=ttu"],
      [{ lat: -34.90255, lng: 138.65708 }, "패밀리마트","161 Glynburn Rd, Firle SA 5070","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525241_3454.png","https://www.google.com/maps/place/%ED%8C%A8%EB%B0%80%EB%A6%AC%EB%A7%88%ED%8A%B8/@-34.9026078,138.6545413,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0ca3511081e6f:0x708601862bd6b711!8m2!3d-34.9026078!4d138.6571109!16s%2Fg%2F11b6drmcqz?entry=ttu"],
      [{ lat: -34.92077, lng: 138.64478 }, "해피마트","5068/298 The Parade, Kensington SA 5068","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525073_4672.png","https://www.google.com/maps/place/%ED%95%B4%ED%94%BC%EB%A7%88%ED%8A%B8/@-34.9210163,138.6422697,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cbe6c6e5590f:0x33ae232935a8ec62!8m2!3d-34.9210163!4d138.6448393!16s%2Fg%2F11b7l4_4_n?entry=ttu"],
      [{ lat: -34.88991, lng: 138.65546 }, "Lucky Mart","482A Payneham Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702524755_5733.png","https://www.google.com/maps/place/Lucky+Mart/@-34.8901096,138.6528632,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0ca39337948e3:0xed957e2bfd60a44f!8m2!3d-34.8901096!4d138.6554328!16s%2Fg%2F11ckr5n8gr?entry=ttu"],
      [{ lat: -34.90063, lng: 138.63490 }, "Together Mart","Shop 2-3/291 Payneham Rd, Royston Park SA","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702524408_0564.jpeg","https://www.google.com/maps/place/Together+Mart/@-34.9007827,138.6323004,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0c9afce28b7f3:0x6617fecdf9c36c8b!8m2!3d-34.9007827!4d138.63487!16s%2Fg%2F11k3g_2z7l?entry=ttu"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    카페: [
      [{ lat: -34.92356, lng: 138.59781 }, "Waffle & Coffee","2/20-24 Leigh St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702622823_3775.jpeg","https://www.google.com/maps/place/Waffle+%26+Coffee/@-34.92378,138.5978,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cf28339c01a3:0x8a7e05064bd4c19d!8m2!3d-34.92378!4d138.5978!16s%2Fg%2F1tjy1mm7?entry=ttu"],
      [{ lat: -34.93224, lng: 138.60337 }, "Seoul Sisters","84 Halifax St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702607639_5521.png","https://www.google.com/maps/place/Seoul+Sisters/@-34.9325146,138.6008715,17z/data=!3m2!4b1!5s0x6ab0ced9536dacf5:0xa487351ec5e5c193!4m6!3m5!1s0x6ab0cf91949e02c7:0xa1e605b8bdcfd62!8m2!3d-34.9325146!4d138.6034411!16s%2Fg%2F11fnb0czjh?entry=ttu"],
      [{ lat: -34.92395, lng: 138.55839 }, "Latte Studio","208 Henley Beach Rd, Torrensville SA 5031","https://adelaideinside.com/data/editor/2312/62f7992637f97312009c21b1875876a0_1702527995_7108.jpg","https://www.google.com/maps/place/Latte+Studio/@-34.9241443,138.5557785,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0c57a041fe223:0x30588f3431f213ae!8m2!3d-34.9241443!4d138.5583481!16s%2Fg%2F11mvwcs55t?entry=ttu"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    회계: [
      [{ lat: -34.99000, lng: 138.59000 }, "Dummy data"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    의료: [
      [{ lat: -34.93101, lng: 138.59610 }, "Harmony Aesthetic Clinic","1st Floor Tenancy 2/22-30 Field St, Adelaide CBD","https://adelaideinside.com/data/editor/2401/c74969568b0da1066b02b045ccf971bc_1704261086_3574.png","https://www.google.com/maps/place/Harmony+Aesthetic+Clinic/@-34.931199,138.5936065,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cf9bc5b32509:0x230e9222b983ab97!8m2!3d-34.931199!4d138.5961761!16s%2Fg%2F11ql8fmmxf?entry=ttu"],
      [{ lat: -34.83199, lng: 138.69123 }, "Bupa Dental Tea Tree Plaza","976 North East Road, Modbury SA 5092","https://adelaideinside.com/data/editor/2312/4fc643dcb48ffe00987b6d9eefb182d1_1703297577_3009.jpg","https://www.google.com/maps/place/Bupa+Dental+Tea+Tree+Plaza/@-34.8321033,138.6885914,17z/data=!3m2!4b1!5s0x6ab0b4edd3504255:0x571df460699a5745!4m6!3m5!1s0x6ab0b4ec2c1147e3:0x53803541e37e7b37!8m2!3d-34.8321033!4d138.691161!16s%2Fg%2F11bzq482l_?entry=ttu"],
      [{ lat: -34.90884, lng: 138.59580 }, "Anew Smile Implant Centre","151-159 Ward St, North Adelaide, SA 5006","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703222890_2085.png","https://www.google.com/maps/place/Anew+Smile+Implant+Centre/@-34.9090294,138.5931383,17z/data=!3m2!4b1!5s0x6ab0c8d6fcdc1b4d:0x6137fac09cfd167a!4m6!3m5!1s0x6ab0c950e7bc295f:0xe7a25418ad9581ce!8m2!3d-34.9090294!4d138.5957079!16s%2Fg%2F11l321d9rl?entry=ttu"],
      [{ lat: -35.06815, lng: 138.86458 }, "Mount Barker Dentists","15 Victoria Crescent, Mt Barker SA 5251","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703222511_6712.jpg","https://www.google.com/maps/place/Mount+Barker+Dentists/@-35.0670637,138.849968,15z/data=!4m10!1m2!2m1!1sMount+Barker+Dentists!3m6!1s0x6ab73066e1cd1229:0x2a78b938a53baa65!8m2!3d-35.0688206!4d138.8646119!15sChVNb3VudCBCYXJrZXIgRGVudGlzdHOSAQ1kZW50YWxfY2xpbmlj4AEA!16s%2Fg%2F11cmhky5pm?entry=ttu"],
      [{ lat: -34.92059, lng: 138.63777 }, "Primary Dental Norwood","201–203 The Parade Norwood SA 5067","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703222137_0339.jpg","https://www.google.com/maps/place/Primary+Dental/@-34.9206703,138.6352385,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0c9589395b7b7:0x8e601a69590abb41!8m2!3d-34.9206703!4d138.6378081!16s%2Fg%2F1thr7948?entry=ttu"],
      [{ lat: -34.80550, lng: 138.61593 }, "Adelaide Disability Medical Services","Shop1, 6-12 Capital St, Mawson Lakes, SA 5095","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703221765_2906.png","https://www.google.com/maps/place/Adelaide+Disability+Medical+Services/@-34.8056793,138.6132598,17z/data=!3m2!4b1!5s0x6ab0b6f264474c97:0xee66826b6d274977!4m6!3m5!1s0x6ab0b6f48c1b6d89:0xe7460cc086c1ebc8!8m2!3d-34.8056793!4d138.6158294!16s%2Fg%2F11ggw19x3g?entry=ttu"],
      [{ lat: -34.97477, lng: 138.60948 }, "Pro Health Care Mitcham","105 Belair Rd, Torrens Park SA 5062","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703221384_0101.jpg","https://www.google.com/maps/place/Pro+Health+Care+Mitcham/@-34.974924,138.6069424,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0c5b4456e85a9:0x60be99560ae961fd!8m2!3d-34.974924!4d138.609512!16s%2Fg%2F1pzqwlcrd?entry=ttu"],
      [{ lat: -34.85181, lng: 138.50824 }, "Trinity Medical Centre - Port Adelaide","28 College St, Port Adelaide SA 5015","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703220868_5386.jpeg","https://www.google.com/maps/place/Trinity+Medical+Centre/@-34.8519657,138.5056426,17z/data=!3m2!4b1!5s0x6ab0c6eff608c9e3:0x9c6032cdeb58ecbc!4m6!3m5!1s0x6ab0c6eff7c3ee6b:0x21e55e96aa2f84b2!8m2!3d-34.8519657!4d138.5082122!16s%2Fg%2F1pv5zrxjx?entry=ttu"],
      [{ lat: -34.92708, lng: 138.63139 }, "Pro Health Care Norwood","93 Kensington Road, Norwood SA 5067","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703218742_0067.png","https://www.google.com/maps/place/Pro+Health+Care+Norwood/@-34.9272598,138.6289044,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0c949082810eb:0x7acc9fcca044f956!8m2!3d-34.9272598!4d138.631474!16s%2Fg%2F11g190s988?entry=ttu"],
      [{ lat: -34.93980, lng: 138.63680 }, "East Adelaide Dental Studio","1 Allinga Ave, Glenside SA 5065","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702622525_8343.png","https://www.google.com/maps/place/East+Adelaide+Dental+Studio/@-34.9398946,138.6341875,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cea84dec5ad1:0xff1ebb5e47a8ec1b!8m2!3d-34.9398946!4d138.6367571!16s%2Fg%2F1tfl0bbh?entry=ttu"],
      [{ lat: -34.92380, lng: 138.60057 }, "JYL Optical Outlet","29 James Pl, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702622277_1957.png","https://www.google.com/maps/place/JYL+Optical+Outlet/@-34.9239211,138.5980291,17z/data=!3m2!4b1!5s0x6ab0ced648d1772b:0xceca2a9bb742a5e3!4m6!3m5!1s0x6ab0ced6453a206d:0xd611117de2202fc8!8m2!3d-34.9239211!4d138.6005987!16s%2Fg%2F1tc_r4br?entry=ttu"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    기타: [
      [{ lat: -34.92255, lng: 138.59974 }, "Study SA 유학원","Level 3, 50 King William Street, Adelaide, SA 5000, Australia","https://adelaideinside.com/data/editor/2401/c74969568b0da1066b02b045ccf971bc_1704258275_0216.png","https://www.google.com/maps/place/Study+SA/@-34.9245402,138.5982769,17z/data=!3m1!5s0x6ab0ced637666c6b:0xefeb7050d713f80!4m10!1m2!2m1!1zU3R1ZHkgU0Eg7Jyg7ZWZ7JuQ!3m6!1s0x6ab0ced63e52f447:0x9029444c887e2a70!8m2!3d-34.9232784!4d138.59988!15sChJTdHVkeSBTQSDsnKDtlZnsm5CSARZlZHVjYXRpb25hbF9jb25zdWx0YW504AEA!16s%2Fg%2F1q63ck4c6?entry=ttu"],
      [{ lat: -34.92433, lng: 138.60087 }, "정에듀 Jung Education Australia","25 grenfell st. Adelaide SA 5000","https://adelaideinside.com/data/editor/2401/a3a06945bdb0b5a399e7e9dbd1491dd4_1704258020_8214.jpg","https://www.google.com/maps/place/25+Grenfell+St,+Adelaide+SA+5000/@-34.9245402,138.5982769,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0ced653b3718f:0x730fa1f73f62ee1f!8m2!3d-34.9245402!4d138.6008465!16s%2Fg%2F11h6sjlbw1?entry=ttu"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
  };
  
  


  // Create an info window to share between markers.
  const infoWindow = new google.maps.InfoWindow( {maxWidth: 210, maxHeight:250} );
  

  // 선택한 카테고리에 해당하는 tourstop 정보 가져오기
  const selectedTourStops = tourStops[category];

  if (selectedTourStops) {
    // 선택한 카테고리에 대한 모든 마커 생성
    for (let i = 0; i < selectedTourStops.length; i++) {
      const [position, title, address, imglink, link] = selectedTourStops[i];
      const marker = new google.maps.Marker({
        position,
        map,
        title: `${i + 1}. ${title}`,
        label: `${i + 1}`,
        optimized: false,
      });


      //infoWindow 디자인 요소
      //const contentString1= `
    //<div style="width: 200px;">
      //<p style="color: #333; margin: 0 0 10px; font-size:16px; font-weight: bold;">${marker.title}</p>`
   

      const contentStringTest = `<div class="card" style="height:17rem">
                                    <img class="card-img-top " style="height:7rem;" src="${imglink}" alt="Card image cap">
                                    <div class="card-body">
                                        <h6 class="card-title">${marker.title}</h6>
                                        <p class="card-text">${address}</p>
                                    </div>
                                    <a href="${link}" target="_blank" class="btn text-white" style="background-color:#ffc51b; font-weight:bold;" >구글맵 바로가기</a>
                                </div>`



      // 마커에 클릭 이벤트 핸들러 추가
      marker.addListener("click", () => {
        infoWindow.close();
        infoWindow.setContent(contentStringTest);
        infoWindow.open(marker.getMap(), marker);
      });
    }
  }
}




    </script>
        
        <?php
        // 분류 사용 여부
        $category_option = '';
        $is_category = false;
        if ($board['bo_use_category']) {
            $is_category = true;
            $category_href = get_pretty_url($bo_table);

            $category_option .= '<li><a class="py-2 px-3" href="'.$category_href.'"';
        if ($sca=='')
            $category_option .= ' id="bo_cate_on"';
            $category_option .= '>전체</a></li>';

        $categories = explode('|', $board['bo_category_list']); // 구분자가 , 로 되어 있음
            for ($i=0; $i<count($categories); $i++) {
                $category = trim($categories[$i]);
                if ($category=='') continue;
                $category_option .= '<li><a class="py-2 px-3" href="'.(get_pretty_url($bo_table,'','sca='.urlencode($category))).'"';
                $category_msg = '';
                if ($category==$sca) { // 현재 선택된 카테고리라면
                $category_option .= ' id="bo_cate_on"';
                $category_msg = '<span class="sound_only">열린 분류 </span>';
            }
            $category_option .= '>'.$category_msg.$category.'</a></li>';
        }
}
         ?>

<nav id="bo_cate" class="sly-tab font-weight-normal mb-2">
	<h3 class="sr-only">FAQ 분류 목록</h3>
	<div class="px-3 px-sm-0">
		<div class="d-flex">
			<div id="bo_cate_list" class="sly-wrap flex-grow-1" style="overflow: hidden;">
				<ul id="bo_cate_ul" class="sly-list d-flex border-left-0 text-nowrap" style="transform: translateZ(0px); width: 330px; min-width: 340px;">
                <?php echo $category_option; ?>
			</div>
			<div>
				<a href="javascript:;" class="sly-btn sly-prev ca-prev py-2 px-3 disabled">
					<i class="fa fa-angle-left" aria-hidden="true"></i>
					<span class="sr-only">이전 분류</span>
				</a>
			</div>
			<div>
				<a href="javascript:;" class="sly-btn sly-next ca-next py-2 px-3 disabled">
					<i class="fa fa-angle-right" aria-hidden="true"></i>
					<span class="sr-only">다음 분류</span>
				</a>				
			</div>
		</div>
	</div>
	<hr>
	<script>
		$(document).ready(function() {
			$('#bo_cate .sly-wrap').sly({
				horizontal: 1,
				itemNav: 'basic',
				smart: 1,
				mouseDragging: 1,
				touchDragging: 1,
				releaseSwing: 1,
				startAt: 0,
				speed: 300,
				prevPage: '#bo_cate .ca-prev',
				nextPage: '#bo_cate .ca-next'
			});

			// Sly Tab
			var cate_id = 'bo_cate';
			var cate_size = na_sly_size(cate_id);

			na_sly(cate_id, cate_size);

			$(window).resize(function(e) {
				na_sly(cate_id, cate_size);
			});
		});
	</script>
</nav>


        <!-- 지도가 표기될 div -->
        
    <div id="map" style=" height:500px;"></div>
    <!--<div style="margin-top : 35px; width:100%;">
        <button type="button" class="btn btn-light"  onclick="findingStores('음식점')">음식점</button>
        <button type="button" class="btn btn-light"  onclick="findingStores('미용')">미용</button>
        <button type="button" class="btn btn-light"  onclick="findingStores('부동산')">부동산</button>
        <button type="button" class="btn btn-light"  onclick="findingStores('정비')">정비</button>
        <button type="button" class="btn btn-light"  onclick="findingStores('정육')">정육</button>
        <button type="button" class="btn btn-light"  onclick="findingStores('한인마트')">한인마트</button>
        <button type="button" class="btn btn-light"  onclick="findingStores('카페')">카페</button>
        <button type="button" class="btn btn-light"  onclick="findingStores('회계')">회계</button>
        <button type="button" class="btn btn-light"  onclick="findingStores('의료')">의료</button>
        <button type="button" class="btn btn-light"  onclick="findingStores('기타')">기타</button>
    </div>-->

    <div class="dropdown" style="margin-top : 20px; display: flex; justify-content: center;" >
            <button class="text-white dropdown-toggle" style="background-color:#ffc51b; font-weight:bold; border-radius:15px; border:none;" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div id="selectedItem">
            한눈에 보기 : <span id="selectedText">전체</span>
           </div>
            </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
            <a class="dropdown-item" href="#" onclick="dropdownClick('음식점')">음식점</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('미용')">미용</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('부동산')">부동산</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('정비')">정비</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('정육')">정육</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('한인마트')">한인마트</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('카페')">카페</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('회계')">회계</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('의료')">의료</a>
            <a class="dropdown-item" href="#" onclick="dropdownClick('기타')">기타</a>
        </div>
    </div>
    <script>
    function dropdownClick(category) {
        console.log(category);
        // 선택된 항목을 div에 표시
        updateSelection(category);
        findingStores(category);
    }
</script>
    </body>

    <?php
}



else if($board['bo_table'] !== 'store' && $member['mb_level'] >= $board['bo_list_level'] && $board['bo_use_list_view'] || empty($wr_id)){
    include_once (G5_BBS_PATH.'/list.php');
}

////////////////

include_once(G5_BBS_PATH.'/board_tail.php');

echo "\n<!-- 사용스킨 : ".(G5_IS_MOBILE ? $board['bo_mobile_skin'] : $board['bo_skin'])." -->\n";

include_once(G5_PATH.'/tail.sub.php');
