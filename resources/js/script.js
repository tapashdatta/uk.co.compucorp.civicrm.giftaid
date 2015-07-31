cj(function () {
    cj('.contribution').addClass('collapsed');
    cj('.line-items').toggle();

    cj('.contribution').on('click', function () {
        var contribution = cj(this);
        var contributionId = contribution.data('contribution-id');
        var financialItems = cj('#line-items-' + contributionId);

        contribution.toggleClass('collapsed');

        if (contribution.hasClass('collapsed')) {
            financialItems.fadeOut(100);
        } else {
            financialItems.fadeIn(100);
        }
    });
});