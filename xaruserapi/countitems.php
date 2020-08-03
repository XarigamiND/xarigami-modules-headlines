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
 * count the number of links in the database
 * @return int number of links in the database
 */
function headlines_userapi_countitems()
{
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    // Security Check
    if(!xarSecurityCheck('OverviewHeadlines')) return;
    $headlinestable = $xartable['headlines'];
    $query = "SELECT COUNT(1)
            FROM $headlinestable";
    $result = $dbconn->Execute($query);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();
    return $numitems;
}
?>