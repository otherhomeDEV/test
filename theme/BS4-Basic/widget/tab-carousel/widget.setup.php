<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// input의 name을 wset[배열키], mo[배열키] 형태로 등록
// 기본은 wset[배열키], 모바일 설정은 mo[배열키] 형식을 가짐

// 데모
if(IS_DEMO && (!isset($wset['d']) || !is_array($wset['d'])) && isset($wset['demo']) && $wset['demo']) {
	@include($widget_path.'/demo/'.$wset['demo'].'.php');
}
?>

<ul class="list-group">
	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">타이틀</label>
			<div class="col-sm-10">
				<?php $wset['title'] = isset($wset['title']) ? $wset['title'] : ''; ?>
				<textarea type="text" name="wset[title]" rows="2" class="form-control"><?php echo $wset['title'] ?></textarea>
			</div>
		</div>
	</li>
	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">탭 설정</label>
			<div class="col-sm-10">
				<style>
					#widgetData.table { border-left:0; border-right:0; }
					#widgetData thead th { border-bottom:0; }
					#widgetData th,
					#widgetData td { vertical-align:middle; border-left:0; border-right:0; }
				</style>

				<p class="form-control-plaintext">
					위젯 폴더명이 있는 것만 출력되며, 마우스 드래그로 위치 이동이 가능합니다.
				</p>

				<div class="table-responsive">
					<table id="widgetData" class="table table-bordered order-list mb-0">
					<thead>
					<tr class="bg-light">
						<th class="text-center nw-15">탭 이름</th>
						<th class="text-center nw-10">위젯 폴더명</th>
						<th class="text-center nw-10">설정값 아이디</th>
						<th class="text-center">위젯 초기값</th>
						<th class="text-center nw-4">삭제</th>
					</tr>
					</thead>
					<tbody id="sortable">
					<?php 
					// 직접등록 입력폼 
					$data = array();
					$data_cnt = (isset($wset['d']['tab']) && is_array($wset['d']['tab'])) ? count($wset['d']['tab']) : 1;

					for($i=0; $i < $data_cnt; $i++) {
						$n = $i + 1;
						$d_tab = isset($wset['d']['tab'][$i]) ? $wset['d']['tab'][$i] : '';
						$d_wname = isset($wset['d']['wname'][$i]) ? $wset['d']['wname'][$i] : '';
						$d_wid = isset($wset['d']['wid'][$i]) ? $wset['d']['wid'][$i] : '';
						$d_wset = isset($wset['d']['wset'][$i]) ? $wset['d']['wset'][$i] : '';

					?>
						<tr class="bg-light<?php echo ($i%2 != 0) ? '' : '-1';?>">
						<td>
							<textarea id="tab_<?php echo $n ?>" name="wset[d][tab][]" rows="1" class="form-control"><?php echo $d_tab ?></textarea>
						</td>
						<td>
							<input type="text" id="wname_<?php echo $n ?>" name="wset[d][wname][]" value="<?php echo $d_wname ?>" class="form-control">
						</td>
						<td>
							<input type="text" id="wid_<?php echo $n ?>" name="wset[d][wid][]" value="<?php echo $d_wid ?>" class="form-control">
						</td>
						<td>
							<input type="text" id="wset_<?php echo $n ?>" name="wset[d][wset][]" value="<?php echo $d_wset ?>" class="form-control">
						</td>
						<td class="text-center">
							<?php if($i > 0) { ?>
								<a href="javascript:;" class="ibtnDel"><i class="fa fa-times-circle fa-2x text-muted"></i></a>
							<?php } ?>
						</td>
						</tr>
					<?php } ?>
					</tbody>
					</table>
				</div>

				<div class="text-center mt-3">
					<button type="button" class="btn btn-outline-primary btn-lg en" id="addrow">
						Add Tab
					</button>
				</div>	
			</div>
		</div>
	</li>

	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">탭 옵션</label>
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
					<td class="text-center">탭 전환 효과</td>
					<td class="text-center">
						<?php $wset['effect'] = isset($wset['effect']) ? $wset['effect'] : ''; ?>
						<select name="wset[effect]" class="custom-select">
							<option value=""<?php echo get_selected('', $wset['effect']); ?>>효과없음</option>
							<option value="slide"<?php echo get_selected('slide', $wset['effect']); ?>>슬라이드</option>
							<option value="fade"<?php echo get_selected('fade', $wset['effect']); ?>>페이드</option>
						</select>
					</td>
					<td class="text-muted">
						&nbsp;
					</td>
					</tr>
					<tr>
					<td class="text-center">선택 탭 컬러</td>
					<td class="text-center">
						<?php $wset['active'] = isset($wset['active']) ? $wset['active'] : ''; ?>
						<input type="text" name="wset[active]" value="<?php echo $wset['active'] ?>" class="form-control" placeholder="#007bff">
					</td>
					<td class="text-muted">
						&nbsp;
					</td>
					</tr>
					<tr>
					<td class="text-center">자동 실행</td>
					<td class="text-center">
						<div class="input-group">
							<?php $wset['auto'] = isset($wset['auto']) ? $wset['auto'] : ''; ?>
							<input type="text" name="wset[auto]" value="<?php echo $wset['auto'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">ms</span>
							</div>
						</div>
					</td>
					<td class="text-muted">
						1000(1초)이상 값 설정시에만 작동함
					</td>
					</tr>
					<tr>
					<td class="text-center">랜덤 출력</td>
					<td class="text-center">
						<div class="custom-control custom-checkbox">
							<?php $wset['rand'] = isset($wset['rand']) ? $wset['rand'] : ''; ?>
							<input type="checkbox" name="wset[rand]" value="1"<?php echo get_checked('1', $wset['rand'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
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
</ul>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script> 
<script>
$(document).ready(function () {
	var counter = <?php echo $data_cnt + 1 ?>;
	$("#addrow").on("click", function () {
		var trbg = (counter%2 === 1) ? 'bg-light-1' : 'bg-light';
		var newRow = $("<tr class=" + trbg + ">");
		var cols = "";

		cols += '<td>';
		cols += '	<textarea id="tab_' + counter + '" name="wset[d][tab][]" rows="1" class="form-control"></textarea>';
		cols += '</td>';
		cols += '<td>';
		cols += '	<input type="text" id="wname_' + counter + '" name="wset[d][wname][]" class="form-control">';
		cols += '</td>';
		cols += '<td>';
		cols += '	<input type="text" id="wid_' + counter + '" name="wset[d][wid][]" class="form-control">';
		cols += '</td>';
		cols += '<td>';
		cols += '	<input type="text" id="wset_' + counter + '" name="wset[d][wset][]" class="form-control">';
		cols += '</td>';
		cols += '<td class="text-center">';
		cols += '	<a href="javascript:;" class="ibtnDel"><i class="fa fa-times-circle fa-2x text-muted"></i></a>';
		cols += '</td>';

		newRow.append(cols);
		$("table.order-list").append(newRow);
		counter++;
	});

	$("table.order-list").on("click", ".ibtnDel", function (event) {
		$(this).closest("tr").remove();
	});

	$("#sortable").sortable();
});
</script>
