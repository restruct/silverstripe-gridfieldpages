<?php

namespace Restruct\Silverstripe\GridFieldPages;

use SilverStripe\CMS\Controllers\CMSMain;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\View\Requirements;
use \Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class GridFieldOrderablePages
extends GridFieldOrderableRows
{
    /**
     * @param GridField $field
     */
    public function getHTMLFragments($field)
    {
        parent::getHTMLFragments($field);

        Requirements::javascript('restruct/silverstripe-gridfieldpages:client/js/gridfieldpages.js');

        /**
         * We call CMSMain savetreenode to update just the position of the reordered page without marking all siblings as changed.
         *
         * Required data:
         * - 'ID': The moved node
         * - 'ParentID': New parent relation of the moved node (0 for root)
         * - 'SiblingIDs': Array of all sibling nodes to the moved node (incl. the node itself).
         *   In case of a 'ParentID' change, relates to the new siblings under the new parent.
         */
        $field->setAttribute('data-url-pagereorder', CMSMain::create()->Link('edit/savetreenode'));
        $field->addExtraClass('ss-gridfield-orderable-pages');
    }

}
