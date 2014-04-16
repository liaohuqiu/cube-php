<?php
class MApps_Admin_Sample_ItemEdit extends MApps_AdminPageBase implements MAdmin_Views_ItemActionDataProvider
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
                'title' => 'Locked input',
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
                'desc' => 'Checkbox description',
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

        $this->controller = new MAdmin_Views_ItemActionEasyController($conf, $this);
        $this->controller->dispatch();
    }

    public function getInfo($identityInfo)
    {
        // fetch info form database and format the data
    }

    public function submit($inputInfo, $identityInfo)
    {
        // update info
    }

    public function delete($identityInfo)
    {
        // delete info
    }

    protected function outputBody()
    {
        $this->controller->output();
    }
}
