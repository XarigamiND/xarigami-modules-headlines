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
/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @return array containing the item types and their description
 */
function headlines_userapi_getitemtypes($args)
{
    $itemtypes = array();
/*
    // do not use this if you only handle one type of items in your module
    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('Headlines')),
                          'title' => xarVarPrepForDisplay(xarML('View Headlines')),
                          'url'   => xarModURL('headlines','user','main'));
*/
    return $itemtypes;
}
?>