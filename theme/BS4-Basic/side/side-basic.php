<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가 

// 페이지에서는 사이드 메뉴 출력
// echo na_widget('sidemenu');
?>
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
                    
                   <!-- 위젯 시작 { -->
                <h3 class="h3 f-lg en"  style="margin-top:53px;" >
                    <a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=freeboard">
		                아무거나 말해요^^
                    </a>
                </h3>
                <hr class="hr"/>
                <div class="mt-3 mb-4">
	                <?php echo na_widget('wr-list', 'freeboard', 'bo_list=freeboard'); ?>
                </div>
                    <!-- } 위젯 끝-->
                    
                    
                   


<!-- 구글 광고 자리 (수정 진행 중)-->
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

