function sendBookingData() {
  var error = false;
  $(".alert").hide();
  $(".add-fc").removeClass("is-invalid");
  $(".add-fc").each(function() {
    if (!$(this).val()) {
      $(this).addClass( "is-invalid" );
      error = 1;
    }
    if (error) {
      $(".add-form-alert .alert-danger").text('Заполните все поля');
      $(".add-form-alert .alert-danger").show('slow/400/fast', function() {})
    }
  });
  var date = $('input#daterange').val();
  var name = $('input#name').val();
  var phonenumber = $('input#phonenumber').val();

  if (date.match(/^\d{2}\.\d{2}\.\d{4}\s-\s\d{2}\.\d{2}\.\d{4}$/g)) {
    console.log('daterange matches');
  } else {
    $("span#daterange-incorrect").show();
    $("div.panel").css("max-height", "600px");
    error = true;
  }

  if (name.match(/^[A-Za-zА-Яа-я0-9 ]+$/g)) {
    console.log('name matches');
  } else {
    $("span#name-incorrect").show();
    $("div.panel").css("max-height", "600px");
    error = true;
  }

  if (phonenumber.match(/^((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{6,7}$/g)) {
    console.log('phonenumber matches');
  } else {
    $("span#phonenumber-incorrect").show();
    $("div.panel").css("max-height", "600px");
    error = true;
  }

  if ($('select#rooms option:selected').val() != "0") {
    console.log('room selected');
  } else {
    $("span#rooms-incorrect").show();
    $("div.panel").css("max-height", "600px");
    error = true;
  }


  // Hide incorrect message after focus 
  $( "form#booking input" ).focus(function() {
    $("span#" + $(this).attr('id') + "-incorrect").hide();
  });

  $( "select#rooms" ).change(function() {
   $("span#" + $(this).attr('id') + "-incorrect").hide();
 });

  var data = $("form#booking").serializeArray();
  // console.log(data);

  if (!error) {
    $.ajax({
      type: 'POST',
      url: '/ajax_booking',
      data: data,
      success: function(data){
        if (data == '1') {
          $("#p-modal-content").text("Запрос принят.");
          $("#myModal").show();
        }
        else if (data == '0') {
          $("#p-modal-content").text("Ошибка запроса. Пожалуйства, попробуйте еще раз.");
          $("#myModal").show();
        }
      },
      error:function () {
        $(".add-form-alert .alert-danger").text('Ошибка при добавлении данных.');
        $(".add-form-alert .alert-danger").show('slow/400/fast', function() {});
      }
    });
  }
}
