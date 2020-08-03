<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami headlines module
 * @copyright (C) 2008-2010 2skies.com
 * @link http://xarigami.com/project/headlines
 */
function headlines_admin_modifyconfig()
{
    if(!xarSecurityCheck('AdminHeadlines')) return;
    $hooks = xarModCallHooks('module', 'modifyconfig', 'headlines', array('module' => 'headlines'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['uniqueid'] = xarModGetVar('headlines','uniqueid');
    $data['menulinks'] = xarModAPIFunc('headlines','admin','getmenulinks');
    $data['showitemimage'] = xarModGetVar('headlines', 'showitemimage');
    $data['showchanimage'] = xarModGetVar('headlines', 'showchanimage');
    $data['showitemcats'] = xarModGetVar('headlines', 'showitemcats');
    $data['shorturlslabel']     = xarML('Enable short URLs?');
    $data['shorturlschecked']   = xarModGetVar('headlines', 'SupportShortURLs') ? true : false;
    $data['modulealias'] = xarModGetVar('headlines', 'useModuleAlias');
    $data['aliasname'] = xarModGetVar('headlines', 'aliasname');
    $data['adminajax'] = xarModGetVar('headlines', 'adminajax');
    $data['userajax'] = xarModGetVar('headlines', 'userajax');
    $data['showcomments'] = xarModGetVar('headlines', 'showcomments');
    if (!xarModIsAvailable('comments') || !xarModIsHooked('comments', 'headlines')) {
        $data['showcomments'] = 0;
    }
    $data['showratings'] = xarModGetVar('headlines', 'showratings');
    if (!xarModIsAvailable('ratings') || !xarModIsHooked('ratings', 'headlines')) {
        $data['showratings'] = 0;
    }
    $data['showhitcount'] = xarModGetVar('headlines', 'showhitcount');
    if (!xarModIsAvailable('hitcount') || !xarModIsHooked('hitcount', 'headlines')) {
        $data['showhitcount'] = 0;
    }
    $data['showkeywords'] = xarModGetVar('headlines', 'showkeywords');
    if (!xarModIsAvailable('keywords') || !xarModIsHooked('keywords', 'headlines')) {
        $data['showkeywords'] = 0;
    }
    $data['maxdescription'] = xarModGetVar('headlines', 'maxdescription');
    // Magpie modvar deprecated
    $data['magpiechecked']      = xarModGetVar('headlines', 'magpie') ? true : false;

    $data['parser']             = xarModGetVar('headlines', 'parser');
    if (empty($data['parser'])) $data['parser'] = ($data['magpiechecked'] ? 'magpie' : 'default');
    // build array of available parsers
    $data['parsers'] = array();
    $data['parsers']['default'] = 'Default';
    if (xarModIsAvailable('magpie')) { // only add parser if available
        $data['parsers']['magpie'] = 'Magpie';
    } else { // if not available, check parser isn't selected (ie module removed)
        if ($data['parser'] == 'magpie') {
            $data['parser'] = 'default';
        }
    }
    if (xarModIsAvailable('simplepie')) { // ony show parser if available
        $data['parsers']['simplepie'] = 'SimplePie';
    } else { // if not available, check parser isn't selected (ie module removed)
        if ($data['parser'] == 'simplepie') {
            $data['parser'] = 'default';
        }
    }

    $data['authid']             = xarSecGenAuthKey();
    $data['pubtypes']           = xarModAPIFunc('articles', 'user', 'getpubtypes');
    $data['importpubtype']      = xarModGetVar('headlines', 'importpubtype');
    return $data;
}
?>
