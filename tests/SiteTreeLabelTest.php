<?php

/**
 * Created by IntelliJ IDEA.
 * User: Werner
 * Date: 09.05.17
 */

namespace jzubero\Tests;


use jzubero\SiteTreeLabels\Models\Label;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\SapphireTest;

class SiteTreeLabelTest extends SapphireTest
{
    protected static $fixture_file = [
        'sitetree-labels.yml'
    ];

    public function testSiteTreeHasSiteTreeLabelExtensionApplied()
    {
        $siteTree = SiteTree::create();
        $hasExtensionApplied = $siteTree->hasExtension('SiteTreeLabelExtension');
        $this->assertTrue($hasExtensionApplied, 'SiteTree should have SiteTreeLabelExtension applied');
    }

    public function testValidationIsValidLabelsWithUniqueTitle()
    {
        $label = Label::create(['Title' => 'New Label (does not exist yet)']);
        /** @var ValidationResult $validationResult */
        $validationResult = $label->validate();
        $isValid = $validationResult->isValid();
        $this->assertTrue($isValid, 'A new label with unique title should be valid');
    }

    public function testValidationFailsWhenLabelWithSameTitleIsCreated()
    {
        $label = SiteTreeLabel::create(['Title' => 'Label 1']); //already exists in fixtures

        /** @var ValidationResult $validationResult */
        $validationResult = $label->validate();
        $isValid = $validationResult->isValid();
        $errorMessages = $validationResult->getMessages();

        $this->assertFalse($isValid, 'Validation should be false when the label already exists');

        $this->assertContains(
            'A label "Label 1" exists already. Consider linking the existing one.',
            $errorMessages,
            'Validation should generate an error message');
    }
}
