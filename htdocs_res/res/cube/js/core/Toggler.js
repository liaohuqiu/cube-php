/**
 * 全局的 Toggler 管理
 *
 */
define('core/Toggler', [ 'core/jQuery', 'core/Toggle' ], function(require, exports, module) {

    var $ = require('core/jQuery'),
        Toggle = require('core/Toggle');

    var Toggler = function(trigger, container) {
        K.mix(this, new K.Pubsub());

        this.trigger = $(trigger);
        this.container = $(container);

        this.trigger.data('Toggler', this);

        new Toggle({
            trigger: this.trigger,
            board: this.container,
            handler: K.bind(function() {
                if (Toggler.getActive() == this) {
                    this.hide();
                }
            }, this)
        });

        trigger.click(K.bind(function(event) {
            event.preventDefault();

            this.toggle();
        }, this));
    };

    K.mix(Toggler, {
        getInstance: function(trigger) {
            var obj = trigger.data('Toggler');
            if (obj) {
                return obj;
            } else {
                return false;
            }
        },

        getActive: function() {
            return this._active;
        },

        setActive: function(toggler) {
            if (toggler) {
                var active = this.getActive();
                if (active && active != toggler) {
                    active.hide();
                }
            }
            this._active = toggler;
        }
    });

    K.mix(Toggler, {
        _active: null
    });

    K.mix(Toggler.prototype, {
        toggle: function() {
            if (Toggler.getActive() == this) {
                this.hide();
            } else {
                this.show();
            }
            return this;
        },

        hide: function() {
            var active = Toggler.getActive();
            if (active == this) {
                this.container.hide();
                Toggler.setActive(null);

                this.fire('hide', this.trigger, this.container);
            }

            return this;
        },

        show: function() {
            var active = Toggler.getActive();
            if (active != this) {
                this.container.show();
                Toggler.setActive(this);

                this.fire('show', this.trigger, this.contaienr);
            }

            return this;
        }
    });

    module.exports = Toggler;
});
