<?php

namespace Kpf;

use Kpf\Exception\OutputException;

class Output extends Singleton
{
    /**
     * @param string $tpl
     * @param array $properties
     * @throws Exception\ConfigException
     * @throws Exception\DirectoryException
     * @throws Exception\Exception
     * @throws OutputException
     */
    protected function display(string $tpl = Constant::NONE, array $properties = array())
    {
        $templateExtension = Constant::DOT . (Application::getConfig()->common("templateExtension") ? Application::getConfig()->common("templateExtension") : Constant::TEMPLATE_EXTENSION);
        $templateFile = Application::getDirectory()->get(Constant::DIR_VIEW) . $tpl . $templateExtension;
        if (is_file($templateFile)) {
            Application::getTemplate()->display($tpl . $templateExtension, $properties);
        } else {
            throw new OutputException("Not Found File. {$templateFile}", 404);
        }
    }
}