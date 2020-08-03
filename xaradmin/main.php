<?php
/**
 * Headlines - Generates a list of feeds
 *
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami headlines module
 * @copyright (C) 2008-2010 2skies.com
 * @link http://xarigami.com/project/headlines
 */
/**
 * Main administration function
 *
 * Redirect to view function
 * @return bool true on success of redirect
 */
function headlines_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;
    $data = array();
    $data['menulinks'] = xarModAPIFunc('headlines','admin','getmenulinks');

    xarResponseRedirect(xarModURL('headlines', 'admin', 'view',$data));
    // success
    return true;
}
?>
