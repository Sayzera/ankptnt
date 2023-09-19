

let ApplicationFile = {
    dataTable: function (tableId) {
        // $(`${tableId} thead tr`)
        //     .clone(true)
        //     .addClass("filters")
        //     .appendTo(`${tableId} thead`);
        $(tableId);
        $(tableId).DataTable({
            destroy: true,
            paging: true,
            ordering: true,
            autoWidth: false,
            //  page length
            pageLength: 10,
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
                // $(".main-preloader").addClass("d-none");
                console.log(settings.json);
            },

            columnDefs: [
                {
                    targets: 0,
                    // center
                    className: "text-center",
                    // style: "width: 76px;",
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
            <a class="text-primary text-decoration-underline" href="#" data-bs-toggle="modal" data-bs-target=".detay-modal">
																										<span>${row.col_trademark}</span>
																									</a>
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
            ${row.col_registration_number ?? '-'}
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

                        siniflar = siniflar.split("-");


                        siniflar =  siniflar.map((item) => {
                            return parseInt(item)
                        }).join("-")



                        let url = `/company/observation/${
                            siniflar
                        }/${row.col_trademark}/${row.col_id}/ydn`.replaceAll(" ", "");
                        return ` <a href="${url}" class="text-primary text-decoration-underline">Gözlemi Başlat</a>
            `;
                    },
                },
            ],

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

            // initComplete: function () {
            //     var api = this.api();
            //
            //     // For each column
            //     api
            //         .columns()
            //         .eq(0)
            //         .each(function (colIdx) {
            //             // Set the header cell to contain the input element
            //             var cell = $(".filters th").eq(
            //                 $(api.column(colIdx).header()).index()
            //             );
            //
            //             var title = $(cell).text();
            //
            //             if (title == "Başvuru Tarihi") {
            //                 $(cell).html(
            //                     '<input type="text" class="basvuru-tarihi"   placeholder="' +
            //                     title +
            //                     '" />'
            //                 );
            //             } else if (title == "Yenileme Tarihi") {
            //                 $(cell).html(
            //                     '<input type="text" class="yenileme-tarihi"  placeholder="' +
            //                     title +
            //                     '" />'
            //                 );
            //             } else {
            //                 $(cell).html('<input type="text" placeholder="' + title + '" />');
            //             }
            //
            //             // On every keypress in this input
            //             $(
            //                 "input",
            //                 $(".filters th").eq($(api.column(colIdx).header()).index())
            //             )
            //                 .off("keyup change")
            //                 .on("change", function (e) {
            //                     // Get the search value
            //                     $(this).attr("title", $(this).val());
            //                     var regexr = "({search})"; //$(this).parents('th').find('select').val();
            //
            //                     var cursorPosition = this.selectionStart;
            //                     // Search the column for that value
            //                     api
            //                         .column(colIdx)
            //                         .search(
            //                             this.value != ""
            //                                 ? regexr.replace("{search}", "(((" + this.value + ")))")
            //                                 : "",
            //                             this.value != "",
            //                             this.value == ""
            //                         )
            //                         .draw();
            //                 })
            //                 .on("keyup", function (e) {
            //                     e.stopPropagation();
            //
            //                     let _this = $(this).trigger("change");
            //                     $(this)
            //                         .focus()[0]
            //                         .setSelectionRange(cursorPosition, cursorPosition);
            //                 });
            //         });
            // },
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

$('input[class="basvuru-tarihi"]').daterangepicker({
    // onchange
    autoUpdateInput: false,
});
