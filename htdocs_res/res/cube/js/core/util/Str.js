define('core/util/Str', [], function(require) {

    function Str() {
    }

    K.mix(Str, {

        getPwdStrength: function(password) {

            //initial strength
            var strength = 0

            //if the password length is less than 6
            if (password.length < 6) {
                return 0;
            }

            //length is ok, lets continue.

            // if length is 6 characters or more, increase strength value
            if (password.length >= 6)
                strength += 1

            // if password contains both lower and uppercase characters, increase strength value
            if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))
                strength += 1

            // if it has numbers and characters, increase strength value
            if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))
                strength += 1

            // if it has one special character, increase strength value
            if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))
                strength += 1

            // if it has two special characters, increase strength value
            if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,",%,&,@,#,$,^,*,?,_,~])/))
                strength += 1
            return strength;
        },

        checkIsMobile: function(input, len) {
            if (!input || (len && input.length != len)) {
                return false;
            }
            var str = '^\\d{' + len + '}$';
            var re = new RegExp(str, "");
            if (!re.test(input)) {
                return false
            }
            return true;
        },
    });

    Str.prototype = {
    };

    return Str;
});
