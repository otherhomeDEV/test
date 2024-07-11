<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

// 스킨설정
$is_skin_setup = (($is_admin == 'super' || IS_DEMO) && is_file($board_skin_path.'/setup.skin.php')) ? true : false;

// 이미지 영역 및 썸네일 크기 설정
$boset['thumb_w'] = (!isset($boset['thumb_w']) || $boset['thumb_w'] == "") ? 400 : (int)$boset['thumb_w'];
$boset['thumb_h'] = (!isset($boset['thumb_h']) || $boset['thumb_h'] == "") ? 225 : (int)$boset['thumb_h'];


//썸네일 사이즈
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
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjCUIgUSAPAgD7gAdxZkwbSacEREeC8TU&callback=initMap"
      defer
    ></script>

    <script>

console.log("work2");

const tourStops = {
    음식점: [
      [{ lat: -35.02058, lng: 138.52303 }, "The Korean Vibe" , "513 Brighton Rd, Brighton SA 5048" ,"https://adelaideinside.com/data/editor/2401/8eb94b7344a02a8e5773374704e519ef_1704760025_1939.png","https://maps.app.goo.gl/swjUL48GYE4tr1Jz8"],
      [{ lat: -34.92984, lng: 138.59404 }, "Seoul Sweetie","270 morphett St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702517477_6954.png" ,"https://maps.app.goo.gl/GGdssvz8aDDq43179"],
      [{ lat: -34.92991, lng: 138.59422 }, "Busan Baby","272 Morphett St, Adelaide SA 5000", "https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702517007_7599.png","https://maps.app.goo.gl/Wt3zvJSCGZiBbj6A9"],
      [{ lat: -34.92781, lng: 138.59710 }, "GO-JJI ADELAIDE BBQ","15 Pitt St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702516629_6749.JPG","https://maps.app.goo.gl/G5df3tdk2PijFsgr5"],
      [{ lat: -34.92759, lng: 138.59352 }, "반반 치킨","145 Franklin St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702516245_1096.png","https://maps.app.goo.gl/bCLqwwErqnSSt9oX7"],
      [{ lat: -34.92620, lng: 138.59575 }, "+82 고기","12 Eliza St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702514956_4662.jpg","https://maps.app.goo.gl/Sz1k9yepSdkUJqZc8"],
      [{ lat: -34.92477, lng: 138.60088 }, "+82 포차","shop 3/25 Grenfell St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702514195_8114.png","https://maps.app.goo.gl/g751ywGx5oS7mUmRA"],
      [{ lat: -34.92303, lng: 138.59875 }, "주로흥할","15 Hindley St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702513514_0472.png","https://maps.app.goo.gl/hGYKrdfDhUoKHZEq5"],
      [{ lat: -34.93120, lng: 138.59589 }, "먹방","31 Field St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702512699_6875.png","https://maps.app.goo.gl/PpCLwCJybL9zBttu6"],
      [{ lat: -34.93089, lng: 138.59491 }, "박봉숙 식당","152 Wright St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/e3a91bfbbc9f9e2578ac4eb86da266f0_1702512056_3372.png","https://maps.app.goo.gl/8JMcfPabfQebv5Pt8"],
      [{ lat: -34.93423, lng: 138.60611 }, "고여사","449 Pulteney St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/9cca288eabfa3249e72ec6d731e7e672_1702511272_6008.png","https://maps.app.goo.gl/EV5TMmQJokWk3oe86"],
      [{ lat: -35.01005, lng: 138.54616 }, "Alice's kitchen","29b Dwyer Rd, Oaklands Park SA 5046","https://adelaideinside.com/data/editor/2405/2a4bd28eb758b94da1677b1a5790fe3d_1715216779_5493.png","https://maps.app.goo.gl/axSj5jBpQ6JiQzng7"],
      
      
      // 추가적인 음식점 tourstops을 여기에 추가
    ],
    정비: [
      [{ lat: -34.90997, lng: 138.68176 }, "Green Crash Repairs","14 Hender Ave, Magill SA 5072","https://adelaideinside.com/data/editor/2312/91cd9edd5d55a5f2ee985364df6a3b81_1702602338_5552.png","https://maps.app.goo.gl/ehLrYSTh1XWHippo9"],
      [{ lat: -34.86047, lng: 138.60285 }, "A.R.M Mechanical Services","375A Main N Rd, Enfield SA 5085","https://adelaideinside.com/data/editor/2403/2c7e72113375b98191266b619d830656_1710736011_694.png","https://maps.app.goo.gl/FxbpqQcpHjUTFK6C9"],
      // 추가적인 정비 tourstops을 여기에 추가
    ],
    미용: [
      [{ lat: -34.92605, lng: 138.60595 }, "Street hair","186a Pulteney St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1705886110_2174.png","https://maps.app.goo.gl/43RtHZKjpcir25pp6"],
      [{ lat: -34.92809, lng: 138.51131 }, "DD Lashes","50 Halsey Road Fulham SA, Australia","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1705885743_0693.png","https://maps.app.goo.gl/UeZjbN57wPLShT5E9"],
      [{ lat: -34.88816, lng: 138.67468 }, "The Born Beauty","6/145 Montacute Rd, Newton SA 5074","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621787_3727.png","https://maps.app.goo.gl/1N3uYwdMu6nPmrqr6"],
      [{ lat: -34.92036, lng: 138.64955 }, "Star Hair Salon","325 The Parade, Beulah Park SA 5067","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621530_4618.png","https://maps.app.goo.gl/APeGpqc7QuLYBcPN7"],
      [{ lat: -34.92217, lng: 138.60354 }, "Hair Beaute","Shop/14 Charles St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621296_0536.jpeg","https://maps.app.goo.gl/1fAyB9kmJz69oXeYA"],
      [{ lat: -34.92856, lng: 138.59450 }, "Oh Kim's Hair Salon","128A Grote St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621176_3691.png","https://maps.app.goo.gl/q7gF7FBtBLMSpnSQ8"],
      [{ lat: -34.92306, lng: 138.60575 }, "KOrean COlor Hairsalon","56 Pulteney St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702621027_2335.png","https://maps.app.goo.gl/W1aGYtTxeHpnpxFT7"],
      [{ lat: -34.89021, lng: 138.65468 }, "Cozy Hair","Shop 6/474-476 Payneham Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702620820_4782.png","https://maps.app.goo.gl/3BAAPSLZXMXEiLiD9"],
      [{ lat: -34.94762, lng: 138.62797 }, "Salon A by Genie","193A Glen Osmond Rd, Frewville SA 5063","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1706571484_6158.png","https://maps.app.goo.gl/aGfkucKaRBucUnBZ9"],
      [{ lat: -34.93111, lng: 138.61210 }, "Hair blooming","169 Hutt St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2404/436759e1ab2f8feeed5f72294075f786_1712099209_2302.png","https://maps.app.goo.gl/vQc3FY9ZNN9Pp77G6"],
      [{ lat: -34.88865, lng: 138.65649 }, "Jun's Barber Shop","503B Lower North East Rd, Felixstow SA 5070","https://adelaideinside.com/data/editor/2405/f18755b3f97ea393ee403f703127c351_1717115601_7814.jpg","https://maps.app.goo.gl/FZUdASsvWWhyTvVy6"],
      // 추가적인 미용 tourstops을 여기에 추가
    ],
    부동산: [
      [{ lat: -35.00159, lng: 138.59293 }, "Otherhome Sharehouse","3 Moore St, Pasadena SA 5042","https://adelaideinside.com/data/editor/2312/95453068bbdb3e279d6deab8d5d8e2d5_1702450536_3551.png","https://maps.app.goo.gl/793BAawdrTaRqgu87"],
      // 추가적인 부동산 tourstops을 여기에 추가
    ],
    정육: [
      [{ lat: -34.88034, lng: 138.68451 }, "yes butcher","2/6 Meredith st. Newton","https://adelaideinside.com/data/editor/2401/c74969568b0da1066b02b045ccf971bc_1704258634_0021.jpg","https://maps.app.goo.gl/cFtFeiKjRipMHU8g6"],
      [{ lat: -34.89270, lng: 138.64912 }, "최가네 정육점","4/418 Payneham Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2312/95453068bbdb3e279d6deab8d5d8e2d5_1702445435_9297.jpg","https://maps.app.goo.gl/mrEhBVuigLCMiSov5"],
      [{ lat: -34.85552, lng: 138.65570 }, "바른 정육점","10A/511 North East Road, Gilles Plains SA 5086","https://adelaideinside.com/data/editor/2403/2c7e72113375b98191266b619d830656_1710736629_813.png","https://maps.app.goo.gl/oRHwqrZLJUUt641y5"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    한인마트: [
      [{ lat: -34.90722, lng: 138.59519 }, "빌리지마트","T25/67 O'Connell St, North Adelaide SA 5006","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525732_1306.jpeg","https://maps.app.goo.gl/43F1VGgTpn7XyCiv6"],
      [{ lat: -34.92841, lng: 138.59668 }, "서울식품","66 Grote St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525593_3085.jpeg","https://maps.app.goo.gl/quhVnctaxP2zSASf9"],
      [{ lat: -34.92886, lng: 138.59693 }, "코리아나마트","Market Plaza, Shop 2-5, 61/63 Grote St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525441_323.jpeg","https://maps.app.goo.gl/mcVd45zFkeXh6r5e6"],
      [{ lat: -34.90255, lng: 138.65708 }, "패밀리마트","161 Glynburn Rd, Firle SA 5070","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525241_3454.png","https://maps.app.goo.gl/KEA3fvGs5JGJ3JSG6"],
      [{ lat: -34.92077, lng: 138.64478 }, "해피마트","5068/298 The Parade, Kensington SA 5068","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702525073_4672.png","https://maps.app.goo.gl/BLrmGJCZp7Jgz9bn8"],
      [{ lat: -34.88991, lng: 138.65546 }, "Lucky Mart","482A Payneham Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702524755_5733.png","https://maps.app.goo.gl/TyJersvs4HyLA2339"],
      [{ lat: -34.90063, lng: 138.63490 }, "Together Mart","Shop 2-3/291 Payneham Rd, Royston Park SA","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702524408_0564.jpeg","https://maps.app.goo.gl/AQ6sYMc1JtgkcB6P8"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    카페: [
      [{ lat: -34.92356, lng: 138.59781 }, "Waffle & Coffee","2/20-24 Leigh St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702622823_3775.jpeg","https://maps.app.goo.gl/RiNQSnYpL2Y7cUzNA"],
      [{ lat: -34.93224, lng: 138.60337 }, "Seoul Sisters","84 Halifax St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702607639_5521.png","https://maps.app.goo.gl/HXv2gzA1SDuxSH1V9"],
      [{ lat: -34.92395, lng: 138.55839 }, "Latte Studio","208 Henley Beach Rd, Torrensville SA 5031","https://adelaideinside.com/data/editor/2312/62f7992637f97312009c21b1875876a0_1702527995_7108.jpg","https://maps.app.goo.gl/7xdYSuC12FmyVxGe9"],
      [{ lat: -34.93426, lng: 138.59929 }, "Hello, Stranger","31 Gilbert St, Adelaide SA 5000","https://adelaideinside.com/data/editor/2405/a8380f9bb0b017fd0f79e55e8a92f310_1715824458_6504.png","https://maps.app.goo.gl/DYUY48EWCXi3eWe16"],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    회계: [
      [{ lat: -34.90566, lng: 138.60926 }, "SKS Accountant", "Shop 59/53-55 Melbourne St, North Adelaide SA 5006", "https://adelaideinside.com/data/editor/2401/38f95a2b5c6a669fc8ea19795edd6621_1706588587_4673.png", "https://maps.app.goo.gl/6ATbxTEKCtjan9Vj9" ],
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    의료: [
      [{ lat: -34.93101, lng: 138.59610 }, "Harmony Aesthetic Clinic","1st Floor Tenancy 2/22-30 Field St, Adelaide CBD","https://adelaideinside.com/data/editor/2401/c74969568b0da1066b02b045ccf971bc_1704261086_3574.png","https://maps.app.goo.gl/ukKuTUGkMtqyi2pG9"],
      [{ lat: -34.83199, lng: 138.69123 }, "Bupa Dental Tea Tree Plaza","976 North East Road, Modbury SA 5092","https://adelaideinside.com/data/editor/2312/4fc643dcb48ffe00987b6d9eefb182d1_1703297577_3009.jpg","https://maps.app.goo.gl/DNUvNrHssayeN2us9"],
      [{ lat: -34.90884, lng: 138.59580 }, "Anew Smile Implant Centre","151-159 Ward St, North Adelaide, SA 5006","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703222890_2085.png","https://maps.app.goo.gl/cZRi6gSYQNRauyXQ9"],
      [{ lat: -35.06815, lng: 138.86458 }, "Mount Barker Dentists","15 Victoria Crescent, Mt Barker SA 5251","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703222511_6712.jpg","https://maps.app.goo.gl/sMvN5e6RDAwMpEhg7"],
      [{ lat: -34.92059, lng: 138.63777 }, "Primary Dental Norwood","201–203 The Parade Norwood SA 5067","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703222137_0339.jpg","https://maps.app.goo.gl/9TSHyZtHWsv8SDpE9"],
      [{ lat: -34.80550, lng: 138.61593 }, "Adelaide Disability Medical Services","Shop1, 6-12 Capital St, Mawson Lakes, SA 5095","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703221765_2906.png","https://maps.app.goo.gl/LZqcvtwgcLUcJCDA9"],
      [{ lat: -34.97477, lng: 138.60948 }, "Pro Health Care Mitcham","105 Belair Rd, Torrens Park SA 5062","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703221384_0101.jpg","https://maps.app.goo.gl/Y2KfVX3kND8sgebp9"],
      [{ lat: -34.85181, lng: 138.50824 }, "Trinity Medical Centre - Port Adelaide","28 College St, Port Adelaide SA 5015","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703220868_5386.jpeg","https://maps.app.goo.gl/6ZfjKU9rZeCyViVT9"],
      [{ lat: -34.92708, lng: 138.63139 }, "Pro Health Care Norwood","93 Kensington Road, Norwood SA 5067","https://adelaideinside.com/data/editor/2312/493eb846c1d08b25da4a70a96acf1b0c_1703218742_0067.png","https://maps.app.goo.gl/ug3ZC5K5GZyPvo4s5"],
      [{ lat: -34.93980, lng: 138.63680 }, "East Adelaide Dental Studio","1 Allinga Ave, Glenside SA 5065","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702622525_8343.png","https://maps.app.goo.gl/Z3ewn4SvMThrNKSW7"],
      [{ lat: -34.92380, lng: 138.60057 }, "JYL Optical Outlet","29 James Pl, Adelaide SA 5000","https://adelaideinside.com/data/editor/2312/7eeda3c48a764178bea3003fdff56c24_1702622277_1957.png","https://maps.app.goo.gl/CfMdqeHq89i3aQGPA"],
      [{ lat: -34.90022, lng: 138.65673 }, "Glynde Veterinary Surgery","125 Glynburn Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2401/b6239d4397d0cf0be9c997f25aa4b601_1706492987_5318.png","https://maps.app.goo.gl/t8MsrycBqRjqrq3Z9"],
      [{ lat: -34.88948, lng: 138.65426 }, "Realign Health Chiro","5/471 Payneham Rd, Felixstow SA 5070","https://adelaideinside.com/data/editor/2404/9becc9fe41775f337d7222210f74ba20_1713318863_0234.png","https://maps.app.goo.gl/pLxtxRtAy1k6zn7t6"],
      [{ lat: -34.98509, lng: 138.51959 }, "Hands on Chiropractic & Health","1/54 Pier St, Glenelg South SA 5045","https://adelaideinside.com/data/editor/2407/826a9216ee4490a0d93c6b2b583febd1_1720052871_6292.png","https://maps.app.goo.gl/GnmVxCTfKpgC5bdt5"],
      
      // 추가적인 정육 tourstops을 여기에 추가
    ],
    기타: [
      [{ lat: -34.92255, lng: 138.59974 }, "Study SA 유학원","Level 3, 50 King William Street, Adelaide, SA 5000, Australia","https://adelaideinside.com/data/editor/2401/c74969568b0da1066b02b045ccf971bc_1704258275_0216.png","https://maps.app.goo.gl/ufcHMezs2Ps4xQtdA"],
      [{ lat: -34.92433, lng: 138.60087 }, "정에듀 Jung Education Australia","25 grenfell st. Adelaide SA 5000","https://adelaideinside.com/data/editor/2401/a3a06945bdb0b5a399e7e9dbd1491dd4_1704258020_8214.jpg","https://maps.app.goo.gl/ycWthuaiyM2QFpjG8"],
      [{ lat: -34.92365, lng: 138.60121 }, "Bravo RPL PTY LTD","25 Grenfell Street Adelaide, SA 5000","https://adelaideinside.com/data/editor/2403/c237e879efd95239c1021c735a247570_1711519494_1765.png","https://maps.app.goo.gl/ha6QBmzAuRoB3Dz8A"],
      [{ lat: -34.89126, lng: 138.65617 }, "GLEE PILATES","15-17 Glynburn Rd, Glynde SA 5070","https://adelaideinside.com/data/editor/2405/f80d0efb804175c2d0d94800d6d50b26_1715303374_6739.png","https://maps.app.goo.gl/7egpjJKJL2L3ktpJ8"],
      [{ lat: -34.92800, lng: 138.59994 }, "Kane's Painting & Decorating Pty Ltd","Adelaide, SA 5000","https://adelaideinside.com/data/editor/2407/826a9216ee4490a0d93c6b2b583febd1_1719794855_4819.jpeg","https://maps.app.goo.gl/t6QPbnAUKwUiuQeHA"],
      [{ lat: -34.80928, lng: 138.70138 }, "Raon painting","Adelaide, SA 5000","https://adelaideinside.com/data/editor/2407/826a9216ee4490a0d93c6b2b583febd1_1719798599_2254.png","https://maps.app.goo.gl/BQQA5PBnv5AvH4ba7"],
      [{ lat: -37.89377, lng: 145.13543 }, "쿠쿠 정수기","u12/21-35 Ricketts Rd, Mount Waverley VIC 3149","https://adelaideinside.com/data/editor/2407/97e9aafaf9abe526c2541f49f16bdb0e_1720156381_9223.png","https://maps.app.goo.gl/3ZMZ9eJa9b3nzYoz6"],
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
        
    <div id="map" style=" height:500px; margin-top:15px; border-radius:10px;"></div>

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
						<div class="img-wrap bg-light mb-2" style="padding-bottom:<?php echo $img_height ?>%; border-radius: 18px;">
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
