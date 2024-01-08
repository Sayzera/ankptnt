let ApplicationFile = {
  trademark_id: null,

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

          console.log("data", data);
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
});
