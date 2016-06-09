<?php
namespace Task;

use Mage\Task\AbstractTask;
use Mage\Yaml\Yaml;

/**
 * Update parameters.yml to server values
 */
class SetParameters extends AbstractTask
{
    /**
     * Printed output
     *
     * @return string
     */
    public function getName()
    {
        return 'Update parameters.yml';
    }

    /**
     * Perform action
     *
     * @return bool
     */
    public function run()
    {
        // Build yml
        $newParameters = $this->getParameters();
        $currentParams = Yaml::parse('app/config/parameters.yml.dist');
        $params = array_merge($currentParams['parameters'], $newParameters);

        // Generate YML
        $yml = Yaml::dump(array('parameters' => $params));

        // Write to server
        return $this->runCommandRemote(sprintf('echo %s > app/config/parameters.yml ', escapeshellarg($yml)));
    }
}
