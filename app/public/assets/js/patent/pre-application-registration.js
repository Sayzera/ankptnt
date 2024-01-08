let ApplicationFile = {
  trademark_id: null,
  col_file_number: null,
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
      autoWidth: false, //  page length
      pageLength: 10,
      dataSrc: "data",
      responsive: true,

      pagingType: "simple_numbers",
      searching: true,

      ajax: {
        url: "/patent/pre-application-registration-json",
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
          targets: 0, // center
          className: "text-center", // style: "width: 76px;",
          render: function (data, type, row, meta) {
            return `
              ${row.col_file_number ?? "-"}
              `;
          },
        },
        {
          targets: 1,
          render: function (data, type, row, meta) {
            return `
             ${row.col_account_referance_number}
              `;
          },
        },
        {
          targets: 2,
          render: function (data, type, row, meta) {
            return `
            <span class="text-primary text-decoration-underline" href="javascript:void(0)" onclick="ApplicationFile.getTrademarkInfo(${
              row.col_id
            })" data-bs-toggle="modal" data-bs-target=".detay-modal">
            ${row.col_invention_subject ? row.col_invention_subject : "-"} 

              </span>
              `;
          },
        },

        {
          targets: 3,
          render: function (data, type, row, meta) {
            return `
              ${row.col_last_status ?? "-"} 
              `;
          },
        },
        {
          targets: 4,
          render: function (data, type, row, meta) {
            return `
              ${row.col_last_process_status ?? "-"} 
              `;
          },
        },
      ],

      fixedHeader: false,
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
  getActionList: function () {
    let id = ApplicationFile.trademark_id;

    $.ajax({
      url: `/patent/pre-application-registration-detail-process-json?col_id=${id}`,
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
          console.log(data);
          let html = "";
          data.map((item) => {
            html += `  <tr>
              <td>${item.islem}</td>
              <td>${item.col_process_date}</td>
              <td>${item.col_process_due_date}</td>
              <td>${
                item.col_process_comment == null || ""
                  ? "-"
                  : item.col_process_comment
              }</td>
              <td>${item.col_name} ${item.col_surname}</td>
              </td>
              <td >${item.col_detail}  </td>
              <td>-</td>
              <td>-</td>
          
          </tr>`;
          });

          $("#actionsContainer").html(html);
        }
      },
    });
  },
  getClassList: function () {
    let id = ApplicationFile.trademark_id;
    $.ajax({
      url: `/brand/domestic/detail-modal?col_id=${id}&type=classes`,
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
  getTrademarkInfo: function (id) {
    // trademark_id = id;
    ApplicationFile.trademark_id = id;
    ApplicationFile.getActionList();

    if ($('.profile-tab a[data-bs-toggle="tab"]').hasClass("active")) {
      let tab = $('.profile-tab  a[data-bs-toggle="tab"].active').attr("href");

      switch (tab) {
        case "#islemler":
          ApplicationFile.getActionList();
          break;

        case "#esyaListesi":
          ApplicationFile.getClassList();
          break;

        case "#faturalar":
          ApplicationFile.getDetailInvoice();
          break;
      }
    }

    $.ajax({
      url: `/patent/pre-application-registration-detail-json?col_id=${id}`,
      method: "get",
      data: {},
      success: function (response) {
        if (response.data) {
          let data = response.data;
          ApplicationFile.col_file_number = data.col_file_number;

          $("#col_file_number").text(data.col_file_number);
          $("#col_account_referance_number").text(
            data.col_account_referance_number
          );
          $("#col_c_file_number").text(data.col_c_file_number);
          $("#col_invention_subject").text(data.col_invention_subject);
          $("#col_last_process_status").text(data.col_last_process_status);
          $("#col_last_status").text(data.col_last_status);
        }
      },
      error: function (xhr, error, code) {
        console.log(xhr);
      },
    });
  },
  getDetailInvoice: function () {
    let file_number = ApplicationFile.trademark_id;
    $.ajax({
      url: `/patent/pre-application-registration-detail-invoice-json`,
      method: "get",
      data: {
        file_number: file_number,
      },
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
  initCustomColumnDate: () => {
    $('input[class="basvuru-tarihi"]').daterangepicker({
      // onchange
      autoUpdateInput: false,
    });
    $('input[class="basvuru-tarihi"]').on(
      "apply.daterangepicker",
      function (ev, picker) {
        $(this).val(
          picker.startDate.format("MM/DD/YYYY") +
            " - " +
            picker.endDate.format("MM/DD/YYYY")
        );
      }
    );
    $('input[class="yenileme-tarihi"]').daterangepicker({
      // onchange
      autoUpdateInput: false,
    });
    $('input[class="yenileme-tarihi"]').on(
      "apply.daterangepicker",
      function (ev, picker) {
        $(this).val(
          picker.startDate.format("MM/DD/YYYY") +
            " - " +
            picker.endDate.format("MM/DD/YYYY")
        );
      }
    );
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
