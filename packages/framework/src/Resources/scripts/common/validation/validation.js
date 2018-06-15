(function ($) {

    Shopsys = window.Shopsys || {};
    Shopsys.validation = Shopsys.validation || {};

    $(document).ready(function () {
        Shopsys.validation.refreshBindings();
    });

    function invalidateForm () {
        $(this).closest('form').addClass('js-no-validate');
    }

    function closeError () {
        $(this).closest('.js-validation-error').hide();
    }

    function toggleError () {
        $(this)
            .closest('.js-validation-errors-list')
            .find('.js-validation-error')
            .toggle();
    }

    Shopsys.validation.refreshBindings = function () {
        $('.js-no-validate-button').off('click', invalidateForm).click(invalidateForm);
        $('.js-validation-error-close').off('click', closeError).click(closeError);
        $('.js-validation-error-toggle').off('click', toggleError).click(toggleError);
    };

    Shopsys.validation.findElementsToHighlight = function ($formInput) {
        return $formInput.filter('input, select, textarea, .form-line');
    };

    Shopsys.validation.highlightSubmitButtons = function ($form) {
        var $submitButtons = $form.find('.btn[type="submit"]:not(.js-no-validate-button)');

        if (Shopsys.validation.isFormValid($form)) {
            $submitButtons.removeClass('btn--disabled');
        } else {
            $submitButtons.addClass('btn--disabled');
        }
    };

})(jQuery);
