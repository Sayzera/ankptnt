let YDAApplicationFile = {
  trademark_id: null,

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
    YDAApplicationFile.trademark_id = id;

    if ($('.profile-tab a[data-bs-toggle="tab"]').hasClass("active")) {
      let tab = $('.profile-tab  a[data-bs-toggle="tab"].active').attr("href");

      switch (tab) {
        case "#islemler":
          YDAApplicationFile.getActionList();
          break;

        case "#esyaListesi":
          YDAApplicationFile.getClassList();
          break;
      }
    }

    $.ajax({
      url: `/brand/international/yda-detail-modal?col_id=${id}&type=ydn`,
      method: "get",
      data: {},
      beforeSend: function () {
        YDAApplicationFile.loading();
      },
      success: function (response) {
        if (response.data) {
          let data = response.data;
          console.log(data);
          $("#yda_col_account_referance_number").text(
            data.col_account_referance_number
          );
          $("#yda_col_trademark").text(data.col_trademark);
          $("#yda_col_distinctive_trademark").text(
            data.col_distinctive_trademark
          );
          $("#yda_col_application_number").text(data.col_application_number);
          $("#yda_col_application_date").text(data.col_application_date);
          $("#yda_col_c_file_number").text(data.col_c_file_number);

          $("#yda_col_class_string").text(data.col_class_string);

          $("#yda_col_application_system").val(data.col_application_system);
          $("#yda_col_application_number").val(data.col_application_number);
          $("#yda_col_renewal_date").val(data.col_renewal_date);
          $("#yda_col_country").val(data.col_country);
          $("#yda_col_last_status").val(data.col_last_status);
          $("#yda_col_publication_date").val(data.col_publication_date);

          YDAApplicationFile.endLoading();
        }
      },
      error: function (xhr, error, code) {
        console.log(xhr);
      },
    });
  },

  getActionList: function () {
    let id = YDAApplicationFile.trademark_id;

    $.ajax({
      url: `/brand/international/yda-detail-modal?col_id=${id}&type=actions`,
      method: "get",
      data: {},
      beforeSend: function () {
        $("#yda_actionsContainer").html(`
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

          $("#yda_actionsContainer").html(html);
        } else {
          $("#yda_actionsContainer").html(`
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
    let id = YDAApplicationFile.trademark_id;
    $.ajax({
      url: `/brand/international/yda-detail-modal?col_id=${id}&type=classes`,
      method: "get",
      data: {},
      beforeSend: function () {
        $("#yda_accordion-nine").html(`
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

            $("#yda_accordion-nine").html(html);
          } else {
            $("#yda_accordion-nine").html(`
                        <div class="alert alert-warning solid alert-square "><strong>Uyarı!</strong> Eşya bilgisi bulunamadı</div>
                        `);
          }
        }
      },
    });
  },

  init: function () {},
  run: function () {
    this.init();
  },
};

$(document).ready(function () {
  YDAApplicationFile.run();
});

$('input[class="basvuru-tarihi"]').daterangepicker({
  // onchange
  autoUpdateInput: false,
});
