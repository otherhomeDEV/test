<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

// 스킨설정
$is_skin_setup = (($is_admin == 'super' || IS_DEMO) && is_file($board_skin_path.'/setup.skin.php')) ? true : false;

// 이미지 영역 및 썸네일 크기 설정
$boset['thumb_w'] = (!isset($boset['thumb_w']) || $boset['thumb_w'] == "") ? 400 : (int)$boset['thumb_w'];
$boset['thumb_h'] = (!isset($boset['thumb_h']) || $boset['thumb_h'] == "") ? 225 : (int)$boset['thumb_h'];

if($boset['thumb_w'] && $boset['thumb_h']) {
	$img_height = ($boset['thumb_h'] / $boset['thumb_w']) * 100;
} else {
	$img_height = (isset($boset['thumb_d']) && $boset['thumb_d']) ? $boset['thumb_d'] : '56.25';
}

$head_color = (isset($boset['head_color']) && $boset['head_color']) ? $boset['head_color'] : 'primary';

$boset['xl'] = isset($boset['xl']) ? (int)$boset['xl'] : 0;
$boset['lg'] = isset($boset['lg']) ? (int)$boset['lg'] : 0;
$boset['md'] = isset($boset['md']) ? (int)$boset['md'] : 3;
$boset['sm'] = isset($boset['sm']) ? (int)$boset['sm'] : 0;
$boset['xs'] = isset($boset['xs']) ? (int)$boset['xs'] : 2;
$gallery_row_cols = na_row_cols($boset['xs'], $boset['sm'], $boset['md'], $boset['lg'], $boset['xl']);

// 글 이동
$is_list_link = false;
$boset['target'] = isset($boset['target']) ? $boset['target'] : '';
switch($boset['target']) {
	case '1' : $target = ' target="_blank"'; break;
	case '2' : $is_list_link = true; break;
	case '3' : $target = ' target="_blank"'; $is_list_link = true; break;
	default	 : $target = ''; break; 
}

// No 이미지
$no_img = isset($boset['no_img']) ? na_url($boset['no_img']) : '';

// 글 수
$list_cnt = count($list);


?>

<!-- 게시판 목록 시작 { -->
<div id="bo_list_wrap" class="mb-4">

	<!-- 검색창 시작 { -->
	<div id="bo_search" class="collapse<?php echo ((isset($boset['search_open']) && $boset['search_open']) || $stx) ? ' show' : ''; ?>">
		<div class="alert bg-light border p-2 p-sm-3 mb-3 mx-3 mx-sm-0">
			<form id="fsearch" name="fsearch" method="get" class="m-auto" style="max-width:600px;">
				<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
				<input type="hidden" name="sca" value="<?php echo $sca ?>">
				<div class="form-row mx-n1">
					<div class="col-6 col-sm-3 px-1">
						<label for="sfl" class="sr-only">검색대상</label>
						<select name="sfl" class="custom-select">
							<?php echo get_board_sfl_select_options($sfl); ?>
						</select>
					</div>
					<div class="col-6 col-sm-3 px-1">
						<select name="sop" class="custom-select">
							<option value="and"<?php echo get_selected($sop, "and") ?>>그리고</option>
							<option value="or"<?php echo get_selected($sop, "or") ?>>또는</option>
						</select>	
					</div>
					<div class="col-12 col-sm-6 pt-2 pt-sm-0 px-1">
						<label for="stx" class="sr-only">검색어</label>
						<div class="input-group">
							<input type="text" id="bo_stx" name="stx" value="<?php echo stripslashes($stx) ?>" required class="form-control" placeholder="검색어를 입력해 주세요.">
							<div class="input-group-append">
								<button type="submit" class="btn btn-primary" title="검색하기">
									<i class="fa fa-search" aria-hidden="true"></i>
									<span class="sr-only">검색하기</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<!-- } 검색창 끝 -->

    <?php 
	// 게시판 카테고리
	if ($is_category)
		include_once($board_skin_path.'/category.skin.php'); 
	?>

	<form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
		<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
		<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
		<input type="hidden" name="stx" value="<?php echo $stx ?>">
		<input type="hidden" name="spt" value="<?php echo $spt ?>">
		<input type="hidden" name="sca" value="<?php echo $sca ?>">
		<input type="hidden" name="sst" value="<?php echo $sst ?>">
		<input type="hidden" name="sod" value="<?php echo $sod ?>">
		<input type="hidden" name="page" value="<?php echo $page ?>">
		<input type="hidden" name="sw" value="">

		<!-- 게시판 페이지 정보 및 버튼 시작 { -->
		<div id="bo_btn_top" class="clearfix f-de font-weight-normal mb-2">
			<div class="d-sm-flex align-items-center">
				<div id="bo_list_total" class="flex-sm-grow-1">
					<div class="px-3 px-sm-0">
						<?php echo (isset($sca) && $sca) ? $sca : '전체'; ?>
						<b><?php echo number_format((int)$total_count) ?></b> / <?php echo $page ?> 페이지
					</div>
					<div class="d-block d-sm-none border-top my-2"></div>
				</div>
				<div class="px-3 px-sm-0 text-right">
					<?php if ($is_admin == 'super' || $admin_href || $is_auth || IS_DEMO) {  ?>
						<div class="btn-group" role="group">
							<button type="button" class="btn btn_admin nofocus dropdown-toggle dropdown-toggle-empty dropdown-toggle-split p-1" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false" title="게시판 관리 옵션">
								<i class="fa fa-cog fa-spin fa-fw fa-md" aria-hidden="true"></i>
								<span class="sr-only">게시판 관리 옵션</span>
							</button>
							<div class="dropdown-menu dropdown-menu-right p-0 border-0 bg-transparent text-right">
								<div class="btn-group-vertical">
									<?php if ($admin_href) { ?>
										<a href="<?php echo $admin_href ?>" class="btn btn-primary py-2" role="button">
											<i class="fa fa-cog fa-fw" aria-hidden="true"></i> 보드설정
										</a>
									<?php } ?>
									<?php if($is_skin_setup) { ?>
										<a href="<?php echo na_setup_href('board', $bo_table) ?>" class="btn btn-primary btn-setup py-2" role="button">
											<i class="fa fa-cogs fa-fw" aria-hidden="true"></i> 스킨설정
										</a>
									<?php } ?>
									<?php if ($is_checkbox) { ?>
										<a href="javascript:;" class="btn btn-primary py-2" role="button">
											<label class="p-0 m-0" for="allCheck">
												<i class="fa fa-check-square-o fa-fw" aria-hidden="true"></i> 
												전체선택						
											</label>
											<div class="sr-only">
												<input type="checkbox" id="allCheck" onclick="if (this.checked) all_checked(true); else all_checked(false);">
											</div>
										</a>
										<button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value" class="btn btn-primary py-2">
											<i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> 
											선택삭제
										</button>
										<button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value" class="btn btn-primary py-2">
											<i class="fa fa-files-o fa-fw" aria-hidden="true"></i> 
											선택복사
										</button>
										<button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value" class="btn btn-primary py-2">
											<i class="fa fa-arrows fa-fw" aria-hidden="true"></i>
											선택이동
										</button>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php }  ?>
					<?php if ($rss_href) { ?>
						<a href="<?php echo $rss_href ?>" class="btn btn_b01 nofocus p-1" title="RSS">
							<i class="fa fa-rss fa-fw fa-md" aria-hidden="true"></i>
							<span class="sr-only">RSS</span>
						</a>
					<?php } ?>
					<div class="btn-group" role="group">
						<button type="button" class="btn btn_b01 nofocus dropdown-toggle dropdown-toggle-empty dropdown-toggle-split p-1" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false" title="게시물 정렬">
							<?php
								switch($sst) {
									case 'wr_datetime'	:	$sst_icon = 'history'; 
															$sst_txt = '날짜순 정렬'; 
															break;
									case 'wr_hit'		:	$sst_icon = 'eye'; 
															$sst_txt = '조회순 정렬'; 
															break;
									case 'wr_good'		:	$sst_icon = 'thumbs-o-up'; 
															$sst_txt = '추천순 정렬'; 
															break;
									case 'wr_nogood'	:	$sst_icon = 'thumbs-o-down'; 
															$sst_txt = '비추천순 정렬'; 
															break;
									default				:	$sst_icon = 'sort-numeric-desc'; 
															$sst_txt = '게시물 정렬'; 
															break;
								}
							?>
							<i class="fa fa-<?php echo $sst_icon ?> fa-fw fa-md" aria-hidden="true"></i>
							<span class="sr-only"><?php echo $sst_txt ?></span>
						</button>
						<div class="dropdown-menu dropdown-menu-right p-0 border-0 bg-transparent text-right">
							<div class="btn-group-vertical bg-white border rounded py-1">
								<?php echo str_replace('>', ' class="btn px-3 py-1 text-left" role="button">', subject_sort_link('wr_datetime', $qstr2, 1)) ?>
									날짜순
								</a>
								<?php echo str_replace('>', ' class="btn px-3 py-1 text-left" role="button">', subject_sort_link('wr_hit', $qstr2, 1)) ?>
									조회순
								</a>
								<?php if($is_good) { ?>
									<?php echo str_replace('>', ' class="btn px-3 py-1 text-left" role="button">', subject_sort_link('wr_good', $qstr2, 1)) ?>
										추천순
									</a>
								<?php } ?>
								<?php if($is_nogood) { ?>
									<?php echo str_replace('>', ' class="btn px-3 py-1 text-left" role="button">', subject_sort_link('wr_nogood', $qstr2, 1)) ?>
										비추천순
									</a>
								<?php } ?>
							</div>
						</div>
					</div>
					<button type="button" class="btn btn_b01 nofocus p-1" title="게시판 검색" data-toggle="collapse" data-target="#bo_search" aria-expanded="false" aria-controls="bo_search">
						<i class="fa fa-search fa-fw fa-md" aria-hidden="true"></i>
						<span class="sr-only">게시판 검색</span>
					</button>
					<?php if ($write_href && !$wr_id) { ?>
						<a href="<?php echo $write_href ?>" class="btn btn-primary nofocus py-1 ml-2" role="button">
							<i class="fa fa-pencil" aria-hidden="true"></i>
							쓰기
						</a>
					<?php } ?>
				</div>
			</div>
		</div>
		<!-- } 게시판 페이지 정보 및 버튼 끝 -->

		<!-- 게시물 목록 시작 { -->
		<section id="bo_list" class="mb-4">

			<!-- 목록 헤드 -->
			<div class="w-100 mb-0 bg-<?php echo $head_color ?>" style="height:4px;"></div>

			<ul class="na-table d-md-table w-100 mb-3">
			<?php
if ($board['bo_table'] === 'store' && empty($sca))  {
    ?>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&callback=initMap&v=weekly"
      defer
    ></script>

    <script>

console.log("work2");

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
      [{ lat: -34.94762, lng: 138.62797 }, "Salon A by Genie","193A Glen Osmond Rd, Frewville SA 5063","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1706571484_6158.png","https://www.google.com/maps/place/Salon+A+by+Genie/@-34.947733,138.6253272,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0cfb042ff4bd1:0x3054f9c21af594f8!8m2!3d-34.947733!4d138.6278968!16s%2Fg%2F11kpb3d097?entry=ttu"],
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
      [{ lat: -34.90566, lng: 138.60926 }, "SKS Accountant", "Shop 59/53-55 Melbourne St, North Adelaide SA 5006", "https://adelaideinside.com/data/editor/2401/38f95a2b5c6a669fc8ea19795edd6621_1706588587_4673.png", "https://www.google.com/maps/place/SKS+Accounting+%26+Business+Pty+Ltd/@-34.9043246,138.6072136,16z/data=!4m6!3m5!1s0x6ab0c93cc9402809:0xd79a9d6923d34471!8m2!3d-34.9059928!4d138.6094779!16s%2Fg%2F11dym0tbjl?entry=ttu" ],
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
      [{ lat: -34.90022, lng: 138.65673 }, "Glynde Veterinary Surgery","125 Glynburn Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1706492987_5318.png","https://www.google.com/maps/place/Glynde+Veterinary+Surgery/@-34.9004674,138.6542654,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0ca35da53f115:0xd937e63c4e5c444f!8m2!3d-34.9004674!4d138.6568403!16s%2Fg%2F1tg_x6pk?entry=ttu"],
      
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    기타: [
      [{ lat: -34.92255, lng: 138.59974 }, "Study SA 유학원","Level 3, 50 King William Street, Adelaide, SA 5000, Australia","https://adelaideinside.com/data/editor/2401/c74969568b0da1066b02b045ccf971bc_1704258275_0216.png","https://www.google.com/maps/place/Study+SA/@-34.9245402,138.5982769,17z/data=!3m1!5s0x6ab0ced637666c6b:0xefeb7050d713f80!4m10!1m2!2m1!1zU3R1ZHkgU0Eg7Jyg7ZWZ7JuQ!3m6!1s0x6ab0ced63e52f447:0x9029444c887e2a70!8m2!3d-34.9232784!4d138.59988!15sChJTdHVkeSBTQSDsnKDtlZnsm5CSARZlZHVjYXRpb25hbF9jb25zdWx0YW504AEA!16s%2Fg%2F1q63ck4c6?entry=ttu"],
      [{ lat: -34.92433, lng: 138.60087 }, "정에듀 Jung Education Australia","25 grenfell st. Adelaide SA 5000","https://adelaideinside.com/data/editor/2401/a3a06945bdb0b5a399e7e9dbd1491dd4_1704258020_8214.jpg","https://www.google.com/maps/place/25+Grenfell+St,+Adelaide+SA+5000/@-34.9245402,138.5982769,17z/data=!3m1!4b1!4m6!3m5!1s0x6ab0ced653b3718f:0x730fa1f73f62ee1f!8m2!3d-34.9245402!4d138.6008465!16s%2Fg%2F11h6sjlbw1?entry=ttu"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
  };


  function initMap() {
  

    const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 11.5,
    center: { lat: -34.92843, lng: 138.60002 },
    });

  // Create an info window to share between markers.
  const infoWindow = new google.maps.InfoWindow( {maxWidth: 190, maxHeight:250} );

  // 선택한 카테고리에 해당하는 tourstop 정보 가져오기
  const selectedTourStops = tourStops["음식점"];

  console.log(selectedTourStops);

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


      const contentString = `<div class="card" style="height:17rem; border-radius: 5px;" >
                                    <img class="card-img-top " style="height:7rem;" src="${imglink}" alt="Card image cap">
                                    <div class="card-body">
                                        <h6 class="card-title" style= font-family:  'arial', sans-serif !important;>${marker.title}</h6>
                                        <p class="card-text" style= font-family:  'arial', sans-serif !important;>${address}</p>
                                    </div>
                                    <a href="${link}" target="_blank" class="btn text-white" style="background-color:#ffc51b; font-family:'arial', sans-serif !important; font-weight:bold;" >구글맵 바로가기</a>
                                </div>`



      // 마커에 클릭 이벤트 핸들러 추가
      marker.addListener("click", () => {
        infoWindow.close();
        infoWindow.setContent(contentString);
        infoWindow.open({
            anchor: marker,
            map,
            shouldFocus: false,
        });
      });
    }
  }

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


      const contentString = `<div class="card" style="height:17rem; border-radius: 5px;" >
                                    <img class="card-img-top " style="height:7rem;" src="${imglink}" alt="Card image cap">
                                    <div class="card-body">
                                        <h6 class="card-title" style= font-family:  'arial', sans-serif !important;>${marker.title}</h6>
                                        <p class="card-text" style= font-family:  'arial', sans-serif !important;>${address}</p>
                                    </div>
                                    <a href="${link}" target="_blank" class="btn text-white" style="background-color:#ffc51b; font-family:'arial', sans-serif !important; font-weight:bold;" >구글맵 바로가기</a>
                                </div>`



      // 마커에 클릭 이벤트 핸들러 추가
      marker.addListener("click", () => {
        infoWindow.close();
        infoWindow.setContent(contentString);
        infoWindow.open({
            anchor: marker,
            map,
            shouldFocus: false,
        });
      });
    }
  }
}



    </script>
        



        <!-- 지도가 표기될 div -->
        
    <div id="map" style=" height:500px; margin-top:15px;"></div>

    <div class="dropdown" style="margin-top : 20px; display: flex; justify-content: center;" >
            <button class="text-white dropdown-toggle" style="background-color:#ffc51b;
  border: 1px solid transparent;
  border-radius: 10px;
  box-shadow: rgba(255, 255, 255, .4) 0 1px 0 0 inset;
  box-sizing: border-box;
  color: #fff;
  cursor: pointer;
  display: inline-block;
  font-family:  'arial', sans-serif !important;
  font-size: 16px;
  font-weight: bold;
  line-height: 1.15385;
  margin: 0;
  outline: none;
  padding: 8px .8em;
  position: relative;
  text-align: center;
  text-decoration: none;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  vertical-align: baseline;
  white-space: nowrap;" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div id="selectedItem">
            한눈에 보기 : <span id="selectedText">음식점</span>
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





			// 공지
			if($board['bo_notice']) {
				for ($i=0; $i < $list_cnt; $i++) { 

					if(!$list[$i]['is_notice'])
						continue;

					$wr_icon = '';
					$is_lock = false;
					if ($list[$i]['icon_secret']) {
						$wr_icon = '<span class="na-icon na-secret"></span>';
						$is_lock = true;
					} else if ($list[$i]['icon_new']) {
						$wr_icon = '<span class="na-icon na-new"></span>';
					}

					// 현재 글
					$li_css = ($wr_id == $list[$i]['wr_id']) ? ' bg-light' : '';

					// 현재 글
					if($wr_id == $list[$i]['wr_id']) {
						$li_css = ' bg-light';
						$list[$i]['num'] = '<span class="na-text text-primary">열람</span>';
						$list[$i]['subject'] = '<b class="text-primary">'.$list[$i]['subject'].'</b>';
					} else {
						$li_css = '';
						$list[$i]['num'] = '<span class="na-notice bg-'.$head_color.'"></span><span class="sr-only">공지사항</span>';
						$list[$i]['subject'] = '<b>'.$list[$i]['subject'].'</b>';
					}
			?>
				<li class="d-md-table-row px-3 py-2 p-md-0 text-md-center text-muted border-bottom<?php echo $li_css;?>">
					<div class="d-none d-md-table-cell nw-5 f-sm font-weight-normal py-md-2 px-md-1">
						<?php echo $list[$i]['num'] ?>
					</div>
					<div class="d-md-table-cell text-left py-md-2 pr-md-1">
						<div class="na-title float-md-left">
							<div class="na-item">
								<?php if ($is_checkbox) { ?>
									<input type="checkbox" class="mb-0 mr-2" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
								<?php } ?>
								<a href="<?php echo $list[$i]['href'] ?>" class="na-subject">
									<?php echo $wr_icon; ?>
									<?php echo $list[$i]['subject'] ?>
								</a>
								<?php
									if(isset($list[$i]['icon_file']))
										echo '<span class="na-ticon na-file"></span>'.PHP_EOL;
								?>
								<?php if($list[$i]['wr_comment']) { ?>
									<div class="na-info mr-3">
										<span class="sr-only">댓글</span>
										<span class="count-plus orangered">
											<?php echo $list[$i]['wr_comment'] ?>
										</span>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="float-right float-md-none d-md-table-cell nw-10 nw-md-auto text-left f-sm font-weight-normal pl-2 py-md-2 pr-md-1">
						<span class="sr-only">등록자</span>
						<?php echo na_name_photo($list[$i]['mb_id'], $list[$i]['name']) ?>
					</div>
					<div class="float-left float-md-none d-md-table-cell nw-6 nw-md-auto f-sm font-weight-normal py-md-2 pr-md-1">
						<i class="fa fa-clock-o d-md-none" aria-hidden="true"></i>
						<span class="sr-only">등록일</span>
						<?php echo na_date($list[$i]['wr_datetime'], 'orangered', 'H:i', 'm.d', 'Y.m.d') ?>
					</div>
					<div class="float-left float-md-none d-md-table-cell nw-4 nw-md-auto f-sm font-weight-normal py-md-2 pr-md-1">
						<i class="fa fa-eye d-md-none" aria-hidden="true"></i>
						<span class="sr-only">조회</span>
						<?php echo $list[$i]['wr_hit'] ?>
					</div>
					<div class="clearfix d-block d-md-none"></div>
				</li>
			<?php 
				}
			} // 공지 ?>
			</ul>

			<?php if(!($is_category && $board['bo_table'] === 'store' && empty($sca))) : ?>
			<div id="bo_gallery" class="px-3 px-sm-0 border-bottom mb-4">
				<ul class="row<?php echo $gallery_row_cols ?> mx-n2">
				<?php
				// 리스트
				$n = 0;
				$cap_new = (isset($boset['new']) && $boset['new']) ? $boset['new'] : 'primary';
				for ($i=0; $i < $list_cnt; $i++) { 

					// 공지는 제외	
					if($list[$i]['is_notice'])
						continue;

					// 글수 체크
					$n++;

					// 이미지용
					$wr_alt = get_text(str_replace('"', '', $list[$i]['wr_subject']));

					// 아이콘 체크
					$wr_icon = $wr_tack = $wr_cap = '';
					if ($list[$i]['icon_secret']) {
						$is_lock = true;
						$wr_icon = '<span class="na-icon na-secret"></span>';
					}

					// 링크 이동
					if($is_list_link && $list[$i]['wr_link1']) {
						$list[$i]['href'] = $list[$i]['link_href'][1];
					}

					// 전체 보기에서 분류 출력하기
					if(!$sca && $is_category && $list[$i]['ca_name']) {
						$list[$i]['subject'] = $list[$i]['ca_name'].' <span class="na-bar"></span> '.$list[$i]['subject'];
					}

					// 새 글, 현재 글 스타일
					$wr_now = '';
					if ($wr_id == $list[$i]['wr_id']) {
						$list[$i]['subject'] = '<b class="text-primary">'.$list[$i]['subject'].'</b>';
						$wr_now = '<div class="wr-now"></div>';
						$wr_cap = '<span class="label-cap en bg-orangered">Now</span>';
					} else if($list[$i]['icon_new']) {
						$wr_cap = '<span class="label-cap en bg-'.$cap_new.'">New</span>';
					}

					// 이미지 추출
					$img = na_wr_img($bo_table, $list[$i]);

					// 썸네일 생성
					$thumb = ($boset['thumb_w']) ? na_thumb($img, $boset['thumb_w'], $boset['thumb_h']) : $img;

					if(!$thumb && $no_img) {
						$thumb = $no_img;
					}
				?>
					<li class="col px-2 pb-4">
						<div class="img-wrap bg-light mb-2" style="padding-bottom:<?php echo $img_height ?>%;">
							<div class="img-item">
								<?php if ($is_checkbox) { ?>
									<span class="chk-box">
										<input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
									</span>
								<?php } ?>
								<a href="<?php echo $list[$i]['href'] ?>"<?php echo $target ?>>
									<?php echo $wr_now ?>
									<?php echo $wr_tack ?>
									<?php echo $wr_cap ?>
									<?php if($thumb) { ?>
										<img src="<?php echo $thumb ?>" alt="<?php echo $wr_alt ?>" class="img-render">
									<?php } ?>
								</a>
							</div>
						</div>
						<div class="na-title">
							<div class="na-item">
								<a href="<?php echo $list[$i]['href'] ?>" class="na-subject"<?php echo $target ?>>
									<?php echo $wr_icon ?>
									<?php echo $list[$i]['subject'] ?>
								</a>
								<?php if($list[$i]['wr_comment']) { ?>
									<div class="na-info">
										<span class="sr-only">댓글</span>
										<span class="count-plus orangered">
											<?php echo $list[$i]['wr_comment'] ?>
										</span>
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="clearfix font-weight-normal f-sm">
							<div class="float-right ml-2">
								<span class="sr-only">등록자</span>
								<?php echo na_name_photo($list[$i]['mb_id'], $list[$i]['name']) ?>
							</div>
							<div class="float-left text-muted">
								<i class="fa fa-clock-o" aria-hidden="true"></i>
								<span class="sr-only">등록일</span>
								<?php echo na_date($list[$i]['wr_datetime'], 'orangered', 'H:i', 'm.d', 'm.d') ?>
							</div>
						</div>
					</li>
				<?php } ?>
				</ul>
				<?php if(!$n) { ?>
					<div class="f-de px-3 py-5 text-muted text-center">
						게시물이 없습니다.
					</div>
				<?php } ?>
			</div>
		</section>
		<!-- } 게시물 목록 끝 -->
	

		<!-- 페이징 시작 { -->
		<div class="font-weight-normal px-3 px-sm-0">
			<ul class="pagination justify-content-center en mb-0">
				<?php if($prev_part_href) { ?>
					<li class="page-item"><a class="page-link" href="<?php echo $prev_part_href;?>">Prev</a></li>
				<?php } ?>
				<?php echo na_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, get_pretty_url($bo_table, '', $qstr.'&amp;page='));?>
				<?php if($next_part_href) { ?>
					<li class="page-item"><a  class="page-link" href="<?php echo $next_part_href;?>">Next</a></li>
				<?php } ?>
			</ul>
		</div>
		<!-- } 페이징 끝 -->
		<?php endif; ?>
	</form>

</div>

<?php if ($is_checkbox) { ?>
<noscript>
<p align="center">자바스크립트를 사용하지 않는 경우 별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>

<script>
function all_checked(sw) {
	var f = document.fboardlist;

	for (var i=0; i<f.length; i++) {
		if (f.elements[i].name == "chk_wr_id[]")
			f.elements[i].checked = sw;
	}
}
function fboardlist_submit(f) {
	var chk_count = 0;

	for (var i=0; i<f.length; i++) {
		if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
			chk_count++;
	}

	if (!chk_count) {
		alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
		return false;
	}

	if(document.pressed == "선택복사") {
		select_copy("copy");
		return;
	}

	if(document.pressed == "선택이동") {
		select_copy("move");
		return;
	}

	if(document.pressed == "선택삭제") {
		if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다.\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
			return false;

		f.removeAttribute("target");
        f.action = g5_bbs_url+"/board_list_update.php";
	}

	return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
	var f = document.fboardlist;

	if (sw == "copy")
		str = "복사";
	else
		str = "이동";

	var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

	f.sw.value = sw;
	f.target = "move";
    f.action = g5_bbs_url+"/move.php";
	f.submit();
}
</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->
