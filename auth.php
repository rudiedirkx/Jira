<?php

require 'inc.bootstrap.php';

// Already logged in
if ( defined('JIRA_AUTH') ) {
	include 'tpl.header.php';

	echo '<p>You are:</p>';

	// $account = jira_get('user', array('username' => JIRA_USER), $error, $info);
	// echo '<pre>';
	// print_r($account);
	// echo '</pre>';

	$session = jira_get(JIRA_AUTH_PATH . 'session', null, $error, $info);
	echo '<pre>';
	print_r($session);
	echo '</pre>';

	$account = jira_get($session->self, null, $error, $info);
	echo '<pre>';
	print_r($account);
	echo '</pre>';

	// Update timezone from Jira
	$db->update('users', array('jira_timezone' => $account->timeZone), array('id' => $user->id));

	include 'tpl.footer.php';
	exit;
}

// Log in
if ( isset($_POST['url'], $_POST['user'], $_POST['pass']) ) {
	// Test connection
	define('JIRA_URL', rtrim($_POST['url'], '/'));
	define('JIRA_USER', $_POST['user']);
	define('JIRA_AUTH', $_POST['user'] . ':' . $_POST['pass']);
	$info = array('unauth_ok' => 1);
	$account = jira_get('user', array('username' => JIRA_USER), $error, $info);

	// Invalid URL
	if ( $error == 404 ) {
		exit('Invalid URL?');
	}
	// Invalid credentials
	else if ( $error || empty($account->active) || empty($account->name) || $account->name !== JIRA_USER ) {
		exit('Invalid login?');
	}

	// Save user to local db for preferences
	try {
		$db->insert('users', array(
			'jira_url' => JIRA_URL,
			'jira_user' => JIRA_USER,
		));
	}
	catch ( db_exception $ex ) {
		// Let's assume it failed because the user already exists.
	}

	$user = User::load();
	$user->unsync();
	$db->update('users', array('jira_timezone' => $account->timeZone), array('id' => $user->id));

	// Save credentials to cookie
	do_login(JIRA_URL, JIRA_AUTH);

	return do_redirect('index');
}

?>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
* { box-sizing: border-box; -webkit-box-sizing: border-box; }
input:not([type="submit"]):not([type="button"]):not([type="radio"]):not([type="checkbox"]), select { width: 100%; }
</style>

<? include 'tpl.login.php' ?>
