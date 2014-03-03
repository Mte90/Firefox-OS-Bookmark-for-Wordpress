function load_manifest() {
  //Check with cookie if the alert was showed for not annoying the user
  if (document.cookie.replace(/(?:(?:^|.*;\s*)appTime\s*\=\s*([^;]*).*$)|^.*$/, "$1") !== "false") {
    var checkIfInstalled = navigator.mozApps.getSelf();
    checkIfInstalled.onsuccess = function() {
      if (!checkIfInstalled.result) {
        var now = new Date;
        var m_app = navigator.mozApps.install(ffos_bookmark.host + '/manifest.webapp');
        m_app.onsuccess = function(data) {
          now.setDate(now.getDate() + 365);
          document.cookie = 'appTime=false; expires=' + now.toGMTString();
        };
        m_app.onerror = function() {
          now.setDate(now.getDate() + 30);
          console.log("Install failed\n\n:" + m_app.error.name);
          document.cookie = 'appTime=false; expires=' + now.toGMTString();
        };
      }
    };
  }
}

//Based on 
//https://github.com/digitarald/chromeless-external-links-snippet/
(function(body) {
// Only enable for chromeless window
  if (locationbar.visible) {
    if (parseInt(ffos_bookmark.ffos) === 1 && !!"mozApps" in navigator && navigator.userAgent.indexOf("Mobile") > -1) {
      load_manifest();
    }else if (parseInt(ffos_bookmark.fffa) === 1 && navigator.userAgent.indexOf('Firefox') > -1 && navigator.userAgent.indexOf("Android") > -1) {
      load_manifest();
    }else if (parseInt(ffos_bookmark.ff) === 1 && navigator.userAgent.indexOf("Firefox") > -1) {
      load_manifest();
    }
    return;
  }

// Shim matchesSelector
  var matches = body.matchesSelector || body.mozMatchesSelector;

// Seelctor matches external links, but allows https/http switching
  var selector = "a[href^='http']:not([href*='://" + location.host + "']):not([target='_blank'])";

// Click event handler
  var handleClickEvent = function(evt) {
// All the way up
    var element = evt.target;
    while (element && element !== body) {
// Only external links allowed
      if (matches.call(element, selector)) {
// Add target when no named target given
        var target = element.getAttribute('target');
        if (!target || target.substr(0, 1) === '_') {
          element.setAttribute('target', '_blank');
        }
        return;
      }
      element = element.parentNode;
    }
  };

// Delegate all clicks on document body
  body.addEventListener('click', handleClickEvent, false);
})(document.body);