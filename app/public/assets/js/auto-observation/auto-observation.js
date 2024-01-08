let observation = {
  observationTable: function () {
    var table = $("#example5").DataTable({
      // /company/observation/auto/get-observation-list
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

      columnDefs: [
        {
          targets: 0, // center
          className: "text-center", // style: "width: 76px;",
          render: function (data, type, row, meta) {
            return `
            <div style="text-align: left; width: 76px;"><img class="v-image v-widget" src="http://apiz.ankarapatent.com/fileServer/tmImage?res=64&amp;appNo=${row.col_application_number}" alt=""></div>           
            `;
          },
        },
        {
          targets: 1,
          render: function (data, type, row, meta) {
            return `
            <span class="text-primary text-decoration-underline " onclick="ApplicationFile.getTrademarkInfo(${row.col_id})"
            data-bs-toggle="modal" data-bs-target=".detay-modal">
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
        {
          targets: 9,

          render: function (data, type, row, meta) {
            let siniflar = row.col_class_string.replaceAll(",", "-");

            let col_last_status = row.col_last_status === "Red" ? true : false;

            siniflar = siniflar.split("-");

            siniflar = siniflar
              .map((item) => {
                return parseInt(item);
              })
              .join("-");

            let url =
              `/company/observation/${siniflar}/${row.col_trademark}/${row.col_id}`.replaceAll(
                " ",
                ""
              );

            if (!col_last_status) {
              return ` <a href="${url}" class="text-primary text-decoration-underline">Gözlemi Başlat</a>
                `;
            } else {
              return ` <a href="#" class="text-danger">Gözlem Yapılamaz</a>
                `;
            }
          },
        },
      ],
    });

    return table;
  },

  hideAutoObservationRobotDiv: function () {
    setTimeout(() => {
      $(".gozlem-container-preloader").addClass("d-none");
      $(".gozlem-container").removeClass("d-none");
    }, 1000);
  },

  init: function () {
    this.observationTable();
    this.hideAutoObservationRobotDiv();
  },
  run: function () {
    this.init();
  },
};

$(document).ready(function () {
  observation.run();
});
