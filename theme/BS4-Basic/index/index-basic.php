<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// WING
if($is_wing)
	@include_once (G5_THEME_PATH.'/_wing.php');
?>

<div class="nt-container px-0 px-sm-4 px-xl-0" style="margin-top:15px;" >
	<div class="row na-row">

		
		<!-- 메인 영역 -->
		<div class="col-md-9 na-col" style="margin-top:10px;">

			<!-- 위젯 시작 { -->
				<h3 class="h3 f-lg en">
			    <a href="<?php echo G5_BBS_URL ?>/group.php?gr_id=news">
				    최신뉴스
				</a>
			</h3>
			<hr class="hr"/>
			<div class="px-3 px-sm-0 mt-3 mb-4"> <!--style="height:220px;"> 위젯 레이아웃 아래마진 - kayden { -->
			<?php echo na_widget('wr-news-big', 'wr-news-big-1'); ?>
			</div>
			<!-- } 위젯 끝-->
		
			<div class="row na-row" style="margin-top:50px;">
			    	<div class="col-md-4 na-col">  <!--  style="margin-bottom:40px;" 위젯 레이아웃 아래마진 - kayden { -->

				  <!-- 위젯 시작 { -->
                <h3 class="h3 f-lg en mb-2">
	                <a href="<?php echo G5_BBS_URL ?>/new.php?view=w">
		                최근글
	                </a>
                </h3>
                <hr class="hr"/>
                    <div class="mt-3 mb-4">
	                    <?php echo na_widget('wr-list', 'new-wr', 'bo_list=board'); ?>
                    </div>
                    <!-- } 위젯 끝-->
				</div>
				<div class="col-md-4 na-col">  <!--  style="margin-bottom:40px;" 위젯 레이아웃 아래마진 - kayden { -->

					
                    <!-- 위젯 시작 { -->
                <h3 class="h3 f-lg en" >
                    <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=freeboard">
		                아무거나 말해요^^
                    </a>
                </h3>
                <hr class="hr"/>
                <div class="mt-3 mb-4">
	                <?php echo na_widget('wr-list', 'freeboard', 'bo_list=freeboard'); ?>
                </div>
                    <!-- } 위젯 끝-->
					
				</div>

				<div class="col-md-4 na-col">  <!--  style="margin-bottom:40px;" 위젯 레이아웃 아래마진 - kayden { -->

					<!-- 위젯 시작 { -->
					<h3 class="h3 f-lg en mb-2">
					  <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=event">
						  업체게시판 및 안내
					  </a>
					</h3>
					<hr class="hr"/>
					  <div class="mt-3 mb-4">
						  <?php echo na_widget('wr-list', 'tlist-1', 'bo_table=event'); ?>
					  </div>
					  <!-- } 위젯 끝-->
				</div>
	
			</div>



			
			<!-- 위젯 시작 { -->
				<h3 class="h3 f-lg en" style="margin-top:20px;">
				<a href="<?php echo G5_BBS_URL ?>/group.php?gr_id=food">
					맛집 & 여행
				</a>
			</h3>
			<hr class="hr"/>
			<div class="px-3 px-sm-0 my-3">
				<?php echo na_widget('wr-gallery', 'gallery-1', 'gr_id=food'); ?>
			</div>
			<!-- } 위젯 끝 end-->


			<div class="row na-row" style="margin-top:25px;">
			<div class="col-md-4 na-col">  <!--  style="margin-bottom:40px;" 위젯 레이아웃 아래마진 - kayden { -->

				<!-- 위젯 시작 { -->
				<h3 class="h3 f-lg en">
					<a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=used_item">
						인사이드 마켓
					</a>
				</h3>
				<hr class="hr"/>
				<div class="mt-3 mb-4">
					<?php echo na_widget('wr-list', 'tlist-4', 'gr_id=fleamarket'); ?>
			</div>
				<!-- } 위젯 끝-->

</div>
				<div class="col-md-4 na-col">  <!--  style="margin-bottom:40px;" 위젯 레이아웃 아래마진 - kayden { -->

					
                    <!-- 위젯 시작 { -->
                <h3 class="h3 f-lg en" >
					<a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=findjob">
						일자리
					</a>
                </h3>
                <hr class="hr"/>
                <div class="mt-3 mb-4">
					<?php echo na_widget('wr-list', 'tlist-2', 'gr_id=jobs'); ?>
                </div>
                    <!-- } 위젯 끝-->

				</div>
				<div class="col-md-4 na-col">  <!--  style="margin-bottom:40px;" 위젯 레이아웃 아래마진 - kayden { -->

					<!-- 위젯 시작 { -->
					<h3 class="h3 f-lg en">
						<a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=share">
							렌트/쉐어
						</a>
					</h3>
					<hr class="hr"/>
					<div class="mt-3 mb-4">
						<?php echo na_widget('wr-list', 'tlist-3', 'gr_id=property'); ?>
					</div>
					<!-- } 위젯 끝-->

				</div>
	
			</div>
			

			<!-- 위젯 시작 { -->
			<h3 class="h3 f-lg en" style="margin-top:20px;">
        	<a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=magazine">
            인사이드 매거진
        	</a>
    		</h3>
    		<hr class="hr"/>
    		<div class="px-3 px-sm-0 my-3">
        		<?php echo na_widget('wr-gallery-slider', 'gallery-3', 'bo_list=gallery rows=8'); ?>
    		</div>
   			 <!-- } 위젯 끝-->

			



			<!-- 위젯 시작 { -->
			<h3 class="h3 f-lg en" style="margin-top:20px;">
        	<a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=performance">
            	공연 & 축제
        	</a>
    		</h3>
    		<hr class="hr"/>
    		<div class="px-3 px-sm-0 my-3">
        		<?php echo na_widget('wr-gallery-slider', 'gallery-4', 'bo_list=gallery rows=8'); ?>
    		</div>
   			 <!-- } 위젯 끝-->	

		</div>

		<!-- 사이드 영역 -->
		<div class="col-md-3 na-col" style="margin-top:8px;">
			<?php @include_once(G5_THEME_PATH.'/side/side-index.php') ?>
		</div>
	</div>
</div>
