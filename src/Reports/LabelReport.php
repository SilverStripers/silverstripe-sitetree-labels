<?php

namespace jzubero\SiteTreeLabels\Reports;

use jzubero\SiteTreeLabels\Models\Label;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Reports\Report;

class LabelReport extends Report {

    public function title() {
        return _t('SiteTreeLabel.REPORT_TITLE', 'Pages with Site Tree Labels assigned');
    }

    /**
     * Shows linked pages if a certain label is specified via dropdown.
     *
     * TODO: Include implicit MenuManager Labels
     *
     * @param null $params
     *
     * @return SS_List
     */
    public function sourceRecords($params = null) {
        if (!isset($params['Label']) ||
            !($labelId = $params['Label']))
            return ArrayList::create();

        return Label::get()->byID($labelId)->Pages();
    }

    public function columns() {
        return [
            'Title' => [
                'title' => 'Title',
                'link'  => true
            ]
        ];
    }

    public function parameterFields() {
        return new FieldList(
            new DropdownField('Label', _t('SiteTreeLabel.SINGULARNAME'), Label::get()->sort('Title')->map())
        );
    }
}