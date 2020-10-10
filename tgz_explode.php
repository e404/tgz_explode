<?php

function tgz_explode($data) {
	$data = @gzdecode($data);
	$filesize = strlen($data);
	if($filesize<1024) return null;
	$total = 0;
	$result = array();
	while($block = substr($data, $total, $total+512)) {
		$meta = array();
		$meta['filename'] = trim(substr($block, 0, 99));
		$meta['mode'] = octdec((int)trim(substr($block, 100, 8)));
		$meta['userid'] = octdec(substr($block, 108, 8));
		$meta['groupid'] = octdec(substr($block, 116, 8));
		$meta['filesize'] = octdec(substr($block, 124, 12));
		$meta['mtime'] = octdec(substr($block, 136, 12));
		$meta['header_checksum'] = octdec(substr($block, 148, 8));
		$meta['link_flag'] = octdec(substr($block, 156, 1));
		$meta['linkname'] = trim(substr($block, 157, 99));
		$meta['databytes'] = ($meta['filesize'] + 511) & ~511;
		if($meta['databytes'] > 0) {
			$block = substr($data, $total+$meta['databytes']);
			$result[] = ['meta' => $meta, 'data' => substr($block, 0, $meta['filesize'])];
			$total += $meta['databytes'];
		}
		$total+= 512;
		if ($total >= $filesize-1024) {
			return $result;
		}
	}
}
