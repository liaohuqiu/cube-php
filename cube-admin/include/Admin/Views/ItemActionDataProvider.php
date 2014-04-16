<?php
interface MAdmin_Views_ItemActionDataProvider
{
    public function getInfo($identityInfo);

    public function submit($inputInfo, $identityInfo);

    public function delete($identityInfo);
}
