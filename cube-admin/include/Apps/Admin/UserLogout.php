<?php
include dirname(dirname(__FILE__)) . '/init.php';
class App extends MAdmin_AdminPageBase
{
    protected function main()
    {
        MAdmin_UserAuth::logout();
        $this->go_to('/admin');
    }
}
$app = new App();
$app->run();
