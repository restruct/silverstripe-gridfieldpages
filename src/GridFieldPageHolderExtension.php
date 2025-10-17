<?php
/**
 * Created by PhpStorm.
 * User: mic
 * Date: 2019-12-09
 * Time: 07:56
 */

namespace Restruct\Silverstripe\GridFieldPages;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use Restruct\Silverstripe\SiteTreeButtons\GridFieldAddNewSiteTreeItemButton;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use Restruct\Silverstripe\SiteTreeButtons\GridFieldEditSiteTreeItemButton;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\GridField\GridField;

class GridFieldPageHolderExtension
extends Extension
{
    // defaults, configurable via config
//    private static $allowed_children = [
//        '*'.GridFieldPage::class,
//    ];
//    private static $default_child = GridFieldPage::class;
    private static $add_default_gridfield = true;
//    private static $apply_sortable = true;
//    private static $subpage_tab = "";
//    private static $gridfield_title = "Manage Subpages";

    public function updateCMSFields(FieldList $fields)
    {
//        die('called');
        // GridFieldPage
        if($this->getOwner()->config()->get('add_default_gridfield')) {
            $this->addPagesGridField($fields);
        }
    }

    public function addPagesGridField(&$fields, $tab='Root.Subpages', $gfTitle='Manage Subpages', $orderable=null)
    {
        $gridFieldConfig = GridFieldConfig::create()
            ->addComponents(
                # new GridFieldToolbarHeader(),
                GridFieldButtonRow::create('before'),
                new GridFieldAddNewSiteTreeItemButton(),
                # new GridFieldTitleHeader(),
                # new GridFieldSortableHeader(),
                GridFieldFilterHeader::create(),
                $dataColumns = GridFieldDataColumns::create(),
                GridFieldPaginator::create(20),
                new GridFieldEditSiteTreeItemButton()
            );

        // Orderable is optional, as often pages may be sorted by other means
        if($orderable===null) $orderable = $this->getOwner()->config()->apply_sortable;
        if ($orderable) {
            $gridFieldConfig->addComponent(GridFieldOrderablePages::create());
        }

        $dataColumns->setDisplayFields([
            'TreeTitleAsHtml' => _t('SilverStripe\\CMS\\Model\\SiteTree.PAGETITLE', 'Page Title'),
            'singular_name' => _t('SilverStripe\\CMS\\Model\\SiteTree.PAGETYPE', 'Page Type'),
            'LastEdited' => _t('SilverStripe\\CMS\\Model\\SiteTree.LASTUPDATED', 'Last Updated'),
        ]);

        $gridField = GridField::create("Subpages",
                Config::inst()->get($this->getOwner()->className, 'gridfield_title'),
                DataObject::get($this->getOwner()->defaultChild(), 'ParentID = '.$this->getOwner()->ID),
                $gridFieldConfig
            )
            ->setModelClass($this->getOwner()->defaultChild());

        $fields->addFieldToTab($tab, $gridField);
    }

}