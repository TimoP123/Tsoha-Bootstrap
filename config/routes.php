<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
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
