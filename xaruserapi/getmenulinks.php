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
 * @author John Cox
 */

/**
 * Utility function pass individual menu items to the main menu
 *
 * @return array containing the menulinks for the main menu items.
 */
function headlines_userapi_getmenulinks()
{

    /* If we return nothing, then we need to tell PHP this, in order to avoid an ugly
     * E_ALL error.
     */
    if (empty($menulinks)) {
        $menulinks = '';
    }
    /* The final thing that we need to do in this function is return the values back
     * to the main menu for display.
     */
    return $menulinks;
}
?>