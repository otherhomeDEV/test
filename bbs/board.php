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


function findingStores(category) {
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 11.5,
    center: { lat: -34.92843, lng: 138.60002 },
  });
  
  const tourStops = {
    음식점: [
      [{ lat: -35.02058, lng: 138.52303 }, "The Korean Vibe"],
      [{ lat: -35.00000, lng: 138.54000 }, "Seoul Sweetie"],
      [{ lat: -34.92991, lng: 138.59422 }, "Busan Baby"],
      [{ lat: -34.92781, lng: 138.59710 }, "GO-JJI ADELAIDE BBQ"],
      [{ lat: -34.92759, lng: 138.59352 }, "반반 치킨"],
      [{ lat: -34.92620, lng: 138.59575 }, "+82 고기"],
      [{ lat: -34.92477, lng: 138.60088 }, "+82 포차"],
      [{ lat: -34.92991, lng: 138.59422 }, "주로흥할"],
      [{ lat: -34.93120, lng: 138.59589 }, "먹방"],
      [{ lat: -34.93089, lng: 138.59491 }, "박봉숙 식당"],
      [{ lat: -34.93423, lng: 138.60611 }, "고여사"],
      // 추가적인 음식점 tourstops을 여기에 추가
    ],
    정비: [
      [{ lat: -34.90997, lng: 138.68176 }, "Green Crash Repairs"],
      // 추가적인 정비 tourstops을 여기에 추가
    ],
    미용: [
      [{ lat: -34.92605, lng: 138.60595 }, "Street hair"],
      [{ lat: -34.92809, lng: 138.51131 }, "DD Lashes"],
      [{ lat: -34.88816, lng: 138.67468 }, "The Born Beauty"],
      [{ lat: -34.92036, lng: 138.64955 }, "Star Hair Salon"],
      [{ lat: -34.92217, lng: 138.60354 }, "Hair Beaute"],
      [{ lat: -34.92856, lng: 138.59450 }, "Oh Kim's Hair Salon"],
      [{ lat: -34.92306, lng: 138.60575 }, "KOrean COlor Hairsalon"],
      [{ lat: -34.89021, lng: 138.65468 }, "Cozy Hair"],
      // 추가적인 미용 tourstops을 여기에 추가
    ],
    부동산: [
      [{ lat: -35.00159, lng: 138.59293 }, "Otherhome Sharehouse"],
      // 추가적인 부동산 tourstops을 여기에 추가
    ],
    정육: [
      [{ lat: -34.88034, lng: 138.68451 }, "yes butcher"],
      [{ lat: -34.89270, lng: 138.64912 }, "최가네 정육점"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    한인마트: [
      [{ lat: -34.90722, lng: 138.59519 }, "빌리지마트"],
      [{ lat: -34.92841, lng: 138.59668 }, "서울식품"],
      [{ lat: -34.92886, lng: 138.59693 }, "코리아나마트"],
      [{ lat: -34.90255, lng: 138.65708 }, "패밀리마트"],
      [{ lat: -34.92077, lng: 138.64478 }, "해피마트"],
      [{ lat: -34.88991, lng: 138.65546 }, "Lucky Mart"],
      [{ lat: -34.90063, lng: 138.63490 }, "Together Mart"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    카페: [
      [{ lat: -34.92356, lng: 138.59781 }, "Waffle & Coffee"],
      [{ lat: -34.93224, lng: 138.60337 }, "Seoul Sisters"],
      [{ lat: -34.92395, lng: 138.55839 }, "Latte Studio"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    회계: [
      [{ lat: -34.99000, lng: 138.59000 }, "Dummy data"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    의료: [
      [{ lat: -34.93101, lng: 138.59610 }, "Harmony Aesthetic Clinic"],
      [{ lat: -34.83199, lng: 138.69123 }, "Bupa Dental Tea Tree Plaza"],
      [{ lat: -34.90884, lng: 138.59580 }, "Anew Smile Implant Centre"],
      [{ lat: -35.06815, lng: 138.86458 }, "Mount Barker Dentists"],
      [{ lat: -34.92059, lng: 138.63777 }, "Primary Dental"],
      [{ lat: -34.80550, lng: 138.61593 }, "Adelaide Disability Medical Services"],
      [{ lat: -34.97477, lng: 138.60948 }, "Pro Health Care Mitcham"],
      [{ lat: -34.85181, lng: 138.50824 }, "Trinity Medical Centre - Port Adelaide"],
      [{ lat: -34.92708, lng: 138.63139 }, "Pro Health Care Norwood"],
      [{ lat: -34.93980, lng: 138.63680 }, "East Adelaide Dental Studio"],
      [{ lat: -34.92380, lng: 138.60057 }, "JYL Optical Outlet"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    기타: [
      [{ lat: -34.92255, lng: 138.59974 }, "Study SA 유학원"],
      [{ lat: -34.92433, lng: 138.60087 }, "정에듀 Jung Education Australia"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
  };
  // Create an info window to share between markers.
  const infoWindow = new google.maps.InfoWindow();

  // 선택한 카테고리에 해당하는 tourstop 정보 가져오기
  const selectedTourStops = tourStops[category];

  if (selectedTourStops) {
    // 선택한 카테고리에 대한 모든 마커 생성
    for (let i = 0; i < selectedTourStops.length; i++) {
      const [position, title] = selectedTourStops[i];
      const marker = new google.maps.Marker({
        position,
        map,
        title: `${i + 1}. ${title}`,
        label: `${i + 1}`,
        optimized: false,
      });

      // 마커에 클릭 이벤트 핸들러 추가
      marker.addListener("click", () => {
        infoWindow.close();
        infoWindow.setContent(marker.getTitle());
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
        
        <div id="map" style=" height: 50%;"></div>
        <div style="margin-top : 40px;">
        <button type="button" class="btn btn-info" onclick="findingStores('음식점')">음식점</button>
        <button type="button" class="btn btn-info" onclick="findingStores('정비')">정비</button>
        <button type="button" class="btn btn-info" onclick="findingStores('미용')">미용</button>
        <button type="button" class="btn btn-info" onclick="findingStores('부동산')">부동산</button>
        <button type="button" class="btn btn-info" onclick="findingStores('정육')">정육</button>
        <button type="button" class="btn btn-info" onclick="findingStores('한인마트')">한인마트</button>
        <button type="button" class="btn btn-info" onclick="findingStores('카페')">카페</button>
        <button type="button" class="btn btn-info" onclick="findingStores('회계')">회계</button>
        <button type="button" class="btn btn-info" onclick="findingStores('의료')">의료</button>
        <button type="button" class="btn btn-info" onclick="findingStores('기타')">기타</button>
    </div>
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
