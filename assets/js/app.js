/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');


require('../js/jquery.marquee.js');
require('../js/jquery.easing.js');
require('../js/jquery.pause.js');

import tippy from 'tippy.js'

var $ = require('jquery');

function _init_gw2_items() {
  var $items = $(document).find('[data-item-id]');

  if($items.length > 0) {
    var ids = new Array();

    $items.each(function(key, item) {
      var id = $(item).data('item-id');

      if(ids.indexOf(id) == -1){
        ids.push(id);
      }

    });

    if(ids) {
      ids = ids.join(',');

      $.get('https://api.guildwars2.com/v2/items', {'ids': ids}, function(res) {
        $.each(res, function(k, item) {
          $('[data-item-id="'+item.id+'"]').addClass('item rarity-'+item.rarity).text('['+item.name+']');
        });
      });
    }

  }

}

function _init_gw2_upgrades() {
  var $upgrades = $(document).find('[data-upgrade-id]');

  if($upgrades.length > 0) {
    var ids = new Array();

    $upgrades.each(function(key, upgrade) {
      var id = $(upgrade).data('upgrade-id');

      if(ids.indexOf(id) == -1){
        ids.push(id);
      }

    });

    if(ids) {
      ids = ids.join(',');

      $.get('https://api.guildwars2.com/v2/guild/upgrades', {'ids': ids}, function(res) {
        $.each(res, function(k, upgrade) {
          $('[data-upgrade-id="'+upgrade.id+'"]').addClass('upgrade').text('['+upgrade.name+']');
        });
      });
    }

  }

}

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

  $('.marquee').marquee();

  $('.flash-message').on('click', '.flash-close', function(e) {
    e.preventDefault();
    $(this).parent().parent().remove();
  });

  _init_gw2_items();
  _init_gw2_upgrades();

});
