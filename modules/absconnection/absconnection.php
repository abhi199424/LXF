<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Absconnection extends Module
{
    public function __construct()
    {
        $this->name = 'absconnection';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Your Name';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('ABS Connection');
        $this->description = $this->l('A simple module with a front controller and template.');
    }

    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }
}
