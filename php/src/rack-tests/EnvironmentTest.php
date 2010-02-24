<?php

class EnvironmentTest extends PhpRack_Test
{

    public function testSvnExists()
    {
    }

    public function testPhpIsConfigured()
    {
        $this->assert->php->version->atLeast('5.2');
        $this->assert->php->extensions
            ->isLoaded('fileinfo')
            ->isLoaded('simplexml')
            ->isLoaded('pdo')
            ->isLoaded('json')
            ->isLoaded('pdo_mysql')
            ->isLoaded('xsl');

        $this->assert->php->extensions->fileinfo->isAlive();
    }

}