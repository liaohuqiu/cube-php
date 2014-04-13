<?php
/**
 *  app base
 *
 * @author      huqiu
 */
abstract class MCore_Web_BaseApp
{
    /**
     * init
     */
    abstract protected function init();

    /**
     * check auth
     */
    abstract protected function checkAuth();

    /**
     * main logic
     */
    abstract protected function main();

    /**
     * output content
     */
    abstract protected function output();

    /**
     * processException
     */
    abstract protected function processException($ex);

    protected $request;

    /**
     * Process Request
     */
    public function processRequest($request)
    {
        $this->request = $request;
        try
        {
            $this->init();
            $this->checkAuth();
            $this->main();
            $this->output();
        }
        catch (Exception $ex)
        {
            $this->processException($ex);
        }
    }

    /**
     * Access request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
