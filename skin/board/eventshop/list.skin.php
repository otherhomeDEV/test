<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

include_once($board_skin_path."/auction.lib.php");
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);


// 이미지 비율
$thumb_w = $board['bo_'.MOBILE_.'gallery_width'];
$thumb_h = $board['bo_'.MOBILE_.'gallery_height'];
$img_h = apms_img_height($thumb_w, $thumb_h); // 이미지 높이

// 너비
$item_w = apms_img_width($board['bo_gallery_cols']);

// 간격
$gap_right = ($boset['gap_r'] == "") ? 15 : $boset['gap_r'];
$gap_bottom = ($boset['gap_b'] == "") ? 30 : $boset['gap_b'];

$ellipsis = (G5_IS_MOBILE) ? '' : ' class="ellipsis"';
$ellipsis = ($thumb_h > 0) ? $ellipsis : '';

$list_cnt = count($list);
?>

<section class="board-list<?php echo (G5_IS_MOBILE) ? ' font-14' : '';?>">

	<?php if($notice_count > 0) include_once($board_skin_path.'/notice.skin.php'); // 공지사항	?>
	<?php //if($is_category) include_once($board_skin_path.'/category.skin.php'); // 카테고리	?>

<style>
.list-wrap .list-container { overflow:hidden; margin-right:<?php echo ($gap_right > 0) ? '-'.$gap_right : 0;?>px; margin-bottom:<?php echo ($gap_bottom > 15) ? 0 : 15;?>px; }
.list-wrap .list-row {
    width: 100%;
    margin-bottom: 20px;
    border: 1px solid #d4d4d4;
    background: #fff;
    box-shadow: 2px 2px 8px 0px #f5f5f5;
	float:left;
	height:122px;
}
.list-wrap .list-item { margin-right:<?php echo $gap_right;?>px;margin-bottom:<?php echo $gap_bottom;?>px;}
.img-wrap {
	width:140px;
	height: 0;
	float:left;
}
.img-wrap .img-item img {
	display: block;
	border: 0;
	width: 140px;
	height: 120px;
	margin: 0px auto;
}
.zhuantai{
	color: #fff;
	float: right;
    padding: 3px 8px;
    overflow: hidden;
    text-align: center;
    border-radius: 50px;	
}
</style>
	<div class="list-wrap">
		<form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post" role="form" class="form">
			<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
			<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
			<input type="hidden" name="stx" value="<?php echo $stx ?>">
			<input type="hidden" name="spt" value="<?php echo $spt ?>">
			<input type="hidden" name="sca" value="<?php echo $sca ?>">
			<input type="hidden" name="sst" value="<?php echo $sst ?>">
			<input type="hidden" name="sod" value="<?php echo $sod ?>">
			<input type="hidden" name="page" value="<?php echo $page ?>">
			<input type="hidden" name="sw" value="">

			<div class="list-container">
			<?php 
				$mod = $board[bo_gallery_cols];
			for ($i=0; $i<count($list); $i++) {
				//if ($i && $i%$mod==0) echo "</tr><tr><td height=10 colspan=2></td></tr><tr>";
				
				auction_successful($list[$i][wr_id]);
			
				$info = get_info_auction($list[$i][wr_id], $list[$i]);
			
				switch ($info[status]) {
					case 0: // 시작전
						if (strtotime($info[start_datetime]) > G5_SERVER_TIME) {
							$auction_status = "시작전";
							$auction_class = "act_status_begin";
							$auction_color = "#b41aed";
							$auction_width = "95px";
							break;
						}
					case 1: // 진행중
						$auction_status = "진행중";
						$auction_class = "act_status_ok";
						$auction_color = "#ffa800";
						$auction_width = "95px";
						break;
					case 2: // 낙찰
						$auction_status = "성공";
						$auction_class = "act_status_end";
						$auction_color = "#1955ff";
						$auction_width = "95px";
						break;
					case 3: // 유찰
						$auction_status = "실패";
						$auction_class = "act_status_no";
						$auction_color = "#999";
						$auction_width = "95px";
						break;
				}

					if($list[$i]['is_notice']) continue;		

					// 썸네일
					$list[$i]['no_img'] = $board_skin_url.'/img/no-img.jpg'; // No-Image
					$img = apms_wr_thumbnail($bo_table, $list[$i], $thumb_w, $thumb_h, false, true);

			?>
				<?php if($k > 0 && $k%$board['bo_gallery_cols'] == 0) { ?>
					<div class="clearfix"></div>
				<?php } ?>
				<div class="list-row">
					<div class="list-item">

                            <div class="img-wrap" style="float:left; width:140px;">
                                <div class="img-item">
                                    <a href="<?php echo $list[$i]['href'];?>">
                                        <img src="<?php echo $img['src'];?>" alt="<?php echo $img['alt'];?>">
                                    </a>
                                </div>
                            </div>

                            <div style="float:left; padding-left:10px; width:720px;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0;">
                                  <tr>
                                    <td height="35">
                                        <?php if ($is_checkbox) { ?>
                                            <label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject'] ?></label>
                                            <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                                        <?php } ?>
                                
                                            <!-- 경매정보 -->
                                            <a href="<?php echo $list[$i]['href'] ?>">
                                                <?php echo("<span style='color:#000;font-weight:bold;font-size:16px;'>".$info['company']."-".$info['product']."</span>"); //echo $list[$i]['subject'] ?>
                                                <?php if ($list[$i]['comment_cnt']) { ?><span class="sound_only">댓글</span><span style="color:red;"><?php echo $list[$i]['comment_cnt']; ?></span><span class="sound_only">개</span><?php } ?>
                                            </a>
                                     </td>
                                  </tr>
                                  <tr>
                                    <td height="30"><font style="color:#9a9a9a;">
                                        시작 <font style="color:#09F;"><?php echo(date("Y/m/d [H:i]",strtotime($info[start_datetime])));?></font> |
                                        종료 <font style="color:#09F;"><?php echo(date("Y/m/d [H:i]",strtotime($info[end_datetime])));?></font> |
                                        참여 <font style="color:#09F;"><?php echo(number_format($info[tender_count]));?></font> |
                                        조회 <font style="color:#09F;"><?php echo number_format($view['wr_hit']) ?></font>
                                        </font>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td height="1" bgcolor="#CCCCCC"></td>
                                  </tr>
                                  <tr>
                                    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 10px 0;">
                                  <tr>
                                    <td>
                                    <?php if($info[status]=='1' || $info[status]=='0'){ ?>
                                        구매가<span style="font-size:1.50em;color:red;font-weight:bold;"><?=$list[$i][wr_3]?></span>&nbsp;포인트
									<?php } else if($info[status]=='2'){?>
                                    	<span style="font-size:13px;color:<?php echo $auction_color;?>;font-weight:bold;"><font style="font-size:13px;color:#1955ff;font-weight:bold;">공동구매 성공 하셧습니다.</font></span>
                                    <?php } else if($info[status]=='3'){?>
                                    	<span style="font-size:13px;color:#999;font-weight:bold;">공동구매 실패 되였습니다.</span>
                                    <?php }?>
                                     </td>
                                    <td align="right" nowrap="nowrap"><span class="zhuantai" style="color:#fff;font-weight:bold;background:<?php echo $auction_color;?>;width:<?php echo $auction_width;?>"><i class="fa fa-gavel" aria-hidden="true"></i> <?php echo($auction_status);?></span></td>
                                  </tr>
                                </table>
                                </td>
                                  </tr>
                            </table>
                            </div>
					</div>
				</div>
			<?php $k++; } ?>
				<div class="clearfix"></div>
			</div>

			<?php if (!$is_list) { ?>
				<div class="text-center text-muted list-none">게시물이 없습니다.</div>
			<?php } ?>

			<div class="list-btn-box">
				<?php if ($list_href || $write_href) { ?>
					<div class="form-group pull-right list-btn">
						<div class="btn-group dropup">
							<?php if ($boset['sort']) { ?>
								<a id="sortLabel" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-black btn-sm">
									<i class="fa fa-sort"></i><span>정렬</span>
								</a>
								<ul class="dropdown-menu" role="menu" aria-labelledby="sortLabel">
									<li<?php echo ($sst == 'wr_datetime') ? ' class="sort"' : '';?>><?php echo subject_sort_link('wr_datetime', $qstr2, 1) ?>날짜순</a></li>
									<li<?php echo ($sst == 'wr_hit') ? ' class="sort"' : '';?>><?php echo subject_sort_link('wr_hit', $qstr2, 1) ?>조회순</a></li>
									<?php if ($is_good) { ?>
										<li<?php echo ($sst == 'wr_good') ? ' class="sort"' : '';?>><?php echo subject_sort_link('wr_good', $qstr2, 1) ?>추천순</a></li>
									<?php } ?>
									<?php if ($is_nogood) { ?>
										<li<?php echo ($sst == 'wr_nogood') ? ' class="sort"' : '';?>><?php echo subject_sort_link('wr_nogood', $qstr2, 1) ?>비추천순</a></li>
									<?php } ?>
									<li><a href="./board.php?bo_table=<?php echo $bo_table;?>&amp;sca=<?php echo urlencode($sca);?>">초기화</a></li>
								</ul>
							<?php } ?>
							<?php if ($list_href) { ?><a href="<?php echo $list_href ?>" class="btn btn-black btn-sm"><i class="fa fa-bars"></i><span>목록</span></a><?php } ?>
							<?php if ($write_href) { ?><a href="<?php echo $write_href ?>" class="btn btn-color btn-sm"><i class="fa fa-pencil"></i><span>글쓰기</span></a><?php } ?>
						</div>
					</div>
				<?php } ?>
				<div class="form-group list-btn font-12">
					<div class="btn-group">
						<a href="#" class="btn btn-black btn-sm" data-toggle="modal" data-target="#searchModal" onclick="return false;"><i class="fa fa-search"></i></a>
						<?php if ($rss_href) { ?>
							<a href="<?php echo $rss_href; ?>" class="btn btn-color btn-sm"><i class="fa fa-rss"></i></a>
						<?php } ?>
						<?php if ($is_checkbox || $setup_href || $admin_href) { ?>
							<?php if ($is_checkbox) { ?>
								<button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value" class="btn btn-black btn-sm"><i class="fa fa-times"></i><span class="hidden-xs"> 선택삭제</span></button>
								<button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value" class="btn btn-black btn-sm"><i class="fa fa-clipboard"></i><span class="hidden-xs"> 선택복사</span></button>
								<button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value" class="btn btn-black btn-sm"><i class="fa fa-arrows"></i><span class="hidden-xs"> 선택이동</span></button>
								<button type="button" id="btn_chkall" class="btn btn-black btn-sm"><i class="fa fa-check"></i><span class="hidden-xs"> 전체선택</span></button>
							<?php } ?>
							<?php if ($admin_href) { ?>
								<a href="<?php echo $admin_href; ?>" class="btn btn-black btn-sm"><i class="fa fa-cog"></i></a>
							<?php } ?>
							<?php if ($setup_href) { ?>
								<a href="<?php echo $setup_href; ?>" class="btn btn-color btn-sm win_memo"><i class="fa fa-cogs"></i><span class="hidden-xs"> 설정</span></a>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</form>

		<?php if($total_count > 0) { ?>
			<div class="list-page text-center">
				<ul class="pagination pagination-sm en">
					<?php if($prev_part_href) { ?>
						<li><a href="<?php echo $prev_part_href;?>">이전검색</a></li>
					<?php } ?>
					<?php echo apms_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, './board.php?bo_table='.$bo_table.$qstr.'&amp;page=');?>
					<?php if($next_part_href) { ?>
						<li><a href="<?php echo $next_part_href;?>">다음검색</a></li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>

		<div class="clearfix"></div>

		<?php if($is_checkbox) { ?>
			<noscript>
			<p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
			</noscript>
		<?php } ?>

		<div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-body">
						<div class="text-center">
							<h4 id="myModalLabel"><i class="fa fa-search fa-lg"></i> Search</h4>
						</div>
						<form name="fsearch" method="get" role="form" class="form" style="margin-top:20px;">
							<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
							<input type="hidden" name="sca" value="<?php echo $sca ?>">
							<input type="hidden" name="sop" value="and">
							<div class="form-group">
								<label for="sfl" class="sound_only">검색대상</label>
								<select name="sfl" id="sfl" class="form-control input-sm">
									<option value="wr_subject"<?php echo get_selected($sfl, 'wr_subject', true); ?>>제목</option>
									<option value="wr_content"<?php echo get_selected($sfl, 'wr_content'); ?>>내용</option>
									<option value="wr_subject||wr_content"<?php echo get_selected($sfl, 'wr_subject||wr_content'); ?>>제목+내용</option>
									<option value="mb_id,1"<?php echo get_selected($sfl, 'mb_id,1'); ?>>회원아이디</option>
									<option value="mb_id,0"<?php echo get_selected($sfl, 'mb_id,0'); ?>>회원아이디(코)</option>
									<option value="wr_name,1"<?php echo get_selected($sfl, 'wr_name,1'); ?>>글쓴이</option>
									<option value="wr_name,0"<?php echo get_selected($sfl, 'wr_name,0'); ?>>글쓴이(코)</option>
								</select>
							</div>
							<div class="form-group">
								<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
								<input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required id="stx" class="form-control input-sm" maxlength="20" placeholder="검색어">
							</div>

							<div class="btn-group btn-group-justified">
								<div class="btn-group">
									<button type="submit" class="btn btn-color"><i class="fa fa-check"></i></button>
								</div>
								<div class="btn-group">
									<button type="button" class="btn btn-black" data-dismiss="modal"><i class="fa fa-times"></i></button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
	var f = document.fboardlist;

	for (var i=0; i<f.length; i++) {
		if (f.elements[i].name == "chk_wr_id[]")
			f.elements[i].checked = sw;
	}
}
$(function(){
	$("#btn_chkall").click(function(){
		var clicked_checked = $(this);

		if(clicked_checked.hasClass('active')) {
			all_checked(false);
			clicked_checked.removeClass('active');
		} else {
			all_checked(true);
			clicked_checked.addClass('active');
		}
	});
});
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
		if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
			return false;

		f.removeAttribute("target");
		f.action = "./board_list_update.php";
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
	f.action = "./move.php";
	f.submit();
}
</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->
