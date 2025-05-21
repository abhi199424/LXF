<?php

class AbsconnectionDisplayModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (Tools::isSubmit('submit_email')) {
            $email = Tools::getValue('email');

            if (!Validate::isEmail($email)) {
                $this->errors[] = $this->module->l('Invalid email address.');
                return;
            }

            if (Customer::customerExists($email)) {
                $targetUrl = $this->context->link->getPageLink('authentication', true, null, ['back' => 'my-account']);
                echo '
                    <form id="authRedirectForm" method="POST" action="' . $targetUrl . '">
                        <input type="hidden" name="email" value="' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '">
                    </form>
                    <script>document.getElementById("authRedirectForm").submit();</script>
                ';
                exit;
            } else {
                $targetUrl = 'https://lxfstore.fr/inscription';

                echo '
                    <form id="emailRedirectForm" method="POST" action="' . $targetUrl . '">
                        <input type="hidden" name="email" value="' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '">
                    </form>
                    <script>document.getElementById("emailRedirectForm").submit();</script>
                ';
                exit;
            }
        }
    }

    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('module:absconnection/views/templates/front/display.tpl');
    }
}
