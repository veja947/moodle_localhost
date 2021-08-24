$(function() {
    var $modal = $('#page_modal_container');
    var $primaryDomainButton = $('.primary-domain-radio');
    var $confirmButton = $modal.find('button.btn.btn-primary');

    /**
     *  Events
     */
    $primaryDomainButton.off().on('click', clickPrimaryDomainButtonEvent);
    $confirmButton.off().on('click', savePrimaryButtonHandler);

    /**
     *  Functions
     */
    function clickPrimaryDomainButtonEvent(e) {
        var id = $(e.target).attr('data-id');
        $modal.attr('subdomain-id', id);
    }

    function savePrimaryButtonHandler() {
        console.log('save');
        var id =  $modal.attr('subdomain-id');
        window.location.href = '../domains/index.php?subdomainid=' + id + '&action=primary';
    }
});