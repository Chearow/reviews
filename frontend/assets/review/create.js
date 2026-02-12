$(document).on('click', '#add-city-btn', function () {
    let query = $('.select2-search__field').val();

    if (!query) {
        alert('Введите название города');
        return;
    }

    $.ajax({
        url: '/city/create-ajax',
        type: 'POST',
        data: {query: query},
        success: function (response) {
            if (response.success) {
                let newOption = new Option(response.text, response.id, true, true);
                $('.select2-search__field').val('');
                $('#city-select').append(newOption).trigger('change');
            } else {
                alert(response.message || 'Ошибка при создании города');
            }
        }
    });
});

$('#save-review').on('click', function () {
    let formData = new FormData($('#review-form')[0]);

    $('#loader').show();

    $.ajax({
        url: '/review/create-ajax',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#loader').hide();

            if (response.success) {
                window.location.href = '/';
            } else {
                alert('Ошибка. Проверьте форму.');
            }
        }
    });
});