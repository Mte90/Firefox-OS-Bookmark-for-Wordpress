function load_manifest() {
  //Check with cookie if the alert was showed for not annoying the user
  if (document.cookie.replace(/(?:(?:^|.*;\s*)appTime\s*\=\s*([^;]*).*$)|^.*$/, "$1") !== "false") {
    new Modal(document.body).open();
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

// Click event handler
  var handleClickEvent = function(evt) {
// Only external links allowed
// Add target when no named target given
    var target = evt.target.getAttribute('target');
    if (!target || target.substr(0, 1) === '_') {
      evt.target.setAttribute('target', '_blank');
    }
  };
// Delegate all clicks on document body
// Selector matches external links, but allows https/http switching
  var _link = document.querySelectorAll("a[href^='http']:not([href*='://" + location.host + "']):not([target='_blank'])");
  for (var _i = 0; _i < _link.length; _i++) {
    _link[_i].addEventListener('click', handleClickEvent, false);
  }
});

//Mini Modal based on http://jsfiddle.net/i_like_robots/W2DA8/ modified for the plugin
(function(name, context, definition) {
  if (typeof define === 'function' && define.amd) {
    define(definition);
  }
  else if (typeof module !== 'undefined' && module.exports) {
    module.exports = definition();
  }
  else {
    context[name] = definition();
  }
})('Modal', this, function() {

  var Modal = function(element) {
    this.target = element;

    if (!this.isOpen) {
      this._init();
    }
  };

  Modal.prototype._init = function() {
    var self = this;

    this.overlay = document.createElement('div');
    this.overlay.className = 'overlay_';
    this.overlay.style.position = 'fixed';
    this.overlay.style.top = 0;
    this.overlay.style.right = 0;
    this.overlay.style.bottom = 0;
    this.overlay.style.left = 0;
    this.overlay.style.zIndex = '99999';
    this.overlay.style.background = 'rgba(0, 0, 0, .5)';
    this.overlay.setAttribute('tabindex', -1);

    this.modalWindow = document.createElement('div');
    this.modalWindow.className = 'modal';
    this.modalWindow.style.position = 'fixed';
    this.modalWindow.style.top = 0;
    this.modalWindow.style.right = 0;
    this.modalWindow.style.bottom = 0;
    this.modalWindow.style.left = 0;
    this.modalWindow.style.width = '80%';
    this.modalWindow.style.height = '30%';
    this.modalWindow.style.margin = 'auto';
    this.modalWindow.style.background = '#EEE';
    this.modalWindow.style.zIndex = '99999';
    this.modalWindow.setAttribute('role', 'dialog');
    this.modalWindow.setAttribute('tabindex', 0);

    this.modalWrapper = document.createElement('div');
    this.modalWrapper.className = 'modal__wrapper';
    this.modalWrapper.style.overflow = 'auto';
    this.modalWrapper.style.height = '100%';

    this.modalContent = document.createElement('div');
    this.modalContent.className = 'modal__content';
    this.modalContent.style.padding = '1em';
    this.modalContent.style.textAlign = 'center';

    this.closeButton = document.createElement('button');
    this.closeButton.className = 'modal__close';
    this.closeButton.style.left = '10px';
    this.closeButton.style.top = '-45px';
    this.closeButton.style.position = 'relative';
    this.closeButton.innerHTML = ffos_bookmark.close;
    this.closeButton.setAttribute('type', 'button');

    this.closeButton.onclick = function() {
      var now = new Date;
      now.setDate(now.getDate() + 30);
      document.cookie = 'appTime=false; expires=' + now.toGMTString();
      self.close();
    };

    this.installButton = document.createElement('button');
    this.installButton.className = 'modal__install';
    this.installButton.style.left = '10px';
    this.installButton.style.top = '-45px';
    this.installButton.style.marginRight = '10px';
    this.installButton.style.position = 'relative';
    this.installButton.innerHTML = 'OK';
    this.installButton.setAttribute('type', 'button');

    this.installButton.onclick = function() {
      var checkIfInstalled = navigator.mozApps.getSelf();
      checkIfInstalled.onsuccess = function() {
        if (!checkIfInstalled.result) {
          var now = new Date;
          var m_app = navigator.mozApps.install(ffos_bookmark.host + '/manifest.webapp');
          m_app.onsuccess = function(data) {
            now.setDate(now.getDate() + 365);
            document.cookie = 'appTime=false; expires=' + now.toGMTString();
            self.close();
          };
          m_app.onerror = function() {
            now.setDate(now.getDate() + 30);
            console.log("Install failed\n\n:" + m_app.error.name);
            document.cookie = 'appTime=false; expires=' + now.toGMTString();
          };
        }
      };
    };

    this.modalWindow.appendChild(this.modalWrapper);
    this.modalWrapper.appendChild(this.modalContent);
    this.modalWindow.appendChild(this.installButton);
    this.modalWindow.appendChild(this.closeButton);

    this.isOpen = false;
  };

  Modal.prototype.open = function(callback) {
    if (this.isOpen) {
      return;
    }

    this.modalContent.innerHTML = ffos_bookmark.content;

    this.target.appendChild(this.overlay);
    this.target.appendChild(this.modalWindow);
    this.modalWindow.focus();

    this.isOpen = true;

    if (callback) {
      callback.call(this);
    }
  };

  Modal.prototype.close = function(callback) {
    this.target.removeChild(this.modalWindow);
    this.target.removeChild(this.overlay);
    this.isOpen = false;

    if (callback) {
      callback.call(this);
    }
  };

  Modal.prototype.teardown = function() {
    if (this.isOpen) {
      this.close();
    }

    delete this.installButton;
    delete this.closeButton;
    delete this.modalContent;
    delete this.modalWrapper;
    delete this.modalWindow;
    delete this.overlay;
    delete this.isOpen;
  };

  return Modal;

});
