let ApplicationFile = {
  patentId: null,
  dataTable: function (tableId) {
    $(''+tableId+' thead tr')
    .clone(true)
    .addClass('filters')
    .appendTo(''+tableId+' thead');
    $(tableId)
      .DataTable({
        orderCellsTop: true,
        fixedHeader: true,
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
        language: {
          url: scriptData.dataTableJsonLang,
          paginate: {
            next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
            previous:
              '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
          },
        },
        paging: true,
        ordering: true,
        info: true,
        responsive: true,
        autoWidth: false,
        lengthChange: true,
        pageLength: 10,
        order: [[5, "desc"]],
      })
      .columns.adjust();
  },

  getDesignInfo: function (id) {
    ApplicationFile.patentId = id;

    if ($('.profile-tab a[data-bs-toggle="tab"]').hasClass("active")) {
      let tab = $('.profile-tab  a[data-bs-toggle="tab"].active').attr("href");

      switch (tab) {
        case "#files":
          ApplicationFile.getDetailFiles();
          break;

        case "#ruchan":
          ApplicationFile.getDetailPriority();
          break;

        case "#invoices":
          ApplicationFile.getDetailInvoice();
          break;

        case "#actions":
          ApplicationFile.getDetailActions();
          break;

        case "#designs":
          ApplicationFile.getDetailDesigns();
          break;
      }
    }

    $.ajax({
      url: `/design-detail-modal/${id}/designInfo`,
      type: "POST",
      data: {},
      success: function (response) {
        console.log("designInfo", response);
        if (response?.data) {
          let data = response.data;

          $("#son_durum_detay").html(data.son_durum_detay);
          $("#dosya_numarasi").html(data.dosya_numarasi);
          $("#basvuru_tarihi").html(data.basvuru_tarihi);
          $("#basvuru_numarasi").html(data.basvuru_numarasi);
          $("#firma_referans_numarasi").html(data.firma_referans_numarasi);
          $("#son_durum_detay").html(data.son_durum_detay);
          $("#bulten_no").html(data.bulten_no ? data.bulten_no : "-");
          $("#dosya_sorumlusu").val(data.sorumlu);
          $("#dosya_sorumlusu-2").val(data.sorumlu);
          $("#tasarim_konusu").html(data.tasarim_konusu);
          $("#basvuru_sistemi_detay").html(data.basvuru_sistemi_detay);
          $("#uluslararasi_ulke").html(data.uluslararasi_ulke);

          $("#tescil_tarihi").val(data.tescil_tarihi);
          $("#yenileme_tarihi").val(data.yenileme_tarihi);
          $("#tasarim_sayisi").val(data.tasarim_sayisi);

          $("#resim_sayisi").val(data.col_extra_design_number);

          $('#yayin_erteleme').val(data.yayin_erteleme);
        }
      },
      error: function (error) {
        console.log(error);
      },
    });
  },

  getDetailFiles: function () {
    let id = ApplicationFile.patentId;
    $.ajax({
      url: "/design-files-modal?col_id=" + id,
      method: "get",
      data: {},
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

  getDetailActions: function () {
    let id = ApplicationFile.patentId;
    $.ajax({
      url: "/design-process-modal?col_id=" + id,
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

  getDetailInvoice: function () {
    $.ajax({
      url: "/design-invoice-modal?col_id=" + ApplicationFile.patentId,
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

  getDetailDesigns: function () {
    let id = ApplicationFile.patentId;

    $.ajax({
      url: "/design-designs-modal?col_id=" + id,
      method: "get",
      data: {},
      beforeSend: function () {
        $("#design-body").html(`
          <tr>
            <td colspan="3" class="text-center">
                <div class="spinner-border text-primary m-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                </td>
          </tr>
        `);
      },
      success: function (response) {
        let tempHtml = "";

        console.log(response);

        let data = response.data;

        $("#sinif-listesi").val(data?.siniflar?.col_class_code);

        if (data.tasarimcilar?.length > 0) {
          data.tasarimcilar.map((item, index) => {
            tempHtml += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.col_title}</td>
                <td>${item.col_address}</td>
            </tr>
        `;
          });

          $("#design-body").html(tempHtml);
        } else {
          $("#design-body").html(`
                <tr>
                  <td colspan="3" class="text-center">
                      <span class="text-danger">Veri bulunamadı</span>
                  </td>
                </tr>
            `);
        }
      },
    });
  },

  // Rüçhan
  getDetailPriority: function () {
    let id = ApplicationFile.patentId;
    $.ajax({
      url: "/design-priority-modal?col_id=" + id,
      method: "get",
      data: {},
      beforeSend: function () {
        $("#priority-rows").html(`
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
                <td>${item.col_name_en}</td>
                <td>${item.col_priority_date}</td>
                <td>${item.col_priority_due_date}</td>
            </tr>`;
          });

          $("#priority-rows").html(html);
        } else {
          $("#priority-rows").html(`
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
