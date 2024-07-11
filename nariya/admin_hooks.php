<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

class G5_NARIYA_ADMIN {

	public $na_basic_number = 100990;
	public $na_xp_number = 100991;

    // Hook 포함 클래스 작성 요령
    // https://github.com/Josantonius/PHP-Hook/blob/master/tests/Example.php
    /**
     * Class instance.
     */

    public static function getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new self();
        }

        return $instance;
    }

    public static function singletonMethod() {
        return self::getInstance();
    }

    public function __construct() {
		global $nariya;

		// 관리자 메뉴 추가
		add_replace('admin_menu', array($this, 'add_admin_menu'), 1, 1);

		// 관리자 페이지 추가
		add_event('admin_get_page_nariya', array($this, 'admin_page_nariya'), 1, 2);

		// 게시판 필드 추가
		if(IS_NA_BBS) {
			add_event('admin_board_form_update', array($this, 'admin_board_form_update'), 1, 2);
		}

		// 경험치 관리 페이지 추가
		if(IS_NA_XP) {
			add_event('admin_get_page_nariya_xp', array($this, 'admin_page_nariya_xp'), 1, 2);
		}

		$this->add_hooks();
    }

	public function add_hooks() {


	}

	public function add_admin_menu($admin_menu){
		global $nariya;
		
		$admin_menu['menu100'][] = array($this->na_basic_number, '나리야 설정', G5_ADMIN_URL.'/view.php?call=nariya', 'nariya');

		if(IS_NA_XP) {
			$admin_menu['menu100'][] = array($this->na_xp_number, '경험치 관리', G5_ADMIN_URL.'/view.php?call=nariya_xp', 'nariya_xp');
		}
		return $admin_menu;
	}

	public function admin_page_nariya($arr_query, $token){
		global $g5, $is_admin, $auth, $member, $nariya;

		$auth[$this->na_basic_number] = isset($auth[$this->na_basic_number]) ? $auth[$this->na_basic_number] : '';

		if(isset($_POST['post_action']) && isset($_POST['token'])){
			
			check_demo();

			auth_check($auth[$this->na_basic_number], 'w');

			// 기본 폴더 체크
			$save_path = G5_DATA_PATH.'/'.NA_DIR;
			if(is_dir($save_path)) {
				; // 통과
			} else {
				@mkdir($save_path, G5_DIR_PERMISSION);
				@chmod($save_path, G5_DIR_PERMISSION);
			}

			// 영상폴더 체크
			$video_path = $save_path.'/video';
			if(is_dir($video_path)) {
				; //통과
			} else {
				@mkdir($video_path, G5_DIR_PERMISSION);
				@chmod($video_path, G5_DIR_PERMISSION);
			}

			// 알림(내글반응)
			if(isset($_POST['na']['noti']) && $_POST['na']['noti'] && !isset($member['as_noti'])) {

				// 회원정보 테이블에 필드 추가
				sql_query(" ALTER TABLE `{$g5['member_table']}`
								ADD `as_noti` int(11) NOT NULL DEFAULT '0' AFTER `mb_10` ", false);

				// 알림(내글반응) 테이블 추가
				$na_db_set = na_db_set();

				if(!sql_query(" DESC {$g5['na_noti']} ", false)) {
					sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['na_noti']}` (
								  `ph_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
								  `ph_to_case` varchar(50) NOT NULL DEFAULT '',
								  `ph_from_case` varchar(50) NOT NULL DEFAULT '',
								  `bo_table` varchar(20) NOT NULL DEFAULT '',
								  `rel_bo_table` varchar(20) NOT NULL DEFAULT '',
								  `wr_id` int(11) NOT NULL DEFAULT 0,
								  `rel_wr_id` int(11) NOT NULL DEFAULT 0,
								  `mb_id` varchar(255) NOT NULL DEFAULT '',
								  `rel_mb_id` varchar(255) NOT NULL DEFAULT '',
								  `rel_mb_nick` varchar(255) DEFAULT NULL,
								  `rel_msg` varchar(255) NOT NULL DEFAULT '',
								  `rel_url` varchar(200) NOT NULL DEFAULT '',
								  `ph_readed` char(1) NOT NULL DEFAULT 'N',
								  `ph_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
								  `parent_subject` varchar(255) NOT NULL,
								  `wr_parent` int(11) DEFAULT 0,
								  PRIMARY KEY (`ph_id`)
							) ".$na_db_set."; ", false);
				}
			}

			// 게시판 플러그인 관련 DB 테이블 및 필드 추가
			$is_bbs_db = true;
			if(!$nariya['bbs'] && isset($_POST['na']['bbs']) && $_POST['na']['bbs']) {
				include_once(NA_PATH.'/extend/bbs/db.php');
			}

			// 멤버십 플러그인 관련 DB 테이블 및 필드 추가
			$is_xp_db = true;
			if(!$nariya['xp'] && isset($_POST['na']['xp']) && $_POST['na']['xp']) {
				include_once(NA_PATH.'/extend/membership/db.php');
			}			

			// 레벨 아이콘 확장자
			if(isset($_POST['na']['lvl_skin']) && $_POST['na']['lvl_skin']) {
				$lvl_skin_path = NA_PATH.'/skin/level/'.$_POST['na']['lvl_skin'];
				if(is_file($lvl_skin_path.'/1.png')) {
					$_POST['na']['lvl_ext'] = 'png';
				} else if(is_file($lvl_skin_path.'/1.jpg')) {
					$_POST['na']['lvl_ext'] = 'jpg';
				}
			}

			// 설정값
			$na = array();
			$na = $_POST['na'];
			na_file_var_save($save_path.'/nariya.php', $na, 'nariya'); //data 폴더 체크

			goto_url(G5_ADMIN_URL.'/view.php?call=nariya');
		}

		auth_check($auth[$this->na_basic_number], 'r');

		$nariya = array();
		$nariya = na_config('nariya');

		include_once(NA_PATH.'/admin_page.php');
	}

	public function admin_page_nariya_xp($arr_query, $token){
		global $is_admin, $auth, $nariya;

		$auth[$this->na_xp_number] = isset($auth[$this->na_xp_number]) ? $auth[$this->na_xp_number] : '';

		include_once(NA_PATH.'/extend/membership/admin_xp.php');
	}

	public function admin_board_form_update($bo_table, $w){
		global $g5;

		// 테이블 필드 추가
		$write_table = $g5['write_prefix'] . $bo_table; // 게시판 테이블
		$row = sql_fetch(" SHOW COLUMNS FROM {$write_table} LIKE 'as_type' ");
		if(!$row){
			sql_query(" ALTER TABLE `{$write_table}`
							ADD `as_type` tinyint(4) NOT NULL DEFAULT '0' AFTER `wr_10`,
							ADD `as_img` tinyint(4) NOT NULL DEFAULT '0' AFTER `as_type`,
							ADD `as_extend` tinyint(4) NOT NULL DEFAULT '0' AFTER `as_img`,
							ADD `as_down` int(11) NOT NULL DEFAULT '0' AFTER `as_extend`,
							ADD `as_view` int(11) NOT NULL DEFAULT '0' AFTER `as_down`,
							ADD `as_star_score` int(11) NOT NULL DEFAULT '0' AFTER `as_view`,
							ADD `as_star_cnt` int(11) NOT NULL DEFAULT '0' AFTER `as_star_score`,
							ADD `as_choice` int(11) NOT NULL DEFAULT '0' AFTER `as_star_cnt`,
							ADD `as_choice_cnt` int(11) NOT NULL DEFAULT '0' AFTER `as_choice`,
							ADD `as_tag` varchar(255) NOT NULL AFTER `as_choice_cnt`,
							ADD `as_thumb` varchar(255) NOT NULL AFTER `as_tag` 
						", false);
		}
	}
}

$GLOBALS['g5_nariya_admin'] = G5_NARIYA_ADMIN::getInstance();