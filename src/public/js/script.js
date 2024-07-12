/** hamburger menu **/
$('.header-hamburger').click(function(){
    $('.modal')[0].showModal();
    $('html, body').css('overflow', 'hidden');
});
$('.modal-menu__btn-close').click(function(){
    $('.modal')[0].close();
    $('html, body').css('overflow', 'auto');
});

/** reservation table **/
if($('input[name=\"date\"]').length) {
    $(window).on('load', function(){
        let dateValue = $('input[name=\"date\"]').val();
        let formattedDate = dateValue.replace(/-/g, '/');
        $('#date').text(formattedDate);
    });
    $('input[name=\"date\"]').on('change', function() {
        let dateValue = $(this).val()
        let formattedDate = dateValue.replace(/-/g, '/');
        $('#date').text(formattedDate);
    });
}
if($('select[name=\"time\"]').length) {
    $(window).on('load', function(){
        $('#time').text($('select[name=\"time\"] option:selected').val());
    });
    $('select[name=\"time\"]').on('change', function() {
        $('#time').text($('option:selected', this).val());
    });
}
if($('select[name=\"number\"]').length) {
    $(window).on('load', function(){
        $('#number').text($('select[name=\"number\"] option:selected').val() + '人');
    });
    $('select[name=\"number\"]').on('change', function() {
        $('#number').text($('option:selected', this).val() + '人');
    });
}

/* preview image */
function previewImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    const form = event.target.closest('form');
    const preview = form.querySelector('.photo-preview');
    const current = form.querySelector('.photo-current');

    reader.onload = function() {
        if (preview) {
            preview.src = reader.result;
            preview.style.display = 'inline-block';
        }
        if (current) {
            current.style.display = 'none';
        }
    };

    if (file) {
        reader.readAsDataURL(file);
    }
}

/* upload image */
$('#uploadArea').on('dragover', function (event) {
	event.preventDefault();
	this.style.backgroundColor = '#e2e8fd';
});

$('#uploadArea').on('dragleave', function () {
	this.style.backgroundColor = '';
});

$('#uploadArea').on('drop', function (event) {
	event.preventDefault();
	this.style.backgroundColor = '';
    var files = event.originalEvent.dataTransfer.files;
    if (files.length > 0) {
        $('#fileInput').prop('files', files);
        $('#fileInput').trigger('change');
    }
});

$('#fileInput').on('change', function (event) {
    previewImage(event);
    /*
    const file = event.target.files[0];
    const reader = new FileReader();
    const form = event.target.closest('form');
    const preview = form.querySelector('.photo-preview');
    const current = form.querySelector('.photo-current');

    reader.onload = function() {
        if (preview) {
            preview.src = reader.result;
            preview.style.display = 'inline-block';
        }
        if (current) {
            current.style.display = 'none';
        }
    };

    if (file) {
        reader.readAsDataURL(file);
    } */
});

/* count text */
$(function() {
    const $target = $('#textarea-count-target');
    const $result = $('#textarea-count-result');

    if($target.length) {
        function countText() {
            const len = $target.val().length;
            $result.text(len);
        }

        // ページ読み込み直後に文字数をカウント
        countText();

        // inputイベントが発生したときに文字数をカウント
        $target.on('input', () => {
            countText();
        });
    }
});