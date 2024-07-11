<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// input의 name을 wset[배열키], mo[배열키] 형태로 등록
// 기본은 wset[배열키], 모바일 설정은 mo[배열키] 형식을 가짐

?>

<ul class="list-group">
	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">출력 설정</label>
			<div class="col-sm-10">
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
					<tbody>
					<tr class="bg-light">
					<th class="text-center nw-c1">구분</th>
					<th class="text-center nw-c2">설정</th>
					<th class="text-center">비고</th>
					</tr>
					<tr>
					<td class="text-center">캐시 설정</td>
					<td>
						<div class="input-group">
							<?php $wset['cache'] = isset($wset['cache']) ? $wset['cache'] : ''; ?>
							<input type="text" name="wset[cache]" value="<?php echo $wset['cache'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">분</span>
							</div>
						</div>
					</td>
					<td class="text-muted">
						&nbsp;
					</td>
					</tr>
					<tr>
					<td class="text-center">새 글 표시</td>
					<td>
						<div class="input-group">
							<?php $wset['bo_new'] = isset($wset['bo_new']) ? $wset['bo_new'] : ''; ?>
							<input type="text" name="wset[bo_new]" value="<?php echo $wset['bo_new'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">시</span>
							</div>
						</div>
					</td>
					<td class="text-muted">
						기본값 : 24시간 이내 등록 글
					</td>
					</tr>
					<tr>
					<td class="text-center">새 글 색상</td>
					<td class="text-center">
						<?php $wset['new'] = isset($wset['new']) ? $wset['new'] : ''; ?>
						<select name="wset[new]" class="custom-select">
							<option value=""<?php echo get_selected('', $wset['new']); ?>>자동 컬러</option>
							<?php echo na_color_options($wset['new']);?>
						</select>
					</td>
					<td class="text-muted">
						&nbsp;
					</td>
					</tr>
					<tr>
					<td class="text-center">글 이동</td>
					<td class="text-center">
						<?php $wset['target'] = isset($wset['target']) ? $wset['target'] : ''; ?>
						<select name="wset[target]" class="custom-select">
							<?php echo na_target_options($wset['target']);?>
						</select>
					</td>
					<td class="text-muted">
						글 내용 또는 관련 링크1 페이지로 이동
					</td>
					</tr>
					<tr>
					<td class="text-center">보드/분류명</td>
					<td>
						<div class="input-group">
							<?php $wset['bo_name'] = isset($wset['bo_name']) ? $wset['bo_name'] : ''; ?>
							<input type="text" name="wset[bo_name]" value="<?php echo $wset['bo_name'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">자</span>
							</div>
						</div>
					</td>
					<td class="text-muted">
						복수 추출시 보드명, 단일 게시판 출력시 분류명 출력 (0 설정시 자르지 않음, 미설정시 출력안함)
					</td>
					</tr>


					<tr>
					<td class="text-center" rowspan="2">분류 색상</td>
					<td class="text-center">
						<?php $wset['cc_rand'] = isset($wset['cc_rand']) ? $wset['cc_rand'] : ''; ?>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" name="wset[cc_rand]" value="1"<?php echo get_checked('1', $wset['cc_rand'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					<td class="text-muted">
						랜덤 색상
					</td>
					</tr>
					<tr>
					<td>
						<?php $wset['c_color'] = isset($wset['c_color']) ? $wset['c_color'] : ''; ?>
						<select name="wset[c_color]" class="custom-select">
							<option value=""<?php echo get_selected('', $wset['c_color']); ?>>자동 컬러</option>
							<?php echo na_color_options($wset['c_color']);?>
						</select>
					</td>
					<td class="text-muted">
						분류 색상설정
					</td>
					</tr>

					</tbody>
					</table>
				</div>
			</div>
		</div>
	</li>

	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">슬라이더 옵션</label>
			<div class="col-sm-10">
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
					<tbody>
					<tr class="bg-light">
					<th class="text-center nw-c1">구분</th>
					<th class="text-center nw-c2">설정</th>
					<th class="text-center">비고</th>
					</tr>
					<tr>
					<td class="text-center">스피드</td>
					<td class="text-center">
						<?php $wset['speed'] = isset($wset['speed']) ? $wset['speed'] : ''; ?>
						<input type="text" name="wset[speed]" value="<?php echo $wset['speed'] ?>" class="form-control" placeholder="">
					</td>
					<td class="text-muted">
						기본:500
					</td>
					</tr>
					<tr>
					<td class="text-center">fade</td>
					<td class="text-center">
						<?php $wset['fade'] = isset($wset['fade']) ? $wset['fade'] : ''; ?>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" name="wset[fade]" value="1"<?php echo get_checked('1', $wset['fade'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					<td class="text-muted">
						fade off
					</td>
					</tr>
					<tr>
					<td class="text-center">수동 실행</td>
					<td class="text-center">
						<?php $wset['auto'] = isset($wset['auto']) ? $wset['auto'] : ''; ?>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" name="wset[auto]" value="1"<?php echo get_checked('1', $wset['auto'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					<td class="text-muted">
						autoplay
					</td>
					</tr>
					<tr>
					<td class="text-center">화살표 숨김</td>
					<td class="text-center">
						<?php $wset['arrows'] = isset($wset['arrows']) ? $wset['arrows'] : ''; ?>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" name="wset[arrows]" value="1"<?php echo get_checked('1', $wset['arrows'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					<td class="text-muted">
						화살표출력
					</td>
					</tr>
					<tr>
					<td class="text-center">도트 출력</td>
					<td class="text-center">
						<?php $wset['dots'] = isset($wset['dots']) ? $wset['dots'] : ''; ?>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" name="wset[dots]" value="1"<?php echo get_checked('1', $wset['dots'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					<td class="text-muted">
						도트출력
					</td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</li>

	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">이미지 설정</label>
			<div class="col-sm-10">
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
					<tbody>
					<tr class="bg-light">
					<th class="text-center nw-c1">구분</th>
					<th class="text-center nw-c2">설정</th>
					<th class="text-center">비고</th>
					</tr>
					<tr>
					<td class="text-center">라운드</td>
					<td class="text-center">
						<?php $wset['round'] = isset($wset['round']) ? $wset['round'] : ''; ?>
						<select name="wset[round]" class="custom-select">
							<option value=""<?php echo get_selected('', $wset['round']);?>>라운드없음</option>
							<option value="1"<?php echo get_selected('1', $wset['round']);?>>na-r1</option>
							<option value="2"<?php echo get_selected('2', $wset['round']);?>>na-r2</option>
							<option value="3"<?php echo get_selected('3', $wset['round']);?>>na-r3</option>
							<option value="4"<?php echo get_selected('4', $wset['round']);?>>na-r4</option>
							<option value="5"<?php echo get_selected('5', $wset['round']);?>>na-r5</option>
						</select>
					</td>
					<td class="text-muted">
						&nbsp;
					</td>
					</tr>
					<tr>
					<td class="text-center">썸네일 너비</td>
					<td>
						<div class="input-group">
							<?php $wset['thumb_w'] = isset($wset['thumb_w']) ? $wset['thumb_w'] : ''; ?>
							<input type="text" name="wset[thumb_w]" value="<?php echo $wset['thumb_w'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">px</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 400 - 0 설정시 썸네일 생성 안 함</td>
					</tr>
					<tr>
					<td class="text-center">썸네일 높이</td>
					<td class="text-center">
						<div class="input-group">
							<?php $wset['thumb_h'] = isset($wset['thumb_h']) ? $wset['thumb_h'] : ''; ?>
							<input type="text" name="wset[thumb_h]" value="<?php echo $wset['thumb_h'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">px</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 225 - 0 설정시 이미지 비율대로 생성</td>
					</tr>
					<tr>
					<td class="text-center">기본 높이</td>
					<td>
						<div class="input-group">
							<?php $wset['thumb_d'] = isset($wset['thumb_d']) ? $wset['thumb_d'] : ''; ?>
							<input type="text" name="wset[thumb_d]" value="<?php echo $wset['thumb_d'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">%</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 56.25 - 썸네일 높이를 0으로 설정시 적용</td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</li>

	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">추출 대상</label>
			<div class="col-sm-10">
				<p class="form-control-plaintext">
					<i class="fa fa-caret-right" aria-hidden="true"></i>
					콤마(,)로 구분해서 복수 등록 가능하며, 그룹 또는 복수 게시판 추출시 검색 가능한 게시판만 추출됨
				</p>
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
					<tbody>
					<tr class="bg-light">
					<th class="text-center nw-c1">구분</th>
					<th class="text-center">대상 설정</th>
					<th class="text-center nw-c1">제외 설정</th>
					</tr>

					<tr>
					<td class="text-center">게시판 그룹</td>
					<td>
						<?php $wset['gr_list'] = isset($wset['gr_list']) ? $wset['gr_list'] : ''; ?>
						<input type="text" name="wset[gr_list]" value="<?php echo $wset['gr_list'] ?>" class="form-control" placeholder="그룹아이디(gr_id)">
					</td>
					<td class="text-center">
						<div class="custom-control custom-checkbox">
							<?php $wset['gr_except'] = isset($wset['gr_except']) ? $wset['gr_except'] : ''; ?>
							<input type="checkbox" name="wset[gr_except]" value="1"<?php echo get_checked('1', $wset['gr_except'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					</tr>
					<tr>
					<td class="text-center">게시판</td>
					<td>
						<?php $wset['bo_list'] = isset($wset['bo_list']) ? $wset['bo_list'] : ''; ?>
						<input type="text" name="wset[bo_list]" value="<?php echo $wset['bo_list'] ?>" class="form-control" placeholder="게시판아이디(bo_table)">
					</td>
					<td class="text-center">
						<div class="custom-control custom-checkbox">
							<?php $wset['bo_except'] = isset($wset['bo_except']) ? $wset['bo_except'] : ''; ?>
							<input type="checkbox" name="wset[bo_except]" value="1"<?php echo get_checked('1', $wset['bo_except'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					</tr>
					<tr>
					<td class="text-center">글 분류</td>
					<td>
						<?php $wset['ca_list'] = isset($wset['ca_list']) ? $wset['ca_list'] : ''; ?>
						<input type="text" name="wset[ca_list]" value="<?php echo $wset['ca_list'] ?>" class="form-control" placeholder="분류명(ca_name) - 단일게시판 추출시에만 작동함">
					</td>
					<td class="text-center">
						<div class="custom-control custom-checkbox">
							<?php $wset['ca_except'] = isset($wset['ca_except']) ? $wset['ca_except'] : ''; ?>
							<input type="checkbox" name="wset[ca_except]" value="1"<?php echo get_checked('1', $wset['ca_except'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					</tr>
					<tr>
					<td class="text-center">등록 회원</td>
					<td>
						<?php $wset['mb_list'] = isset($wset['mb_list']) ? $wset['mb_list'] : ''; ?>
						<input type="text" name="wset[mb_list]" value="<?php echo $wset['mb_list'] ?>" class="form-control" placeholder="회원아이디(mb_id)">
					</td>
					<td class="text-center">
						<div class="custom-control custom-checkbox">
							<?php $wset['mb_except'] = isset($wset['mb_except']) ? $wset['mb_except'] : ''; ?>
							<input type="checkbox" name="wset[mb_except]" value="1"<?php echo get_checked('1', $wset['mb_except'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</li>

	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">추출 옵션</label>
			<div class="col-sm-10">
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
					<tbody>
					<tr class="bg-light">
					<th class="text-center nw-c1">구분</th>
					<th class="text-center nw-c2">설정</th>
					<th class="text-center">비고</th>
					</tr>
					<tr>
					<td class="text-center">랜덤 출력</td>
					<td class="text-center">
						<?php $wset['rand'] = isset($wset['rand']) ? $wset['rand'] : ''; ?>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" name="wset[rand]" value="1"<?php echo get_checked('1', $wset['rand'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					<td class="text-muted">
						게시물 랜덤출력
					</td>
					</tr>
					<tr>
					<td class="text-center">제목 줄</td>
					<td class="text-center">
						<div class="input-group">
							<?php $wset['subject_line'] = (!isset($wset['subject_line']) || $wset['subject_line'] == "") ? 1 : (int)$wset['subject_line']; ?>
							<input type="text" name="wset[subject_line]" value="<?php echo $wset['subject_line'] ?>" class="form-control" placeholder="">
							<div class="input-group-append">
								<span class="input-group-text">줄</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 1줄</td>
					</tr>
					<tr>
					<td class="text-center">글내용 길이</td>
					<td class="text-center">
						<div class="input-group">
							<?php $wset['wcut'] = (!isset($wset['wcut']) || $wset['wcut'] == "") ? 80 : (int)$wset['wcut']; ?>
							<input type="text" name="wset[wcut]" value="<?php echo $wset['wcut'] ?>" class="form-control" placeholder="80">
							<div class="input-group-append">
								<span class="input-group-text">자</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 80자</td>
					</tr>
					<tr>
					<td class="text-center">회원정보 숨김</td>
					<td class="text-center">
						<?php $wset['profile'] = isset($wset['profile']) ? $wset['profile'] : ''; ?>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" name="wset[profile]" value="1"<?php echo get_checked('1', $wset['profile'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					<td class="text-muted">
						회원정보 출력여부
					</td>
					</tr>
					<?php if(IS_NA_BBS) { ?>
					<tr>
					<td class="text-center">메인글</td>
					<td class="text-center">
						<div class="custom-control custom-checkbox">
							<?php $wset['main'] = isset($wset['main']) ? $wset['main'] : ''; ?>
							<input type="checkbox" name="wset[main]" value="1"<?php echo get_checked('1', $wset['main'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					<td class="text-muted">
						메인글 체크된 게시물만 추출
					</td>
					</tr>
					<?php } ?>
					<tr>
					<td class="text-center">PC 목록수</td>
					<td>
						<div class="input-group">
							<?php $wset['rows'] = isset($wset['rows']) ? $wset['rows'] : ''; ?>
							<input type="text" name="wset[rows]" value="<?php echo $wset['rows'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">개</span>
							</div>
						</div>
					</td>
					<td class="text-muted">
						&nbsp;
					</td>
					</tr>
					<tr>
					<td class="text-center">모바일 목록수</td>
					<td>
						<div class="input-group">
							<?php $mo['rows'] = isset($mo['rows']) ? $mo['rows'] : ''; ?>
							<input type="text" name="mo[rows]" value="<?php echo $mo['rows'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">개</span>
							</div>
						</div>
					</td>
					<td class="text-muted">
						&nbsp;
					</td>
					</tr>
					<tr>
					<td class="text-center">추출 페이지</td>
					<td>
						<div class="input-group">
							<?php $wset['page'] = isset($wset['page']) ? $wset['page'] : ''; ?>
							<input type="text" name="wset[page]" value="<?php echo $wset['page'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">쪽</span>
							</div>
						</div>
					</td>
					<td class="text-muted">
						추출 목록수 기준 페이지임
					</td>
					</tr>
					<tr>
					<td class="text-center">정렬 방법</td>
					<td>
						<?php $wset['sort'] = isset($wset['sort']) ? $wset['sort'] : ''; ?>
						<select name="wset[sort]" class="custom-select">
							<?php echo na_sort_options($wset['sort']);?>
						</select>
					</td>
					<td class="text-muted">
						복수 게시판 추출시 날짜 정렬만 작동함
					</td>
					</tr>
					<tr>
					<td class="text-center">기간 설정</td>
					<td>
						<?php $wset['term'] = isset($wset['term']) ? $wset['term'] : ''; ?>
						<select name="wset[term]" class="custom-select">
							<?php echo na_term_options($wset['term']);?>
						</select>
					</td>
					<td class="text-muted">
						&nbsp;
					</td>
					</tr>
					<tr>
					<td class="text-center">일자 지정</td>
					<td>
						<div class="input-group">
							<?php $wset['dayterm'] = isset($wset['dayterm']) ? $wset['dayterm'] : ''; ?>
							<input type="text" name="wset[dayterm]" value="<?php echo $wset['dayterm'];?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">일</span>
							</div>
						</div>
					</td>
					<td class="text-muted">
						기간 설정에서 일자 지정시 작동함
					</td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</li>

</ul>
