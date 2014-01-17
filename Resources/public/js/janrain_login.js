scripts = document.getElementsByTagName( 'script' );
thisScriptTag = scripts[ scripts.length - 1 ];

(function ($, thisScriptTag) {
  var $script = $(thisScriptTag);
  try {
  if ($script.attr('application_name') === undefined) console.error('Janrain loader script must have application_id parameter');
  } catch(err) { return false;}

  if (typeof window.janrain !== 'object') window.janrain = {};
  if (typeof window.janrain.settings !== 'object') window.janrain.settings = {};
  
  janrain.settings.tokenUrl = "%protocol%//%host%/%url%"
    .replace('%protocol%', location.protocol)
    .replace('%host%', location.host)
    .replace('%url%', Routing.generate('janrain.check').replace(/^\//, ''));

  function isReady() {
    janrain.ready = true;
  };

  if (document.addEventListener) {
    document.addEventListener("DOMContentLoaded", isReady, false);
  } else {
    window.attachEvent('onload', isReady);
  }

  var e = document.createElement('script');
  e.type = 'text/javascript';
  e.id = 'janrainAuthWidget';

  if (document.location.protocol === 'https:') {
    e.src = 'https://rpxnow.com/js/lib/' + $script.attr('application_name') + '/engage.js';
  } else {
    e.src = 'http://widget-cdn.rpxnow.com/js/lib/' + $script.attr('application_name') + '/engage.js';
  }

  var s = document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(e, s);
})(jQuery, thisScriptTag);
