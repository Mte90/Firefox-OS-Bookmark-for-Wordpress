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
document.addEventListener("DOMContentLoaded", function() {
// Only enable for chromeless window
  if (locationbar.visible) {
    if (parseInt(ffos_bookmark.ffos) === 1 && !!"mozApps" in navigator && navigator.userAgent.indexOf("Mobile") > -1) {
      load_manifest();
    } else if (parseInt(ffos_bookmark.fffa) === 1 && navigator.userAgent.indexOf('Firefox') > -1 && navigator.userAgent.indexOf("Android") > -1) {
      load_manifest();
    } else if (parseInt(ffos_bookmark.ff) === 1 && navigator.userAgent.indexOf("Firefox") > -1) {
      load_manifest();
    }
    return;
  }

// Selector matches external links, but allows https/http switching
  var selector = "a[href^='http']:not([href*='://" + location.host + "']):not([target='_blank'])";

  Array.prototype.forEach.call(document.querySelectorAll(selector), function(el) {
    var target = el.getAttribute('target');
    if (!target || target.substr(0, 1) === '_') {
      el.setAttribute('target', '_blank');
    }
  });

});