<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Wed, 27 Jul 2011 14:55:22 GMT
 */

if ( ! defined( 'NV_IS_MOD_LAWS' ) ) die( 'Stop!!!' );

$alias = isset( $array_op[1] ) ? $array_op[1] : "";

if ( ! preg_match( "/^([a-z0-9\-\_\.]+)$/i", $alias ) )
{
    Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
    exit();
}

$catid = 0;
foreach( $nv_laws_listsubject as $c )
{
	if( $c['alias'] == $alias )
	{
		$catid = $c['id'];
		break;
	}
}

if( empty( $catid ) )
{
	Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
	exit();
}

// Set page title, keywords, description
$page_title = $mod_title = $nv_laws_listsubject[$catid]['title'];
$key_words = empty( $nv_laws_listsubject[$catid]['keywords'] ) ? $module_info['keywords'] : $nv_laws_listsubject[$catid]['keywords'];
$description = empty( $nv_laws_listsubject[$catid]['introduction'] ) ? $page_title : $nv_laws_listsubject[$catid]['introduction'];

//
$page = $nv_Request->get_int( 'page', 'get', 0 );
$per_page = $nv_laws_setting['numsub'];
$base_url = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=subject/" . $nv_laws_listsubject[$catid]['alias'];

$order = $nv_laws_setting['typeview'] ? "ASC" : "DESC";
	
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `" . NV_PREFIXLANG . "_" . $module_data . "_row` WHERE `status`=1 AND `sid`=" . $catid . " ORDER BY `addtime` " . $order . " LIMIT " . $page . "," . $per_page;

$result = $db->query( $sql );
$query = $db->query( "SELECT FOUND_ROWS()" );
$all_page = $query->fetchColumn();

if ( ! $all_page or $page >= $all_page )
{
	if ( $nv_Request->isset_request( 'page', 'get' ) )
	{
		Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
		exit();
	}
	else
	{
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( '' );
		include NV_ROOTDIR . '/includes/footer.php';
		exit();
	}
}
	
$generate_page = nv_generate_page( $base_url, $all_page, $per_page, $page );

$array_data = array();
$stt = $page + 1;
while ( $row = $result->fetch() )
{
	$row['url'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=detail/" . $row['alias'];
	$row['stt'] = $stt;
	
	$array_data[] = $row;
	$stt ++;
}

$contents = nv_theme_laws_subject( $array_data, $generate_page, $nv_laws_listsubject[$catid] );

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';

?>