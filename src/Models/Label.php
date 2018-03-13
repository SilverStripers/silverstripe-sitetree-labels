<?php

namespace jzubero\SiteTreeLabels\Models;


use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\ORM\DataObject;


class Label extends DataObject {

    private static $show_menu_labels = true;

    private static $label_color = '#426ef4';

    private static $db = [
        'Title' => 'Varchar',
        'Color' => 'Varchar(7)'
    ];

    private static $belongs_many_many = [
        'Pages' => SiteTree::class
    ];

    private static $table_name = 'SiteTreeLabel';

    public function getCMSFields() {
        $f = parent::getCMSFields();
        $f->addFieldToTab('Root.Main', ColorField::create('Color', $this->fieldLabel('Color'), $this->Color ?: $this->getDefaultColor()));

        return $f;
    }

    /**
     * @return ValidationResult
     */
    public function validate() {
        $validator = parent::validate();

        // Forbid duplicate titles
        if (!$this->isInDB() &&
            Label::get()->filter('Title', $this->Title)->exists())
            $validator->addError(_t('SiteTreeLabel.ERROR_TITLE_EXISTS', "A label \"{title}\" exists already. Consider linking the existing one.", '', array('title' => $this->Title)));

        return $validator;
    }

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);
        $labels['Title'] = _t('SiteTreeLabel.TITLE', 'Title');
        $labels['Color'] = _t('SiteTreeLabel.COLOR', 'Color');
        return $labels;
    }

    public function getDefaultColor() {
        return self::config()->get('label_color');
    }
}
