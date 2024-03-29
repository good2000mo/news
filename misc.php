<?php
/**
 * Provides various features for forum users (ie: display rules, send emails through the forum, mark a forum as read, etc).
 *
 * @copyright (C) 2008-2009 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (isset($_GET['action']))
	define('FORUM_QUIET_VISIT', 1);

if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');
require FORUM_ROOT.'include/common.php';

($hook = get_hook('mi_start')) ? eval($hook) : null;

// Load the misc.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/misc.php';


$action = isset($_GET['action']) ? $_GET['action'] : null;
$errors = array();

// Show the forum rules?
if ($action == 'rules')
{
	if ($forum_config['o_rules'] == '0' || ($forum_user['is_guest'] && $forum_user['g_read_board'] == '0' && $forum_config['o_regs_allow'] == '0'))
		message($lang_common['Bad request']);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		array($lang_common['Rules'], forum_link($forum_url['rules']))
	);

	($hook = get_hook('mi_rules_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'rules');
	require FORUM_ROOT.'header.php';

	// START SUBST - <!-- forum_main -->
	ob_start();

	($hook = get_hook('mi_rules_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $lang_common['Rules'] ?></span></h2>
	</div>

	<div class="main-content main-frm">
		<div class="ct-box user-box">
			<?php echo $forum_config['o_rules_message']."\n" ?>
		</div>
	</div>
<?php

	($hook = get_hook('mi_rules_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
	ob_end_clean();
	// END SUBST - <!-- forum_main -->

	require FORUM_ROOT.'footer.php';
}


// Mark all topics/posts as read?
else if ($action == 'markread')
{
	if ($forum_user['is_guest'])
		message($lang_common['No permission']);

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('markread'.$forum_user['id'])))
		csrf_confirm_form();

	($hook = get_hook('mi_markread_selected')) ? eval($hook) : null;

	$query = array(
		'UPDATE'	=> 'users',
		'SET'		=> 'last_visit='.$forum_user['logged'],
		'WHERE'		=> 'id='.$forum_user['id']
	);

	($hook = get_hook('mi_markread_qr_update_last_visit')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// Reset tracked topics
	set_tracked_topics(null);

	($hook = get_hook('mi_markread_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['index']), $lang_misc['Mark read redirect']);
}


// Mark the topics/posts in a forum as read?
else if ($action == 'markforumread')
{
	if ($forum_user['is_guest'])
		message($lang_common['No permission']);

	$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
	if ($fid < 1)
		message($lang_common['Bad request']);

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('markforumread'.$fid.$forum_user['id'])))
		csrf_confirm_form();

	($hook = get_hook('mi_markforumread_selected')) ? eval($hook) : null;

	// Fetch some info about the forum
	$query = array(
		'SELECT'	=> 'f.forum_name',
		'FROM'		=> 'forums AS f',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$fid
	);

	($hook = get_hook('mi_markforumread_qr_get_forum_info')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	if (!$forum_db->num_rows($result))
		message($lang_common['Bad request']);

	$forum_name = $forum_db->result($result);

	$tracked_topics = get_tracked_topics();
	$tracked_topics['forums'][$fid] = time();
	set_tracked_topics($tracked_topics);

	($hook = get_hook('mi_markforumread_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['forum'], array($fid, sef_friendly($forum_name))), $lang_misc['Mark forum read redirect']);
}


// Send form e-mail?
else if (isset($_GET['email']))
{
	if ($forum_user['is_guest'] || $forum_user['g_send_email'] == '0')
		message($lang_common['No permission']);

	$recipient_id = intval($_GET['email']);
	
	if ($recipient_id < 2)
		message($lang_common['Bad request']);

	($hook = get_hook('mi_email_selected')) ? eval($hook) : null;

	// User pressed the cancel button
	if (isset($_POST['cancel']))
		redirect(forum_htmlencode($_POST['redirect_url']), $lang_common['Cancel redirect']);

	$query = array(
		'SELECT'	=> 'u.username, u.email, u.email_setting',
		'FROM'		=> 'users AS u',
		'WHERE'		=> 'u.id='.$recipient_id
	);

	($hook = get_hook('mi_email_qr_get_form_email_data')) ? eval($hook) : null;

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	
	if (!$forum_db->num_rows($result))
		message($lang_common['Bad request']);

	list($recipient, $recipient_email, $email_setting) = $forum_db->fetch_row($result);

	if ($email_setting == 2 && !$forum_user['is_admmod'])
		message($lang_misc['Form e-mail disabled']);

	if ($recipient_email == '')
		message($lang_common['Bad request']);

	if (isset($_POST['form_sent']))
	{
		($hook = get_hook('mi_email_form_submitted')) ? eval($hook) : null;

		// Clean up message and subject from POST
		$subject = forum_trim($_POST['req_subject']);
		$message = forum_trim($_POST['req_message']);

		if ($subject == '')
			$errors[] = $lang_misc['No e-mail subject'];
		if ($message == '')
			$errors[] = $lang_misc['No e-mail message'];
		else if (strlen($message) > FORUM_MAX_POSTSIZE_BYTES)
			$errors[] = sprintf($lang_misc['Too long e-mail message'], forum_number_format(strlen($message)), forum_number_format(FORUM_MAX_POSTSIZE_BYTES));
		if ($forum_user['last_email_sent'] != '' && (time() - $forum_user['last_email_sent']) < $forum_user['g_email_flood'] && (time() - $forum_user['last_email_sent']) >= 0)
			$errors[] = sprintf($lang_misc['Email flood'], $forum_user['g_email_flood']);

		($hook = get_hook('mi_email_end_validation')) ? eval($hook) : null;

		// Did everything go according to plan?
		if (empty($errors))
		{
			// Load the "form e-mail" template
			$mail_tpl = forum_trim(file_get_contents(FORUM_ROOT.'lang/'.$forum_user['language'].'/mail_templates/form_email.tpl'));

			// The first row contains the subject
			$first_crlf = strpos($mail_tpl, "\n");
			$mail_subject = forum_trim(substr($mail_tpl, 8, $first_crlf-8));
			$mail_message = forum_trim(substr($mail_tpl, $first_crlf));

			$mail_subject = str_replace('<mail_subject>', $subject, $mail_subject);
			$mail_message = str_replace('<sender>', $forum_user['username'], $mail_message);
			$mail_message = str_replace('<board_title>', $forum_config['o_board_title'], $mail_message);
			$mail_message = str_replace('<mail_message>', $message, $mail_message);
			$mail_message = str_replace('<board_mailer>', sprintf($lang_common['Forum mailer'], $forum_config['o_board_title']), $mail_message);

			($hook = get_hook('mi_email_new_replace_data')) ? eval($hook) : null;

			if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
				require FORUM_ROOT.'include/email.php';

			forum_mail($recipient_email, $mail_subject, $mail_message, $forum_user['email'], $forum_user['username']);

			// Set the user's last_email_sent time
			$query = array(
				'UPDATE'	=> 'users',
				'SET'		=> 'last_email_sent='.time(),
				'WHERE'		=> 'id='.$forum_user['id'],
			);

			($hook = get_hook('mi_email_qr_update_last_email_sent')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);

			($hook = get_hook('mi_email_pre_redirect')) ? eval($hook) : null;

			redirect(forum_htmlencode($_POST['redirect_url']), $lang_misc['E-mail sent redirect']);
		}
	}

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = forum_link($forum_url['email'], $recipient_id);

	$forum_page['hidden_fields'] = array(
		'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
		'redirect_url'	=> '<input type="hidden" name="redirect_url" value="'.forum_htmlencode($forum_user['prev_url']).'" />',
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
	);

	// Setup main heading
	$forum_page['main_head'] = sprintf($lang_misc['Send forum e-mail'], forum_htmlencode($recipient));

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		sprintf($lang_misc['Send forum e-mail'], forum_htmlencode($recipient))
	);

	($hook = get_hook('mi_email_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'formemail');
	require FORUM_ROOT.'header.php';

	// START SUBST - <!-- forum_main -->
	ob_start();

	($hook = get_hook('mi_email_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $forum_page['main_head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div class="ct-box warn-box">
			<p class="important"><?php echo $lang_misc['E-mail disclosure note'] ?></p>
		</div>
<?php

	// If there were any errors, show them
	if (!empty($errors))
	{
		$forum_page['errors'] = array();
		foreach ($errors as $cur_error)
			$forum_page['errors'][] = '<li class="warn"><span>'.$cur_error.'</span></li>';

		($hook = get_hook('mi_pre_email_errors')) ? eval($hook) : null;

?>
		<div class="ct-box error-box">
			<h2 class="warn hn"><?php echo $lang_misc['Form e-mail errors'] ?></h2>
			<ul class="error-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['errors'])."\n" ?>
			</ul>
		</div>
<?php

	}

?>
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?php printf($lang_common['Required warn'], '<em>'.$lang_common['Required'].'</em>') ?></p>
		</div>
		<form id="afocus" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('mi_email_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_misc['Write e-mail'] ?></strong></legend>
<?php ($hook = get_hook('mi_email_pre_subject')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text required longtext">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_misc['E-mail subject'] ?>  <em><?php echo $lang_common['Required'] ?></em></span></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="req_subject" value="<?php echo(isset($_POST['req_subject']) ? forum_htmlencode($_POST['req_subject']) : '') ?>" size="75" maxlength="70" /></span>
					</div>
				</div>
<?php ($hook = get_hook('mi_email_pre_message_contents')) ? eval($hook) : null; ?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_misc['E-mail message'] ?>  <em><?php echo $lang_common['Required'] ?></em></span></label>
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="req_message" rows="10" cols="95"><?php echo(isset($_POST['req_message']) ? forum_htmlencode($_POST['req_message']) : '') ?></textarea></span></div>
					</div>
				</div>
<?php ($hook = get_hook('mi_email_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('mi_email_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit"><input type="submit" name="submit" value="<?php echo $lang_common['Submit'] ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?php echo $lang_common['Cancel'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

	($hook = get_hook('mi_email_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
	ob_end_clean();
	// END SUBST - <!-- forum_main -->

	require FORUM_ROOT.'footer.php';
}


// Report a post?
else if (isset($_GET['report']))
{
	if ($forum_user['is_guest'])
		message($lang_common['No permission']);

	$post_id = intval($_GET['report']);
	if ($post_id < 1)
		message($lang_common['Bad request']);


	($hook = get_hook('mi_report_selected')) ? eval($hook) : null;

	// User pressed the cancel button
	if (isset($_POST['cancel']))
		redirect(forum_link($forum_url['post'], $post_id), $lang_common['Cancel redirect']);


	if (isset($_POST['form_sent']))
	{
		($hook = get_hook('mi_report_form_submitted')) ? eval($hook) : null;

		// Start with a clean slate
		$errors = array();

		// Flood protection
		if ($forum_user['last_email_sent'] != '' && (time() - $forum_user['last_email_sent']) < $forum_user['g_email_flood'] && (time() - $forum_user['last_email_sent']) >= 0)
			message(sprintf($lang_misc['Report flood'], $forum_user['g_email_flood']));

		// Clean up reason from POST
		$reason = forum_linebreaks(forum_trim($_POST['req_reason']));
		if ($reason == '')
			message($lang_misc['No reason']);

		if (strlen($reason) > FORUM_MAX_POSTSIZE_BYTES)
		{
			$errors[] = sprintf($lang_misc['Too long reason'], forum_number_format(strlen($reason)), forum_number_format(FORUM_MAX_POSTSIZE_BYTES));
		}

		if (empty($errors)) {
			// Get some info about the topic we're reporting
			$query = array(
				'SELECT'	=> 't.id, t.subject, t.forum_id',
				'FROM'		=> 'posts AS p',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'topics AS t',
						'ON'			=> 't.id=p.topic_id'
					)
				),
				'WHERE'		=> 'p.id='.$post_id
			);

			($hook = get_hook('mi_report_qr_get_topic_data')) ? eval($hook) : null;
			$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
			if (!$forum_db->num_rows($result))
				message($lang_common['Bad request']);

			list($topic_id, $subject, $forum_id) = $forum_db->fetch_row($result);

			($hook = get_hook('mi_report_pre_reports_sent')) ? eval($hook) : null;

			// Should we use the internal report handling?
			if ($forum_config['o_report_method'] == 0 || $forum_config['o_report_method'] == 2)
			{
				$query = array(
					'INSERT'	=> 'post_id, topic_id, forum_id, reported_by, created, message',
					'INTO'		=> 'reports',
					'VALUES'	=> $post_id.', '.$topic_id.', '.$forum_id.', '.$forum_user['id'].', '.time().', \''.$forum_db->escape($reason).'\''
				);

				($hook = get_hook('mi_report_add_report')) ? eval($hook) : null;
				$forum_db->query_build($query) or error(__FILE__, __LINE__);
			}

			// Should we e-mail the report?
			if ($forum_config['o_report_method'] == 1 || $forum_config['o_report_method'] == 2)
			{
				// We send it to the complete mailing-list in one swoop
				if ($forum_config['o_mailing_list'] != '')
				{
					$mail_subject = 'Report('.$forum_id.') - \''.$subject.'\'';
					$mail_message = 'User \''.$forum_user['username'].'\' has reported the following message:'."\n".forum_link($forum_url['post'], $post_id)."\n\n".'Reason:'."\n".$reason;

					if (!defined('FORUM_EMAIL_FUNCTIONS_LOADED'))
						require FORUM_ROOT.'include/email.php';

					($hook = get_hook('mi_report_modify_message')) ? eval($hook) : null;

					forum_mail($forum_config['o_mailing_list'], $mail_subject, $mail_message);
				}
			}

			// Set last_email_sent time to prevent flooding
			$query = array(
				'UPDATE'	=> 'users',
				'SET'		=> 'last_email_sent='.time(),
				'WHERE'		=> 'id='.$forum_user['id']
			);

			($hook = get_hook('mi_report_qr_update_last_email_sent')) ? eval($hook) : null;
			$forum_db->query_build($query) or error(__FILE__, __LINE__);

			($hook = get_hook('mi_report_pre_redirect')) ? eval($hook) : null;

			redirect(forum_link($forum_url['post'], $post_id), $lang_misc['Report redirect']);
		}
	}

	// Setup form
	$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
	$forum_page['form_action'] = forum_link($forum_url['report'], $post_id);

	$forum_page['hidden_fields'] = array(
		'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
		'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
	);

	// Setup breadcrumbs
	$forum_page['crumbs'] = array(
		array($forum_config['o_board_title'], forum_link($forum_url['index'])),
		$lang_misc['Report post']
	);

	// Setup main heading
	$forum_page['main_head'] = end($forum_page['crumbs']);

	($hook = get_hook('mi_report_pre_header_load')) ? eval($hook) : null;

	define('FORUM_PAGE', 'report');
	require FORUM_ROOT.'header.php';

	// START SUBST - <!-- forum_main -->
	ob_start();

	($hook = get_hook('mi_report_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
		<h2 class="hn"><span><?php echo $forum_page['main_head'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<div id="req-msg" class="req-warn ct-box error-box">
			<p class="important"><?php printf($lang_common['Required warn'], '<em>'.$lang_common['Required'].'</em>') ?></p>
		</div>
<?php
		// If there were any errors, show them
		if (!empty($errors)) {
			$forum_page['errors'] = array();
			foreach ($errors as $cur_error) {
				$forum_page['errors'][] = '<li class="warn"><span>'.$cur_error.'</span></li>';
			}

			($hook = get_hook('mi_pre_report_errors')) ? eval($hook) : null;
?>
		<div class="ct-box error-box">
			<h2 class="warn hn"><?php echo $lang_misc['Report errors'] ?></h2>
			<ul class="error-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['errors'])."\n" ?>
			</ul>
		</div>
<?php
		}
?>
		<form id="afocus" class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('mi_report_pre_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_common['Required information'] ?></strong></legend>
<?php ($hook = get_hook('mi_report_pre_reason')) ? eval($hook) : null; ?>
				<div class="txt-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="txt-box textarea required">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_misc['Reason'] ?>  <em><?php echo $lang_common['Required'] ?></em></span> <small><?php echo $lang_misc['Reason help'] ?></small></label><br />
						<div class="txt-input"><span class="fld-input"><textarea id="fld<?php echo $forum_page['fld_count'] ?>" name="req_reason" rows="5" cols="60"></textarea></span></div>
					</div>
				</div>
<?php ($hook = get_hook('mi_report_pre_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('mi_report_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit"><input type="submit" name="submit" value="<?php echo $lang_common['Submit'] ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?php echo $lang_common['Cancel'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

	($hook = get_hook('mi_report_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
	ob_end_clean();
	// END SUBST - <!-- forum_main -->

	require FORUM_ROOT.'footer.php';
}


// Subscribe to a topic?
else if (isset($_GET['subscribe']))
{
	if ($forum_user['is_guest'] || $forum_config['o_subscriptions'] != '1')
		message($lang_common['No permission']);

	$topic_id = intval($_GET['subscribe']);
	if ($topic_id < 1)
		message($lang_common['Bad request']);

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('subscribe'.$topic_id.$forum_user['id'])))
		csrf_confirm_form();

	($hook = get_hook('mi_subscribe_selected')) ? eval($hook) : null;

	// Make sure the user can view the topic
	$query = array(
		'SELECT'	=> 'subject',
		'FROM'		=> 'topics AS t',
		'JOINS'		=> array(
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> '(fp.forum_id=t.forum_id AND fp.group_id='.$forum_user['g_id'].')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$topic_id.' AND t.moved_to IS NULL'
	);
	($hook = get_hook('mi_subscribe_qr_topic_exists')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	if (!$forum_db->num_rows($result))
		message($lang_common['Bad request']);

	$subject = $forum_db->result($result);

	$query = array(
		'SELECT'	=> '1',
		'FROM'		=> 'subscriptions AS s',
		'WHERE'		=> 'user_id='.$forum_user['id'].' AND topic_id='.$topic_id
	);

	($hook = get_hook('mi_subscribe_qr_check_subscribed')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	if ($forum_db->num_rows($result))
		message($lang_misc['Already subscribed']);

	$query = array(
		'INSERT'	=> 'user_id, topic_id',
		'INTO'		=> 'subscriptions',
		'VALUES'	=> $forum_user['id'].' ,'.$topic_id
	);

	($hook = get_hook('mi_subscribe_add_subscription')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	($hook = get_hook('mi_subscribe_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['topic'], array($topic_id, sef_friendly($subject))), $lang_misc['Subscribe redirect']);
}


// Unsubscribe from a topic?
else if (isset($_GET['unsubscribe']))
{
	if ($forum_user['is_guest'] || $forum_config['o_subscriptions'] != '1')
		message($lang_common['No permission']);

	$topic_id = intval($_GET['unsubscribe']);
	if ($topic_id < 1)
		message($lang_common['Bad request']);

	// We validate the CSRF token. If it's set in POST and we're at this point, the token is valid.
	// If it's in GET, we need to make sure it's valid.
	if (!isset($_POST['csrf_token']) && (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== generate_form_token('unsubscribe'.$topic_id.$forum_user['id'])))
		csrf_confirm_form();

	($hook = get_hook('mi_unsubscribe_selected')) ? eval($hook) : null;

	$query = array(
		'SELECT'	=> 't.subject',
		'FROM'		=> 'topics AS t',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'subscriptions AS s',
				'ON'			=> 's.user_id='.$forum_user['id'].' AND s.topic_id=t.id'
			)
		),
		'WHERE'		=> 't.id='.$topic_id
	);

	($hook = get_hook('mi_unsubscribe_qr_check_subscribed')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	if (!$forum_db->num_rows($result))
		message($lang_misc['Not subscribed']);

	$subject = $forum_db->result($result);

	$query = array(
		'DELETE'	=> 'subscriptions',
		'WHERE'		=> 'user_id='.$forum_user['id'].' AND topic_id='.$topic_id
	);

	($hook = get_hook('mi_unsubscribe_qr_delete_subscription')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	($hook = get_hook('mi_unsubscribe_pre_redirect')) ? eval($hook) : null;

	redirect(forum_link($forum_url['topic'], array($topic_id, sef_friendly($subject))), $lang_misc['Unsubscribe redirect']);
}


($hook = get_hook('mi_new_action')) ? eval($hook) : null;

// pun_attachment
if ($action == 'pun_attachment' && !$forum_config['attach_disable_attach'] && isset($_GET['item']))
{
	$attach_item = intval($_GET['item']);
	if ($attach_item < 1)
		message($lang_common['Bad request']);

	if (isset($_GET['secure_str']))
	{
		preg_match('~(\d+)f(\d+)~', $_GET['secure_str'], $match);
		if (isset($match[0]))
		{
			$query = array(
				'SELECT'	=>	'a.id, post_id, topic_id, owner_id, filename, file_ext, file_mime_type, size, file_path, secure_str',
				'FROM'		=>	'attach_files AS a',
				'JOINS'		=>	array(
					array(
						'INNER JOIN' => 'forums AS f',
						'ON'		=> 'f.id = '.$match[2]
					),
					array(
						'LEFT JOIN'	=> 'forum_perms AS fp',
						'ON'		=> '(fp.forum_id = f.id AND fp.group_id = '.$forum_user['g_id'].')'
					)
				),
				'WHERE'		=> 'a.id = '.$attach_item.' AND (fp.read_forum IS NULL OR fp.read_forum = 1) AND secure_str = \''.$match[0].'\''
			);
		}
		else
		{
			preg_match('~(\d+)t(\d+)~', $_GET['secure_str'], $match);
			if (isset($match[0]))
			{
				$query = array(
					'SELECT'	=>	'a.id, post_id, topic_id, owner_id, filename, file_ext, file_mime_type, size, file_path, secure_str',
					'FROM'		=>	'attach_files AS a',
					'JOINS'		=>	array(
						array(
							'INNER JOIN'	=> 'topics AS t',
							'ON'		=> 't.id = '.$match[2]
						),
						array(
							'INNER JOIN'	=> 'forums AS f',
							'ON'		=> 'f.id = t.forum_id'
						),
						array(
							'LEFT JOIN'		=> 'forum_perms AS fp',
							'ON'		=> '(fp.forum_id = f.id AND fp.group_id = '.$forum_user['g_id'].')'
						)
					),
					'WHERE'		=> 'a.id = '.$attach_item.' AND (fp.read_forum IS NULL OR fp.read_forum = 1) AND secure_str = \''.$match[0].'\''
				);
			}
			else
				message($lang_common['Bad request']);
		}
		if ($forum_user['id'] != $match[1])
			message($lang_common['Bad request']);
	}
	else
		$query = array(
			'SELECT'	=>	'a.id, post_id, topic_id, owner_id, filename, file_ext, file_mime_type, size, file_path, secure_str',
			'FROM'		=>	'attach_files AS a',
			'JOINS'		=>	array(
				array(
					'INNER JOIN'	=> 'topics AS t',
					'ON'		=> 't.id = a.topic_id'
				),
				array(
					'INNER JOIN'	=> 'forums AS f',
					'ON'	=> 'f.id = t.forum_id'
				),
				array(
					'LEFT JOIN'		=> 'forum_perms AS fp',
					'ON'		=> '(fp.forum_id = f.id AND fp.group_id = '.$forum_user['g_id'].')'
				)
			),
			'WHERE'		=> 'a.id = '.$attach_item.' AND (fp.read_forum IS NULL OR fp.read_forum = 1)'
		);

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	if (!$forum_db->num_rows($result))
		message($lang_common['Bad request']);
	$attach_info = $forum_db->fetch_assoc($result);

	$query = array(
		'SELECT'	=> 'g_pun_attachment_allow_download',
		'FROM'		=> 'groups',
		'WHERE'		=> 'g_id = '.$forum_user['group_id']
	);
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if (!$forum_db->num_rows($result))
		message($lang_common['Bad request']);

	$perms = $forum_db->fetch_assoc($result);
	if ($forum_user['g_id'] != FORUM_ADMIN && !$perms['g_pun_attachment_allow_download'])
		message($lang_common['Bad request']);
	if (isset($_GET['preview']) && in_array($attach_info['file_ext'], array('png', 'jpg', 'gif', 'tiff')))
	{
		if (file_exists(FORUM_ROOT.'extensions/pun_attachment/lang/'.$forum_user['language'].'/pun_attachment.php'))
			require FORUM_ROOT.'extensions/pun_attachment/lang/'.$forum_user['language'].'/pun_attachment.php';
		else
			require FORUM_ROOT.'extensions/pun_attachment/lang/English/pun_attachment.php';
		require FORUM_ROOT.'extensions/pun_attachment/url.php';

		$forum_page = array();
		$forum_page['download_link'] = !empty($attach_info['secure_str']) ? forum_link($attach_url['misc_download_secure'], array($attach_item, $attach_info['secure_str'])) : forum_link($attach_url['misc_download'], $attach_item);
		$forum_page['view_link'] = !empty($attach_info['secure_str']) ? forum_link($attach_url['misc_view_secure'], array($attach_item, $attach_info['secure_str'])) : forum_link($attach_url['misc_view'], $attach_info['id']);

		// Setup breadcrumbs
		$forum_page['crumbs'] = array(
			array($forum_config['o_board_title'], forum_link($forum_url['index'])),
			$lang_attach['Image preview']
		);

		define('FORUM_PAGE', 'attachment-preview');
		require FORUM_ROOT.'header.php';

		// START SUBST - <!-- forum_main -->
		ob_start();

		?>
		<div class="main-head">
			<h2 class="hn"><span><?php echo $lang_attach['Image preview']; ?></span></h2>
		</div>

		<div class="main-content main-frm">
			<div class="content-head">
				<h2 class="hn"><span><?php echo $attach_info['filename']; ?></span></h2>
			</div>
			<fieldset class="frm-group group1">
				<span class="show-image"><img src="<?php echo $forum_page['view_link']; ?>" alt="<?php echo forum_htmlencode($attach_info['filename']); ?>" /></span>
				<p><?php echo $lang_attach['Download:']; ?> <a href="<?php echo $forum_page['download_link']; ?>"><?php echo forum_htmlencode($attach_info['filename']); ?></a></p>
			</fieldset>
		</div>
		<?php

		$tpl_temp = trim(ob_get_contents());
		$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
		ob_end_clean();
		// END SUBST - <!-- forum_main -->

		require FORUM_ROOT.'footer.php';
	}
	else
	{
		$fp = fopen($forum_config['attach_basefolder'].$attach_info['file_path'], 'rb');

		if (!$fp)
			message($lang_common['Bad request']);
		else
		{
			header('Content-Disposition: attachment; filename="'.$attach_info['filename'].'"');
			header('Content-Type: '.$attach_info['file_mime_type']);
			header('Pragma: no-cache');
			header('Expires: 0');
			header('Connection: close');
			header('Content-Length: '.$attach_info['size']);

			fpassthru ($fp);

			if (isset($_GET['download']) && intval($_GET['download']) == 1 && $attach_info['owner_id'] != 0 && $forum_user['id'] != $attach_info['owner_id'])
			{
				$query = array(
					'UPDATE'	=> 'attach_files',
					'SET'		=> 'download_counter = download_counter + 1',
					'WHERE'		=> 'id = '.$attach_item
				);
				$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
			}
			exit();
		}
	}
}
// pun_attachment

message($lang_common['Bad request']);
