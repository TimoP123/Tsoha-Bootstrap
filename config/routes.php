<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });
  
  $routes->get('/resepti', function() {
    ReseptiController::index();
  });
  
  $routes->post('/resepti', function(){
    ReseptiController::store();
  });
  
  $routes->get('/resepti/new', function() {
    ReseptiController::create();
  });
  
  $routes->get('/resepti/:id', function($id){
	ReseptiController::show($id);
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  
  // Suunnitelmasivut
  
  $routes->get('/login', function() {
    HelloWorldController::login();
  });
  
  $routes->get('/reseptit', function() {
    HelloWorldController::reseptit();
  });
  
  $routes->get('/reseptiesimerkki', function() {
    HelloWorldController::reseptiesimerkki();
  });
  
  $routes->get('/muokkaa', function() {
    HelloWorldController::muokkaa();
  });
  
  $routes->get('/etusivu', function() {
    HelloWorldController::etusivu();
  });
