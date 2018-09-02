(function() {

    var tabs = document.querySelectorAll('.tabs a');
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].addEventListener('click', function(e) {
            e.preventDefault();
            displayTabs(this);
        })
    }

    var displayTabs = function(element) {
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
    }

})();