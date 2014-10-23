<?php
class MCore_Str_Html
{
    public static function options($nameValueList, $currentValue = null)
    {
        $html = '';
		foreach ($nameValueList as $name => $value)
        {
            if ($value == $currentValue)
            {
                $html .= "<option value='$value' selected = 'true'>$name</option>";
            }
            else
            {
                $html .= "<option value='$value'>$name</option>";
            }
        }
        return $html;
    }

    static function space2nbsp($str)
    {
        return str_replace("\n ", "\n&nbsp;", str_replace("  ", "&nbsp; ", $str));
    }
}
