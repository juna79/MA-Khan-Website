<?php
defined('ABSPATH') || exit;
$html = file_get_contents(get_template_directory() . '/site.html');
$html = str_replace('assets/', esc_url(get_template_directory_uri()) . '/assets/', $html);
$html = str_replace('data-endpoint=""', 'data-endpoint="' . esc_url(rest_url('makhan/v1/enquiry')) . '"', $html);
ob_start(); wp_head(); $head = ob_get_clean();
ob_start(); wp_footer(); $footer = ob_get_clean();
$html = str_replace('</head>', $head . '</head>', $html);
$html = str_replace('</body>', $footer . '</body>', $html);
// Checked-in trusted template. No visitor input is interpolated here.
echo $html;
