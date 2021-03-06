<?php

  $routes->get('/', function() {
    HelloWorldController::index();
  });

  $routes->get('/login', function(){
    UserController::login();
  });

  $routes->get('/logout', function(){
    UserController::logout();
  });
  
  $routes->post('/login', function(){
    UserController::handle_login();
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
  
  $routes->get('/resepti/search', function() {
    ReseptiController::search();
  });
  
  $routes->post('/resepti/search', function() {
    ReseptiController::handle_search();
  });
  
  $routes->get('/kayttaja', function() {
    UserController::index();
  });

  $routes->post('/kayttaja', function() {
    UserController::store();
  });
  
  $routes->get('/kayttaja/new', function() {
    UserController::create();
  });
  
  $routes->get('/nimet', function() {
	  ReseptiController::listJSON();
  });
  
  $routes->get('/tagit', function() {
	  TagController::listJSON();
  });
  
  $routes->get('/aineet', function() {
	  AineController::listJSON();
  });
  
  $routes->get('/resepti/:id', function($id){
	  ReseptiController::show($id);
  });

  $routes->get('/resepti/:id/edit', function($id){
    ReseptiController::edit($id);
  });

  $routes->post('/resepti/:id/edit', function($id){
    ReseptiController::update($id);
  });

  $routes->post('/resepti/:id/destroy', function($id){
    ReseptiController::destroy($id);
  });
  
  $routes->get('/kayttaja/:id', function($id){
	  UserController::show($id);
  });

  $routes->get('/kayttaja/:id/edit', function($id){
    UserController::edit($id);
  });

  $routes->post('/kayttaja/:id/edit', function($id){
    UserController::update($id);
  });

  $routes->post('/kayttaja/:id/destroy', function($id){
    UserController::destroy($id);
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });
  
  
  // Suunnitelmasivut
  
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
