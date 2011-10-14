<?php
/**
 * Outputs the footer used by most forum pages.
 *
 * @copyright (C) 2008-2009 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// START SUBST - <!-- forum_about -->
ob_start();
($hook = get_hook('ft_about_output_start')) ? eval($hook) : null;

// Display the "Jump to" drop list
if ($forum_user['g_read_board'] == '1' && $forum_config['o_quickjump'] == '1')
{
	($hook = get_hook('ft_about_pre_quickjump')) ? eval($hook) : null;

	// Load cached quickjump
	if (file_exists(FORUM_CACHE_DIR.'cache_quickjump_'.$forum_user['g_id'].'.php'))
		include FORUM_CACHE_DIR.'cache_quickjump_'.$forum_user['g_id'].'.php';

	if (!defined('FORUM_QJ_LOADED'))
	{
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_quickjump_cache($forum_user['g_id']);
		require FORUM_CACHE_DIR.'cache_quickjump_'.$forum_user['g_id'].'.php';
	}
}

($hook = get_hook('ft_about_pre_copyright')) ? eval($hook) : null;

// pun_bbcode
if (defined('PUN_BBCODE_BAR_INCLUDE')) {
	include FORUM_ROOT.'extensions/pun_bbcode/bar.php';
?>
<script type="text/javascript"><!--
var pun_bbcode_bar = document.getElementById("pun_bbcode_bar");
if (pun_bbcode_bar) {
	pun_bbcode_bar.innerHTML = "<?php echo pun_bbcode_bar(); ?>";
	pun_bbcode_bar.style.display = "block";
	pun_bbcode_bar.style.visibility = "visible";
}
--></script>
<?php
}
// pun_bbcode

?>
	<p id="copyright"><?php echo sprintf($lang_common['Powered by'], '<a href="http://punbb.informer.com/">PunBB</a>'.($forum_config['o_show_version'] == '1' ? ' '.$forum_config['o_cur_version'] : ''), '<a href="http://www.informer.com/">Informer Technologies, Inc</a>') ?></p>
<?php

($hook = get_hook('ft_about_end')) ? eval($hook) : null;

// pun_attachment
if (!defined('PUN_EXTENSIONS_USED') && !empty($pun_extensions_used))
			{
				define('PUN_EXTENSIONS_USED', 1);
				echo '<p id="extensions-used">Currently used extensions: '.implode(', ', $pun_extensions_used).'. Copyright &copy; 2008 <a href="http://punbb.informer.com/">PunBB</a></p>';
			}
// pun_attachment

// pun_bbcode
if (!defined('PUN_EXTENSIONS_USED') && !empty($pun_extensions_used))
{
	define('PUN_EXTENSIONS_USED', 1);
	if (count($pun_extensions_used) == 1)
		echo '<p style="clear: both; ">The '.$pun_extensions_used[0].' official extension is installed. Copyright &copy; 2003&ndash;2009 <a href="http://punbb.informer.com/">PunBB</a>.</p>';
	else
		echo '<p style="clear: both; ">Currently installed <span id="extensions-used" title="'.implode(', ', $pun_extensions_used).'.">'.count($pun_extensions_used).' official extensions</span>. Copyright &copy; 2003&ndash;2009 <a href="http://punbb.informer.com/">PunBB</a>.</p>';
}
// pun_bbcode

// pun_admin_add_user
if (!defined('PUN_EXTENSIONS_USED') && !empty($pun_extensions_used))
{
	define('PUN_EXTENSIONS_USED', 1);
	if (count($pun_extensions_used) == 1)
		echo '<p style="clear: both; ">The '.$pun_extensions_used[0].' official extension is installed. Copyright &copy; 2003&ndash;2009 <a href="http://punbb.informer.com/">PunBB</a>.</p>';
	else
		echo '<p style="clear: both; ">Currently installed <span id="extensions-used" title="'.implode(', ', $pun_extensions_used).'.">'.count($pun_extensions_used).' official extensions</span>. Copyright &copy; 2003&ndash;2009 <a href="http://punbb.informer.com/">PunBB</a>.</p>';
}
// pun_admin_add_user

$tpl_temp = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_about -->', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_about -->


// START SUBST - <!-- forum_debug -->
if (defined('FORUM_DEBUG') || defined('FORUM_SHOW_QUERIES'))
{
	ob_start();

	($hook = get_hook('ft_debug_output_start')) ? eval($hook) : null;

	// Display debug info (if enabled/defined)
	if (defined('FORUM_DEBUG'))
	{
		// Calculate script generation time
		list($usec, $sec) = explode(' ', microtime());
		$time_diff = forum_number_format(((float)$usec + (float)$sec) - $forum_start, 3);
		echo '<p id="querytime">[ '.sprintf($lang_common['Querytime'], $time_diff, forum_number_format($forum_db->get_num_queries())).' ]</p>'."\n";
	}

	if (defined('FORUM_SHOW_QUERIES'))
		echo get_saved_queries();

	($hook = get_hook('ft_debug_end')) ? eval($hook) : null;

	$tpl_temp = forum_trim(ob_get_contents());
	$tpl_main = str_replace('<!-- forum_debug -->', $tpl_temp, $tpl_main);
	ob_end_clean();
}
// END SUBST - <!-- forum_debug -->

// Last call!
($hook = get_hook('ft_end')) ? eval($hook) : null;

// End the transaction
$forum_db->end_transaction();

// Close the db connection (and free up any result data)
$forum_db->close();

// Spit out the page
exit($tpl_main);
