<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\MinkExtension\Context\MinkContext,
    Behat\Mink\Exception\ExpectationException,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

$sDirRoot = dirname(realpath((dirname(__FILE__)) . "/../../../../../"));
set_include_path(get_include_path().PATH_SEPARATOR.$sDirRoot);

require_once("tests/behat/features/bootstrap/BaseFeatureContext.php");

/**
 * LiveStreet custom feature context
 */
class FeatureContext extends BaseFeatureContext
{
    protected $pluginName = 'lsgallery';

    /**
     * @Given /^I press button "([^"]*)"$/
     */
    public function IPressButton($path)
    {

        $element = $this->getSession()->getPage()->find('css', 'input[name="' . $path . '"]');
        if ($element) {
            $element->click();
        }
        else {
            throw new ExpectationException('Button not found', $this->getSession());
        }
    }

}

