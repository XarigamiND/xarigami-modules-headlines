<?php
/**
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami headlines module
 * @copyright (C) 2008-2010 2skies.com
 * @link http://xarigami.com/project/headlines
*/
/**
 * view items
 */
function headlines_admin_view()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum','str:1:',$startnum,1,XARVAR_NOT_REQUIRED)) return;
    $data['items'] = array();
    $data['menulinks'] = xarModAPIFunc('headlines','admin','getmenulinks');
    // Specify some labels for display
    $data['urllabel'] = xarVarPrepForDisplay(xarML('URL'));
    $data['orderlabel'] = xarVarPrepForDisplay(xarML('Order'));
    $data['warninglabel'] = xarVarPrepForDisplay(xarML('Status'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('headlines', 'user', 'countitems'),
                                    xarModURL('headlines', 'admin', 'view', array('startnum' => '%%')),
                                    xarModGetVar('headlines', 'itemsperpage'));

    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;
    // The user API function is called
    $links = xarModAPIFunc('headlines', 'user', 'getall',
                          array('startnum' => $startnum,
                                'numitems' => xarModGetVar('headlines',
                                                           'itemsperpage')));
    if (empty($links)){
        xarResponseRedirect(xarModURL('headlines', 'admin', 'new'));
    }
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        if (xarSecurityCheck('EditHeadlines',0)) {
            $links[$i]['editurl'] = xarModURL('headlines', 'admin', 'modify',
                                              array('hid' => $link['hid']));
        } else {
            $links[$i]['editurl'] = '';
        }
        $links[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteHeadlines',0)) {
            $links[$i]['deleteurl'] = xarModURL('headlines', 'admin', 'delete',
                                                array('hid' => $link['hid'],
                                                      'authid' => $data['authid']));
            $links[$i]['javascript'] = "return xar_base_confirmLink(this, '" . xarML('Delete Headline Feed') . " $link[url] ?')";
        } else {
            $links[$i]['deleteurl'] = '';
        }
        $links[$i]['deletetitle'] = xarML('Delete');
    }
    // Add the array of items to the template variables
    $data['items'] = $links;
    return $data;
}
?>
