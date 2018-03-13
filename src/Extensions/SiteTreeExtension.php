<?php

namespace jzubero\SiteTreeLabels\Extensions;

use jzubero\SiteTreeLabels\Models\Label;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;


class SiteTreeExtension extends DataExtension {

    private static $show_labels = true;

    private static $many_many = [
        'Labels' => Label::class
    ];

    public function updateSettingsFields(FieldList $fields) {
        $fields->addFieldToTab('Root.Settings',
            GridField::create('Labels', _t('SiteTreeLabel.LABELS', 'Label'), $this->owner->Labels(),
            GridFieldConfig_RecordEditor::create()));
    }

    /**
     * Fetches all assigned site tree labels.
     *
     * @return ArrayList
     */
    public function SiteTreeLabels() {
        $labels = ArrayList::create();

        // Return empty ArrayList if labels are deactivated
        if (!$this->doShowLabels())
            return $labels;

        // Add Menu Labels if available and activated
        if ($this->doShowMenuLabels())
            foreach (Heyday\MenuManager\MenuItem::get()->filter('PageID', $this->owner->ID) as $menuItem) {
                $labels->add([
                    'Title' => $menuItem->MenuSet()->Name,
                    'Color' => singleton(Label::class)->getDefaultColor()
                ]);
            }

        // Add labels with call be reference hook
        $this->owner->extend('updateSiteTreeLabels', $labels);

        // Add page's labels
        $labels->merge($this->owner->Labels()->toArray());

        return $labels;
    }

    /**
     * Checks if labels are activated.
     *
     * @return bool
     */
    private function doShowLabels() {
        return SiteTree::config()->get('show_labels') === true;
    }

    /**
     * Checks for HeyDay's Menu Manager Module and the flag for activating it.
     *
     * @return bool
     */
    private function doShowMenuLabels() {
        return class_exists('Heyday\MenuManager\MenuItem') && class_exists('Heyday\MenuManager\MenuSet') &&
            Label::config()->get('show_menu_labels');
    }
}
