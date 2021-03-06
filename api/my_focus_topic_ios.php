<?php
include "ci_db.php";

$get = $_GET;
$per_page = 10;
if( !empty( $get['per_page'] ) ) $per_page = $get['per_page'];

if( empty( $get['uid'] ) ) exit_error('参数不完整');
if( !isset( $get['page'] ) ) $get['page'] = 1;

//查总数
$total_rows = $db->select("COUNT(*) AS total_rows")->from(TABLE_PREFIX.'topic_focus')->where(array('uid'=>$get['uid']))->get()->row_array();
if( $total_rows['total_rows']  == 0 )  exit_success($total_rows);


if( $get['page'] > ceil($total_rows['total_rows']/$per_page) || $get['page'] < 1 ) exit_success(array('total_rows'=>0,'rows'=>''));


//修正page
$get['page'] = ($get['page']<1)?1:$get['page'];
$get['page'] = ($get['page']>ceil($total_rows['total_rows']/$per_page))?ceil($total_rows['total_rows']/$per_page):$get['page'];

$offset = ($get['page']-1)*$per_page.','.$per_page;

$rows = $db->select("topic_id")->from(TABLE_PREFIX.'topic_focus')->where(array('uid'=>$get['uid']))->order_by('add_time','DESC')->limit($per_page,$offset)->get()->result_array();
$str = array();
foreach( $rows as $k => $v ){
	$str[] = $v['topic_id'];
}
$str = implode(',',$str);

$rows = $db->query("SELECT topic_id,topic_title,topic_description,topic_pic FROM ".TABLE_PREFIX."topic WHERE topic_id IN(".$str.")")->result_array();

foreach ($rows as $k => $v) {
	$rows[$k]['topic_pic'] = @str_replace( '_32_32', '_100_100', $v['topic_pic'] );
}

exit_success(array('total_rows'=>$total_rows['total_rows'],'rows'=>$rows));