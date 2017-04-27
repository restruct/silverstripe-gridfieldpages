<?php

class GridFieldPageHolder extends Page
{

    private static $allowed_children = array('*GridFieldPage');
    private static $default_child = "GridFieldPage";

    private static $add_default_gridfield = true;
    private static $apply_sortable = true;
    private static $subpage_tab = "Root.Subpages";
    private static $gridfield_title = "Manage Subpages";

    public static function setAddDefaultGridField($val)
    {
        Deprecation::notice('1.0', 'setAddDefaultGridField is deprecated please use Config instead');
        self::config()->update('add_default_gridfield', (bool) $val);
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $PageTab = Config::inst()->get($this->owner->className, 'subpage_tab');

        // GridFieldPage
        if (Config::inst()->get($this->owner->className, 'add_default_gridfield')) {
            $gridFieldConfig = GridFieldConfig::create()->addComponents(
                new GridFieldToolbarHeader(),
                new GridFieldAddNewSiteTreeItemButton('toolbar-header-right'),
                new GridFieldSortableHeader(),
                new GridFieldFilterHeader(),
                $dataColumns = new GridFieldDataColumns(),
                new GridFieldPaginator(20),
                new GridFieldEditSiteTreeItemButton()
            );
            // Orderable is optional, as often pages may be sorted by other means
            if (Config::inst()->get($this->owner->className, 'apply_sortable')) {
                // OrderableRows will auto-deactivate when users Sort via SortableHeader
                $gridFieldConfig->addComponent(new GridFieldOrderableRows());
                $fields->addFieldToTab($PageTab, new LiteralField('SortWarning',
                        "<p class=\"message warning\" style=\"display: inline-block;\">"
                        . _t("GridFieldPages.PUBLISHAFTERSORTWARNING",
                    "After reordering, the new sort order will get active after one of the pages gets (re)published")
                        . "</p>"));
            }
            $dataColumns->setDisplayFields(array(
                'Title' => 'Title',
                'URLSegment'=> 'URL',
                'getStatus' => 'Status',
                //'LastEdited' => 'Changed',
            ));

            // include both live and stage versions of pages
            // use gridfield as normal
            $gridField = new GridField("Subpages",
                    Config::inst()->get($this->owner->className, 'gridfield_title'),
                    //DataObject::get($this->defaultChild(), 'ParentID = '.$this->ID),
                    //SiteTree::get()->filter('ParentID', $this->ID),
                    $this->AllChildrenIncludingDeleted(),
                    $gridFieldConfig);

            $gridField->setModelClass($this->defaultChild());

            $fields->addFieldToTab($PageTab, $gridField);
        }

        return $fields;
    }

    // @TODO: A page still needs to be published for the sortorder to be updated,
    // we need some kind of warning/info to inform CMS users about this
//	public function SortedChildren(){
//		//return DataObject::get($this->defaultChild(), 'ParentID = '.$this->ID);
//		return SiteTree::get()->filter('ParentID', $this->ID)->sort('Sort');
//		//$pagetype = $this->defaultChild();
//		//return $pagetype::get()->filter('ParentID', $this->ID);
//	}

//	public function onBeforeGridFieldReorder($grid){
////		$message = sprintf(
////			'Published %s %s',
////			$this->owner->record->i18n_singular_name(),
////			$title
////		);
//        //$grid->Form->sessionMessage('TESTMESSAGE', 'good');
//	}

//	public function MostRecent($amount = 3){
//		return BlogGridPage::get()->filter('ParentID', $this->ID)->sort('Date DESC')->limit($amount);
//	}
}

class GridFieldPageHolder_Controller extends Page_Controller
{
}
