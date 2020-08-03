<?php
/**
 * Headlines - Generates a list of feeds
 *
 * * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami headlines module
 * @copyright (C) 2008-2011 2skies.com
 * @link http://xarigami.com/project/headlines
 * @author John Cox
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Headlines module development team
 * @return array containing the menulinks for the main menu items.
 */
function headlines_adminapi_getmenulinks()
{
    // Security Check
 if(xarSecurityCheck('EditHeadlines')) {

        $menulinks[] = Array('url'   => xarModURL('headlines', 'admin', 'view'),
                              'title' => xarML('View and Edit Headlines'),
                              'label' => xarML('View'),
                              'active' => array('view')
                              );
    }
    if(xarSecurityCheck('AddHeadlines')) {
        $menulinks[] = Array('url'   => xarModURL('headlines', 'admin',  'new'),
                              'title' => xarML('Add a new Headline into the system'),
                              'label' => xarML('Add'),
                              'active' => array('new')
                              );
    }

    if(xarSecurityCheck('AdminHeadlines')) {

        $menulinks[] = Array('url'   => xarModURL('headlines', 'admin','modifyconfig'),
                              'title' => xarML('Edit the Headlines Configuration'),
                              'label' => xarML('Modify Config'),
                              'active' => array('modifyconfig')
                              );
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>