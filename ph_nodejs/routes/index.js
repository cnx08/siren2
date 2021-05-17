var express = require('express');
var http = require('http');
var router = express.Router();


router.get('/', function(req, res, next) {

  res.render('index', { 
    title: 'Фотоконтроль',
    body : '<b>Hello</b>',
    div_mark : '<b>Hello</b>',
    div : '<div id = "events" class = "blok"></div>'
  });

});
router.get('/photo2', function(req, res, next) {

  res.render('index2', {
    title: 'Фотоконтроль',
    body : '<b>Hello</b>',
    div_mark : '<b>Hello</b>',
    div : '<div id = "events" class = "blok"></div>'
  });

});

module.exports = router;
