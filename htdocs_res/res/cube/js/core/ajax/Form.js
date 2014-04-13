/**
 * 异步表单
 *
 */
define('core/ajax/Form', [ 'core/jQuery', 'core/ajax/Request' ], function(require, exports, module) {

    var $ = require('core/jQuery');

    var Form = function() {

    };

    var disabledList = [];

    K.mix(Form, {
        enableQuickSubmit: function(element) {
            $(element).on('keydown.autoSumbit', function(event) {
                if (event.keyCode == 13 && (event.ctrlKey || event.metaKey)) {
                    $(this).closest('form').submit();
                }
            });
        },

        getElements: function(container) {
            container = $(container).get(0);
            if (container.tagName == 'FORM') {
                return container.elements;
            } else {
                return Form._getInputs(container);
            }
        },

        setDisabled: function(container, bool) {
            var elements = Form.getElements(container);
            K.forEach(elements, function(element) {
                var $element = $(element),
                    prop = $element.prop('disabled'),
                    originalDisabled;

                if (prop !== undefined) {
                    originalDisabled = $element.data('originalDisabled');
                    if (bool) {
                        if (originalDisabled !== undefined) {
                            $element.data('originalDisabled', prop);
                        }
                        if (prop === false && originalDisabled !== true) {
                            disabledList.push($element.get(0));
                        }
                        $element.prop('disabled', true)
                            .addClass('disabled input_disabled');
                    } else {
                        if (originalDisabled !== true) {
                            $element.prop('disabled', false)
                                .removeClass('disabled input_disabled');
                            K.without(disabledList, $element.get(0));
                        }
                        $element.removeData('originalDisabled');
                    }
                }
            });
        },

        serialize: function(container) {
            var elements = Form.getElements(container);
            var data = {};
            K.forEach(elements, function(element) {
                if (! element.name) {
                    return;
                }

                if (element.disabled) {
                    return;
                }

                var type = element.type,
                    tag = element.tagName;

                if ((K.contains(['radio', 'checkbox'], type) && element.checked) ||
                    K.contains(['text', 'hidden', 'password', 'email'], type) ||
                    K.contains(['TEXTAREA', 'SELECT'], tag)) {
                    data[element.name] = element.value;
                }
            });
            return data;
        }
    });

    K.mix(Form, {
        _getInputs: function(container) {
            var $container = $(container);
            return $container.find('input')
                .add($container.find('textarea'))
                .add($container.find('select'))
                .add($container.find('button'));
        }
    });

    // TODO
    window.onunload = function() {
        K.forEach(disabledList, function(element) {
            $(element).prop('disabled', false).removeClass('disabled input_disabled');
        });
    };

    module.exports = Form;
});
