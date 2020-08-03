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
$modversion['name']         = 'Headlines';
$modversion['id']           = '777';
$modversion['version']      = '1.3.0';
$modversion['displayname']  = 'Headlines';
$modversion['description']  = 'Generates a list of feeds.';
$modversion['official']     = 1;
$modversion['author']       = 'John Cox';
$modversion['contact']      = 'niceguyeddie@xaraya.com';
$modversion['admin']        = 1;
$modversion['user']         = 1;
$modversion['class']        = 'Complete';
$modversion['category']     = 'Content';
$modversion['dependency']   = array(151); //Articles dependency for import
$modversion['dependencyinfo']   = array(
                                    0 => array(
                                            'name' => 'core',
                                            'version_ge' => '1.4.0'
                                         ),
                                    151 => array(
                                            'name' => 'articles',
                                            'version_ge' => '1.6.0'
                                        )
                                );
if (false) {
    xarML('Headlines');
    xarML('Generates a list of feeds');
}
?>
