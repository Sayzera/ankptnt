let ApplicationFile = {
  dataTable: function (tableId) {
    $(`${tableId} thead tr`)
      .clone(true)
      .addClass("filters")
      .appendTo(`${tableId} thead`);
    $(tableId)
      .DataTable({
        orderCellsTop: true,
        fixedHeader: true,
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
        order: [[0, "desc"]],

        initComplete: function () {
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

              if (title == "Başvuru Tarihi") {
                $(cell).html(
                  '<input type="text" class="basvuru-tarihi"   placeholder="' +
                    title +
                    '" />'
                );
              } else if (title == "Veriliş Tarihi") {
                $(cell).html(
                  '<input type="text" class="verilis-tarihi"  placeholder="' +
                    title +
                    '" />'
                );
              } else if (title == "Taks. Tarihi") {
                $(cell).html(
                  '<input type="text" class="taks-tarihi"  placeholder="' +
                    title +
                    '" />'
                );
              } else {
                $(cell).html(
                  '<input type="text" placeholder="' + title + '" />'
                );
              }

              // On every keypress in this input
              $(
                "input",
                $(".filters th").eq($(api.column(colIdx).header()).index())
              )
                .off("keyup change")
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
      })
      .columns.adjust();
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

    $('input[class="verilis-tarihi"]').daterangepicker({
      // onchange
      autoUpdateInput: false,
    });
    $('input[class="verilis-tarihi"]').on(
      "apply.daterangepicker",
      function (ev, picker) {
        $(this).val(
          picker.startDate.format("MM/DD/YYYY") +
            " - " +
            picker.endDate.format("MM/DD/YYYY")
        );
      }
    );

    $('input[class="taks-tarihi"]').daterangepicker({
      // onchange
      autoUpdateInput: false,
    });
    $('input[class="taks-tarihi"]').on(
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
