/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');


var $ = require('jquery');

$(document).ready(function() {
  $('button#buttonCheckApiKey').on('click', function() {
    var $token = $('input#inputToken');

    if(typeof $token.length === 'undefined') {
      return;
    }

    if($token.val().length == 0) {
      $token.focus();
      return;
    }

    $.get('/guilds/ajax/get', {token: $token.val()}, function(res) {

      console.log(res);

      var $select = $('select#selectGid');
      $select.html('');

      $.each(res.guilds, function(k, v) {
        $select.append('<option value="'+k+'">'+v+'</option>');
      });

    }).fail(function(res) {
      console.log(res);
    });
  });
});
