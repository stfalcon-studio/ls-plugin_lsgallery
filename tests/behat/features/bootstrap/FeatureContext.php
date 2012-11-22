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
    private $oEngine;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->useContext('base', new BaseFeatureContext($parameters));

        $this->oEngine = Engine::getInstance();
        $this->oEngine->Init();
    }

    public function getMinkContext()
    {
        return $this->getMainContext();
    }

    /**
     * Check is sets are present in content
     *
     * @Then /^the response have sets:$/
     */
    public function ResponseHaveSets(TableNode $table)
    {
        $actual = $this->getMainContext()->getSession()->getPage()->getContent();

        foreach ($table->getHash() as $genreHash) {
            $regex  = '/'.preg_quote($genreHash['value'], '/').'/ui';
            if (!preg_match($regex, $actual)) {
                $message = sprintf('The string "%s" was not found anywhere in the HTML response of the current page.', $genreHash['value']);
                throw new ExpectationException($message, $this->getMainContext()->getSession());
            }
        }
    }

    /**
     * Get content type and compare with set
     *
     * @Then /^content type is "([^"]*)"$/
     */
    public function contentTypeIs($contentType)
    {
        $header = $this->getMinkContext()->getSession()->getResponseHeaders();

        if ($contentType != $header['Content-Type']) {
            $message = sprintf('Current content type is "%s", but "%s" expected.', $header['Content-Type'], $contentType);
            throw new ExpectationException($message, $this->getSession());
        }
    }

    /**
     * Try to login user
     *
     * @Then /^I want to login as "([^"]*)"$/
     */
    public function iWantToLoginAs($sUserLogin)
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $moduleUser = $this->oEngine->GetModuleObject('ModuleUser');

        $oUser = $moduleUser->GetUserByLogin($sUserLogin);
        if (!$oUser) {
            throw new ExpectationException( sprintf('User %s not found', $sUserLogin), $this->getMinkContext()->getSession());
        }

        $moduleUser->User_Authorization($oUser, true);
        $sSessionKey = $moduleUser->GetSessionByUserId($oUser->getId())->getKey();

        $this->getMinkContext()->getSession()->getDriver()->setCookie("key", $sSessionKey);
    }

    /**
     * Checking for activity of plugin
     *
     * @Then /^check is plugin active "([^"]*)"$/
     */
    public function CheckIsPluginActive($sPluginName)
    {
        $this->oEngine = Engine::getInstance();
        $activePlugins = $this->oEngine->Plugin_GetActivePlugins();

        if (!in_array($sPluginName, $activePlugins)) {
            throw new ExpectationException( sprintf('Plugin %s is not active', $sPluginName), $this->getMinkContext()->getSession());
        }
    }


}