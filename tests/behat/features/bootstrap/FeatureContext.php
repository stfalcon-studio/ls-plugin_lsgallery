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
class FeatureContext extends MinkContext
{
    protected $pluginName = 'lsgallery';

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->useContext('base', new BaseFeatureContext($parameters));
    }

    public function getEngine() {
        return $this->getSubcontext('base')->getEngine();
    }

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

    /**
     * @Then /^I set carma "([^"]*)" to user "([^"]*)"$/
     */
    public function iSetCarmaToUser($carmaPoints, $userName)
    {
        $oUser = $this->getEngine()->User_GetUserByLogin($userName);
        if (!$oUser) {
            throw new ExpectationException('User non exists', $this->getSession());
        }

        $oUser->setRating((int)$carmaPoints);
        $this->getEngine()->User_Update($oUser);
    }

    /**
     * @Then /^I am going to page "([^"]*)"$/
     */
    public function iAmGoingToPage($pageUrl)
    {
        $activePlugins = $this->getEngine()->Plugin_GetActivePlugins();

        $prefix = '';
        if (in_array('l10n', $activePlugins)) {
            $prefix = '/en';
        }

        $this->getSession()->visit($this->locatePath($prefix . $pageUrl));
    }
}