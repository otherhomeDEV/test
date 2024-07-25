<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가 
?>

<div class="d-none d-md-block mb-4" >
	<?php echo na_widget('outlogin'); // 외부로그인 위젯 ?>
</div>


                 
					

	               <!-- 위젯 시작 { -->
					<h3 class="h3 f-lg en" style="margin-top:30px;" >
						<a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=event">
							업체게시판 및 안내
						</a>
					</h3>
					<hr class="hr"/>
					<div class="mt-3 mb-4">
						<?php echo na_widget('wr-list', 'tlist-1', 'bo_table=event'); ?>
					</div>
					<!-- } 위젯 끝-->

                <!-- 위젯 시작 { -->
					<h3 class="h3 f-lg en" style="margin-top:42px;">
						<a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=findjob">
							일자리
						</a>
					</h3>
					<hr class="hr"/>
					<div class="mt-3 mb-4">
						<?php echo na_widget('wr-list', 'tlist-2', 'gr_id=jobs'); ?>
					</div>
					<!-- } 위젯 끝-->
                    <!-- 위젯 시작 { -->
					<h3 class="h3 f-lg en" style="margin-top:42px;"> <!--  style="margin-bottom:40px;" 위젯 레이아웃 아래마진 - kayden { -->
						<a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=share">
							렌트/쉐어
						</a>
					</h3>
					<hr class="hr"/>
					<div class="mt-3 mb-4">
						<?php echo na_widget('wr-list', 'tlist-3', 'gr_id=property'); ?>
					</div>
					<div style="margin-top:97px;">
				    <?php  include_once(G5_PATH.'/newfeature/googlemap.php');  ?>
					</div>
					<!-- } 위젯 끝-->
					
					   <!-- 위젯 시작 { -->
					 	   <!-- <h3 class="h3 f-lg en" style="margin-top:49px;"> <!--  style="margin-bottom:40px;" 위젯 레이아웃 아래마진 - kayden { -->
			
							   <!-- 글 갯수	
					
					 </h3>
					<hr class="hr"/>
					<div class="mt-3 mb-4">
						<?php echo na_widget('rank', 'mb-rank-9'); ?>
					</div>
					<!-- } 위젯 끝-->
					
					
					
										


  	      

<div class="mt-3 mb-4" style="width: 100%; overflow: hidden;">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1954225037401260"
     crossorigin="anonymous"></script>
<!-- 메인 페이지 -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-1954225037401260"
     data-ad-slot="7658841041"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
</div>


