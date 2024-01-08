let ApplicationFile = {
  trademark_id: null,
  dataTable: function (tableId) {

    $(`${tableId} thead tr`)
        .clone(true)
        .addClass("filters")
        .appendTo(`${tableId} thead`);
    $(tableId);
    $(tableId).DataTable({
      initComplete: function () {
        var api = this.api();

        // For each column
        api
            .columns()
            .eq(0)
            .each(function (colIdx) {
                // Set the header cell to contain the input element
                var cell = $('.filters th').eq(
                    $(api.column(colIdx).header()).index()
                );
                var title = $(cell).text();
                $(cell).html('<input type="text" placeholder="' + title + '" />');

                // On every keypress in this input
                $(
                    'input',
                    $('.filters th').eq($(api.column(colIdx).header()).index())
                )
                    .off('keyup change')
                    .on('change', function (e) {
                        // Get the search value
                        $(this).attr('title', $(this).val());
                        var regexr = '({search})'; //$(this).parents('th').find('select').val();

                        var cursorPosition = this.selectionStart;
                        // Search the column for that value
                        api
                            .column(colIdx)
                            .search(
                                this.value != ''
                                    ? regexr.replace('{search}', '(((' + this.value + ')))')
                                    : '',
                                this.value != '',
                                this.value == ''
                            )
                            .draw();
                    })
                    .on('keyup', function (e) {
                        e.stopPropagation();

                        $(this).trigger('change');
                        $(this)
                            .focus()[0]
                            .setSelectionRange(cursorPosition, cursorPosition);
                    });
            });
    },
      destroy: true,
      paging: true,
      ordering: true,
      autoWidth: false,
      //  page length
      pageLength: 10,
      dataSrc: "data",
      responsive: true,

      pagingType: "simple_numbers",
      searching: true,

      ajax: {
        url: scriptData.getTrademarkUrl +'?type=' + scriptData.type,
        type: "GET",
        beforeSend: function (xhr) {
          //   $(".main-preloader").removeClass("d-none");
        },

        error: function (xhr, error, code) {
          console.log(xhr);
        },
      },

      drawCallback: function (settings) {
        // $(".main-preloader").addClass("d-none");
        console.log(settings.json);
      },

      columnDefs: [
        {
          targets: 0,
          // center
          className: "text-center",
          // style: "width: 76px;",
          render: function (data, type, row, meta) {
            return `
            <div style="text-align: left; width: 76px;"><img
            loading="lazy"
            class="v-image v-widget" src="http://apiz.ankarapatent.com/fileServer/tmImage?res=64&amp;appNo=${row.col_application_number}" alt=""></div>           
            `;
          },
        },
        {
          targets: 1,
          render: function (data, type, row, meta) {
            return `
            <span onclick="ApplicationFile.getTrademarkInfo(${row.col_id})" class="text-primary text-decoration-underline" href="#" data-bs-toggle="modal" data-bs-target=".detay-modal">
																										<span>${row.col_trademark}</span>
																									</span>
            `;
          },
        },
        {
          targets: 2,
          render: function (data, type, row, meta) {
            return `
            ${row.col_class_string ? row.col_class_string : "-"} 
            `;
          },
        },

        {
          targets: 3,
          render: function (data, type, row, meta) {
            return `
            ${row.col_file_number ?? "-"} 
            `;
          },
        },
        {
          targets: 4,
          render: function (data, type, row, meta) {
            return `
            ${row.col_application_number ?? "-"} 
            `;
          },
        },
        {
          targets: 5,
          render: function (data, type, row, meta) {
            return `
            ${row.col_registration_date ?? "-"} 
            `;
          },
        },
        {
          targets: 6,
          render: function (data, type, row, meta) {
            return `
            ${row.col_registration_number ?? "-"}
            `;
          },
        },
        {
          targets: 7,
          render: function (data, type, row, meta) {
            return `
            ${row.col_renewal_date ?? "-"}
            `;
          },
        },
        {
          targets: 8,
          render: function (data, type, row, meta) {
            return `
            ${row.col_last_status ?? "-"}
            `;
          },
        },
        //     {
        //         targets: 9,

        //         render: function (data, type, row, meta) {

        //             let siniflar = row.col_class_string.replaceAll(",", "-");

        //             siniflar = siniflar.split("-");

        //             siniflar =  siniflar.map((item) => {
        //                 return parseInt(item)
        //             }).join("-")

        //             let url = `/company/observation/${
        //                 siniflar
        //             }/${row.col_trademark}/${row.col_id}/ydn`.replaceAll(" ", "");
        //             return ` <a href="${url}" class="text-primary text-decoration-underline">Gözlemi Başlat</a>
        // `;
        //         },
        //     },
      ],

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
    });
  },

  loading: function () {
    $(".main-preloader").removeClass("d-none");
    $(".main-preloader ").css("display", "flex");
  },

  endLoading: function () {
    $(".main-preloader").addClass("d-none");
    $(".main-preloader").css("display", "none");
  },

  getTrademarkInfo: function (id) {
    // trademark_id = id;
    ApplicationFile.trademark_id = id;

    if ($('.profile-tab a[data-bs-toggle="tab"]').hasClass("active")) {
      let tab = $('.profile-tab  a[data-bs-toggle="tab"].active').attr("href");

      switch (tab) {
        case "#islemler":
          ApplicationFile.getActionList();
          break;

        case "#esyaListesi":
          ApplicationFile.getClassList();
          break;

        case "#ruchanListesi":
          ApplicationFile.getPriority();
          break;

        case "#faturalar":
          ApplicationFile.getDetailInvoice();
          break;

        case "#dosyalar":
          ApplicationFile.getDetailFiles();
          break;
      }
    }

    $.ajax({
      url: `/brand/international/yda-detail-modal?col_id=${id}&type=ydn`,
      method: "get",
      data: {},
      beforeSend: function () {
        ApplicationFile.loading();
      },
      success: function (response) {
        if (response.data) {
          let data = response.data;
          console.log(data);
          $("#col_account_referance_number").text(
            data.col_account_referance_number
          );
          $("#col_trademark").text(data.col_trademark);
          $("#col_distinctive_trademark").text(data.col_distinctive_trademark);
          $("#col_application_number").text(data.col_application_number);
          $("#col_application_date").text(data.col_application_date);
          $("#col_c_file_number").text(data.col_c_file_number);

          $("#col_class_string").text(data.col_class_string);

          $("#col_application_system").val(data.col_application_system);
          $("#col_application_number").val(data.col_application_number);
          $("#col_renewal_date").val(data.col_renewal_date);
          $("#col_country").val(data.col_country);
          $("#col_last_status").val(data.col_last_status);
          $("#col_publication_date").val(data.col_publication_date);

          $("#col_tpe_application_number").val(data.col_tpe_application_number);
          $("#col_adding_country_date").val(data.col_adding_country_date);
          $("#col_registration_number").val(data.col_registration_number);
          $("#col_registration_date").val(data.col_registration_date);

          ApplicationFile.endLoading();
        }
      },
      error: function (xhr, error, code) {
        console.log(xhr);
      },
    });
  },

  getPriority: function () {
    let id = ApplicationFile.trademark_id;
    $.ajax({
      url: `/brand/domestic/get-appeal-file-priority`,
      method: "GET",
      data: {
        id: id,
      },
      beforeSend: function () {
        $("#priorityContainer").html(`
               <tr>
                    <td colspan="4" class="text-center">
                        <div class="spinner-border text-primary m-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
                `);
      },
      success: function (response) {
        if (response?.data?.length > 0) {
          let data = response.data;
          let html = "";
          data.map((item) => {
            html += `  <tr>
            <td>${item.col_priority_number}</td>
            <td>${item.ulke}</td>
            <td>${item.col_priority_date ?? "-"}</td>
            <td>${item.col_priority_due_date ?? "-"}</td>
            </td>
        </tr>`;
          });

          $("#priorityContainer").html(html);
        } else {
          $("#priorityContainer").html(`
                        <tr>
                    <td colspan="4" class="text-center">
                        <div class="alert alert-warning solid alert-square "><strong>Uyarı!</strong> Rüçhan bilgisi bulunamadı</div>
                    </td>
                </tr>
                        `);
        }
      },
    });
  },

  getActionList: function () {
    let id = ApplicationFile.trademark_id;

    $.ajax({
      url: `/brand/international/yda-detail-modal?col_id=${id}&type=actions`,
      method: "get",
      data: {},
      beforeSend: function () {
        $("#actionsContainer").html(`
               <tr>
                    <td colspan="6" class="text-center">
                        <div class="spinner-border text-primary m-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
                `);
      },
      success: function (response) {
        if (response?.data?.length > 0) {
          let data = response.data;
          let html = "";
          data.map((item) => {
            html += `  <tr>
            <td>${item.col_process_comment}</td>
            <td>${item.col_process_date}</td>
            <td>${item.col_process_due_date}</td>
            <td>${item.col_description}</td>
            </td>
          
            <td >${item.son_durum}
            </td>
        
        </tr>`;
          });

          $("#actionsContainer").html(html);
        } else {
          $("#actionsContainer").html(`
                        <tr>
                    <td colspan="6" class="text-center">
                        <div class="alert alert-warning solid alert-square "><strong>Uyarı!</strong> İşlem bilgisi bulunamadı</div>
                    </td>
                </tr>
                        `);
        }
      },
    });
  },

  getClassList: function () {
    let id = ApplicationFile.trademark_id;
    $.ajax({
      url: `/brand/international/yda-detail-modal?col_id=${id}&type=classes`,
      method: "get",
      data: {},
      beforeSend: function () {
        $("#accordion-nine").html(`
                <div class="text-center">
                 <div class="spinner-border text-primary m-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                </div>
                `);
      },
      success: function (response) {
        if (response.data) {
          if (response?.data?.length > 0) {
            let data = response.data;
            let html = "";

            data.forEach((item) => {
              html += `
                                    <div class="accordion-item">
                            <div class="accordion-header rounded-lg collapsed" id="accord-${item.col_id}" data-bs-toggle="collapse" data-bs-target="#collapse-${item.col_id}" aria-controls="collapse-${item.col_id}" aria-expanded="false" role="button">
                                <span class="accordion-header-icon"></span>
                                <span class="accordion-header-text">${item.col_class_code}</span>
                                <span class="accordion-header-indicator"></span>
                            </div>
                            <div id="collapse-${item.col_id}" class="accordion__body collapse " aria-labelledby="accord-${item.col_id}" data-bs-parent="#accordion-nine" style="">
                                <div class="accordion-body-text">
                                  ${item.col_goods_text}
                                </div>
                            </div>
                        </div>
                            `;
            });

            $("#accordion-nine").html(html);
          } else {
            $("#accordion-nine").html(`
                        <div class="alert alert-warning solid alert-square "><strong>Uyarı!</strong> Eşya bilgisi bulunamadı</div>
                        `);
          }
        }
      },
    });
  },

  getDetailFiles: function () {
    let id = ApplicationFile.trademark_id;
    $.ajax({
      url: "/brand/domestic/appeal-files",
      method: "get",
      data: {
        id,
      },
      beforeSend: function () {
        $("#design-rows").html(`
               <tr>
                    <td colspan="6" class="text-center">
                        <div class="spinner-border text-primary m-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
                `);
      },
      success: function (response) {
        console.log("files log", response);
        if (response?.data?.length > 0) {
          let data = response.data;
          let html = "";
          data.map((item) => {
            html += `  <tr>
            <td>${item.col_name}</td>
            <td>${item.col_content_type}</td>
            <td>${parseFloat(item.col_size)}</td>
            <td>
            <a href="javascript:void(0)"
              class="text-primary text-decoration-underline"
            >İndir</a>
            </td>
        
        </tr>`;
          });

          $("#design-rows").html(html);
        } else {
          $("#design-rows").html(`
                 <tr>
                        <td colspan="6" class="text-center">
                            <span class="text-danger">Veri bulunamadı</span>
                        </td>
                    </tr>
                    `);
        }
      },
    });
  },
  getDetailInvoice: function () {
    let id = ApplicationFile.trademark_id;
    $.ajax({
      url: `/brand/international/yda-detail-modal?col_id=${id}&type=invoice`,
      method: "get",
      data: {},
      beforeSend: function () {
        $("#invoice-tbody").html(`
          <tr>
            <td colspan="6" class="text-center">
                <div class="spinner-border text-primary m-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
          </tr>
        `);
      },
      success: function (response) {
        console.log("invoices", response);
        if (response?.data?.length > 0) {
          let data = response.data;
          let tableRow = "";

          console.log(response, "response");
          data.forEach((item) => {
            tableRow += `
              <tr>
                <td>${item.col_apb_reference_number}</td>
                <td>${item.col_ettn}</td>
                <td>${item.col_lks_date}</td>
                <td>${item.col_total_tl_cost}</td>
                <td>${item.col_comment}</td>
                <td>${item.col_status}</td>
                <td>Yapım Aşamasında!</td>
              </tr>
            `;
          });

          $("#invoice-tbody").html(tableRow);
        } else {
          $("#invoice-tbody").html(`
                <tr>
                <td colspan="6" class="text-center">
                    <span class="text-danger">Veri bulunamadı</span>
                </td>
                </tr>
            `);
        }
      },
    });
  },

  init: function () {
    this.dataTable("#applicationFileList");
  },
  run: function () {
    this.init();
  },
};

$(document).ready(function () {
  ApplicationFile.run();
});

$('input[class="basvuru-tarihi"]').daterangepicker({
  // onchange
  autoUpdateInput: false,
});
