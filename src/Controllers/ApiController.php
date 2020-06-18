<?php


namespace TaylorNetwork\LaravelApiResource\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Response;

abstract class ApiController extends Controller
{
    public function ok()
    {
        return new Response('OK', 200);
    }
}