<?php
if (!defined('_GNUBOARD_')) exit; // Unable to access direct pages

//return;

/*
// 게시판의 경우 게시판 설정에서 이미지 폭 크기 값으로 resize 됩니다.
// 에디터 이미지 경우 아래 상수에 width 값을 지정해 주세요.
*/
define('CUSTOM_EDITOR_RESIZE_WIDTH', 740);

add_replace('write_update_upload_file', 'custom_upload_file_resize', 10, 4);
add_replace('get_editor_upload_url', 'custom_editor_upload_url', 10, 3);

// bbs/view_image.php 에서 이미지를 호출시 위의 상수값 CUSTOM_EDITOR_RESIZE_WIDTH 보다 이미지 width 값이 크다면 CUSTOM_EDITOR_RESIZE_WIDTH 으로 파일을 리사이즈 합니다.
add_replace('get_view_imagesize', 'custom_get_view_imagesize', 10, 3);

function custom_get_view_imagesize($size, $filepath, $editor_file){

    if( isset($size[0]) && (int) $size[0] > CUSTOM_EDITOR_RESIZE_WIDTH ){
        $filepath = custom_imagefile_resize($filepath, CUSTOM_EDITOR_RESIZE_WIDTH);
        $size = @getimagesize($filepath);
    }

    return $size;
}

function custom_imagefile_resize($dest_file, $thumb_width=0){

    include_once(G5_LIB_PATH.'/thumbnail.lib.php');

    $size = @getimagesize($dest_file);
    if(empty($size))
        return $dest_file;

    if(in_array($size[2], array(IMAGETYPE_JPEG, IMAGETYPE_PNG))) {

        if(function_exists('exif_read_data')) {
            // exif 정보를 기준으로 회전각도 구함
            $exif = @exif_read_data($dest_file);
            $degree = 0;
            if(!empty($exif['Orientation'])) {
                switch($exif['Orientation']) {
                    case 8:
                        $degree = 90;
                        break;
                    case 3:
                        $degree = 180;
                        break;
                    case 6:
                        $degree = -90;
                        break;
                }

                // 회전각도를 구한다.
                if($degree) {
                    // 세로사진의 경우 가로, 세로 값 바꿈
                    if($degree == 90 || $degree == -90) {
                        $tmp = $size;
                        $size[0] = $tmp[1];
                        $size[1] = $tmp[0];
                    }
                }
            }
        }

        // 원본 width가 thumb_width보다 작다면
        if($size[0] <= $thumb_width)
            return $dest_file;
        
        $thumb_height = ceil($thumb_width * $size[1] / $size[0]);

        $path_parts = pathinfo($dest_file);
        
        if( $path_parts['dirname'] ){
            $thumb = thumbnail($path_parts['basename'], $path_parts['dirname'], $path_parts['dirname'], $thumb_width, $thumb_height, true, true);

            if($thumb) {
                @unlink($dest_file);
                rename($path_parts['dirname'].'/'.$thumb, $dest_file);
            }
        }

    }

    return $dest_file;
}

function custom_editor_upload_url($fileurl, $filepath, $args=array()){
    global $config;

    if( $config['cf_editor'] === 'smarteditor2' && (defined('SMARTEDITOR_UPLOAD_RESIZE') && SMARTEDITOR_UPLOAD_RESIZE) ){
        return $fileurl;
    }

    if ( file_exists($filepath) && defined('CUSTOM_EDITOR_RESIZE_WIDTH') && CUSTOM_EDITOR_RESIZE_WIDTH ){
        
        $thumb_width = CUSTOM_EDITOR_RESIZE_WIDTH;
        $filepath = custom_imagefile_resize($filepath, $thumb_width); 
    }

    return $fileurl;
}

function custom_upload_file_resize($dest_file, $board, $wr_id, $w){

    if( file_exists($dest_file) && $board['bo_image_width'] ){

        // 게시판 관리자에서 설정된 width 값
        $thumb_width = $board['bo_image_width'];

        $dest_file = custom_imagefile_resize($dest_file, $thumb_width);
    }

    return $dest_file;
}
?>