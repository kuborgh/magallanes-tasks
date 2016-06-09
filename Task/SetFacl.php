<?php
namespace Task;

use Mage\Task\AbstractTask;
use Mage\Yaml\Yaml;

/**
 * Grant write permission to the web server
 */
class SetFacl extends AbstractTask
{
    /**
     * Printed output
     *
     * @return string
     */
    public function getName()
    {
        return 'set fACLs';
    }

    /**
     * Perform action
     *
     * @return bool
     */
    public function run()
    {
        $folders = $this->getParameter('folders', array());
        $currentUser = $this->getConfig()->deployment('remote_user', 'user');

        // Does not work, when nginx is installed together with apache
        //$this->runCommandRemote("ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1", $apacheUser);
        $apacheUser = 'apache';

        foreach ($folders as $folder) {
            $this->runCommandRemote(sprintf('mkdir -p %s', $folder));
            $this->runCommandRemote(sprintf('setfacl -m u:%s:rwX -m u:%s:rwX %s', $currentUser, $apacheUser, $folder));
            $this->runCommandRemote(sprintf('setfacl -dR -m u:%s:rwX -m u:%s:rwX %s', $currentUser, $apacheUser, $folder));
        }

        return true;
    }
}
