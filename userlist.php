<?php
/**
 * Provides a list of forum users that can be sorted based on various criteria.
 *
 * @copyright (C) 2008-2009 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');
require FORUM_ROOT.'include/common.php';

($hook = get_hook('ul_start')) ? eval($hook) : null;

// pun_admin_add_user
if ($forum_user['g_id'] == FORUM_ADMIN)
			{
				$errors_add_users = array();
				if (isset($_POST['add_user_form_sent']) && $_POST['add_user_form_sent'] == 1)
				{
					$forum_extension['admin_add_user']['user_added'] = false;
	
					require_once FORUM_ROOT.'include/functions.php';
					require_once FORUM_ROOT.'lang/'.$forum_user['language'].'/profile.php';
	
					$username = trim($_POST['req_username']);
					$email = strtolower(trim($_POST['req_email']));
	
					// Validate the username
					$errors_add_users = validate_username($username);
	
					// ... and the e-mail address
					require_once FORUM_ROOT.'include/email.php';
	
					if (!is_valid_email($email))
					   $errors_add_users[] = $lang_common['Invalid e-mail'];
	
					// Check if it's a banned e-mail address
					$banned_email = is_banned_email($email);
					if ($banned_email && $forum_config['p_allow_banned_email'] == '0')
						$errors_add_users[] = $lang_profile['Banned e-mail'];
	
					// Check if someone else already has registered with that e-mail address
					$q = array(
						'SELECT'	=> 'u.username',
						'FROM'	  => 'users AS u',
						'WHERE'	 => 'u.email=\''.$email.'\''
					);
	
					$result = $forum_db->query_build( $q ) or error(__FILE__, __LINE__);
	
					if (($forum_config['p_allow_dupe_email'] == '0') && ($forum_db->num_rows($result) ))
						$errors_add_users[] = $lang_profile['Dupe e-mail'];
	
					if (empty($errors_add_users))
					{
						$salt = random_key(12);
						$password = random_key(8, true);
						$password_hash = sha1($salt.sha1($password));
	
						$errors = add_user(
							array(
								'username'				=> $username,
								'group_id'				=> ($forum_config['o_regs_verify'] == '0') ? $forum_config['o_default_user_group'] : FORUM_UNVERIFIED,
								'salt'					=> $salt,
								'password'				=> $password,
								'password_hash'			=> $password_hash,
								'email'					=> $email,
								'email_setting'			=> 1,
								'save_pass'				=> 0,
								'timezone'				=> $forum_config['o_default_timezone'],
								'dst'					=> 0,
								'language'				=> $forum_config['o_default_lang'],
								'style'					=> $forum_config['o_default_style'],
								'registered'			=> time(),
								'registration_ip'		=> get_remote_address(),
								'activate_key'			=> ($forum_config['o_regs_verify'] == '1') ? '\''.random_key(8, true).'\'' : 'NULL',
								'require_verification'	=> ($forum_config['o_regs_verify'] == '1'),
								'notify_admins'			=> ($forum_config['o_regs_report'] == '1')
								),
								$new_uid
						);
						
						if (file_exists(FORUM_ROOT.'extensions/pun_admin_add_user/lang/'.$forum_user['language'].'/pun_admin_add_user.php'))
							require FORUM_ROOT.'extensions/pun_admin_add_user/lang/'.$forum_user['language'].'/pun_admin_add_user.php';
						else
							require FORUM_ROOT.'extensions/pun_admin_add_user/lang/English/pun_admin_add_user.php';
						
						if (isset($_POST['edit_identity']) && $_POST['edit_identity'] == 1)
							redirect(forum_link($forum_url['profile_identity'], $new_uid), $lang_admin_add_user['User added successfully']);
	
						$ext_admin_add_user_user_added = true;
					}
					else
						$ext_admin_add_user_user_added = false;
				}
			}
// pun_admin_add_user

if ($forum_user['g_read_board'] == '0')
	message($lang_common['No view']);
else if ($forum_user['g_view_users'] == '0')
	message($lang_common['No permission']);

// Load the userlist.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/userlist.php';

// Miscellaneous setup
$forum_page['show_post_count'] = ($forum_config['o_show_post_count'] == '1' || $forum_user['is_admmod']) ? true : false;
$forum_page['username'] = (isset($_GET['username']) && $_GET['username'] != '-' && $forum_user['g_search_users'] == '1') ? $_GET['username'] : '';
$forum_page['show_group'] = (!isset($_GET['show_group']) || intval($_GET['show_group']) < -1 && intval($_GET['show_group']) > 2) ? -1 : intval($_GET['show_group']);
$forum_page['sort_by'] = (!isset($_GET['sort_by']) || $_GET['sort_by'] != 'username' && $_GET['sort_by'] != 'registered' && ($_GET['sort_by'] != 'num_posts' || !$forum_page['show_post_count'])) ? 'username' : $_GET['sort_by'];
$forum_page['sort_dir'] = (!isset($_GET['sort_dir']) || strtoupper($_GET['sort_dir']) != 'ASC' && strtoupper($_GET['sort_dir']) != 'DESC') ? 'ASC' : strtoupper($_GET['sort_dir']);


// Create any SQL for the WHERE clause
$where_sql = array();
$like_command = ($db_type == 'pgsql') ? 'ILIKE' : 'LIKE';

if ($forum_user['g_search_users'] == '1' && $forum_page['username'] != '')
	$where_sql[] = 'u.username '.$like_command.' \''.$forum_db->escape(str_replace('*', '%', $forum_page['username'])).'\'';
if ($forum_page['show_group'] > -1)
	$where_sql[] = 'u.group_id='.$forum_page['show_group'];


// Fetch user count
$query = array(
	'SELECT'	=> 'COUNT(u.id)',
	'FROM'		=> 'users AS u',
	'WHERE'		=> 'u.id > 1 AND u.group_id != '.FORUM_UNVERIFIED
);

if (!empty($where_sql))
	$query['WHERE'] .= ' AND '.implode(' AND ', $where_sql);

($hook = get_hook('ul_qr_get_user_count')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
$forum_page['num_users'] = $forum_db->result($result);

// Determine the user offset (based on $_GET['p'])
$forum_page['num_pages'] = ceil($forum_page['num_users'] / 50);
$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : intval($_GET['p']);
$forum_page['start_from'] = 50 * ($forum_page['page'] - 1);
$forum_page['finish_at'] = min(($forum_page['start_from'] + 50), ($forum_page['num_users']));

$forum_page['users_searched'] = (($forum_user['g_search_users'] == '1' && $forum_page['username'] != '') || $forum_page['show_group'] > -1);

if ($forum_page['num_users'] > 0)
	$forum_page['items_info'] = generate_items_info( (($forum_page['users_searched']) ? $lang_ul['Users found'] : $lang_ul['Users']), ($forum_page['start_from'] + 1), $forum_page['num_users']);
else
	$forum_page['items_info'] = $lang_ul['Users'];

// Generate paging links
$forum_page['page_post']['paging'] = '<p class="paging"><span class="pages">'.$lang_common['Pages'].'</span> '.paginate($forum_page['num_pages'], $forum_page['page'], $forum_url['users_browse'], $lang_common['Paging separator'], array($forum_page['show_group'], $forum_page['sort_by'], $forum_page['sort_dir'], ($forum_page['username'] != '') ? urlencode($forum_page['username']) : '-')).'</p>';

// Navigation links for header and page numbering for title/meta description
if ($forum_page['page'] < $forum_page['num_pages'])
{
	$forum_page['nav']['last'] = '<link rel="last" href="'.forum_sublink($forum_url['users_browse'], $forum_url['page'], $forum_page['num_pages'], array($forum_page['show_group'], $forum_page['sort_by'], $forum_page['sort_dir'], ($forum_page['username'] != '') ? urlencode($forum_page['username']) : '-')).'" title="'.$lang_common['Page'].' '.$forum_page['num_pages'].'" />';
	$forum_page['nav']['next'] = '<link rel="next" href="'.forum_sublink($forum_url['users_browse'], $forum_url['page'], ($forum_page['page'] + 1), array($forum_page['show_group'], $forum_page['sort_by'], $forum_page['sort_dir'], ($forum_page['username'] != '') ? urlencode($forum_page['username']) : '-')).'" title="'.$lang_common['Page'].' '.($forum_page['page'] + 1).'" />';
}
if ($forum_page['page'] > 1)
{
	$forum_page['nav']['prev'] = '<link rel="prev" href="'.forum_sublink($forum_url['users_browse'], $forum_url['page'], ($forum_page['page'] - 1), array($forum_page['show_group'], $forum_page['sort_by'], $forum_page['sort_dir'], ($forum_page['username'] != '') ? urlencode($forum_page['username']) : '-')).'" title="'.$lang_common['Page'].' '.($forum_page['page'] - 1).'" />';
	$forum_page['nav']['first'] = '<link rel="first" href="'.forum_link($forum_url['users_browse'], array($forum_page['show_group'], $forum_page['sort_by'], $forum_page['sort_dir'], ($forum_page['username'] != '') ? urlencode($forum_page['username']) : '-')).'" title="'.$lang_common['Page'].' 1" />';
}

// Setup main options
if (empty($_GET))
	$forum_page['main_head_options'] = array();
else
	$forum_page['main_head_options'] = array(
		'new_search'	=> '<span'.(empty($forum_page['main_foot_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['users']).'">'.$lang_ul['Perform new search'].'</a></span>'
	);

// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = $base_url.'/userlist.php';

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($lang_common['User list'], forum_link($forum_url['users']))
);

// Setup main heading
if ($forum_page['num_pages'] > 1)
	$forum_page['main_head_pages'] = sprintf($lang_common['Page info'], $forum_page['page'], $forum_page['num_pages']);

($hook = get_hook('ul_pre_header_load')) ? eval($hook) : null;

define('FORUM_ALLOW_INDEX', 1);

define('FORUM_PAGE', 'userlist');
require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();

($hook = get_hook('ul_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-head">
<?php

	if (!empty($forum_page['main_head_options']))
		echo "\t\t".'<p class="options">'.implode(' ', $forum_page['main_head_options']).'</p>'."\n";

?>
		<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
	</div>
	<div class="main-content main-frm">
		<form id="afocus" method="get" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
		<div class="frm-form">
<?php ($hook = get_hook('ul_search_fieldset_start')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo $lang_ul['User find legend'] ?></strong></legend>
<?php ($hook = get_hook('ul_pre_username')) ? eval($hook) : null; ?>
<?php if ($forum_user['g_search_users'] == '1'): ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box text">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_ul['Search for username'] ?></span> <small><?php echo $lang_ul['Username help'] ?></small></label><br />
						<span class="fld-input"><input type="text" id="fld<?php echo $forum_page['fld_count'] ?>" name="username" value="<?php echo forum_htmlencode($forum_page['username']) ?>" size="35" maxlength="25" /></span>
					</div>
				</div>
<?php endif; ?>
<?php ($hook = get_hook('ul_pre_group_select')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_ul['User group'] ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="show_group">
						<option value="-1"<?php if ($forum_page['show_group'] == -1) echo ' selected="selected"' ?>><?php echo $lang_ul['All users'] ?></option>
<?php

($hook = get_hook('ul_search_new_group_option')) ? eval($hook) : null;

// Get the list of user groups (excluding the guest group)
$query = array(
	'SELECT'	=> 'g.g_id, g.g_title',
	'FROM'		=> 'groups AS g',
	'WHERE'		=> 'g.g_id!='.FORUM_GUEST,
	'ORDER BY'	=> 'g.g_id'
);

($hook = get_hook('ul_qr_get_groups')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

while ($cur_group = $forum_db->fetch_assoc($result))
{
	if ($cur_group['g_id'] == $forum_page['show_group'])
		echo "\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
	else
		echo "\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.forum_htmlencode($cur_group['g_title']).'</option>'."\n";
}

?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('ul_pre_sort_by')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box select">
						<label for="fld<?php echo ++$forum_page['fld_count'] ?>"><span><?php echo $lang_ul['Sort users by'] ?></span></label><br />
						<span class="fld-input"><select id="fld<?php echo $forum_page['fld_count'] ?>" name="sort_by">
						<option value="username"<?php if ($forum_page['sort_by'] == 'username') echo ' selected="selected"' ?>><?php echo $lang_ul['Username'] ?></option>
						<option value="registered"<?php if ($forum_page['sort_by'] == 'registered') echo ' selected="selected"' ?>><?php echo $lang_ul['Registered'] ?></option>
<?php if ($forum_page['show_post_count']): ?>
						<option value="num_posts"<?php if ($forum_page['sort_by'] == 'num_posts') echo ' selected="selected"' ?>><?php echo $lang_ul['No of posts'] ?></option>
<?php endif; ($hook = get_hook('ul_new_sort_by_option')) ? eval($hook) : null; ?>
						</select></span>
					</div>
				</div>
<?php ($hook = get_hook('ul_pre_sort_order_fieldset')) ? eval($hook) : null; ?>
				<fieldset class="mf-set set<?php echo ++$forum_page['item_count'] ?>">
					<legend><span><?php echo $lang_ul['User sort order'] ?></span></legend>
<?php ($hook = get_hook('ul_pre_sort_order')) ? eval($hook) : null; ?>
					<div class="mf-box mf-yesno">
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="sort_dir" value="ASC"<?php if ($forum_page['sort_dir'] == 'ASC') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_ul['Ascending'] ?></label>
						</div>
						<div class="mf-item">
							<span class="fld-input"><input type="radio" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="sort_dir" value="DESC"<?php if ($forum_page['sort_dir'] == 'DESC') echo ' checked="checked"' ?> /></span>
							<label for="fld<?php echo $forum_page['fld_count'] ?>"><?php echo $lang_ul['Descending'] ?></label>
						</div>
					</div>
<?php ($hook = get_hook('ul_pre_sort_order_fieldset_end')) ? eval($hook) : null; ?>
				</fieldset>
<?php ($hook = get_hook('ul_pre_search_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('ul_search_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit"><input type="submit" name="search" value="<?php echo $lang_ul['Submit user search'] ?>" /></span>
			</div>
		</div>
		</form>
<?php

// Grab the users
$query = array(
	'SELECT'	=> 'u.id, u.username, u.title, u.num_posts, u.registered, g.g_id, g.g_user_title',
	'FROM'		=> 'users AS u',
	'JOINS'		=> array(
		array(
			'LEFT JOIN'		=> 'groups AS g',
			'ON'			=> 'g.g_id=u.group_id'
		)
	),
	'WHERE'		=> 'u.id > 1 AND u.group_id != '.FORUM_UNVERIFIED,
	'ORDER BY'	=> $forum_page['sort_by'].' '.$forum_page['sort_dir'].', u.id ASC',
	'LIMIT'		=> $forum_page['start_from'].', 50'
);

if (!empty($where_sql))
	$query['WHERE'] .= ' AND '.implode(' AND ', $where_sql);

($hook = get_hook('ul_qr_get_users')) ? eval($hook) : null;
$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
$forum_page['item_count'] = 0;

if ($forum_db->num_rows($result))
{
	($hook = get_hook('ul_results_pre_header')) ? eval($hook) : null;

	$forum_page['table_header'] = array();
	$forum_page['table_header']['username'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.$lang_ul['Username'].'</th>';
	$forum_page['table_header']['title'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.$lang_ul['Title'].'</th>';

	if ($forum_page['show_post_count'])
		$forum_page['table_header']['posts'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.$lang_ul['Posts'].'</th>';

	$forum_page['table_header']['registered'] = '<th class="tc'.count($forum_page['table_header']).'" scope="col">'.$lang_ul['Registered'].'</th>';

	($hook = get_hook('ul_results_pre_header_output')) ? eval($hook) : null;

?>
		<div class="ct-group">
			<table cellspacing="0" summary="<?php echo $lang_ul['Table summary'] ?>">
				<thead>
					<tr>
						<?php echo implode("\n\t\t\t\t\t\t", $forum_page['table_header'])."\n" ?>
					</tr>
				</thead>
				<tbody>
<?php

	while ($user_data = $forum_db->fetch_assoc($result))
	{
		($hook = get_hook('ul_results_row_pre_data')) ? eval($hook) : null;

		$forum_page['table_row'] = array();
		$forum_page['table_row']['username'] = '<td class="tc'.count($forum_page['table_row']).'"><a href="'.forum_link($forum_url['user'], $user_data['id']).'">'.forum_htmlencode($user_data['username']).'</a></td>';
		$forum_page['table_row']['title'] = '<td class="tc'.count($forum_page['table_row']).'">'.get_title($user_data).'</td>';

		if ($forum_page['show_post_count'])
			$forum_page['table_row']['posts'] = '<td class="tc'.count($forum_page['table_row']).'">'.forum_number_format($user_data['num_posts']).'</td>';

		$forum_page['table_row']['registered'] = '<td class="tc'.count($forum_page['table_row']).'">'.format_time($user_data['registered'], 1).'</td>';

		++$forum_page['item_count'];

		($hook = get_hook('ul_results_row_pre_data_output')) ? eval($hook) : null;

?>
				<tr class="<?php echo ($forum_page['item_count'] % 2 != 0) ? 'odd' : 'even' ?><?php echo ($forum_page['item_count'] == 1) ? ' row1' : '' ?>">
					<?php echo implode("\n\t\t\t\t\t\t", $forum_page['table_row'])."\n" ?>
				</tr>
<?php

	}

?>
				</tbody>
			</table>
		</div>
<?php

}
else
{

?>
		<div class="ct-box">
			<p><strong><?php echo $lang_ul['No users found'] ?></strong></p>
		</div>
<?php

}

?>
	</div>
	<div class="main-foot">
<?php

	if (!empty($forum_page['main_foot_options']))
		echo "\n\t\t\t".'<p class="options">'.implode(' ', $forum_page['main_foot_options']).'</p>';

?>
		<h2 class="hn"><span><?php echo $forum_page['items_info'] ?></span></h2>
	</div>
<?php

($hook = get_hook('ul_end')) ? eval($hook) : null;

// pun_admin_add_user
if ($forum_user['g_id'] == FORUM_ADMIN)
			{
				if (file_exists(FORUM_ROOT.'extensions/pun_admin_add_user/lang/'.$forum_user['language'].'/pun_admin_add_user.php'))
					require FORUM_ROOT.'extensions/pun_admin_add_user/lang/'.$forum_user['language'].'/pun_admin_add_user.php';
				else
					require FORUM_ROOT.'extensions/pun_admin_add_user/lang/English/pun_admin_add_user.php';

				$username = '';
				$email = '';
				$edit_identity = '';
				$result_message = '';

				if (isset($_POST['add_user_form_sent']) && $_POST['add_user_form_sent'] == 1)
				{
					if ($ext_admin_add_user_user_added === true)
						$result_message = '<div class="frm-info"><p>'.$lang_admin_add_user['User added successfully'].'/p></div>';
					else
					{
						$username = $_POST['req_username'];
						$email = $_POST['req_email'];
						$edit_identity = isset($_POST['edit_identity']);
					}
				}

				$buffer_old = ob_get_contents();
				
				ob_end_clean();
				
				ob_start();
				
				$pun_add_user_form_action = $base_url.'/userlist.php';

				// Get output buffer and insert form
				$pos = strpos($buffer_old, '<div class="main-foot">');
				echo substr($buffer_old, 0 , $pos);
				?>

				<div class="main-head">
					<h2 class="hn"><span><?php echo $lang_admin_add_user['Add user'] ?></span></h2>
				</div>
				<div class="main-content main-frm">
				<?php

				if (!empty($errors_add_users))
				{
					$error_li = array();
					for ($err_num = 0; $err_num < count($errors_add_users); $err_num++)
						$error_li[] = '<li class="warn"><span>'.$errors_add_users[$err_num].'</span></li>';

				?>
					<div class="ct-box error-box">
						<h2 class="warn hn"><?php echo $lang_admin_add_user['There are some errors']; ?></h2>
						<ul class="error-list">
						<?php echo implode("\n\t\t\t\t\t\t", $error_li)."\n" ?>
						</ul>
					</div>
				<?php } ?>
					<form class="frm-form" id="frm-adduser" action="<?php echo $pun_add_user_form_action ?>#adduser-content" method="post">
						<fieldset class="frm-group group1">
							<div class="sf-set set1">
								<div class="sf-box text">
									<label for="add_user_username">
										<span><?php echo $lang_admin_add_user['Username'] ?></span>
										<small>
											<?php echo $lang_admin_add_user['Between 2 and 25 characters'] ?>
										</small>
									</label>
									<span class="fld-input"><input type="text" id="add_user_username" name="req_username" size="35" value="<?php echo $username ?>" maxlength="25" /></span>
								</div>
							</div>

							<div class="sf-set set2">
								<div class="sf-box text">
									<label for="add_user_email">
										<span><?php echo $lang_admin_add_user['E-mail'] ?></span>
										<small>
											<?php echo $lang_admin_add_user['Enter a current and valid e-mail address'] ?>
										</small>
									</label>
									<span class="fld-input"><input type="text" id="add_user_email" name="req_email" size="35" value="<?php echo $email ?>" maxlength="80" /></span>
								</div>
							</div>

							<fieldset class="mf-set set3">
								<legend><span><?php echo $lang_admin_add_user['Edit user identity'] ?></span></legend>
								<div class="mf-box mf-yesno">
									<span class="fld_input">
										<input type="checkbox" id="add_user_edit_user_identity" name="edit_identity" value="1"<?php echo $edit_identity ? ' checked="checked"' : '' ?> />
									</span>
									<label for="add_user_edit_user_identity">
										<span>
											<?php echo $lang_admin_add_user['Edit User Identity after adding User'] ?>
										</span>
									</label>
								</div>
							</fieldset>

						</fieldset>

						<div class="frm-buttons">
							<span class="submit"><input type="submit" name="submit" value="<?php echo $lang_admin_add_user['Add user'] ?>" /></span>
						</div>

						<div class="hidden">
								<input type="hidden" name="csrf_token" value="<?php echo generate_form_token($pun_add_user_form_action) ?>" />
								<input type="hidden" name="add_user_form_sent" value="1" />
						</div>

					</form>
				</div>
				<?php

				echo substr($buffer_old, $pos, strlen($buffer_old) - $pos);
			}
// pun_admin_add_user

$tpl_temp = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

require FORUM_ROOT.'footer.php';
