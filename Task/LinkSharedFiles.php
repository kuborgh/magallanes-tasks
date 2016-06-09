<?php
namespace Task;

use Mage\Task\BuiltIn\Filesystem\LinkSharedFilesTask;

/**
 * Create shared file/folder when not exists before
 */
class LinkSharedFiles extends LinkSharedFilesTask
{
    /**
     * Printed output
     *
     * @return string
     */
    public function getName()
    {
        return sprintf('Linking shared files/folders');
    }

    /**
     * Perform action
     *
     * @return bool
     */
    public function run()
    {
        $sharedFolder = rtrim($this->getConfig()->deployment('to'), '/').'/shared/';
        $currentUser = $this->getConfig()->deployment('remote_user', 'user');
        $apacheUser = $this->getParameter('apache_user', 'apache');
        $releasesDirectoryPath = $this->getConfig()->release('directory', 'releases');
        $currentCopy = $releasesDirectoryPath.'/'.$this->getConfig()->getReleaseId();

        // Touch files
        foreach ($this->getParameter(self::LINKED_FILES, array()) as $file) {
            $filePath = $sharedFolder.$file;
            $commands = array();
            $commands[] = sprintf('touch %s', $filePath);
            $commands[] = sprintf('setfacl -m u:%s:rwX -m u:%s:rwX %s', $currentUser, $apacheUser, $filePath);

            $this->runCommand(implode(';', $commands));
        }

        // Create shared folders
        foreach ($this->getParameter(self::LINKED_FOLDERS, array()) as $folder) {
            $folderPath = $sharedFolder.$folder;
            $commands = array();
            $commands[] = sprintf('mkdir -p %s', $folderPath);
            $commands[] = sprintf('setfacl -m u:%s:rwX -m u:%s:rwX %s', $currentUser, $apacheUser, $folderPath);
            $commands[] = sprintf('setfacl -d -m u:%s:rwX -m u:%s:rwX %s', $currentUser, $apacheUser, $folderPath);
            $commands[] = sprintf('mkdir -p %s/%s', $currentCopy, dirname($folder));

            $this->runCommand(implode(';', $commands));
        }

        // Create local folders

        return parent::run();
    }
}
