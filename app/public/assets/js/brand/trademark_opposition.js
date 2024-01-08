let ApplicationFileOpp = {
  trademark_id: null,
  oppositionResult: null,
  itirazEdilecekMarkaId: null,
  dataTable: function (tableId) {

    $('#applicationFileList thead tr')
    .clone(true)
    .addClass('filters')
    .appendTo('#applicationFileList thead');


    $(tableId)
      .DataTable({
        // serverSide: true,
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
        responsive: true,
        processing: true,
        order: [[0, "desc"]],
        pageLength: 10,

        language: {
          url: scriptData.dataTableJsonLang,
          paginate: {
            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
            previous:
              '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
          },
        },
        ajax: {
          url: "/brand/domestic/appeal-file-json",
          type: "GET",
          beforeSend: function (xhr) {
            setTimeout(() => {
              $(".main-preloader").removeClass("d-none");
              $(".main-preloader ").css("display", "flex");
            }, 500);
          },

          error: function (xhr, error, code) {
            console.log(xhr);
          },
        },

        drawCallback: function (settings) {
          //
          $(".main-preloader").addClass("d-none");
          $(".main-preloader").css("display", "none");
        },

        columnDefs: [
          {
            targets: 0, // center
            className: "text-center", // style: "width: 76px;",
            render: function (data, type, row, meta) {
              return `
              ${row.col_application_number}
            `;
            },
          },
          {
            targets: 1,
            render: function (data, type, row, meta) {
              return `
              ${row.col_ground_trademark}
            `;
            },
          },
          {
            targets: 2,
            render: function (data, type, row, meta) {
              return `
            ${row.col_application_number ? row.col_application_number : "-"} 
            `;
            },
          },

          {
            targets: 3,
            render: function (data, type, row, meta) {
              return `
              <spam onclick="ApplicationFileOpp.getTrademarkInfo(${
                row.col_id
              })" data-bs-toggle="modal" data-bs-target=".detay-modal" class="text-primary text-decoration-underline">

            ${row.col_trademark ?? "-"} 
            </spam>
            `;
            },
          },
          {
            targets: 4,
            render: function (data, type, row, meta) {
              return `
              ${row.col_opp_type}

            `;
            },
          },
          {
            targets: 5,
            render: function (data, type, row, meta) {
              return `
            ${row.col_applicant ?? "-"} 
            `;
            },
          },

          {
            targets: 6,
            render: function (data, type, row, meta) {
              return `
              -
            `;
            },
          },
          {
            targets: 7,
            render: function (data, type, row, meta) {
              return `
       ${row.col_last_status}
            `;
            },
          },
          {
            targets: 8,
            render: function (data, type, row, meta) {
              return `
            ${row.col_first_opposition_result ?? "-"} 
            `;
            },
          },
          {
            targets: 9,
            render: function (data, type, row, meta) {
              return `
            ${row.col_second_opposition_result ?? "-"}
            `;
            },
          },
        ],
      })
      .columns.adjust();
  },

  loading: function () {
    $(".main-preloader").removeClass("d-none");
    $(".main-preloader ").css("display", "flex");
  },

  endLoading: function () {
    $(".main-preloader").addClass("d-none");
    $(".main-preloader").css("display", "none");
  },

  getDetailInvoice: function () {
    let id = ApplicationFileOpp.trademark_id;
    $.ajax({
      url: `/brand/domestic/appeal-file-invoices?col_id=${id}`,
      method: "get",
      data: {},
      beforeSend: function () {
        $("#itiraz_invoice-tbody").html(`
          <tr>
            <td colspan="6" class="text-center">
                <div class="spinner-border text-primary m-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
          </tr>
        `);
      },
      success: function (response) {
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

          $("#itiraz_invoice-tbody").html(tableRow);
        } else {
          $("#itiraz_invoice-tbody").html(`
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

  getTrademarkInfo: function (col_id) {
    ApplicationFileOpp.trademark_id = col_id;
    if ($('.profile-tab a[data-bs-toggle="tab"]').hasClass("active")) {
      let tab = $('.profile-tab  a[data-bs-toggle="tab"].active').attr("href");

      switch (tab) {
        case "#itiraz_esyaListesi":
          ApplicationFileOpp.getClassList();
          break;

        case "#itiraz_islemler":
          ApplicationFileOpp.getActionList();
          break;

        case "#itiraz_islemler":
          ApplicationFileOpp.getDetailInvoice();
          break;
      }
    }
    $.ajax({
      url: `/brand/domestic/appeal-file-detail`,
      method: "get",
      data: {
        col_id: col_id,
      },
      beforeSend: function () {
        ApplicationFileOpp.loading();
      },
      success: function (response) {
        if (response.data) {
          let data = response.data;
          console.log(response, "res");

          // Modal açıldığında itiraz edilecek markanın bilgisi tut
          ApplicationFileOpp.itirazEdilecekMarkaId =
            response.itiraz_edilecek_marka_id;

          localStorage.setItem(
            "itirazEdilecekMarkaId",
            response.itiraz_edilecek_marka_id
          );

          ApplicationFileOpp.oppositionResult = data;

          $("#itiraz_col_file_number").text(data.col_file_number);
          $("#itiraz_col_trademark").text(data.col_trademark);
          $("#itiraz_col_applicant").text(data.col_applicant);
          $("#itiraz_col_application_number").text(data.col_application_number);

          $("#itiraz_col_last_status").val(data.col_last_status);
          $("#itiraz_col_opposition_due_date").val(
            data.col_opposition_due_date
          );
          $("#itiraz_col_application_number").val(data.col_application_number);
          $("#itiraz_col_applicant-inpt").val(data.col_applicant);

          $("#itiraz_col_opp_type").val(data.col_opp_type);
          $("#itiraz_col_msg_type").val(data.col_msg_type);

          $("#itiraz_col_first_opposition_result").val(
            data.col_first_opposition_result
              ? data.col_first_opposition_result
              : "-"
          );
          $("#itiraz_col_second_opposition_result").val(
            data.col_second_opposition_result
              ? data.col_second_opposition_result
              : "-"
          );
        }

        ApplicationFileOpp.endLoading();
      },
      error: function (xhr, error, code) {
        console.log(xhr);
      },
    });
  },

  getClassList: function () {
    let result = ApplicationFileOpp.oppositionResult;

    // let classList = new Set(
    //   result.col_class_string.split(",").map((item) => item.trim())
    // );
    // classList = [...classList];

    $.ajax({
      url: `/brand/domestic/appeal-file-goods`,
      method: "get",
      data: {
        col_id: result.col_id,
      },
      beforeSend: function () {
        $(".inventory-list").html(`
        <div class="text-center">
         <div class="spinner-border text-primary m-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
        </div>
        `);
      },
      success: function (response) {
        if (response.data) {
          let data = response.data;

          let html = "";

          Object.keys(data).forEach((key) => {
            let goods_text = data[key];
            html += `
          <div class="accordion-item">
          <div class="accordion-header rounded-lg collapsed" id="accord-${key}" data-bs-toggle="collapse" data-bs-target="#collapse-${key}" aria-controls="collapse-${key}" aria-expanded="false" role="button">
          <span class="accordion-header-icon"></span>
          <span class="accordion-header-text">${key}</span>
          <span class="accordion-header-indicator"></span>
          </div>
          <div id="collapse-${key}" class="accordion__body collapse " aria-labelledby="accord-${key}" data-bs-parent="#accordion-nine" style="">
          <div class="accordion-body-text">
          ${goods_text}
          </div>
          </div>
          </div>
    `;
          });

          $(".inventory-list").html(html);
        }
      },
    });
  },

  getActionList: function () {
    let id = ApplicationFileOpp.trademark_id;

    $.ajax({
      url: `/brand/domestic/appeal-file-actions`,
      method: "get",
      data: {
        col_id: id,
      },
      beforeSend: function () {
        $("#itiraz_actionsContainer").html(`
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
        console.log(response);
        if (response?.data?.length > 0) {
          let data = response.data;
          let html = "";
          data.map((item) => {
            html += `  <tr>
            <td>${item.col_description}</td>
            <td>${item.col_process_date}</td>
            <td>${item.col_process_due_date}</td>
            <td>${item.col_process_comment}</td>
            </td>
          
            <td >${item.son_durum}
            </td>
        
        </tr>`;
          });

          $("#itiraz_actionsContainer").html(html);
        } else {
          $("#itiraz_actionsContainer").html(`
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
  ApplicationFileOpp.run();
});

$('input[class="basvuru-tarihi"]').daterangepicker({
  // onchange
  autoUpdateInput: false,
});
