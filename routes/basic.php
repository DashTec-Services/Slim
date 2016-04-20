<?php


$app->get('/', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {



    /**
     *  Site render engine
     */
    $this->renderer->render($response, 'test.phtml', $args);

})->setName('basic');

