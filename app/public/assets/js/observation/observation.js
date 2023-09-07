let observation = {
  observationTable: function () {
    var table = $("#example5").DataTable({
      destroy: true,
      searching: true,
      paging: true,
      select: true,
      info: false,
      lengthChange: false,
      language: {
        // url: twigData.dataTableLangUrl,
        paginate: {
          next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
          previous:
            '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
        },
      },
      columnDefs: [
        {
          targets: 0,
          orderable: false,
        },
      ],
    });

    return table;
  },

  ScrollGoTo: function (className) {
    var top = $(className).offset().top - 100;
    $("html, body").animate(
      {
        scrollTop: top,
      },
      1000
    );
  },

  clickToHamburger: function (className) {
    // eğer is-active clası yoksa
    if (!$(className).hasClass("is-active")) {
      $(className).trigger("click");
    }
  },

  SelectClassInput: function () {
    $("#class-select").select2({
      placeholder: "Sınıf Seç",
    });
  },

  observationResultCount: function (className, count) {
    $(className).html(count + " adet sonuç bulundu.");
  },
  getObservationListFormData: function (e) {
    // get form values
    var formValues = $(e.target).serializeArray();
    let siniflar = [];
    formValues.forEach((element) => {
      if (element.name == "sinif") {
        siniflar.push(element.value);
      }
    });
    formValues = formValues.filter(function (item) {
      return item.name != "sinif";
    });

    formValues.push({ name: "siniflar", value: siniflar });

    let formData = new FormData();
    formValues.forEach((element) => {
      formData.append(element.name, element.value);
    });

    formData.append("token", twigData.token);

    return formData;
  },

  sendObservationListRequestWithAjax: function (formValues) {
    $.ajax({
      type: "POST",
      url: twigData.getObservationListUrl,
      data: formValues,
      processData: false,
      contentType: false,
      beforeSend: function () {
        $(".observation-search").removeClass("d-none");
      },
      success: function (response) {
        $(".form-errors").addClass("d-none");
        $(".observation-search").addClass("d-none");
        $(".gozlem-container-preloader").addClass("d-none");
        $(".gozlem-container").removeClass("d-none");

        observation.ScrollGoTo(".marka-gozlem-list");
        observation.clickToHamburger(".hamburger");
        observation.observationResultCount(
          ".observation-brand-result",
          response?.data?.trademarkSearchList?.length
        );
        if (response?.data?.trademarkSearchList?.length > 0) {
          let list = response.data.trademarkSearchList;

          observation.observationTable().clear().draw();
          list.forEach((element) => {
            observation
              .observationTable()
              .row.add([
                `  <td>
                <div class="form-check custom-checkbox ms-2">
                    <input type="checkbox" class="form-check-input" id="customCheckBox3" required="">
                    <label class="form-check-label" for="customCheckBox2"></label>
                </div>
            </td>`,
                element.trademarkName,
                element.trademarkName,
                `<a href="#" data-bs-toggle="modal" data-bs-target=".detay-modal" class="text-primary text-decoration-underline">
                ${element.holderName}
                </a>
`,
                element.niceClasses,
                `<a href="#" class="text-primary text-decoration-underline">
                ${element.bulletinNo}
                </a>
                `,

                element.applicationNo,
                element.shapeSimilarity,
                ' <a href="#" class="btn btn-primary btn-sm">İtiraz Et</a>',
              ])
              .draw(false);
          });
        } else {
          observation.observationTable().clear().draw();
        }
      },
      error: function (xhr) {
        $(".observation-search").addClass("d-none");

        let error = xhr.responseJSON;

        if (error?.validations) {
          let errorTemp = "";
          Object.keys(error?.validations).map((key, index) => {
            errorTemp += `<p class="text-danger mt-1" >${error?.validations[key]}</p>`;
          });

          $(".form-errors").removeClass("d-none").html(errorTemp);
        }

        commonjs.toastrError("Formda hata var. Lütfen kontrol ediniz.");
      },
    });
  },
  getObservationListOnSubmit: function (e) {
    e.preventDefault();
    let formValues = this.getObservationListFormData(e);
    this.sendObservationListRequestWithAjax(formValues);
  },

  init: function () {
    this.SelectClassInput();
  },
  run: function () {
    this.init();
  },
};

$(document).ready(function () {
  observation.run();
});
