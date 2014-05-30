<?php
class MCore_Web_RequestDispatcher
{
    public function dispatch($request_info)
    {
        $class_name = $request_info['class_name'];
        if (!class_exists($class_name, true))
        {
            header('Status: 404 Not Found');
            echo '<h1>404 Not Found</h1>';
            if (!MCore_Tool_Env::isProd())
            {
                echo '<pre>';
                var_export($request_info);
            }
            return;
        }
        $app = new $class_name;
        $app->processRequest(MCore_Web_Request::create($request_info));
    }
}
