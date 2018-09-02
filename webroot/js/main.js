var mediaTab = function() {
    this.activate();
};
mediaTab.prototype.activate = function() {
    var tabs = document.querySelectorAll('.tabs a');
    var self = this;
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].addEventListener('click', function(e) {
            e.preventDefault();
            self.displayTabs(this);
        })
    }
};
mediaTab.prototype.displayTabs = function(element) {
    var parent = element.parentNode;
    var container = parent.parentNode.parentNode;
    var activeTab = container.querySelector('.tabs .active');
    var contentTab = container.querySelector('.tab-content .active');
    var targetTab = container.querySelector(element.getAttribute('href'));

    if (!parent.classList.contains('active')) {
        activeTab.classList.remove('active');
        activeTab.classList.remove('in');
        parent.classList.add('active');
        parent.classList.add('in');

        contentTab.classList.remove('active');
        contentTab.classList.remove('in');
        targetTab.classList.add('active');
        targetTab.classList.add('in');
    }
};

var mediaUpload = function(url) {
    var self = this,
        $template = document.getElementById('template'),
        $gallery = document.getElementById('gallery'),
        $overlay = document.querySelector('.overlay'),
        $errorModal = document.getElementById('error-modal'),
        dragEnteredEls = [];

    new Dropzone(document.querySelector('body'), {
        url: url,
        clickable: "#browse",
        previewTemplate: false,
        dragenter: function (e) {
            dragEnteredEls.push(e.target);
            $overlay.stop().fadeIn();
        },
        drop: function (e) {
            $overlay.stop().fadeOut();
        },
        dragleave: function (e) {
            dragEnteredEls = _.without(dragEnteredEls, e.target);
            if (dragEnteredEls.length === 0) {
                $overlay.stop().fadeOut();
            }
        },
        addedfile: function(file){
            var newTemplate = $template.cloneNode(true);
            newTemplate.style.display = 'block';
            $gallery.appendChild(newTemplate);
            document.getElementById('browse').classList.remove('active');
            document.getElementById('browse').classList.remove('in');
            $gallery.classList.add('active');
            $gallery.classList.add('in');
        },
        success: function (file, data) {
            if (typeof data !== 'object') {
                data = JSON.parse(data);
            }
            if(data.error){
                var modalBody = document.getElementById('modal-body');
                modalBody.innerText = data.error.file.global;
                $errorModal.style.display = 'block';
                self.removeTemplate($gallery, 'template');
            }else{
                removeIsActive();
                self.removeTemplate($gallery, 'template');
                var div = document.createElement('div');
                div.innerHTML = data.content.trim();
                div.firstChild.classList.add('is-active');
                $gallery.appendChild(div.firstChild);
                openGalleryItemInfos();
            }
        },
        uploadprogress: function(file, percent) {
            var progress = document.getElementsByClassName('progress');
            var progressBar = document.getElementsByClassName('progress-bar');
            progress[0].style.display = 'block';
            progressBar[0].style.width = percent + '%';
        }
    });
};
mediaUpload.prototype.removeTemplate = function(selector, element){
    var templates = selector.getElementsByClassName(element);
    for(i=0; templates.length>i; i++){
        templates[i].parentNode.removeChild(templates[i]);
    }
};
mediaUpload.prototype.deleteItem = function (msg) {
    var links = document.querySelectorAll('.gallery');
    for (var i = 0; i < links.length; i++) {
        delegate(links[i], 'click', '.delete', function(e) {
            var link = this;
            e.preventDefault();
            if (confirm(msg)) {
                var xhr = mediaAjax.makeRequest({
                    method: 'GET',
                    url: link.getAttribute('href'),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            document.getElementById('gallery').removeChild(document.getElementsByClassName('is-active')[0]);
                        }
                    }
                };
            }
        })
    }
};

function removeIsActive() {
    var activeItems = document.getElementsByClassName('is-active');
    if (activeItems.length > 0) {
        activeItems[0].classList.remove('is-active');
    }
}
function openGalleryItemInfos() {
    var itemInfos = document.getElementsByClassName('gallery-item-infos');
    for (var i = 0; i < itemInfos.length; i++) {
        if (itemInfos[i].style.display === 'block') {
            itemInfos[i].style.display = 'none';
        }
    }
    document.getElementsByClassName('is-active')[0].getElementsByClassName('gallery-item-infos')[0].style.display = 'block';
}

delegate(document, 'click', 'body',  function() {
   var modal = document.getElementById('error-modal');
   if (modal.style.display === 'block') {
       modal.style.display = 'none';
   }
});

var mediaGallery = document.getElementsByClassName('gallery');
for (var i =0; i < mediaGallery.length; i++) {
    delegate(mediaGallery[i], 'click', '.gallery-item-thumb', function(e) {
        e.preventDefault();
        removeIsActive();
        this.parentNode.classList.add('is-active');
        openGalleryItemInfos();
    });
}

var mediaAjax = {
    getHttpRequest: function() {
        "use strict";
        var httpRequest = false;
        if (window.XMLHttpRequest) {
            httpRequest = new XMLHttpRequest();
            if (httpRequest.overrideMimeType) {
                httpRequest.overrideMimeType('text/xml');
            }
        }
        else if (window.ActiveXObject) {
            try {
                httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e) {
                try {
                    httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e) {}
            }
        }
        if (!httpRequest) {
            alert('Abandon :( Impossible de créer une instance XMLHTTP');
            return false;
        }
        return httpRequest;
    },
    makeRequest: function(opts) {
        "use strict";
        var that = this;
        var xhr = that.getHttpRequest();
        xhr.open(opts.method, opts.url);
        if (opts.headers) {
            Object.keys(opts.headers).forEach(function(key) {
                xhr.setRequestHeader(key, opts.headers[key]);
            });
        }
        var params = opts.params;
        if (params && typeof params === 'object') {
            params = Object.keys(params).map( function(key) {
                return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
            }).join('&');
        }
        xhr.send(params);
        return xhr;
    },
    toJSONString: function( form ) {
        var obj = {};
        var elements = form.querySelectorAll("input", 'checkbox');
        for( var i = 0; i < elements.length; ++i ) {
            var element = elements[i];
            var name = element.name;
            var value = element.value;

            if( name ) {
                obj[ name ] = value;
            }
        }
        return JSON.stringify( obj );
    }
};

function delegate(el, evt, sel, handler) {
    el.addEventListener(evt, function(event) {
        var t = event.target;
        while (t && t !== this) {
            if (t.matches(sel)) {
                handler.call(t, event);
            }
            t = t.parentNode;
        }
    });
}