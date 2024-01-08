const user = {
  dataTable: function (tableId) {
    $("" + tableId + " thead tr")
      .clone(true)
      .addClass("filters")
      .appendTo("" + tableId + " thead");

    $(tableId).DataTable({
      destroy: true,
      paging: true,
      ordering: false,
      autoWidth: false, //  page length
      pageLength: 10,
      responsive: true,

      pagingType: "simple_numbers",
      searching: true,

      fixedHeader: true,
      language: {
        url: scriptData.dataTableJsonLang,
        processing:
          '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>Processing...',
        paginate: {
          next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
          previous:
            '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
        },
      },
      initComplete: function () {
        var api = this.api();

        // For each column
        api
          .columns()
          .eq(0)
          .each(function (colIdx) {
            // Set the header cell to contain the input element
            var cell = $(".filters th").eq(
              $(api.column(colIdx).header()).index()
            );
            var title = $(cell).text();
            $(cell).html('<input type="text" placeholder="' + title + '" />');

            // On every keypress in this input
            $(
              "input",
              $(".filters th").eq($(api.column(colIdx).header()).index())
            )
              .off("keyup change")
              .on("change", function (e) {
                // Get the search value
                $(this).attr("title", $(this).val());
                var regexr = "({search})"; //$(this).parents('th').find('select').val();

                var cursorPosition = this.selectionStart;
                // Search the column for that value
                api
                  .column(colIdx)
                  .search(
                    this.value != ""
                      ? regexr.replace("{search}", "(((" + this.value + ")))")
                      : "",
                    this.value != "",
                    this.value == ""
                  )
                  .draw();
              })
              .on("keyup", function (e) {
                e.stopPropagation();

                $(this).trigger("change");
                $(this)
                  .focus()[0]
                  .setSelectionRange(cursorPosition, cursorPosition);
              });
          });
      },
    });
  },
  editUserModal: function (e) {
    $(".user-edit-modal").trigger("click");

    let data_id = $(e).attr("data-id");
    let data_email = $(e).attr("data-email");
    let data_company_name = $(e).attr("data-company-name");
    let username = $(e).attr("data-username");

    $("#user-id").val(data_id);
    $("#company-name").val(data_company_name);
    $("#e-posta").val(data_email);
    $("#user-name").val(username);
  },
  updateUser: function () {
    let userId = $("#user-id").val();
    let companyName = $("#company-name").val();
    let ePosta = $("#e-posta").val();
    let username = $("#user-name").val();
    let password = $("#password").val();

    let data = {
      userId: userId.trim(),
      ePosta: ePosta.trim(),
      username: username.trim(),
      password: password.trim(),
    };

    $.ajax({
      type: "POST",
      url: "/user/edit/" + userId,
      data: data,
      success: function (response) {
        alert(response.message);
        if (response?.success) {
          window.location.reload();
        }
      },
    });
  },
  deleteUser: function (id) {
    let durum = confirm("Bu işlemi yapmak istediğinize emin misiniz ?");

    if (!durum) {
      return;
    }

    let userId = id;

    $.ajax({
      type: "POST",
      url: "/user/delete/" + userId,
      data: { id: userId },
      success: function (response) {
        alert(response.message);
        if (response?.success) {
          window.location.reload();
        }
      },
    });
  },

  select2Accounts: function () {
    // has class
    if (!$(".select2-accounts").length) {
      return;
    }

    $(".select2-accounts").select2({
      placeholder: "Hesap Seçiniz",
      dropdownParent: $(".user-create-modal"),
      language: {
        searching: function () {
          return "Hesaplar yükleniyor...";
        },
        // loading more
        loadingMore: function () {
          return "Daha fazla hesap yükleniyor...";
        },
      },

      ajax: {
        url: "/get-all-accounts",
        delay: 250,
        data: function (params) {
          var query = {
            q: params.term,
            page: params.page || 1,
          };

          return query;
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          // Transforms the top-level key of the response object from 'items' to 'results'
          return {
            results: data.items,
            pagination: {
              more: true,
            },
          };
        },
        // cache: true,
      },
    });
  },

  createUserBtnDisable: function () {
    $("#create-user-btn").on("click", function () {
      $("#create-user-btn").attr("disabled", true);
    });
  },

  createUserForm: function () {
    let form = $("#create-user-form").validate({
      rules: {
        ["user-ad"]: {
          required: true,
          minlength: 2,
        },
        ["user-lastname"]: {
          required: true,
          minlength: 2,
        },
        ["user-name"]: {
          required: true,
          minlength: 2,
        },
        ["e-posta"]: {
          required: true,
          email: true,
        },
        ["firma-id"]: {
          required: true,
        },
        "password-create": {
          required: true,
          minlength: 5,
        },
        password_again: {
          required: true,
          minlength: 5,
          equalTo: "#password-create",
        },
      },
      messages: {
        ["user-ad"]: {
          required: "Lütfen kullanıcı adınızı giriniz",
          minlength: "Kullanıcı adınız en az 2 karakter olmalıdır",
        },
        ["user-name"]: {
          required: "Lütfen kullanıcı adınızı giriniz",
          minlength: "Kullanıcı adınız en az 2 karakter olmalıdır",
        },
        ["user-lastname"]: {
          required: "Lütfen kullanıcı soyadınızı giriniz",
          minlength: "Kullanıcı soyadınız en az 2 karakter olmalıdır",
        },
        ["user-name"]: {
          required: "Lütfen kullanıcı adınızı giriniz",
          minlength: "Kullanıcı adınız en az 2 karakter olmalıdır",
        },
        ["e-posta"]: {
          required: "Lütfen kullanıcı e-postanızı giriniz",
          email: "Lütfen geçerli bir e-posta adresi giriniz",
        },
        ["firma-id"]: {
          required: "Lütfen kullanıcı hesabınızı seçiniz",
        },
        ["password-create"]: {
          required: "Lütfen kullanıcı şifrenizi giriniz",
          minlength: "Şifreniz en az 5 karakter olmalıdır",
        },
        password_again: {
          required: "Lütfen kullanıcı şifrenizi giriniz",
          minlength: "Şifreniz en az 5 karakter olmalıdır",
          equalTo: "Şifreleriniz eşleşmiyor",
        },
      },
    });

    $("#create-user-form").on("submit", function (e) {
      user.createUserBtnDisable();

      e.preventDefault();

      if (!form.valid()) {
        return;
      }

      // serialize form data
      let data = $(this).serializeArray();

      data = data.reduce(function (obj, item) {
        obj[item.name] = item.value;
        return obj;
      }, {});

      $.ajax({
        type: "POST",
        url: "/user/create",
        data: data,
        success: function (response) {
          if (response?.success) {
            swal
              .fire({
                title: "Bilgi",
                html: `Kullanıcı başarıyla oluşturuldu`,
                type: "success",
                showCancelButton: false,
                confirmButtonText: "Tamam",
              })
              .then((result) => {
                if (result.value) {
                  window.location.reload();
                }
              });
          } else {
            swal.fire({
              title: "Hata",
              html: "Kullanıcı oluşturulurken bir hata oluştu",
              type: "error",
              showCancelButton: false,
              confirmButtonText: "Tamam",
            });
          }
        },
      });
    });
  },
  editUserForm: function () {
    let form = $("#edit-user-profile-form").validate({
      rules: {
        ["ad"]: {
          required: true,
          minlength: 2,
        },
        ["soyad"]: {
          required: true,
          minlength: 2,
        },
        ["username"]: {
          required: true,
          minlength: 2,
        },
        ["email"]: {
          required: true,
          email: true,
        },
        "update_password": {
          minlength: 5,
        },
        "update-re-password": {
          minlength: 5,
          equalTo: "#update_password",
        },
      },
      messages: {
        ["ad"]: {
          required: "Lütfen kullanıcı adınızı giriniz",
          minlength: "Kullanıcı adınız en az 2 karakter olmalıdır",
        },
        ["soyad"]: {
          required: "Lütfen kullanıcı soyadınızı giriniz",
          minlength: "Kullanıcı soyadınız en az 2 karakter olmalıdır",
        },
        ["username"]: {
          required: "Lütfen kullanıcı adınızı giriniz",
          minlength: "Kullanıcı adınız en az 2 karakter olmalıdır",
        },
        ["email"]: {
          required: "Lütfen kullanıcı e-postanızı giriniz",
          email: "Lütfen geçerli bir e-posta adresi giriniz",
        },
       
        ["update_password"]: {
        },
        ["update-re-password"]: {
          equalTo: "Şifreleriniz eşleşmiyor",
        },
      },
    });

    $("#edit-user-profile-form").on("submit", function (e) {
      e.preventDefault();

      if (!form.valid()) {
        return;
      }

      // serialize form data
      let data = $(this).serializeArray();

      data = data.reduce(function (obj, item) {
        obj[item.name] = item.value;
        return obj;
      }, {});

   
      $.ajax({
        type: "POST",
        url: "/user/update-profile",
        data: data,
        success: function (response) {
          if (response?.success) {
            swal
              .fire({
                title: "Bilgi",
                html: `Kullanıcı başarıyla güncellendi`,
                type: "success",
                showCancelButton: false,
                confirmButtonText: "Tamam",
              })
              .then((result) => {
                if (result.value) {
                  window.location.reload();
                }
              });
          } else {
            swal.fire({
              title: "Hata",
              html: "Kullanıcı güncellenirken bir hata oluştu",
              type: "error",
              showCancelButton: false,
              confirmButtonText: "Tamam",
            });
          }
        },
      });
    });
  },
  init: function () {
    this.select2Accounts();
    this.dataTable("#userList");
    this.createUserForm();
    this.editUserForm();
  },
};

$(document).ready(function () {
  user.init();
});
