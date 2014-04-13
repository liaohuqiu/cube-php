<?php
include dirname(dirname(dirname(__FILE__))) . '/init.php';
class App extends MAdmin_AdminPageBase implements MAdmin_Views_EditDataProvider
{
    private $controller;

    protected function main()
    {
        $editInfo =  array (
            'k_text_input' => array(
                'title' => 'Text input',
                'placeholder' => 'text input',
                'type' => 'text',
            ),
            'k_lock_input' => array(
                'title' => 'Lock input',
                'lock' => 1,
                'value' => 'value can not edit',
            ),
            'k_password' => array(
                'title'=>'Password',
                'type' => 'password',
            ),
            'k_textarea' => array(
                'title'=>'Textarea',
                'type' => 'textarea',
                'size' => '400x50',
            ),
            'k_check' => array(
                'title'=> 'Checkbox',
                'type' => 'checkbox',
                'check_desc' => 'Checkbox description',
                'value' => 1, // value greater then 0 will be checked
            ),
            'k_select' => array(
                'title'=> 'Select',
                'type' => 'select',
                'options' => array(
                    1 => 'Option 1',
                    2 => 'Option 2',
                ),
            ),
            'k_ratio' => array(
                'title'=> 'Radio',
                'type' => 'radio',
                'options' => array(
                    1 => 'Option 1',
                    2 => 'Option 2',
                ),
            ),
        );

        $conf = array();
        $conf['edit_info'] = $editInfo;
        $conf['identity_keys'] = array();
        $conf['post_url'] = '';

        $this->controller = new MAdmin_Views_EditController($conf);
        $this->controller->setDataProvider($this);
        $this->controller->render();
    }

    public function getInfo($identityInfo)
    {
    }

    public function submit($inputInfo, $identityInfo)
    {
    }

    public function delete($identityInfo)
    {
    }

    protected function outputBody()
    {
        $this->controller->output();
    }
}
$app = new App();
$app->run();
