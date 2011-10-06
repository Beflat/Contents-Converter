<?php

/**
 * 共通設定ファイル
 **/

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

set_include_path(ini_get('include_path') . PATH_SEPARATOR . dirname(dirname(__FILE__)) . '/zf/library');
