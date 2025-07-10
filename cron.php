<?php
/**
 * Run cron job from the CLI
 * We are using dedicated cli front controller for that
 */
$_GET['fc'] = 'module';
$_GET['module'] = 'dpdbaltics';
$_GET['controller'] = 'cli';

require_once dirname(__FILE__) . '/../../index.php';
