<?php

require __DIR__ . '/bootstrap.php';

use rdx\jira\Config;
use rdx\jira\BasicAuth;
use rdx\jira\Client;
use rdx\jira\Auth1Request;

$config = new Config(RDX_JIRA_LIB_TEST_URL, array(
	'Response' => 'MySuperResponse', // @see classes.php
));
$auth = new BasicAuth(RDX_JIRA_LIB_TEST_USER, RDX_JIRA_LIB_TEST_PASS);
$client = new Client($config, $auth);

// Fetch issue
$issue = $client->open(RDX_JIRA_LIB_TEST_ISSUE);
print_r($issue);
