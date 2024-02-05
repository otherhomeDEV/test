<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// input의 name을 wset[배열키], mo[배열키] 형태로 등록
// 기본은 wset[배열키], 모바일 설정은 mo[배열키] 형식을 가짐

// 아이콘 선택기
na_script('iconpicker');

?>

<ul class="list-group">
	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">제외 회원</label>
			<div class="col-sm-10">
				<input type="text" name="wset[mb_list]" value="<?php echo $wset['mb_list'] ?>" class="form-control">
				<p class="form-text text-muted pb-0 mb-0">
					회원아이디(mb_id)를 콤마(,)로 구분해서 복수 등록 가능. ex) 최고관리자 아이디
				</p>
			</div>
		</div>
	</li>
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
					<td class="text-center">랭크 표시</td>
					<td>
						<select name="wset[rank]" class="custom-select">
							<option value=""<?php echo get_selected('', $wset['rank']); ?>>표시 안 함</option>
							<?php echo na_color_options($wset['rank']);?>
						</select>
					</td>
					<td class="text-muted">
						&nbsp;
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
					<td class="text-center">추출 방법</td>
					<td>
						<select name="wset[mode]" class="custom-select">
							<?php echo na_member_options($wset['mode']);?>
						</select>
					</td>
					<td class="text-muted">
						기간설정시 적립된 것(양수)만 체크함
					</td>
					</tr>
					<tr>
					<td class="text-center">PC 추출수</td>
					<td>
						<div class="input-group">
							<input type="text" name="wset[rows]" value="<?php echo $wset['rows'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">명</span>
							</div>
						</div>
					</td>
					<td class="text-muted">
						&nbsp;
					</td>
					</tr>
					<tr>
					<td class="text-center">모바일 추출수</td>
					<td>
						<div class="input-group">
							<input type="text" name="mo[rows]" value="<?php echo $mo['rows'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">명</span>
							</div>
						</div>
					</td>
					<td class="text-muted">
						&nbsp;
					</td>
					</tr>
					<tr>
					<td class="text-center">기간 설정</td>
					<td>
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
