<?php

class indexController extends Controller
{
    public function indexAction()
    {
        $M = new indexModel();
        $helloWorld = $M->helloWorld();
        $this->render('index', $helloWorld, 'helloWorld');
    }
}
