<?php
interface MAdmin_Views_OnItemAction
{
    public function onEdit($identityInfo);

    public function onDelete($identityInfo);

    public function onSubmit($identityInfo, $inputInfo);
}
