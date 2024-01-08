let ApplicationFile = {
  patentId: null,
  dataTable: function (tableId) {
    $(`${tableId} thead tr`)
    .clone(true)
    .addClass("filters")
    .appendTo(`${tableId} thead`);

    $(tableId)
      .DataTable({
        orderCellsTop: true,
        fixedHeader: true,
        dataSrc: "data",
        language: {
          url: scriptData.dataTableJsonLang,
          paginate: {
            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
            previous:
              '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
          },
        },

        ajax: {
          url: "/patent/application-list-json?type="+scriptData.type,
          type: "GET",
          beforeSend: function (xhr) {
            //   $(".main-preloader").removeClass("d-none");
          },

          error: function (xhr, error, code) {
            console.log(xhr);
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

        columnDefs: [
          {
            targets: 0, // center
            className: "text-center", // style: "width: 76px;",
            render: function (data, type, row, meta) {
              return `
              ${row.firma_referans_numarasi}
              `;
            },
          },
          {
            targets: 1,
            render: function (data, type, row, meta) {
              return `
              ${row.dosya_numarasi ?? "-"}
              `;
            },
          },
          {
            targets: 2,
            render: function (data, type, row, meta) {
              return `
              ${row.basvuru_turu ?? "-"}
              `;
            },
          },

          {
            targets: 3,
            render: function (data, type, row, meta) {
              return `
              <span onclick="ApplicationFile.getDetailInfo(${row.id})" class="text-primary text-decoration-underline" data-bs-toggle="modal" data-bs-target=".detay-modal">${row.baslik}</span>

              `;
            },
          },
          {
            targets: 4,
            render: function (data, type, row, meta) {
              return `
              ${row.son_durum_detay ?? "-"} 
              `;
            },
          },
          {
            targets: 5,
            render: function (data, type, row, meta) {
              return `
              ${row.ulke ?? "-"} 
              `;
            },
          },
          {
            targets: 6,
            render: function (data, type, row, meta) {
              return `
              ${row.basvuru_numarasi ?? "-"}
              `;
            },
          },
          {
            targets: 7,
            render: function (data, type, row, meta) {
              return `
              ${row.basvuru_tarihi ?? "-"}
              `;
            },
          },
          {
            targets: 8,
            render: function (data, type, row, meta) {
              return `
              ${row.ep_yayin_numarasi ?? "-"}
              `;
            },
          },
          {
            targets: 9,
            render: function (data, type, row, meta) {
              return `
              ${row.patent_numarasi ?? "-"}
              `;
            },
          },
          {
            targets: 10,
            render: function (data, type, row, meta) {
              return `
              ${row.patent_belge_tarihi ?? "-"}
              `;
            },
          },
          {
            targets: 11,
            render: function (data, type, row, meta) {
              return `
              ${row.gelecek_taksit_tarihi ?? "-"}
              `;
            },
          },
          {
            targets: 12,
            render: function (data, type, row, meta) {
              return `
              ${row.gelecek_taksit_yili ?? "-"}
              `;
            },
          },
          {
            targets: 13,
            render: function (data, type, row, meta) {
              return `
              ${row.aciklama ? row.aciklama.substring(0, 100) + "..." : "-"}
              `;
            },
          },
        ],

        paging: true,
        ordering: true,
        info: true,
        responsive: true,
        autoWidth: false,
        lengthChange: true,
        pageLength: 10,
        order: [[7, "desc"]],
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

  getDetailInfo: function (id) {
    ApplicationFile.patentId = id;
    if ($('.profile-tab a[data-bs-toggle="tab"]').hasClass("active")) {
      let tab = $('.profile-tab  a[data-bs-toggle="tab"].active').attr("href");

      switch (tab) {
        case "#islemler":
          ApplicationFile.getDetailActions();
          break;

        case "#ulkeler2":
          ApplicationFile.getDetailEntrance();
          break;

        case "#dosyalar":
          ApplicationFile.getDetailFiles();
          break;

        case "#getDetailPublications":
          ApplicationFile.getDetailPublications();
          break;

        case "#taksit":
          ApplicationFile.getDetailInstallment();
          break;

        case "#ek":
          ApplicationFile.getDetailAdditional();
          break;

        case "#faturalar":
          ApplicationFile.getDetailInvoice();
          break;

        case "#ruchan":
          ApplicationFile.getPriority();
          break;
      }
    }
    $.ajax({
      url: "/patent/application-detail-json?col_id=" + id,
      method: "GET",
      data: {},
      beforeSend: function () {
        ApplicationFile.loading();
      },
      success: function (response) {
        if (response.data) {
          let data = response.data;

          console.log("patent", data);

          $("#dosya_numarasi").text(data.dosya_numarasi ?? "-");
          $("#firma_referans_numarasi").text(
            data.firma_referans_numarasi ?? "-"
          );
          $("#basvuru_numarasi").text(data.basvuru_numarasi) ?? "-";
          $("#baslik").text(data.baslik ?? "-");
          $("#basvuru_turu").text(data.basvuru_turu ?? "-");
          $("#basvuru_sistemi").text(data.basvuru_sistemi ?? "-");
          $("#basvuru_tarihi").text(data.basvuru_tarihi ?? "-");
          $("#ulke").text(data.ulke ?? "-");
          $("#son_durum_detay").text(data.son_durum_detay ?? "-");

          if (response.dosya) {
            let dosya = response.dosya;
            $("#col_preparing_person").val(dosya.col_preparing_person ?? "-");
            $("#col_preparing_person-d").val(dosya.col_preparing_person ?? "-");
            $("#col_spec_creator").val(dosya.col_spec_creator ?? "-");
            $("#account_title").val(dosya.account_title ?? "-");
            $("#col_poll_count").val(dosya.col_poll_count ?? "-");
            $("#col_ipc").val(dosya.col_ipc ?? "-");
            $("#tubitak_type").val(dosya.tubitak_type ?? "-");
          }

          if (response.inventor) {
            let buluscular = "";

            response.inventor.forEach((element) => {
              buluscular += `\n${element.col_title} ${element.col_tckn}`;
            });

            $("#buluscular").val(buluscular);
          }

          ApplicationFile.endLoading();
        }
      },
    });
  },

  getPriority: function () {
    let id = ApplicationFile.patentId;
    $.ajax({
      url: `/patent/appeal-file-priority`,
      method: "get",
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

          console.log(data, "datap");
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
  getDetailActions: function () {
    let id = ApplicationFile.patentId;
    $.ajax({
      url: "/patent/application-detail-actions-json?col_id=" + id,
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
            <td>${item.islem}</td>
            <td>${item.col_process_date}</td>
            <td>${item.col_process_due_date}</td>
            <td>${
              item.col_process_comment == null ? "-" : item.col_process_comment
            }</td>
            <td>${item.kullanici_adi}</td>
            <td>${item.detay}</td>
            <td>-</td>
            <td>-</td>
        
        </tr>`;
          });

          $("#actionsContainer").html(html);
        } else {
          $("#actionsContainer").html(`
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
  getDetailFiles: function () {
    let id = ApplicationFile.patentId;
    $.ajax({
      url: "/patent/application-detail-modal-files-json?col_id=" + id,
      method: "get",
      data: {},
      beforeSend: function () {
        $("#filesContainer").html(`
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

          $("#filesContainer").html(html);
        }
      },
    });
  },
  getDetailEntrance: function () {
    let id = ApplicationFile.patentId;

    $.ajax({
      url: "/patent/application-detail-modal-country-json?col_id=" + id,
      method: "get",
      data: {},
      beforeSend: function () {
        // ulke-container
        $("#ulke-loading").html(`
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

          $("#ulke-loading").html("");

          $("#col_application_number").val(data.col_application_number ?? "-");
          $("#col_application_date").val(data.col_application_date ?? "-");
          $("#col_eppublication_number").val(
            data.col_eppublication_number ?? "-"
          );

          $("#col_epapproval_date").val(data.col_epapproval_date ?? "-");

          $("#col_eppublication_date").val(data.col_eppublication_date ?? "-");
          $("#col_eppublication_number").val(
            data.col_eppublication_number ?? "-"
          );
          $("#col_eppublication_date").val(data.col_eppublication_date ?? "-");
          $("#col_eapcvalidity_duedate").val(
            data.col_eapcvalidity_duedate ?? "-"
          );

          $("#ref_temporary_file_id").val(
            data.ref_temporary_file_id ? "VAR" : "YOK"
          );

          $("#col_pctapplication_date").val(
            data.col_pctapplication_date ?? "-"
          );

          $("#col_pctapplication_number").val(
            data.col_pctapplication_number ?? "-"
          );

          $("#col_pctpublication_number").val(
            data.col_pctpublication_number ?? "-"
          );

          $("#col_pctpublication_date").val(
            data.col_pctpublication_date ?? "-"
          );
        }
      },
    });
  },
  getDetailPublications: function () {
    $.ajax({
      url: "/patent/publication-information?col_id=" + ApplicationFile.patentId,
      method: "get",
      data: {},
      beforeSend: function () {
        ApplicationFile.patentPublicationReset();
        $(".publishing-container-message").show();
        $(".publishing-container-message").html(`
          <div class="text-center">
          <div class="spinner-border text-primary m-2" role="status">
          <span class="visually-hidden">Loading...</span>
            </div>
           </div>
        `);
      },
      success: function (response) {
        console.log(response, "response");
        $(".publishing-container-message").hide();

        if (response.success) {
          console.log(response.data, "response.data");
          let data = response.data;
          $("#yayin_no").val(data.yayin_no ?? "-");
          $("#yayin_no-2").val(data.yayin_no ?? "-");
          $("#col_report_pub_date").val(data.col_report_pub_date ?? "-");
          $("#col_early_period_pub_date").val(
            data.col_early_period_pub_date ?? "-"
          );
          $("#col_force_major_pub_date").val(
            data.col_force_major_pub_date ?? "-"
          );
          $("#col_patent_paper_cert_date").val(
            data.col_patent_paper_cert_date ?? "-"
          );

          $("#basvuru_sistemi").val(data.basvuru_sistemi ?? "-");
          $("#col_patent_paper_date").val(data.col_patent_paper_date ?? "-");
          $("#col_patent_paper_cert_date").val(
            data.col_patent_paper_cert_date ?? "-"
          );

          $("#col_disposal_evidence_due_date").val(
            data.col_disposal_evidence_due_date ?? "-"
          );
          ("");
          $("#col_disposal_evidence_date").val(
            data.col_disposal_evidence_date ?? "-"
          );

          $("#patent_fm_verildi_mi").val(
            data.col_patent_paper_cert_date ? "EVET" : "HAYIR"
          );
        } else {
        }
      },
    });
  },
  getDetailInstallment: function () {
    $.ajax({
      url:
        "/patent/application-detail-modal-installment-json?col_id=" +
        ApplicationFile.patentId,
      method: "get",
      data: {},
      beforeSend: function () {
        $("#installment-loading").html(`
          <div class="text-center">
          <div class="spinner-border text-primary m-2" role="status">
          <span class="visually-hidden">Loading...</span>
            </div>
           </div>
        `);
      },
      success: function (response) {
        $("#installment-loading").html("");

        if (response.data) {
          let data = response.data;

          $("#col_installment_pursuance").val(
            data.col_installment_pursuance ? "VAR" : "YOK"
          );
          $("#installmentrepresentative_col_id").val(
            data.installmentrepresentative_col_id ?? "-"
          );

          $("#col_installment_reminding").val(
            data.col_installment_reminding ? "VAR" : "YOK"
          );

          $("#col_last_paid_date").val(data.col_last_paid_date ?? "-");
          $("#col_next_paid_year").val(data.col_next_paid_year ?? "-");
          $("#col_last_paid_year").val(data.col_last_paid_year ?? "-");

          $("#col_limitation_paid").val(
            data.col_limitation_paid ? "VAR" : "YOK"
          );

          $("#col_major_unpaid_order").val(data.col_major_unpaid_order);

          $("#col_comment").val(data.col_comment ?? "-");
          $("#col_last_paid_year").val(data.col_last_paid_year ?? "-");
          $("#col_next_paid_date").val(data.col_next_paid_date ?? "-");
        }
      },
    });
  },
  getDetailAdditional: function () {
    let id = ApplicationFile.patentId;
    $.ajax({
      url: "/patent/application-detail-modal-file-additional-json?col_id=" + id,
      method: "get",
      data: {},
      beforeSend: function () {
        $("#additional-loading").html(`
          <div class="text-center">
          <div class="spinner-border text-primary m-2" role="status">
          <span class="visually-hidden">Loading...</span>
            </div>
           </div>
        `);
      },
      success: function (response) {
        $("#additional-loading").html("");
        console.log("additional", response);

        if (response.data) {
          let data = response.data;
          $("#col_additional_patent_appnum").val(
            data.col_additional_patent_appnum ?? "-"
          );
          $("#col_additional_patent_filenum").val(
            data.col_additional_patent_filenum ?? "-"
          );
          $("#col_additional_patent_filling_date").val(
            data.col_additional_patent_filling_date ?? "-"
          );
          $("#col_divided_patent_filenum").val(
            data.col_divided_patent_filenum ?? "-"
          );
          $("#col_divided_patent_appnum").val(
            data.col_divided_patent_appnum ?? "-"
          );
          $("#col_divided_patent_filling_date").val(
            data.col_divided_patent_filling_date ?? "-"
          );
        }
      },
    });
  },
  getDetailInvoice: function () {
    $.ajax({
      url:
        "/patent/application-detail-modal-file-invoice-json?col_id=" +
        ApplicationFile.patentId,
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
        console.log(response, "response");
        if (response.success == true) {
          if (response.data) {
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
          }
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

  patentPublicationReset: function () {
    $("#yayin_no").val("");
    $("#yayin_no-2").val("");
    $("#col_report_pub_date").val("");
    $("#col_early_period_pub_date").val("");
    $("#col_force_major_pub_date").val("");
    $("#col_patent_paper_cert_date").val("");

    $("#basvuru_sistemi").val("");
    $("#col_patent_paper_date").val("");
    $("#col_patent_paper_cert_date").val("");
    $("#col_disposal_evidence_due_date").val("");
    $("#col_disposal_evidence_date").val("");
    $("#patent_fm_verildi_mi").val("");
  },

  init: function () {
    this.dataTable("#applicationFileList");
    setTimeout(() => {
      this.initCustomColumnDate();
    }, 1000);
  },
  run: function () {
    this.init();
  },
};

$(document).ready(function () {
  ApplicationFile.run();
});
