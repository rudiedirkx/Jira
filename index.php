<?php

require 'inc.bootstrap.php';

do_logincheck();

$perPage = $user->config('index_page_size');

// Default query
$query = $selectedQuery = 'status != Closed ORDER BY priority DESC, created DESC';
$querySource = '';
$filterOptions = $user->filter_query_options;

// GET search
if ( !empty($_GET['search']) ) {
	$query = $selectedQuery = "text ~ '" . addslashes(trim($_GET['search'])) . "' ORDER BY updated DESC";
	$querySource = 'search';
}
// GET query
else if ( !empty($_GET['query']) ) {
	$query = $selectedQuery = trim($_GET['query']);
	$querySource = isset($filterOptions[$query]) ? 'filter:get' : 'query:get';
}
// GET project
else if ( !empty($_GET['project']) ) {
	$query = $selectedQuery = 'project = "' . addslashes(strtoupper(trim($_GET['project']))) . '" AND ' . $query;
	$querySource = 'project:get';
}
// User's query
else if ( $user->index_query ) {
	$query = $selectedQuery = $user->index_query;
	$querySource = 'query:setting';
}
// User's Jira filter
else if ( $user->index_filter_object ) {
	$query = $selectedQuery = $user->index_filter_object->jql;
	$querySource = 'filter:setting';
}
// User's project
else if ( $user->index_project ) {
	$query = $selectedQuery = 'project = "' . $user->index_project . '" AND ' . $query;
	$querySource = 'project:setting';
}

// Add Project condition to query
if ( !empty($_GET['project']) && $querySource != 'project:get' ) {
	$query = 'project = "' . addslashes(strtoupper(trim($_GET['project']))) . '" AND ' . $query;
}

list($activeTab) = explode(':', $querySource);

// Execute
$page = max(0, (int)@$_GET['page']);
$issues = jira_get('search', array('maxResults' => $perPage, 'startAt' => $page * $perPage, 'jql' => $query), $error, $info);
if ($issues) {
	$issues->issues = Issue::map($issues->issues);
}

// Ajax callback
if ( IS_AJAX ) {
	include 'tpl.issues.php';
	exit;
}

$index = true;
$_title = '';
include 'tpl.header.php';

include 'tpl.indextabs.php';

if ( $error ) {
	echo '<pre>';
	print_r($info);
	exit;
}

echo '<div id="content">';
include 'tpl.issues.php';
echo '</div>';

// echo implode('<br>', $jira_requests);
// echo '<pre>';
// print_r($issues);

include 'tpl.footer.php';
