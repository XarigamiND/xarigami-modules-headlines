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
 * delete an headlines
 * @param $args['hid'] ID of the headline
 * @returns bool
 * @return true on success, false on failure
 */
function headlines_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($hid)) {
         throw new BadParameterException('hid');
    }
    // The user API function is called
    $link = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));
    if ($link == false) return;

    // Security Check
    if(!xarSecurityCheck('DeleteHeadlines')) return;
    // Get datbase setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $headlinestable = $xartable['headlines'];

    // Delete the item
    $query = "DELETE FROM $headlinestable
            WHERE xar_hid = ?";
    $bindvars = array($hid);
    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $hid, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>