$(document).ready(function() {
    var url = $(location).attr('href');
    var parts = url.split("/");
    var last_part = parts[3];
    var active_tab = $('ul#sidebar_navigation li a#'+last_part);
    active_tab.addClass( "bg-gray-100" );

    var parent = active_tab.closest('ul');
    $(parent).removeClass("hidden");


    function calculateOneRepMax(weight, rep){
        console.log(weight);
        console.log(rep);
        console.log(weight / (1.0278 - 0.0278 * rep));
        console.log( Math.trunc(weight / (1.0278 - 0.0278 * rep)));
        console.log('----');
        
        return Math.trunc(weight / (1.0278 - 0.0278 * rep));
    }

    function validateFields(){
        var weight = $('#weight').val();
        var repetitions = $('#repetitions').val();

        var regex = /^\d{1,5}$|(?=^.{1,5}$)^\d+\.\d{0,2}$/;
        if(!regex.test(weight) ) {
            $('#weight').val('');
        }

        var repetitions_regex = /^\d+$/;
        if(!repetitions_regex.test(repetitions) ) {
            $('#repetitions').val('');
        }
    }

    $('#conversion_form').on('keyup change paste', 'input, select, textarea', function() {
        var result = '';
        var weight = $('#weight').val();
        var repetitions = $('#repetitions').val();

        validateFields();
        // TODO:
        if(repetitions > 150){
            console.log('manda erro e não avança');
        }
        if(weight > 699){
            console.log('manda erro e não avança');
        }

        if(weight != 0 && repetitions != 0){
          result =  calculateOneRepMax(weight, repetitions);

          $('#one_rep_max').text(result);
          console.log('calculating..');

          for (let i = 1; i < 16; i++) {
            rep_options = '';
           rep_options =  calculateOneRepMax(weight, i);
          // console.log(i);
          // console.log(rep_options);
           //console.log('-----');
           $('#'+i+ '_rm').text(calculateOneRepMax(weight, i));
          }
        }
    });
});
