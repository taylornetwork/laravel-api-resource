<?php


namespace TaylorNetwork\LaravelApiResource\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Response;

abstract class ApiController extends Controller
{
    public function ok(): Response
    {
        return new Response('OK', 200);
    }

    public function notImplemented(): Response
    {
        return new Response('Not Implemented', 501);
    }
}