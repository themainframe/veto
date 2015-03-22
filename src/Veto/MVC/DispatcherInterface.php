<?php
/**
 * Veto.
 * PHP Microframework.
 *
 * @author Damien Walsh <me@damow.net>
 * @copyright Damien Walsh 2013-2014
 * @version 0.1
 * @package veto
 */
namespace Veto\MVC;

use Veto\HTTP\Request;
use Veto\HTTP\Response;

/**
 * Classes that can take a Request instance and dispatch it to some external code, obtaining a response.
 */
interface DispatcherInterface
{
    /**
     * @param Request $request
     * @return Response
     */
    public function dispatch(Request $request);
}
