<?php
/**
 * Overview displays standard Overview page
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
 * Overview displays standard Overview page
 *
 * @returns array xarTplModule with $data containing template data
 * @since 2 Oct 2005
 */
function headlines_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminHeadlines',0)) return;
    $data=array();

    $data['menulinks'] = xarModAPIFunc('headlines','admin','getmenulinks');
    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview
     */
   $data['menulinks'] = xarModAPIFunc('headlines','admin','getmenulinks');
    return xarTplModule('headlines', 'admin', 'main', $data,'main');
}

?>
