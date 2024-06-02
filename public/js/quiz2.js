


function checkForValidInput(current_step) {
    var current_input = $('.step_' + current_step).find("input:first");

    // se o tipo for radio, é para multiplos se não faz validação normal

    var current_input_name = current_input.attr("name");
    var input_value = $('[name="' + current_input_name + '"]')


    if(!input_value.prop('required')){
        unblockNextButton();
        return;
    } 

    if (current_input.attr('type') === 'text') {
        if (input_value.val().length !== 0) {
            unblockNextButton();
        } else {
            blockNextButton();
        }
        return;
    }
    if (current_input.attr('type') === 'radio') {
        var multiples_inputs = $('.step_' + current_step).find("input:checked");

        if (multiples_inputs.length !== 0) {
            unblockNextButton();
        } else {
            blockNextButton();
        }
        return;
    }

    if (current_input.attr('type') === 'range') {
        if (input_value.val() != 0) {
            unblockNextButton();
        } else {
            blockNextButton();
        }
        return;
    }

}

function callNextStep(current_step) {
    global_step = current_step;
    checkForValidInput(current_step);
}

function previousStep(current_step) {
    global_step = current_step;
    var current_input = $('.step_' + current_step).find("input:first");
    var current_input_name = current_input.attr("name");
    checkForValidInput(current_step);
}

var global_step = 1;

$(document).ready(function() {
    var current_input = $('.step_' + global_step).find("input:first");
    var current_input_name = current_input.attr("name");

    checkForValidInput(global_step);

    $('input[name=' + current_input_name + ']').on("keyup", function() {
        checkForValidInput(global_step);
    });


    $('.multiple-option-container').on('click', function() {
        var input_name = $(this).find('input').attr('name');
        var input_id = $(this).find('input').attr('id');
        if($(this).find('input').is(":checked") == false){
            $(this).find('input').prop('checked', true);
            $(this).closest('.multiple-option-container').css("background-color", "white");
        } else {
            $(this).find('input').prop('checked', false);
            $(this).closest('.multiple-option-container').css("background-color", "transparent");
        }
      
    
        if ($(this).find('input').is(':checked')) {
            unblockNextButton();
        }
    });


    $('.option-container').on('click', function() {
        var input_name = $(this).find('input').attr('name');
        var input_id = $(this).find('input').attr('id');
        $(this).find('input').prop('checked', true);
        $('.option-container').css("background-color", "transparent");
        $(this).closest('.option-container').css("background-color", "white");

        if ($(this).find('input').is(':checked')) {
            unblockNextButton();
        }
    });

    $('.detailed-option-container').on('click', function() {
        var input_name = $(this).find('input').attr('name')
        var input_id = $(this).find('input').attr('id')
        $(this).find('input').prop('checked', true);
        $('.detailed-option-container').css("background-color", "transparent");
        $(this).closest('.detailed-option-container').css("background-color", "white");

        if ($(this).find('input').is(':checked')) {
            unblockNextButton();
        }
    });


    $(".calculator input").on("input change", function(event) {
        var parameterName = $(this).attr("id");
        var range_value = $(this).val();
        $("#label_" + parameterName).html("Valor: " + range_value + "");

        if (range_value != 0) {
            unblockNextButton();
        }
    });

    $(".datepicker").datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        minDate: new Date(1924, 10 - 1, 25),
        maxDate: '+100Y',
        yearRange: '1924:c',
        onSelect: function() {
            var dateObject = $(this).datepicker('getDate');
            var date = $('.datepicker').datepicker({
                dateFormat: 'dd-mm-yy'
            }).val();
            unblockNextButton();
        }
    });
});

function app() {
    return {
        step: 1
    }
}