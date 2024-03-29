<?php
/**
 * Post deletion page.
 *
 * Deletes the specified post (and, if necessary, the topic it is in).
 *
 * @copyright (C) 2008-2009 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


if (!defined('FORUM_ROOT'))
	define('FORUM_ROOT', './');
require FORUM_ROOT.'include/common.php';

($hook = get_hook('dl_start')) ? eval($hook) : null;

// pun_attachment
require FORUM_ROOT.'extensions/pun_attachment/include/attach_func.php';
			if (file_exists(FORUM_ROOT.'extensions/pun_attachment/lang/'.$forum_user['language'].'/pun_attachment.php'))
				require FORUM_ROOT.'extensions/pun_attachment/lang/'.$forum_user['language'].'/pun_attachment.php';
			else
				require FORUM_ROOT.'extensions/pun_attachment/lang/English/pun_attachment.php';
// pun_attachment

if ($forum_user['g_read_board'] == '0')
	message($lang_common['No view']);

// Load the delete.php language file
require FORUM_ROOT.'lang/'.$forum_user['language'].'/delete.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message($lang_common['Bad request']);


// Fetch some info about the post, the topic and the forum
$query = array(
	'SELECT'	=> 'f.id AS fid, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.first_post_id, t.closed, p.poster, p.poster_id, p.message, p.hide_smilies, p.posted',
	'FROM'		=> 'posts AS p',
	'JOINS'		=> array(
		array(
			'INNER JOIN'	=> 'topics AS t',
			'ON'			=> 't.id=p.topic_id'
		),
		array(
			'INNER JOIN'	=> 'forums AS f',
			'ON'			=> 'f.id=t.forum_id'
		),
		array(
			'LEFT JOIN'		=> 'forum_perms AS fp',
			'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
		)
	),
	'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$id
);

($hook = get_hook('dl_qr_get_post_info')) ? eval($hook) : null;

// pun_attachment
if (!$forum_config['attach_disable_attach'])
			{
				$query['SELECT'] .= ', g_pun_attachment_allow_upload, g_pun_attachment_upload_max_size, g_pun_attachment_files_per_post, g_pun_attachment_disallowed_extensions, g_pun_attachment_allow_delete_own, g_pun_attachment_allow_delete';
				$query['JOINS'][] = array('LEFT JOIN' => 'groups AS g', 'ON' => 'g.g_id = '.$forum_user['g_id']);
			}
// pun_attachment

$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
if (!$forum_db->num_rows($result))
	message($lang_common['Bad request']);

$cur_post = $forum_db->fetch_assoc($result);

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_post['moderators'] != '') ? unserialize($cur_post['moderators']) : array();
$forum_page['is_admmod'] = ($forum_user['g_id'] == FORUM_ADMIN || ($forum_user['g_moderator'] == '1' && array_key_exists($forum_user['username'], $mods_array))) ? true : false;

$cur_post['is_topic'] = ($id == $cur_post['first_post_id']) ? true : false;

($hook = get_hook('dl_pre_permission_check')) ? eval($hook) : null;

// Do we have permission to delete this post?
if ((($forum_user['g_delete_posts'] == '0' && !$cur_post['is_topic']) ||
	($forum_user['g_delete_topics'] == '0' && $cur_post['is_topic']) ||
	$cur_post['poster_id'] != $forum_user['id'] ||
	$cur_post['closed'] == '1') &&
	!$forum_page['is_admmod'])
	message($lang_common['No permission']);


($hook = get_hook('dl_post_selected')) ? eval($hook) : null;

// User pressed the cancel button
if (isset($_POST['cancel']))
	redirect(forum_link($forum_url['post'], $id), $lang_common['Cancel redirect']);

// User pressed the delete button
else if (isset($_POST['delete']))
{
	($hook = get_hook('dl_form_submitted')) ? eval($hook) : null;
	
	// pun_attachment
	if (!$forum_config['attach_disable_attach'])
			{
				$attach_query = array(
					'SELECT'	=>	'id, file_path, owner_id',
					'FROM'		=>	'attach_files'
				);
				$attach_query['WHERE'] = $cur_post['is_topic'] ? 'post_id != 0 AND topic_id = '.$cur_post['tid'] : 'post_id = '.$id;
			}
	// pun_attachment

	if (!isset($_POST['req_confirm']))
		redirect(forum_link($forum_url['post'], $id), $lang_common['No confirm redirect']);

	if ($cur_post['is_topic'])
	{
		// Delete the topic and all of it's posts
		delete_topic($cur_post['tid'], $cur_post['fid']);

		($hook = get_hook('dl_topic_deleted_pre_redirect')) ? eval($hook) : null;
		
		// pun_attachment
		if (!$forum_config['attach_disable_attach'])
			{
				remove_attachments($attach_query, $cur_post);
			}
		// pun_attachment

		redirect(forum_link($forum_url['forum'], array($cur_post['fid'], sef_friendly($cur_post['forum_name']))), $lang_delete['Topic del redirect']);
	}
	else
	{
		// Delete just this one post
		delete_post($id, $cur_post['tid'], $cur_post['fid']);

		($hook = get_hook('dl_post_deleted_pre_redirect')) ? eval($hook) : null;
		
		// pun_attachment
		if (!$forum_config['attach_disable_attach'])
			{
				remove_attachments($attach_query, $cur_post);
			}
		// pun_attachment

		redirect(forum_link($forum_url['topic'], array($cur_post['tid'], sef_friendly($cur_post['subject']))), $lang_delete['Post del redirect']);
	}
}

// Run the post through the parser
if (!defined('FORUM_PARSER_LOADED'))
	require FORUM_ROOT.'include/parser.php';

$cur_post['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);

// Setup form
$forum_page['group_count'] = $forum_page['item_count'] = $forum_page['fld_count'] = 0;
$forum_page['form_action'] = forum_link($forum_url['delete'], $id);

$forum_page['hidden_fields'] = array(
	'form_sent'		=> '<input type="hidden" name="form_sent" value="1" />',
	'csrf_token'	=> '<input type="hidden" name="csrf_token" value="'.generate_form_token($forum_page['form_action']).'" />'
);

// Setup form information
$forum_page['frm_info'] = array(
	'<li><span>'.$lang_delete['Forum'].':<strong> '.forum_htmlencode($cur_post['forum_name']).'</strong></span></li>',
	'<li><span>'.$lang_delete['Topic'].':<strong> '.forum_htmlencode($cur_post['subject']).'</strong></span></li>',
	'<li><span>'.sprintf((($cur_post['is_topic']) ? $lang_delete['Delete topic info'] : $lang_delete['Delete post info']), forum_htmlencode($cur_post['poster']), format_time($cur_post['posted'])).'</span></li>'
);

// Generate the post heading
$forum_page['post_ident'] = array();
$forum_page['post_ident']['byline'] = '<span class="post-byline">'.sprintf((($cur_post['is_topic']) ? $lang_delete['Topic byline'] : $lang_delete['Reply byline']), '<strong>'.forum_htmlencode($cur_post['poster']).'</strong>').'</span>';
$forum_page['post_ident']['link'] = '<span class="post-link"><a class="permalink" href="'.forum_link($forum_url['post'], $cur_post['tid']).'">'.format_time($cur_post['posted']).'</a></span>';

($hook = get_hook('dl_pre_item_ident_merge')) ? eval($hook) : null;

// Generate the post title
if ($cur_post['is_topic'])
	$forum_page['item_subject'] = sprintf($lang_delete['Topic title'], $cur_post['subject']);
else
	$forum_page['item_subject'] = sprintf($lang_delete['Reply title'], $cur_post['subject']);

$forum_page['item_subject'] = forum_htmlencode($forum_page['item_subject']);

// Setup breadcrumbs
$forum_page['crumbs'] = array(
	array($forum_config['o_board_title'], forum_link($forum_url['index'])),
	array($cur_post['forum_name'], forum_link($forum_url['forum'], array($cur_post['fid'], sef_friendly($cur_post['forum_name'])))),
	array($cur_post['subject'], forum_link($forum_url['topic'], array($cur_post['tid'], sef_friendly($cur_post['subject'])))),
	(($cur_post['is_topic']) ? $lang_delete['Delete topic'] : $lang_delete['Delete post'])
);

($hook = get_hook('dl_pre_header_load')) ? eval($hook) : null;

define ('FORUM_PAGE', 'postdelete');
require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();

($hook = get_hook('dl_main_output_start')) ? eval($hook) : null;

?>
	<div class="main-content main-frm">
		<div class="ct-box info-box">
			<ul class="info-list">
				<?php echo implode("\n\t\t\t\t", $forum_page['frm_info'])."\n" ?>
			</ul>
		</div>
<?php ($hook = get_hook('dl_pre_post_display')) ? eval($hook) : null; ?>
		<div class="post singlepost">
			<div class="posthead">
				<h3 class="hn post-ident"><?php echo implode(' ', $forum_page['post_ident']) ?></h3>
<?php ($hook = get_hook('dl_new_post_head_option')) ? eval($hook) : null; ?>
			</div>
			<div class="postbody">
				<div class="post-entry">
					<h4 class="entry-title hn"><?php echo $forum_page['item_subject'] ?></h4>
					<div class="entry-content">
						<?php echo $cur_post['message']."\n" ?>
					</div>
<?php ($hook = get_hook('dl_new_post_entry_data')) ? eval($hook) : null; ?>
				</div>
			</div>
		</div>
		<form class="frm-form" method="post" accept-charset="utf-8" action="<?php echo $forum_page['form_action'] ?>">
			<div class="hidden">
				<?php echo implode("\n\t\t\t\t", $forum_page['hidden_fields'])."\n" ?>
			</div>
<?php ($hook = get_hook('dl_pre_confirm_delete_fieldset')) ? eval($hook) : null; ?>
			<fieldset class="frm-group group<?php echo ++$forum_page['group_count'] ?>">
				<legend class="group-legend"><strong><?php echo ($cur_post['is_topic']) ? $lang_delete['Delete topic'] : $lang_delete['Delete post'] ?></strong></legend>
<?php ($hook = get_hook('dl_pre_confirm_delete_checkbox')) ? eval($hook) : null; ?>
				<div class="sf-set set<?php echo ++$forum_page['item_count'] ?>">
					<div class="sf-box checkbox">
						<span class="fld-input"><input type="checkbox" id="fld<?php echo ++$forum_page['fld_count'] ?>" name="req_confirm" value="1" checked="checked" /></span>
						<label for="fld<?php echo $forum_page['fld_count'] ?>"><span><?php echo $lang_delete['Please confirm'] ?></span> <?php printf(((($cur_post['is_topic'])) ? $lang_delete['Delete topic label'] : $lang_delete['Delete post label']), forum_htmlencode($cur_post['poster']), format_time($cur_post['posted'])) ?></label>
					</div>
				</div>
<?php ($hook = get_hook('dl_pre_confirm_delete_fieldset_end')) ? eval($hook) : null; ?>
			</fieldset>
<?php ($hook = get_hook('dl_confirm_delete_fieldset_end')) ? eval($hook) : null; ?>
			<div class="frm-buttons">
				<span class="submit"><input type="submit" name="delete" value="<?php echo ($cur_post['is_topic']) ? $lang_delete['Delete topic'] : $lang_delete['Delete post'] ?>" /></span>
				<span class="cancel"><input type="submit" name="cancel" value="<?php echo $lang_common['Cancel'] ?>" /></span>
			</div>
		</form>
	</div>
<?php

$forum_id = $cur_post['fid'];

($hook = get_hook('dl_end')) ? eval($hook) : null;

$tpl_temp = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

require FORUM_ROOT.'footer.php';
