(function ($) {

    // assure plugin object structure
    pulpit = window.pulpit || {};
    pulpit.admin = pulpit.admin || {};
    pulpit.admin.utils = pulpit.admin.utils || {}

    $.extend(pulpit.admin.utils, {
        toHHMMSS: function (n) {
            var sec_num = parseInt(n, 10); // don't forget the second param
            var hours = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = sec_num - (hours * 3600) - (minutes * 60);

            if (hours < 10) {
                hours = "0" + hours;
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            if (seconds < 10) {
                seconds = "0" + seconds;
            }
            return hours + ':' + minutes + ':' + seconds;
        },

        calculateSpeechTime: function () {
            var wc = new wp.utils.WordCounter();
            var speechTime = (wc.count(tinyMCE.activeEditor.getContent())) / 110 * 60;
            $('#speech-length-value').html(pulpit.admin.utils.toHHMMSS(speechTime));
        }
    });


    $(document).on('tinymce-editor-init', function (event, editor) {
        $('td#wp-word-count').after('<td id="speech-length-message">'+pulpit_speech.speech_time + ': '+'<span id="speech-length-value"></span></td>');
        pulpit.admin.utils.calculateSpeechTime();

        window.tinymce.get('content').on('keyup', function (e) {
            pulpit.admin.utils.calculateSpeechTime();
        });
    });


})(jQuery);