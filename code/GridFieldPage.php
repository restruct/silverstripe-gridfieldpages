<?php

require_once(__DIR__ . '/GridFieldPageTrait.php');

/**
 * Extend this object to implement grid field capability for Page objects. Optionally, you can apply the
 * GridFieldPageExtension instead.
 *
 * @author   Michael van Schaik, mic@restruct.nl
 * @since    2017-04-26
 */

class GridFieldPage extends Page
{

    private static $can_be_root = false;
    private static $allowed_children = "none";

    private static $defaults = array(
        'ShowInMenus' => false,
    );

    private static $searchable_fields = array(
        'Title', 'MenuTitle'
    );

    private static $summary_fields = array(
        "Title", 'MenuTitle'
    );

    use GridFieldPageTrait;

}

class GridFieldPage_Controller extends Page_Controller
{

}
