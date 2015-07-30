cj(function () {
    cj('.contribution').addClass('collapsed');
    cj('.financial-items').toggle();

    cj('.contribution').on('click', function () {
        var contribution = cj(this);
        var contributionId = contribution.data('contribution-id');
        var financialItems = cj('#financial-items-' + contributionId);

        contribution.toggleClass('collapsed');
        financialItems.toggle();
    });
});