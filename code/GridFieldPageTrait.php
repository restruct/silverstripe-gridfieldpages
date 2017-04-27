<?php
/**
 * Contains core functionality required to implement grid field capability for Page objects. Implement this by either
 * extending the GridFieldPage class or applying the GridFieldPageExtension to your page via SilverStripe API:
 * https://docs.silverstripe.org/en/3/developer_guides/extending/extensions/
 *
 * @author   Michael van Schaik, mic@restruct.nl
 * @author   Patrick Nelson, pat@catchyour.com
 * @since    2017-04-26
 */

trait GridFieldPageTrait
{

    /**
     * Page instance will vary depending on context (i.e. is this trait on page or page extension?)
     *
     * @return GridFieldPage
     */
    private function getGridFieldPage()
    {
        if ($this instanceof GridFieldPageExtension) {
            return $this->getOwner();

        } elseif ($this instanceof GridFieldPage) {
            return $this;

        } else {
            // Shouldn't happen but in case someone silly incorporates this trait in the wrong context ;)
            return GridFieldPage::create();
        }
    }

    /**
     * add an arrow-overlay to this page's icon when open in the CMS
     */
    public function getTreeTitle()
    {
        return str_replace(
            'jstree-pageicon',
            'jstree-pageicon gridfieldpage-overlay',
            $this->getGridFieldPage()->getTreeTitle());
    }

    /*
	 * Display status in the CMS grid
	 */
    public function getStatus($cached = true)
    {

        $status = null;
        $statusflag = null;
        $page = $this->getGridFieldPage();


        if ($page->hasMethod("isPublished")) {

            $published = $page->isPublished();

            if ($published) {
                $status = _t(
                    "GridFieldPage.StatusPublished",
                    '<i class="btn-icon btn-icon-accept"></i> Published on {date}',
                    "State for when a post is published.",
                    array(
                        "date" => $page->dbObject("LastEdited")->Nice()
                    )
                );
                //$status = 'Published';

                // Special case where sortorder changed
                $liveRecord = Versioned::get_by_stage(get_class($page), 'Live')->byID($page->ID);
                //return $page->Sort . ' - ' . $liveRecord->Sort;
                if ($liveRecord->Sort && $liveRecord->Sort != $page->Sort) {
                    // override published status
                    $status = _t(
                        "GridFieldPage.StatusDraftReordered",
                        '<i class="btn-icon btn-icon-arrow-circle-double"></i> Draft modified (reordered)',
                        "State for when a page has been reordered."
                    );
                    //$status = 'Draft modified (reordered)';
                }

                // Special case where deleted from draft
                if ($page->IsDeletedFromStage) {
                    // override published status
                    $statusflag = "<span class='modified'>"
                        . _t("GridFieldPage.StatusDraftDeleted", "draft deleted") . "</span>";
                    //$status = 'Draft deleted';
                }

                // If modified on stage, add
                if ($page->IsModifiedOnStage) {
                    // add to published status
                    $statusflag = "<span class='modified'>"
                        . _t("GridFieldPage.StatusModified", "draft modified") . "</span>";
                    //$status = 'Draft modified';
                }

                // If same on stage...
                if ($page->IsSameOnStage) {
                    // leave as is
                }

            } else {
                if ($page->IsAddedToStage) {
                    $status = _t(
                        "GridFieldPage.StatusDraft",
                        '<i class="btn-icon btn-icon-pencil"></i> Saved as Draft on {date}',
                        "State for when a post is saved but not published.",
                        array(
                            "date" => $page->dbObject("LastEdited")->Nice()
                        )
                    );
                    //$status = 'Draft';
                }

            }

        }

        // allow for extensions
        $page->extend('updateStatus', $status, $statusflag);
        return DBField::create_field('HTMLVarchar', $status . $statusflag);
    }

}
