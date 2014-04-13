<?php
interface MAdmin_Views_EditDataProvider
{
    public function getInfo($identityInfo);

    public function submit($inputInfo, $identityInfo);

    public function delete($identityInfo);
}
