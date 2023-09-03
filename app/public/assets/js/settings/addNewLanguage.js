// Autor:Sezer Bölük
let AddNewLanguage = {
     allMessages : [],
    AddMessageWithAjax: (e) => {
        e.preventDefault();
        let formSerializeArray = $(
            '#addNewLanguageForm'
        ).serializeArray();

        var formData = new FormData();

        $.each(formSerializeArray, function (i, field) {
            formData.append(field.name, field.value);
        })

        $.ajax({
            type: 'POST',
            url: addNewMessageData.url,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                // Butonu pasif hale getiriyoruz.
                AddNewLanguage.disabledBtn('addNewLanguageForm');
            },
            success: function (data) {
                // İşlem başarılı ise hata mesajları gizleniyor
                AddNewLanguage.hideValidationMessage('addNewLanguageForm');
                // İşlem başarılı ise hata mesajları gizleniyor
                AddNewLanguage.hideErrorMessage('addNewLanguageForm');

                if (data.status == 'success') {
                    // İşlem başarılı ise mesaj kutusu gösteriliyor
                    AddNewLanguage.readSuccessMessage('addNewLanguageForm',data.message);
                    AddNewLanguage.dictionaryListAjaxDataTable();
                }


                // Butonu aktif hale getiriyoruz.
                AddNewLanguage.showBtn('addNewLanguageForm');
            },
            error: function (data) {
                let validations = data?.responseJSON?.validations;
                if (validations) {
                    // input hata mesajları gösteriliyor
                    AddNewLanguage.readValidationMessage('addNewLanguageForm',validations);
                } else if (data?.responseJSON?.message) {
                    // İşlem başarısız ise hata mesajları gösteriliyor
                    AddNewLanguage.readErrorMessage('addNewLanguageForm',data?.responseJSON?.message);
                }
                // Butonu aktif hale getiriyoruz.
                AddNewLanguage.showBtn('addNewLanguageForm');
            }
        });
    },
    readValidationMessage: (formId,validations) => {
        $(`#${formId}  .invalid-feedback`).css('display', 'none');
        Object.keys(validations).forEach(function (key) {
            $( `#${formId}  #${key}`).css('display', 'block');
            $(`#${formId}  #${key}`).html(validations[key]);
        })
    },
    hideValidationMessage: (formId) => {
        $(`#${formId}  .invalid-feedback`).each(function () {
            $(this).css('display', 'none');
        })
    },
    readErrorMessage: (formId, error) => {
        AddNewLanguage.hideValidationMessage();
        $(`#${formId}  .alert-container`).html(`
           <div class="alert alert-danger alert-dismissible alert-alt  show custom-alert-message">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                                    </button>
                                    <strong>Hata!</strong> <span class="custom-message">${error}</span>
         </div>`);
    },
    readSuccessMessage: (formId, message) => {
        AddNewLanguage.hideValidationMessage();
        $(`#${formId}  .alert-container`).html(`
          <div class="alert alert-success alert-dismissible alert-alt fade show custom-alert-message">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
            </button>
            <strong>Başarılı!</strong> ${message}.
        </div>`);

    },
    hideErrorMessage: (formId) => {
        $(`#${formId}  .custom-alert-message`).addClass('d-none');
    },
    disabledBtn: (formId) => {
        $(`#${formId}  .form-btn`).attr('disabled', true);
        $(`#${formId}  .form-btn`).text('Lütfen Bekleyiniz...');
    },
    showBtn: (formId) => {
        $(`#${formId}  .form-btn`).attr('disabled', false);
        $(`#${formId}  .form-btn`).text('Kaydet');
    },
    dictionaryListAjaxDataTable: () => {

        $('#dictionaryTable').DataTable({

            "destroy": true,
                language: {
                    // url:dataTableData.filePath,
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    },
                },
            "ajax": {
                "url": addMessageJsonUrl.url,
                "dataSrc": "data"
            },
            columns: [
                { data: 'key', title: 'Metin Adı'},
                { data: 'value', title: 'Metin Değeri' },
                {
                  'data': 'key',
                    'title': 'İşlemler',
                    'render': function (data, type, row, meta) {
                      let id = row.id;

                      return `
                       <div class="d-flex">
                                    <a href="javascript:void(0)" onclick="AddNewLanguage.editMessageModalShow(${id})"  class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
                                    <a href="javascript:void(0)" onclick="AddNewLanguage.deleteMessageItem(${id})" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
                                </div>
                      `

                    },
                }

            ],

            initComplete: function (settings, json) {
                AddNewLanguage.allMessages = json.data;
            }
        })

    },
    editMessageModalShow: (id) => {
        localStorage.setItem('messageId', id);
        let message = AddNewLanguage.allMessages.find(item => item.id == id);
        $('.dictionary-modal').trigger('click');

        $('#editLanguageMessageForm input[name="key"]').val(message.key);
        $('#editLanguageMessageForm input[name="value"]').val(message.value);


    },
    editMessage: (e) => {

         // editLanguageMessageForm

        e.preventDefault();
        let formSerializeArray = $(
            '#editLanguageMessageForm'
        ).serializeArray();

        var formData = new FormData();

        formData.append('id', localStorage.getItem('messageId'));
        $.each(formSerializeArray, function (i, field) {
            formData.append(field.name, field.value);
        })

        $.ajax({
            type: 'POST',
            url: addMessageJsonUrl.editMessageUrl,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                // Butonu pasif hale getiriyoruz.
                AddNewLanguage.disabledBtn('editLanguageMessageForm');
            },
            success: function (data) {
                // İşlem başarılı ise hata mesajları gizleniyor
                AddNewLanguage.hideValidationMessage('editLanguageMessageForm');
                // İşlem başarılı ise hata mesajları gizleniyor
                AddNewLanguage.hideErrorMessage('editLanguageMessageForm');

                if (data.status == 'success') {
                    // İşlem başarılı ise mesaj kutusu gösteriliyor
                    AddNewLanguage.readSuccessMessage('editLanguageMessageForm',data.message);
                    AddNewLanguage.dictionaryListAjaxDataTable();
                }


                // Butonu aktif hale getiriyoruz.
                AddNewLanguage.showBtn('editLanguageMessageForm');
            },
            error: function (data) {

                console.log('data', data)
                let validations = data?.responseJSON?.validations;
                if (validations) {
                    // input hata mesajları gösteriliyor
                    AddNewLanguage.readValidationMessage('editLanguageMessageForm',validations);
                } else if (data?.responseJSON?.message) {
                    // İşlem başarısız ise hata mesajları gösteriliyor
                    AddNewLanguage.readErrorMessage('editLanguageMessageForm',data?.responseJSON?.message);
                }
                // Butonu aktif hale getiriyoruz.
                AddNewLanguage.showBtn('editLanguageMessageForm');
            }
        });
    },
    deleteMessageItem: (id) => {
        let token = addMessageJsonUrl.token;
        let messageId = id;

        let formData = new FormData();
        formData.append('id', messageId);
        formData.append('token', token);

        $.ajax({
            type: 'POST',
            url: addMessageJsonUrl.deleteUrl,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {

            },
            success: function (data) {
                AddNewLanguage.dictionaryListAjaxDataTable();
            },
            error: function (data) {
                console.log('data', data);
            }

        })




    },
    init: function () {
        this.dictionaryListAjaxDataTable();
    },
    run: function () {
        this.init();
    },
}


$(document).ready(function () {
    AddNewLanguage.run();


})