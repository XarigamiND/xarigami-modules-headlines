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
function headlines_admin_new()
{
    // Security Check
    if(!xarSecurityCheck('AddHeadlines')) return;
    $item = array();

    $item['module'] = 'headlines';
    $item['itemtype'] = NULL; // forum
    $hooks = xarModCallHooks('item','new','',$item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['url'] = '';
    $data['title'] = '';
    $data['desc'] = '';
    // show per feed options when adding a feed
    $data['itemsperpage'] = xarModGetVar('headlines', 'feeditemsperpage');
    $data['maxdescription'] = xarModGetVar('headlines', 'maxdescription');
    $data['parser'] = xarModGetVar('headlines', 'parser');
    // see if we're using simplepie
    if ($data['parser'] == 'simplepie') {
        $data['showchanimage'] = xarModGetVar('headlines', 'showchanimage');
        $data['showitemimage'] = xarModGetVar('headlines', 'showitemimage');
        $data['showitemcats'] = xarModGetVar('headlines', 'showitemcats');
    }
    $data['menulinks'] = xarModAPIFunc('headlines','admin','getmenulinks');
    $data['submitlabel'] = xarML('Submit');
    $data['authid'] = xarSecGenAuthKey();

    // Return the output
    return $data;
}
?>
