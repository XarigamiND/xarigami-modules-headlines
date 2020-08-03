<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami headlines module
 * @copyright (C) 2008-2011 2skies.com
 * @link http://xarigami.com/project/headlines
 */
/**
 * delete item
 * @param 'hid' the id of the item to be deleted
 * @param 'confirmation' confirmation that this item can be deleted
 */
function headlines_admin_delete()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('hid','int:1:',$hid)) return;
    if (!xarVarFetch('obid','str:1:',$obid,$hid,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;

    // Security Check
    if(!xarSecurityCheck('DeleteHeadlines')) return;
    $link['menulinks'] = xarModAPIFunc('headlines','admin','getmenulinks');
    // The user API function is called
    $link = xarModAPIFunc('headlines', 'user', 'get',
                          array('hid' => $hid));

    if ($link == false) return;
    // Check for confirmation.
    if (empty($confirm)) {
    $link['menulinks'] = xarModAPIFunc('headlines','admin','getmenulinks');
    $link['submitlabel'] = xarML('Confirm Delete');
    $link['authid'] = xarSecGenAuthKey();

    return $link;

    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // The API function is called
    if (!xarModAPIFunc('headlines', 'admin','delete',
                       array('hid' => $hid))) return;
    $msg = xarML('Feed successfully deleted.');
    xarTplSetMessage($msg,'status');
    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));

    // Return
    return true;
}
?>
