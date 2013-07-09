<?php

namespace Chash\Command\Files;

use Chash\Command\Database\CommonChamiloDatabaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clean the archives directory, leaving only index.html, twig and Serializer
 * @return bool True on success, false on error
 */

class CleanTempFolderCommand extends CommonChamiloDatabaseCommand
{
    /**
     *
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('files:clean_temp_folder')
            ->setDescription('Cleans the temp directory');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool|int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $output->writeln('<comment>Starting Clean temp folders </comment>');

        $dialog = $this->getHelperSet()->get('dialog');

        if (!$dialog->askConfirmation(
            $output,
            '<question>Are you sure you want to clean the Chamilo temp files? (y/N)</question>',
            false
        )
        ) {
            return;
        }

        $_configuration = $this->getConfigurationHelper()->getConfiguration();
        

        /*if (empty($_configuration['root_sys'])) {
            $output->writeln(
                '$_configuration[\'root_sys\'] is empty. In these conditions, it is too dangerous to proceed with the deletion.'
            );
            $output->writeln('Please ensure this variable is defined in the configuration.php file');
            return 0;
        }*/

        $dir   = $_configuration['root_sys'].'temp';
        $files = scandir($dir);
        if (!empty($files)) {
            foreach ($files as $file) {
                if (substr($file, 0, 1) == '.') {
                    //ignore
                } elseif ($file == 'twig') {
                    $err = @system('rm -rf '.$dir.'/twig/*');
                } elseif ($file == 'Serializer') {
                    $err = @system('rm -rf '.$dir.'/Serializer/*');
                } else {
                    $err = @system('rm -rf '.$dir.'/'.$file);
                }
            }
        }
        $output->writeln('<info>Files were cleaned</info>');
    }

    public function detectTempFolders()
    {
    }
}
