let ApplicationFile = {
  trademark_id: null,
  gozlem_durumu: null,

  aktifAciklama : `Markanız 'Hızlı Gözlem' listesindedir. Bu işlem sizin daha hızlı gözlem yapmanızı sağlar. 'Hızlı Gözlem' listesinden kaldırmak için tıklayınız. `,
  pasifAciklama : `Markanızın gözlemi pasif durumdadır. Bu işlem sizin daha hızlı filtreleme yapmanızı sağlar. Gözlemi aktif yapmak için tıklayınız.`,
  dataTable: function (tableId) {
    $(tableId + " thead tr")
      .clone(true)
      .addClass("filters")
      .appendTo(tableId + " thead");

    $(`${tableId} thead tr`);
    // .clone(true)
    // .addClass("filters")
    // .appendTo(`${tableId} thead`);
    $(tableId);
    $(tableId).DataTable({
      destroy: true,
      paging: true,
      ordering: false,
      autoWidth: false, //  page length
      pageLength: 100,
      dataSrc: "data",
      responsive: true,

      pagingType: "simple_numbers",
      searching: true,

      ajax: {
        url: scriptData.getTrademarkUrl,
        type: "GET",
     
        beforeSend: function (xhr) {
          //   $(".main-preloader").removeClass("d-none");
        },

        error: function (xhr, error, code) {
          console.log(xhr);
        },
      },

      drawCallback: function (settings) {
        tippy('[data-tippy-content]', {
          theme: 'tomato',
        });
        // $(".main-preloader").addClass("d-none");
        let json = settings.json;

     

        if (json?.data?.length > 0) {
          let data = json.data;
          let trademarkData = [];

          data.forEach((item, index) => {
            let classString = item.col_class_string.replaceAll(",", "-");
            classString = classString
              .split("-")
              .map((item) => parseInt(item))
              .join("-");

            let id = item.col_id;
            let trademark = item.col_trademark;

            // company/observation/11/eternity/17748749

            let url = `/company/observation/${classString}/${trademark}/${id}?type=${item.type}`;

            trademarkData.push({
              order: index + 1,
              trademark: item.col_trademark,
              id: item.col_id,
              url: url,
              classString: classString,
            });
          });

          // storage
          localStorage.setItem("trademarkData", JSON.stringify(trademarkData));
        }
      },

      columnDefs: [
        // {
        //   targets: 0,
        //   className: "text-center",
        //   render: function (data, type, row, meta) {
        //     return ` ${row.type == 1 ? "Yurtiçi" : "Yurtdışı"} `;
        //   },
        // },
        // {
        //   targets: 1, // center
        //   className: "text-center", // style: "width: 76px;",
        //   render: function (data, type, row, meta) {
        //     // return `
        //     // <div style="text-align: left; width: 76px;"><img class="v-image v-widget" src="http://apiz.ankarapatent.com/fileServer/tmImage?res=64&amp;appNo=${row.col_application_number}" alt=""></div>
        //     // `;

        //     return "";
        //   },
        // },
        {
          targets: 0,
          render: function (data, type, row, meta) {
            return `
          ${
            row.type == 1
              ? `<span class="text-primary text-decoration-underline "
                onclick="ApplicationFile.getTrademarkInfo(${row.col_id})"
                data-bs-toggle="modal"
                data-bs-target=".detay-modal"
              >
                <span>${row.col_trademark}</span>
              </span>`
              : `<span
                class="text-primary text-decoration-underline "
                onclick="YDAApplicationFile.getTrademarkInfo(${row.col_id})"
                data-bs-toggle="modal"
                data-bs-target=".yda-detay-modal"
              >
                <span>${row.col_trademark}</span>
              </span>`
          }
            `;
          },
        },
        {
          targets: 1,
          render: function (data, type, row, meta) {
            return `
            ${row.col_class_string ? row.col_class_string : "-"} 
            `;
          },
        },

        // {
        //   targets: 4,
        //   render: function (data, type, row, meta) {
        //     return `
        //     ${row.col_file_number ?? "-"}
        //     `;
        //   },
        // },
        {
          targets: 2,
          render: function (data, type, row, meta) {
            return `
            ${row.col_application_number ?? "-"} 
            `;
          },
        },
        {
          targets: 3,
          render: function (data, type, row, meta) {
            return `
            ${row.col_registration_date ?? "-"} 
            `;
          },
        },
        {
          targets: 4,
          render: function (data, type, row, meta) {
            return `
            ${row.col_registration_number ?? "-"}
            `;
          },
        },
        {
          targets: 5,

          render: function (data, type, row, meta) {
            let siniflar = row.col_class_string.replaceAll(",", "-");

            let col_last_status = row.col_last_status === "Red" ? true : false;

            siniflar = siniflar.split("-");

            siniflar = siniflar
              .map((item) => {
                return parseInt(item);
              })
              .join("-");

              // eğer sonunda soru işareti varsa sil
              let marka = row.col_trademark.replaceAll("?", "");


            let url =
              `/company/observation/${siniflar}/${marka}/${row.col_id}?type=${row.type}`.replaceAll(
                " ",
                ""
              );

            if (!col_last_status) {
              return ` <div class="d-flex justify-content-between items-center" style="width:100% !important">
              <a href="${url}" class="text-primary text-decoration-underline "  ${row.tescil_tarihi_durumu == true ? `data-theme='tomato' data-tippy-content="Bu markanızın tescil tarihinden itibaren 5 yıldan fazla zaman geçtiği için karşı taraf markanızın kullanıldığına dair kanıt isteyebilir."` : null}>Gözlemi Başlat</a>
             
              ${row.tescil_tarihi_durumu == true ? `<i data-tippy-content="Bu markanızın tescil tarihinden itibaren 5 yıldan fazla zaman geçtiği için karşı taraf markanızın kullanıldığına dair kanıt isteyebilir." class="fas fa-exclamation-triangle text-warning"></i>` : ''  } 
              </div>
                `;
            } else {
              return ` <a href="#" class="text-danger">Gözlem Yapılamaz</a>
                `;
            }
          },
        },
   
      ],

      // DESC
      // order: [[3, "desc"]],

      initComplete: function () {
        tippy('[data-tippy-content]', {
          theme: 'tomato',
        });

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

            console.log(title);

            if (title.trim() === "Aktif/Pasif") {
              // convert selectbox
              $(cell).html(`
              <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="aktif-pasif-select-box">
                <option value="">Seçiniz</option>
                <option value="Hızlı Gözleme Ekle">Aktif Markalar</option>
                <option value="Hızlı Gözlemden Kaldır">Pasif Markalar</option>
              </select>
              `);
            } else {
              $(cell).html('<input type="text" placeholder="' + title + '" />');
            }

            // On every keypress in this input
            $(
              "input, select",
              $(".filters th").eq($(api.column(colIdx).header()).index())
            )
              .off("keyup")
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
      url: `/brand/domestic/detail-modal?col_id=${id}&type=actions`,
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
            <td>${item.department}</td>
            <td>${item.col_process_date}</td>
            <td>${item.col_process_due_date}</td>
            <td>${item.description}</td>
            </td>
          
            <td >${item.processlaststatus}
            </td>
        
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

  markalarinGozlemDurumuAktifPasif : function(id) {
    console.log(id);

    $.ajax({
      url: '/brand/domestic/observation-status',
      method: 'get',
      data: {
        col_id: id
      },
      beforeSend: function() {
          $('#marka-aktif-pasif-div-'+id).find('.aktif-pasif-loading').removeClass('d-none');
          $('#marka-aktif-pasif-div-'+id).find('#marka-aktif-pasif-span-'+id).addClass('d-none');
      },
      success: function(response) {
          $('#marka-aktif-pasif-div-'+id).find('.aktif-pasif-loading').addClass('d-none');
          $('#marka-aktif-pasif-div-'+id).find('#marka-aktif-pasif-span-'+id).removeClass('d-none');
          $('#marka-aktif-pasif-div-'+id).find('#marka-aktif-pasif-span-'+id).text(response.durum == 1 ? 'Hızlı Gözlemden Kaldır' : 'Hızlı Gözleme Ekle');
          $('#marka-aktif-pasif-div-'+id).find('#marka-aktif-pasif-icon-'+id).attr('data-tippy-content', response.durum == 1 ? ApplicationFile.aktifAciklama : ApplicationFile.pasifAciklama);
          tippy('[data-tippy-content]', {
            theme: 'tomato',
          });
          if(response.durum == 1) {
            $('#marka-aktif-pasif-div-'+id).find('#marka-aktif-pasif-span-'+id).removeClass('text-success').addClass('text-danger');
          } else {
            $('#marka-aktif-pasif-div-'+id).find('#marka-aktif-pasif-span-'+id).removeClass('text-danger').addClass('text-success');
          }

      },
    })
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

        case "#faturalar":
          ApplicationFile.getDetailInvoice();
          break;
      }
    }

    $.ajax({
      url: `/brand/domestic/detail-modal?col_id=${id}&type=yim`,
      method: "get",
      data: {},
      success: function (response) {
        if (response.data) {
          let data = response.data;
          // console.log(data)
          $("#col_account_referance_number").text(
            data.col_account_referance_number ?? "-"
          );
          $("#col_trademark").text(data.col_trademark);
          $("#col_distinctive_trademark").text(data.col_distinctive_trademark);
          $("#col_application_number").text(data.col_application_number);
          $("#col_application_date").text(data.col_application_date);
          $("#col_c_file_number").text(data.col_c_file_number);
          $("#col_class_string").text(data.col_class_string);
          $("#col_last_status").val(data.col_last_status);
          $("#col_renewal_date").val(data.col_renewal_date);
          $("#col_secondary_publication_number").val(
            data.col_secondary_publication_number
          );
          $("#ref_trademark_bulletin_id").val(data.ref_trademark_bulletin_id);
          $("#col_basvuru_sistemi").val(data.basvuru_sistemi);
          $("#col_registration_number").val(data.col_registration_number);

          $("#col_registration_date").val(data.col_registration_date);
          $("#col_tpe_registration_date").val(data.col_tpe_registration_date);
        }
      },
      error: function (xhr, error, code) {
        console.log(xhr);
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


$(document).on('change','#aktif-pasif-select-box', function() {
  let value = $(this).val();

  if(value == 'Hızlı Gözleme Ekle') {
    value = 3;
  } else if(value == 'Hızlı Gözlemden Kaldır') {
    value = 4;
  } else {
    value = '';
  }
  // $('#applicationFileList').DataTable().column(6).search(value).draw();

  // eğer url de type yoksa ?gozlem_durumu=1 olarak ekle ama type varsa &gozlem_durumu=1 olarak ekle sonuna ekle 
  let url = new URL(window.location.href);

  url.searchParams.set("gozlem_durumu", value);

  // sayfayı urlle yenile
  window.location.href = url.href;

})

$('#bultenler').on('change', function() {
  localStorage.setItem('bulten', $(this).val());
  $('#bulten-search-action').attr('href', `/brand/domestic/international?type=${ApplicationFile.gozlem_durumu}&bulten=${$(this).val()}`)
})






});

$('input[class="basvuru-tarihi"]').daterangepicker({
  // onchange
  autoUpdateInput: false,
});

